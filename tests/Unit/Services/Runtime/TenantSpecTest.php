<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Runtime;

use App\Services\Runtime\TenantSpec;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class TenantSpecTest extends TestCase
{
    private function valid(array $overrides = []): TenantSpec
    {
        $defaults = [
            'agentName' => 'spawn-bot',
            'personality' => 'helpful concise assistant',
            'telegramBotToken' => '123:abc',
            'llmApiKey' => 'sk-test',
            'imageTag' => '2026.1.29-r1',
            'webhookBaseDomain' => 'agents.thespawn.io',
        ];
        $args = array_merge($defaults, $overrides);

        return new TenantSpec(
            agentName: $args['agentName'],
            personality: $args['personality'],
            telegramBotToken: $args['telegramBotToken'],
            llmApiKey: $args['llmApiKey'],
            imageTag: $args['imageTag'],
            webhookBaseDomain: $args['webhookBaseDomain'],
            allowlist: $args['allowlist'] ?? [],
            llmProvider: $args['llmProvider'] ?? 'anthropic',
        );
    }

    public function testHappyPathExposesAllFields(): void
    {
        $spec = $this->valid();

        $this->assertSame('spawn-bot', $spec->agentName);
        $this->assertSame('helpful concise assistant', $spec->personality);
        $this->assertSame('123:abc', $spec->telegramBotToken);
        $this->assertSame('sk-test', $spec->llmApiKey);
        $this->assertSame('2026.1.29-r1', $spec->imageTag);
        $this->assertSame('agents.thespawn.io', $spec->webhookBaseDomain);
        $this->assertSame([], $spec->allowlist);
        $this->assertSame('anthropic', $spec->llmProvider);
    }

    public function testCustomAllowlistAndProviderAreRetained(): void
    {
        $spec = $this->valid([
            'allowlist' => ['111', '222'],
            'llmProvider' => 'openai',
        ]);

        $this->assertSame(['111', '222'], $spec->allowlist);
        $this->assertSame('openai', $spec->llmProvider);
    }

    public function testEmptyAgentNameRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->valid(['agentName' => '']);
    }

    public function testEmptyPersonalityRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->valid(['personality' => '   ']);
    }

    public function testEmptyTelegramTokenRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->valid(['telegramBotToken' => '']);
    }

    public function testEmptyLlmApiKeyRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->valid(['llmApiKey' => '']);
    }

    public function testEmptyImageTagRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->valid(['imageTag' => '']);
    }

    public function testEmptyWebhookBaseDomainRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->valid(['webhookBaseDomain' => '']);
    }
}
