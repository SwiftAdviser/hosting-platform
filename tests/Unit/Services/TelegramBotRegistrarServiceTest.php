<?php
declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\Telegram\TelegramHttpClient;
use App\Services\Telegram\TelegramTransportException;
use App\Services\TelegramBotRegistrarService;
use PHPUnit\Framework\TestCase;

final class TelegramBotRegistrarServiceTest extends TestCase
{
    public function test_empty_token_returns_false_without_calling_client(): void
    {
        $fake = new FakeTelegramHttpClient();
        $fake->getMeNextResponse = ['ok' => true];
        $service = new TelegramBotRegistrarService($fake);

        $result = $service->validateToken('');

        $this->assertFalse($result);
        $this->assertSame(0, $fake->getMeCallCount);
    }

    public function test_valid_token_ok_true_returns_true(): void
    {
        $fake = new FakeTelegramHttpClient();
        $fake->getMeNextResponse = ['ok' => true, 'result' => ['id' => 1]];
        $service = new TelegramBotRegistrarService($fake);

        $result = $service->validateToken('123:abc');

        $this->assertTrue($result);
        $this->assertSame(1, $fake->getMeCallCount);
        $this->assertSame('123:abc', $fake->getMeLastToken);
    }

    public function test_invalid_token_ok_false_returns_false(): void
    {
        $fake = new FakeTelegramHttpClient();
        $fake->getMeNextResponse = ['ok' => false, 'error_code' => 401, 'description' => 'Unauthorized'];
        $service = new TelegramBotRegistrarService($fake);

        $result = $service->validateToken('bad:token');

        $this->assertFalse($result);
        $this->assertSame(1, $fake->getMeCallCount);
    }

    public function test_transport_exception_returns_false(): void
    {
        $fake = new FakeTelegramHttpClient();
        $fake->getMeNextException = new TelegramTransportException('boom');
        $service = new TelegramBotRegistrarService($fake);

        $result = $service->validateToken('123:abc');

        $this->assertFalse($result);
        $this->assertSame(1, $fake->getMeCallCount);
    }

    public function test_whitespace_only_token_returns_false_without_calling_client(): void
    {
        $fake = new FakeTelegramHttpClient();
        $fake->getMeNextResponse = ['ok' => true];
        $service = new TelegramBotRegistrarService($fake);

        $result = $service->validateToken('   ');

        $this->assertFalse($result);
        $this->assertSame(0, $fake->getMeCallCount);
    }

    public function test_register_webhook_empty_token_returns_invalid_no_client_call(): void
    {
        $fake = new FakeTelegramHttpClient();
        $service = new TelegramBotRegistrarService($fake);

        $result = $service->registerWebhook('', 'https://example.com/hook');
        $this->assertSame('invalid', $result['status']);
        $this->assertNull($result['webhook_url']);
        $this->assertSame('empty token', $result['error']);

        $result2 = $service->registerWebhook('   ', 'https://example.com/hook');
        $this->assertSame('invalid', $result2['status']);
        $this->assertNull($result2['webhook_url']);
        $this->assertSame('empty token', $result2['error']);

        $this->assertSame(0, $fake->setWebhookCallCount);
        $this->assertSame(0, $fake->getMeCallCount);
    }

    public function test_register_webhook_non_https_url_returns_invalid_no_client_call(): void
    {
        $fake = new FakeTelegramHttpClient();
        $service = new TelegramBotRegistrarService($fake);

        $result = $service->registerWebhook('123:abc', 'http://example.com/hook');

        $this->assertSame('invalid', $result['status']);
        $this->assertNull($result['webhook_url']);
        $this->assertStringContainsString('https', $result['error']);
        $this->assertSame(0, $fake->setWebhookCallCount);
    }

    public function test_register_webhook_invalid_host_returns_invalid_no_client_call(): void
    {
        $fake = new FakeTelegramHttpClient();
        $service = new TelegramBotRegistrarService($fake);

        $result = $service->registerWebhook('123:abc', 'https://nohostdot');

        $this->assertSame('invalid', $result['status']);
        $this->assertNull($result['webhook_url']);
        $this->assertStringContainsString('host', $result['error']);
        $this->assertSame(0, $fake->setWebhookCallCount);
    }

    public function test_register_webhook_happy_path_returns_registered(): void
    {
        $fake = new FakeTelegramHttpClient();
        $fake->setWebhookNextResponse = ['ok' => true, 'result' => true];
        $service = new TelegramBotRegistrarService($fake);

        $result = $service->registerWebhook('123:abc', 'https://example.com/hook');

        $this->assertSame(
            ['status' => 'registered', 'webhook_url' => 'https://example.com/hook', 'error' => null],
            $result,
        );
        $this->assertSame(1, $fake->setWebhookCallCount);
        $this->assertSame(['123:abc', 'https://example.com/hook'], $fake->setWebhookLastArgs);
    }

    public function test_register_webhook_client_exception_returns_failed(): void
    {
        $fake = new FakeTelegramHttpClient();
        $fake->setWebhookNextException = new TelegramTransportException('transport boom');
        $service = new TelegramBotRegistrarService($fake);

        try {
            $result = $service->registerWebhook('123:abc', 'https://example.com/hook');
        } catch (\Throwable $e) {
            $this->fail('leaked');
        }

        $this->assertSame(
            ['status' => 'failed', 'webhook_url' => 'https://example.com/hook', 'error' => 'transport boom'],
            $result,
        );
        $this->assertSame(1, $fake->setWebhookCallCount);
    }

    public function test_register_webhook_telegram_rejected_returns_failed(): void
    {
        $fake = new FakeTelegramHttpClient();
        $fake->setWebhookNextResponse = ['ok' => false, 'error_code' => 400, 'description' => 'Bad Request: bad webhook'];
        $service = new TelegramBotRegistrarService($fake);

        $result = $service->registerWebhook('123:abc', 'https://example.com/hook');

        $this->assertSame(
            ['status' => 'failed', 'webhook_url' => 'https://example.com/hook', 'error' => 'Bad Request: bad webhook'],
            $result,
        );
        $this->assertSame(1, $fake->setWebhookCallCount);
    }
}

final class FakeTelegramHttpClient implements TelegramHttpClient
{
    public int $getMeCallCount = 0;
    public ?string $getMeLastToken = null;
    public array $getMeNextResponse = [];
    public ?\Throwable $getMeNextException = null;

    public int $setWebhookCallCount = 0;
    public array $setWebhookLastArgs = [];
    public array $setWebhookNextResponse = [];
    public ?\Throwable $setWebhookNextException = null;

    public function getMe(string $token): array
    {
        $this->getMeCallCount++;
        $this->getMeLastToken = $token;
        if ($this->getMeNextException) {
            throw $this->getMeNextException;
        }
        return $this->getMeNextResponse;
    }

    public function setWebhook(string $token, string $webhookUrl): array
    {
        $this->setWebhookCallCount++;
        $this->setWebhookLastArgs = [$token, $webhookUrl];
        if ($this->setWebhookNextException) {
            throw $this->setWebhookNextException;
        }
        return $this->setWebhookNextResponse;
    }
}
