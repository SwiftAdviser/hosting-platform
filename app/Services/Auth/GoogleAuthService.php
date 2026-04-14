<?php
declare(strict_types=1);

namespace App\Services\Auth;

final class GoogleAuthService
{
    public function __construct(
        private readonly GoogleIdentityClient $client,
    ) {
    }

    public function signIn(string $authorizationCode): array
    {
        if (trim($authorizationCode) === '') {
            return [
                'status' => 'invalid_code',
                'user' => null,
                'error' => 'empty authorization code',
            ];
        }

        try {
            $profile = $this->client->fetchProfile($authorizationCode);
        } catch (GoogleAuthException $e) {
            return [
                'status' => 'failed',
                'user' => null,
                'error' => $e->getMessage(),
            ];
        }

        if (
            !isset($profile['id'], $profile['email'], $profile['name'])
            || $profile['id'] === ''
            || $profile['email'] === ''
            || $profile['name'] === ''
        ) {
            return [
                'status' => 'failed',
                'user' => null,
                'error' => 'malformed google profile',
            ];
        }

        return [
            'status' => 'authenticated',
            'user' => [
                'id' => $profile['id'],
                'email' => $profile['email'],
                'name' => $profile['name'],
            ],
            'error' => null,
        ];
    }
}
