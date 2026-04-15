<?php

declare(strict_types=1);

namespace App\Services\Runtime\Coolify;

use App\Services\Runtime\AgentRuntimeProvisioner;
use App\Services\Runtime\TenantHandle;
use App\Services\Runtime\TenantSpec;
use App\Services\Runtime\TenantStatus;
use App\Services\Runtime\TenantStatusSnapshot;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Str;

final class CoolifyAgentRuntimeProvisioner implements AgentRuntimeProvisioner
{
    public function __construct(
        private readonly HttpFactory $http,
        private readonly string $baseUrl,
        private readonly string $token,
        private readonly string $serverUuid,
        private readonly string $destinationUuid,
        private readonly ?string $projectUuid,
        private readonly string $imageName,
    ) {
    }

    public function create(TenantSpec $spec): TenantHandle
    {
        $tenantId = (string) Str::uuid();
        $shortId = substr(str_replace('-', '', $tenantId), 0, 12);
        $appName = 'spawn-agent-'.$shortId;
        $fqdn = 'https://'.$shortId.'.'.$spec->webhookBaseDomain;

        $payload = [
            'name' => $appName,
            'server_uuid' => $this->serverUuid,
            'destination_uuid' => $this->destinationUuid,
            'environment_name' => 'production',
            'docker_registry_image_name' => $this->imageName,
            'docker_registry_image_tag' => $spec->imageTag,
            'build_pack' => 'dockerimage',
            'ports_exposes' => '8080',
            'instant_deploy' => false,
        ];
        if ($this->projectUuid !== null) {
            $payload['project_uuid'] = $this->projectUuid;
        }

        try {
            $createResponse = $this->client()->post($this->url('/applications/public'), $payload);
            $this->assertOk($createResponse, 'create application');
        } catch (RequestException $e) {
            throw new CoolifyApiException('create application: '.$e->getMessage(), 0, $e);
        }

        $body = $createResponse->json();
        if (! is_array($body) || ! isset($body['uuid']) || ! is_string($body['uuid']) || $body['uuid'] === '') {
            throw new CoolifyApiException('create application: malformed response');
        }

        $providerRef = $body['uuid'];

        try {
            $fqdnResponse = $this->client()->patch($this->url('/applications/'.$providerRef), [
                'fqdn' => $fqdn,
            ]);
            $this->assertOk($fqdnResponse, 'set fqdn');
        } catch (RequestException $e) {
            throw new CoolifyApiException('set fqdn: '.$e->getMessage(), 0, $e);
        }

        $envVars = [
            'AGENT_NAME' => $spec->agentName,
            'AGENT_PERSONALITY' => $spec->personality,
            'TELEGRAM_BOT_TOKEN' => $spec->telegramBotToken,
            'LLM_API_KEY' => $spec->llmApiKey,
            'LLM_PROVIDER' => $spec->llmProvider,
            'WEBHOOK_PUBLIC_URL' => rtrim($fqdn, '/'),
        ];

        foreach ($envVars as $key => $value) {
            $this->upsertEnv($providerRef, $key, $value);
        }

        return new TenantHandle(
            id: $tenantId,
            providerRef: $providerRef,
            fqdn: $fqdn,
        );
    }

    public function start(TenantHandle $handle): void
    {
        try {
            $response = $this->client()->post(
                $this->url('/deploy').'?uuid='.urlencode($handle->providerRef).'&force=false'
            );
            $this->assertOk($response, 'deploy application');
        } catch (RequestException $e) {
            throw new CoolifyApiException('deploy application: '.$e->getMessage(), 0, $e);
        }

        $body = $response->json();
        if (! is_array($body) || ! isset($body['deployments']) || ! is_array($body['deployments'])) {
            throw new CoolifyApiException('deploy application: malformed response');
        }
    }

    public function status(TenantHandle $handle): TenantStatusSnapshot
    {
        try {
            $response = $this->client()->get($this->url('/applications/'.$handle->providerRef));
        } catch (RequestException $e) {
            throw new CoolifyApiException('status: '.$e->getMessage(), 0, $e);
        }

        if ($response->status() === 404) {
            return new TenantStatusSnapshot(
                status: TenantStatus::Failed,
                fqdn: $handle->fqdn,
                error: 'application not found',
            );
        }

        $this->assertOk($response, 'status');

        $body = $response->json();
        $raw = is_array($body) && isset($body['status']) && is_string($body['status']) ? $body['status'] : '';
        $fqdn = is_array($body) && isset($body['fqdn']) && is_string($body['fqdn']) ? $body['fqdn'] : $handle->fqdn;

        $status = $this->mapStatus($raw);

        return new TenantStatusSnapshot(
            status: $status,
            fqdn: $fqdn,
            error: null,
        );
    }

    public function stop(TenantHandle $handle): void
    {
        try {
            $response = $this->client()->post($this->url('/applications/'.$handle->providerRef.'/stop'));
        } catch (RequestException $e) {
            throw new CoolifyApiException('stop: '.$e->getMessage(), 0, $e);
        }

        $code = $response->status();
        if (! in_array($code, [200, 201, 204], true)) {
            throw new CoolifyApiException('stop: unexpected status '.$code.' body '.$response->body());
        }
    }

    public function destroy(TenantHandle $handle): void
    {
        try {
            $response = $this->client()->delete($this->url('/applications/'.$handle->providerRef));
        } catch (RequestException $e) {
            throw new CoolifyApiException('destroy: '.$e->getMessage(), 0, $e);
        }

        $code = $response->status();
        if (! in_array($code, [200, 204, 404], true)) {
            throw new CoolifyApiException('destroy: unexpected status '.$code.' body '.$response->body());
        }
    }

    public function upgrade(TenantHandle $handle, string $imageTag): void
    {
        try {
            $response = $this->client()->patch($this->url('/applications/'.$handle->providerRef), [
                'docker_registry_image_tag' => $imageTag,
            ]);
            $this->assertOk($response, 'upgrade image tag');
        } catch (RequestException $e) {
            throw new CoolifyApiException('upgrade: '.$e->getMessage(), 0, $e);
        }

        $this->start($handle);
    }

    private function upsertEnv(string $providerRef, string $key, string $value): void
    {
        $body = [
            'key' => $key,
            'value' => $value,
            'is_buildtime' => false,
            'is_runtime' => true,
        ];

        try {
            $response = $this->client()->patch(
                $this->url('/applications/'.$providerRef.'/envs'),
                $body
            );
        } catch (RequestException $e) {
            throw new CoolifyApiException('set env '.$key.': '.$e->getMessage(), 0, $e);
        }

        if ($response->status() === 404) {
            try {
                $response = $this->client()->post(
                    $this->url('/applications/'.$providerRef.'/envs'),
                    $body
                );
            } catch (RequestException $e) {
                throw new CoolifyApiException('set env '.$key.' fallback: '.$e->getMessage(), 0, $e);
            }
        }

        $this->assertOk($response, 'set env '.$key);
    }

    private function mapStatus(string $raw): TenantStatus
    {
        $lower = strtolower($raw);

        if (str_contains($lower, 'running') && str_contains($lower, 'healthy')) {
            return TenantStatus::Running;
        }
        if (str_contains($lower, 'running')) {
            return TenantStatus::Degraded;
        }
        if (str_contains($lower, 'starting')) {
            return TenantStatus::Starting;
        }
        if (str_contains($lower, 'exited') || str_contains($lower, 'stopped')) {
            return TenantStatus::Stopped;
        }
        if (str_contains($lower, 'error') || str_contains($lower, 'failed')) {
            return TenantStatus::Failed;
        }

        return TenantStatus::Provisioning;
    }

    private function client(): PendingRequest
    {
        return $this->http
            ->withToken($this->token)
            ->acceptJson()
            ->asJson();
    }

    private function url(string $path): string
    {
        return rtrim($this->baseUrl, '/').$path;
    }

    private function assertOk(Response $response, string $context): void
    {
        if ($response->successful()) {
            return;
        }
        throw new CoolifyApiException($context.': HTTP '.$response->status().' body '.$response->body());
    }
}
