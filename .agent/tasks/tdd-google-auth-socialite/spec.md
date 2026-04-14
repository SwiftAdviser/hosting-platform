# Task Spec: tdd-google-auth-socialite

## Metadata
- Task ID: tdd-google-auth-socialite
- Created: 2026-04-14T15:21:18+00:00
- Repo root: /Users/krutovoy/Projects/hosting-platform
- Working directory at init: /Users/krutovoy/Projects/hosting-platform
- Cycle: seventh TDD proof-loop cycle

## Guidance sources
- /Users/krutovoy/Projects/hosting-platform/CLAUDE.md
- /Users/krutovoy/Projects/hosting-platform/docs/sprint_v0.1.md
- /Users/krutovoy/Projects/hosting-platform/app/Services/TelegramBotRegistrarService.php
- /Users/krutovoy/Projects/hosting-platform/tests/Unit/Services/TelegramBotRegistrarServiceTest.php
- /Users/krutovoy/Projects/hosting-platform/app/Services/OnChainOS/OnChainOSClient.php

## Original task statement
Seventh TDD cycle. Wire Google OAuth for hosting-platform via `laravel/socialite` (installed by the `land-laravel-framework` task). Introduce a narrow domain service `App\Services\Auth\GoogleAuthService` with a method that takes a raw Google OAuth callback payload (email, name, google_id) and returns a canonical user record. The service is injected with a `GoogleIdentityClient` interface that wraps Socialite's driver so tests can fake it without booting Laravel. The RED phase writes the test file first; the GREEN phase lands the interface, exception, and service. Out of scope: the production `SocialiteGoogleIdentityClient` implementation; that is a later task.

## Acceptance criteria

- AC1: A new file `app/Services/Auth/GoogleIdentityClient.php` exists with `declare(strict_types=1);`, `namespace App\Services\Auth;`, and declares `interface GoogleIdentityClient`.
- AC2: The `GoogleIdentityClient` interface declares exactly one method: `public function fetchProfile(string $authorizationCode): array;`. The method documents that the returned associative array has at least keys `id` (string Google user ID), `email` (string), and `name` (string), and that transport or auth failures throw `GoogleAuthException`. The docblock may reference `https://accounts.google.com/o/oauth2/v2/auth` but only inside a comment.
- AC3: A new file `app/Services/Auth/GoogleAuthException.php` exists with `declare(strict_types=1);`, `namespace App\Services\Auth;`, and declares `final class GoogleAuthException extends \RuntimeException`.
- AC4: A new file `app/Services/Auth/GoogleAuthService.php` exists with `declare(strict_types=1);`, `namespace App\Services\Auth;`, and declares `final class GoogleAuthService` with constructor `public function __construct(private readonly GoogleIdentityClient $client)`.
- AC5: `GoogleAuthService` exposes a public method with the exact signature `public function signIn(string $authorizationCode): array`.
- AC6: When `$authorizationCode` is empty after `trim`, `signIn` returns `['status' => 'invalid_code', 'user' => null, 'error' => 'empty authorization code']` and does NOT call `$this->client->fetchProfile`.
- AC7: When `$authorizationCode` contains only whitespace, `signIn` returns `['status' => 'invalid_code', 'user' => null, 'error' => 'empty authorization code']` and does NOT call the client.
- AC8: On a non-empty authorization code, `signIn` calls `$this->client->fetchProfile($authorizationCode)` exactly once inside a `try` block.
- AC9: When the client throws `GoogleAuthException $e`, `signIn` catches it and returns `['status' => 'failed', 'user' => null, 'error' => $e->getMessage()]`. The exception MUST NOT propagate out of `signIn`.
- AC10: When the returned profile array is missing any of the keys `id`, `email`, or `name`, or any of those keys holds an empty string, `signIn` returns `['status' => 'failed', 'user' => null, 'error' => 'malformed google profile']`.
- AC11: On a valid profile, `signIn` returns `['status' => 'authenticated', 'user' => ['id' => $profile['id'], 'email' => $profile['email'], 'name' => $profile['name']], 'error' => null]`.
- AC12: A new file `tests/Unit/Services/Auth/GoogleAuthServiceTest.php` exists with `declare(strict_types=1);` and `namespace Tests\Unit\Services\Auth;`, and extends `PHPUnit\Framework\TestCase`.
- AC13: The test file defines an in-file `final class FakeGoogleIdentityClient implements GoogleIdentityClient` with public fields `$callCount`, `$lastCode`, `$nextProfile`, and `$nextException`. The fake is NOT a PHPUnit mock.
- AC14: The test file contains no calls to `createMock`, `getMockBuilder`, or `MockObject`.
- AC15: The test file defines the test method `test_empty_authorization_code_returns_invalid_code_and_does_not_call_client`, which asserts the invalid_code shape and that `$fake->callCount === 0`.
- AC16: The test file defines `test_whitespace_only_authorization_code_returns_invalid_code_and_does_not_call_client`, which passes a string of spaces or tabs and asserts the invalid_code shape plus `$fake->callCount === 0`.
- AC17: The test file defines `test_happy_path_returns_authenticated_with_canonical_user_shape`. It uses the pinned fixture: code `'code_abc'`, fake profile `['id' => '123456789', 'email' => 'roman@example.com', 'name' => 'Roman Krutovoy']`. It asserts the service returns exactly `['status' => 'authenticated', 'user' => ['id' => '123456789', 'email' => 'roman@example.com', 'name' => 'Roman Krutovoy'], 'error' => null]`, that `$fake->callCount === 1`, and that `$fake->lastCode === 'code_abc'`.
- AC18: The test file defines `test_client_exception_returns_failed_without_propagating`. The fake throws `new GoogleAuthException('invalid_grant')`. The test asserts the service returns exactly `['status' => 'failed', 'user' => null, 'error' => 'invalid_grant']` and that no exception escapes the `signIn` call.
- AC19: The test file defines `test_malformed_profile_missing_email_returns_failed`. The fake returns `['id' => '123456789', 'email' => '', 'name' => 'Roman']`. The test asserts `status === 'failed'`, `user === null`, and that the `error` string contains the substring `'malformed'`.
- AC20: The test file defines `test_malformed_profile_missing_id_returns_failed`, which covers the missing `id` key path and asserts the `status === 'failed'`, `user === null`, and `error` contains `'malformed'`.
- AC21: The test file defines `test_malformed_profile_missing_name_returns_failed`, which covers the missing `name` key path and asserts the same failed shape with `'malformed'` in the error string.
- AC22: The `signIn` method body MUST NOT contain the tokens ` new `, `::`, or ` static `. A `catch (GoogleAuthException $e)` clause is permitted and does not count as a use of `::` or `new`.
- AC23: Every authored PHP file (`GoogleIdentityClient.php`, `GoogleAuthException.php`, `GoogleAuthService.php`, `GoogleAuthServiceTest.php`) begins with `<?php` immediately followed by `declare(strict_types=1);`.
- AC24: `composer test` passes on the GREEN phase and reports at least 44 tests total (prior proof-loop work plus the 7 new tests from this task). None of the 14 prior proof-loop cycles regress.
- AC25: No em dashes are introduced in any authored file or in this spec.

## Constraints

- New namespace is `App\Services\Auth`; no other namespaces are modified.
- The domain service MUST NOT import or reference `Laravel\Socialite` symbols directly. Socialite integration lives behind the `GoogleIdentityClient` interface.
- The service MUST NOT import Laravel framework facades, helpers, or Eloquent models.
- The service MUST NOT touch the filesystem, network, database, logger, or cache.
- `GoogleAuthException` MUST be a `final class` extending `\RuntimeException`, no custom constructor required.
- The RED phase lands the test file first with the production files absent or incomplete enough to fail.
- The GREEN phase lands the interface, the exception, and the service so all 7 new tests pass.
- Tests use a hand-written fake, not PHPUnit mocks.
- `composer test` is the single gate for pass/fail.
- No em dashes anywhere in authored files.
- Do not touch other in-flight spec-freezer artifacts under `.agent/tasks/`.

## Non-goals

- Writing a `SocialiteGoogleIdentityClient` that calls `Socialite::driver('google')->user()`. That ships in a later task.
- Registering routes, controllers, middleware, or Inertia pages for Google login.
- Persisting the canonical user record to the database or creating an Eloquent `User`.
- Session handling, CSRF, or cookie wiring for OAuth.
- Installing or configuring the `laravel/socialite` Composer package (handled by the `land-laravel-framework` task).
- Integration tests that boot Laravel or hit `accounts.google.com`.
- Multi-provider social auth abstraction; this cycle is Google-only.

## Verification plan

- Build: none required; PHP files are autoloaded through Composer.
- Unit tests: `composer test` must report at least 44 tests passing, including all 7 new `GoogleAuthServiceTest` methods.
- Static shape checks: `grep` each authored file for `declare(strict_types=1);` and confirm `GoogleAuthServiceTest.php` contains zero occurrences of `createMock`, `getMockBuilder`, or `MockObject`.
- Method body audit: extract the body of `GoogleAuthService::signIn` and confirm it contains no ` new `, no `::`, and no ` static ` token.
- Regression gate: the prior 14 TDD cycles remain green in the same `composer test` run.
- Manual spot-check: confirm no em dashes in `spec.md`, the interface, the exception, the service, and the test file.
