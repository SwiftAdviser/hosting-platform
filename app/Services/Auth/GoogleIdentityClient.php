<?php
declare(strict_types=1);

namespace App\Services\Auth;

interface GoogleIdentityClient
{
    /**
     * Exchange a Google OAuth authorization code for the signed-in user's profile.
     *
     * Reference: https://accounts.google.com/o/oauth2/v2/auth (v0.1 wraps this seam).
     *
     * @return array associative array with keys id, email, name
     * @throws GoogleAuthException on transport or auth failure
     */
    public function fetchProfile(string $authorizationCode): array;
}
