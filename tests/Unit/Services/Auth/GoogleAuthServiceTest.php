<?php
declare(strict_types=1);

namespace Tests\Unit\Services\Auth;

use App\Services\Auth\GoogleAuthException;
use App\Services\Auth\GoogleAuthService;
use App\Services\Auth\GoogleIdentityClient;
use PHPUnit\Framework\TestCase;

final class GoogleAuthServiceTest extends TestCase
{
    private function makeFake(): FakeGoogleIdentityClient
    {
        return new FakeGoogleIdentityClient();
    }

    public function test_empty_authorization_code_returns_invalid_code_and_does_not_call_client(): void
    {
        $fake = $this->makeFake();
        $service = new GoogleAuthService($fake);

        $result = $service->signIn('');

        $this->assertSame('invalid_code', $result['status']);
        $this->assertNull($result['user']);
        $this->assertSame('empty authorization code', $result['error']);
        $this->assertSame(0, $fake->callCount);
    }

    public function test_whitespace_only_authorization_code_returns_invalid_code_and_does_not_call_client(): void
    {
        $fake = $this->makeFake();
        $service = new GoogleAuthService($fake);

        $result = $service->signIn('   ');

        $this->assertSame('invalid_code', $result['status']);
        $this->assertNull($result['user']);
        $this->assertSame('empty authorization code', $result['error']);
        $this->assertSame(0, $fake->callCount);
    }

    public function test_happy_path_returns_authenticated_with_canonical_user_shape(): void
    {
        $fake = $this->makeFake();
        $fake->nextProfile = [
            'id' => '123456789',
            'email' => 'roman@example.com',
            'name' => 'Roman Krutovoy',
        ];
        $service = new GoogleAuthService($fake);

        $result = $service->signIn('code_abc');

        $this->assertSame(
            [
                'status' => 'authenticated',
                'user' => [
                    'id' => '123456789',
                    'email' => 'roman@example.com',
                    'name' => 'Roman Krutovoy',
                ],
                'error' => null,
            ],
            $result
        );
        $this->assertSame(1, $fake->callCount);
        $this->assertSame('code_abc', $fake->lastCode);
    }

    public function test_client_exception_returns_failed_without_propagating(): void
    {
        $fake = $this->makeFake();
        $fake->nextException = new GoogleAuthException('invalid_grant');
        $service = new GoogleAuthService($fake);

        try {
            $result = $service->signIn('code_abc');
        } catch (\Throwable $e) {
            $this->fail('leaked');
        }

        $this->assertSame(
            [
                'status' => 'failed',
                'user' => null,
                'error' => 'invalid_grant',
            ],
            $result
        );
    }

    public function test_malformed_profile_missing_email_returns_failed(): void
    {
        $fake = $this->makeFake();
        $fake->nextProfile = [
            'id' => '123456789',
            'email' => '',
            'name' => 'Roman',
        ];
        $service = new GoogleAuthService($fake);

        $result = $service->signIn('code_abc');

        $this->assertSame('failed', $result['status']);
        $this->assertNull($result['user']);
        $this->assertStringContainsString('malformed google profile', $result['error']);
    }

    public function test_malformed_profile_missing_id_returns_failed(): void
    {
        $fake = $this->makeFake();
        $fake->nextProfile = [
            'id' => '',
            'email' => 'x@y.z',
            'name' => 'Roman',
        ];
        $service = new GoogleAuthService($fake);

        $result = $service->signIn('code_abc');

        $this->assertSame('failed', $result['status']);
        $this->assertNull($result['user']);
        $this->assertStringContainsString('malformed google profile', $result['error']);
    }

    public function test_malformed_profile_missing_name_returns_failed(): void
    {
        $fake = $this->makeFake();
        $fake->nextProfile = [
            'id' => '1',
            'email' => 'x@y.z',
            'name' => '',
        ];
        $service = new GoogleAuthService($fake);

        $result = $service->signIn('code_abc');

        $this->assertSame('failed', $result['status']);
        $this->assertNull($result['user']);
        $this->assertStringContainsString('malformed google profile', $result['error']);
    }
}

final class FakeGoogleIdentityClient implements GoogleIdentityClient
{
    public int $callCount = 0;
    public ?string $lastCode = null;
    public array $nextProfile = [];
    public ?\Throwable $nextException = null;

    public function fetchProfile(string $authorizationCode): array
    {
        $this->callCount++;
        $this->lastCode = $authorizationCode;
        if ($this->nextException !== null) {
            throw $this->nextException;
        }
        return $this->nextProfile;
    }
}
