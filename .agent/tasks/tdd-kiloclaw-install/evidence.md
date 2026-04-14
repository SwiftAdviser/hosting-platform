# Evidence Bundle: tdd-kiloclaw-install

## Summary
- Overall status: PASS
- Last updated: 2026-04-13
- Baseline: OK (14 tests, 34 assertions), EXIT=0
- RED: EXIT=255 (Interface App\Services\KiloClaw\KiloClawHttpClient not found at tests/Unit/Services/KiloClawClientServiceTest.php:200)
- GREEN: OK (24 tests, 64 assertions), EXIT=0
- Final composer test after dump-autoload: OK (30 tests, 87 assertions), EXIT=0 (includes 6 tests added by parallel task tdd-telegram-set-webhook)

## Acceptance criteria evidence

### AC1
- Status: PASS
- Proof:
  - tests/Unit/Services/KiloClawClientServiceTest.php::test_manifest_missing_id_returns_invalid_and_does_not_call_client asserts $result['status'] === 'invalid' and $fake->callCount === 0
  - artifacts/phpunit-red.txt (EXIT=255) captured before production code landed
  - artifacts/pre-build-stub.txt + artifacts/pre-build-services.txt snapshot scaffold-only state at red time
- Gaps: []

### AC2
- Status: PASS
- Proof:
  - tests/Unit/Services/KiloClawClientServiceTest.php::test_manifest_missing_name_returns_invalid_and_does_not_call_client
  - Fake records callCount; test asserts 0 after install with no name key
  - artifacts/phpunit-red.txt
- Gaps: []

### AC3
- Status: PASS
- Proof:
  - tests/Unit/Services/KiloClawClientServiceTest.php::test_manifest_missing_version_returns_invalid_and_does_not_call_client
  - Asserts 'invalid' and callCount 0 after install with no version key
  - artifacts/phpunit-red.txt
- Gaps: []

### AC4
- Status: PASS
- Proof:
  - tests/Unit/Services/KiloClawClientServiceTest.php::test_manifest_missing_skills_returns_invalid_and_does_not_call_client
  - Asserts 'invalid' and callCount 0 after install with no skills key
  - artifacts/phpunit-red.txt
- Gaps: []

### AC5
- Status: PASS
- Proof:
  - tests/Unit/Services/KiloClawClientServiceTest.php::test_happy_path_ready_returns_canonical_shape
  - assertSame on the full canonical array ['status' => 'ready', 'kiloclaw_id' => 'kc_abc', 'manifest_id' => 'plugin.atlas', 'error' => null]
  - artifacts/phpunit-red.txt, artifacts/phpunit-green.txt
- Gaps: []

### AC6
- Status: PASS
- Proof:
  - tests/Unit/Services/KiloClawClientServiceTest.php::test_happy_path_booting_returns_canonical_shape
  - assertSame on ['status' => 'booting', 'kiloclaw_id' => 'kc_def', 'manifest_id' => 'plugin.atlas', 'error' => null]
  - artifacts/phpunit-green.txt
- Gaps: []

### AC7
- Status: PASS
- Proof:
  - tests/Unit/Services/KiloClawClientServiceTest.php::test_client_exception_returns_failed_without_propagating
  - Fake seeded with new KiloClawException('transport boom'); test body wraps install in try/catch and calls this->fail on leak
  - Asserts status=failed, kiloclaw_id=null, manifest_id=plugin.atlas, error contains 'transport boom'
  - artifacts/phpunit-green.txt
- Gaps: []

### AC8
- Status: PASS
- Proof:
  - tests/Unit/Services/KiloClawClientServiceTest.php::test_malformed_response_missing_kiloclaw_id_returns_failed
  - Fake returns ['status' => 'ready']; test asserts status=failed and error contains 'malformed'
  - artifacts/phpunit-green.txt
- Gaps: []

### AC9
- Status: PASS
- Proof:
  - tests/Unit/Services/KiloClawClientServiceTest.php::test_malformed_response_invalid_status_returns_failed
  - Fake returns ['kiloclaw_id' => 'kc_xyz', 'status' => 'booting_or_something']; test asserts failed + 'malformed'
  - artifacts/phpunit-green.txt
- Gaps: []

### AC10
- Status: PASS
- Proof:
  - tests/Unit/Services/KiloClawClientServiceTest.php::test_idempotency_key_is_stable_across_two_calls_same_manifest
  - Two install calls with canonical manifest; test asserts $fake->lastArgs[1] is identical across calls, starts with 'kiloclaw-', and callCount === 2
  - artifacts/phpunit-green.txt
- Gaps: []

### AC11
- Status: PASS
- Proof:
  - artifacts/phpunit-green.txt: OK (24 tests, 64 assertions), EXIT=0
  - 14 baseline tests + 10 new kiloclaw tests execute as 24 total
- Gaps: []

### AC12
- Status: PASS
- Proof:
  - artifacts/phpunit-green.txt: OK (24 tests, 64 assertions), EXIT=0
  - artifacts/composer-test-final.txt: OK (30 tests, 87 assertions), EXIT=0 (parallel task added 6 more tests after green was captured; all still green)
- Gaps: []

### AC13
- Status: PASS
- Proof:
  - app/Services/KiloClaw/KiloClawHttpClient.php: `<?php`, `declare(strict_types=1);` on line 2, `namespace App\Services\KiloClaw;`, `interface KiloClawHttpClient`, single method `public function install(array $manifest, string $idempotencyKey): array;`
  - The only `https://` is inside the docblock ` * Reference: https://kiloclaw.example/api/install (v0.1 stubbed).`
- Gaps: []

### AC14
- Status: PASS
- Proof:
  - app/Services/KiloClaw/KiloClawException.php: strict_types, namespace, `final class KiloClawException extends \RuntimeException`
- Gaps: []

### AC15
- Status: PASS
- Proof:
  - app/Services/KiloClawClientService.php: strict_types, `namespace App\Services;`, `final class KiloClawClientService`, constructor `public function __construct(private readonly KiloClawHttpClient $client)`, public `install(array $manifest): array`; no other public methods besides constructor and install
- Gaps: []

### AC16
- Status: PASS
- Proof:
  - tests/Unit/Services/KiloClawClientServiceTest.php: strict_types, `namespace Tests\Unit\Services;`, `final class KiloClawClientServiceTest extends TestCase`
  - In-file `final class FakeKiloClawHttpClient implements KiloClawHttpClient` with public int $callCount, public array $lastArgs, public array $nextResponse, public ?\Throwable $nextException
- Gaps: []

### AC17
- Status: PASS
- Proof:
  - Validation foreach in install body checks id/name/version as non-empty strings and skills as array
  - On any miss returns `['status' => 'invalid', 'kiloclaw_id' => null, 'manifest_id' => null, 'error' => 'missing or invalid manifest keys: ...']`
  - AC1..AC4 tests assert callCount remains 0 and pass in phpunit-green.txt
- Gaps: []

### AC18
- Status: PASS
- Proof:
  - app/Services/KiloClawClientService.php contains the literal expression `'kiloclaw-' . sha1($manifest['id'] . ':' . $manifest['version'])` on the idempotencyKey line
- Gaps: []

### AC19
- Status: PASS
- Proof:
  - app/Services/KiloClawClientService.php: try { $this->client->install($manifest, $idempotencyKey); } catch (KiloClawException $e) { return ['status' => 'failed', 'kiloclaw_id' => null, 'manifest_id' => $manifest['id'], 'error' => $e->getMessage()]; }
  - AC7 test wraps install in try/catch and would fail via this->fail on leak; test passes in phpunit-green.txt
- Gaps: []

### AC20
- Status: PASS
- Proof:
  - Post-call guard in app/Services/KiloClawClientService.php checks isset/is_string on kiloclaw_id and in_array status against allowlist ['ready','booting','failed']
  - Failure branch returns ['status' => 'failed', 'kiloclaw_id' => null, 'manifest_id' => $manifest['id'], 'error' => 'malformed kiloclaw response']
  - AC8 and AC9 tests pass in phpunit-green.txt
- Gaps: []

### AC21
- Status: PASS
- Proof:
  - Success return in install: ['status' => $response['status'], 'kiloclaw_id' => $response['kiloclaw_id'], 'manifest_id' => $manifest['id'], 'error' => null]
  - AC5 uses fixture ['id' => 'plugin.atlas', 'name' => 'atlas', 'version' => '0.1.0', 'skills' => []] and fake response ['kiloclaw_id' => 'kc_abc', 'status' => 'ready']; asserts the canonical return
- Gaps: []

### AC22
- Status: PASS
- Proof:
  - install method body contains no ` new ` (spaced), no `::`, no ` static ` (spaced)
  - `catch (KiloClawException $e)` uses no `::`
  - `in_array` is a function call with no namespace separator
- Gaps: []

### AC23
- Status: PASS
- Proof:
  - tests/Unit/Services/KiloClawClientServiceTest.php defines `final class FakeKiloClawHttpClient implements KiloClawHttpClient` with the four recorder fields (int $callCount, array $lastArgs, array $nextResponse, ?\Throwable $nextException)
  - Zero occurrences of createMock / getMockBuilder / MockObject in the test file
- Gaps: []

### AC24
- Status: PASS
- Proof:
  - artifacts/php-lint.txt: `No syntax errors detected` on all four authored files
- Gaps: []

### AC25
- Status: PASS
- Proof:
  - artifacts/autoload-check.txt: all ten symbols print `ok`, EXIT=0
  - artifacts/autoload-check.php covers AgentDeployerService, KiloClawClientService, OnChainOSPaymentService, TelegramBotRegistrarService, Telegram\TelegramHttpClient, Telegram\TelegramTransportException, OnChainOS\OnChainOSClient, OnChainOS\OnChainOSException, KiloClaw\KiloClawHttpClient, KiloClaw\KiloClawException
- Gaps: []

### AC26
- Status: PASS
- Proof:
  - Zero occurrences of file_get_contents, curl_init, curl_exec, fsockopen, fopen('http in the four authored files
  - Only `https://` reference in the four files is the docblock line ` * Reference: https://kiloclaw.example/api/install ...` in app/Services/KiloClaw/KiloClawHttpClient.php (starts with ` * `)
- Gaps: []

### AC27
- Status: PASS
- Proof:
  - artifacts/phpunit-red.txt: EXIT=255, message "Interface \"App\\Services\\KiloClaw\\KiloClawHttpClient\" not found"
  - artifacts/pre-build-stub.txt captures the pre-RED scaffold-only state
  - raw/build.txt records the ordering: test file first, then red, then production code, then green
- Gaps: []

### AC28
- Status: PASS
- Proof:
  - artifacts/phpunit-green.txt: OK (24 tests, 64 assertions), EXIT=0
- Gaps: []

### AC29
- Status: PASS
- Proof:
  - artifacts/status-bootstrap-proof-loop.txt: verdict_overall_status PASS
  - artifacts/status-scaffold-service-stubs.txt: PASS
  - artifacts/status-test-harness.txt: PASS
  - artifacts/status-landing-mockup-steve-ive.txt: PASS
  - artifacts/status-tdd-telegram-validate-token.txt: PASS
  - artifacts/status-wizard-mockup-steve-ive.txt: PASS
  - artifacts/status-tdd-onchainos-create-charge.txt: PASS
  - artifacts/status-agents-list-mockup-steve-ive.txt: PASS
- Gaps: []

### AC30
- Status: PASS
- Proof:
  - artifacts/post-build-ls.txt: app/Services/ now contains KiloClaw/ (the one new subdir); no other new top-level paths
  - This task authored: app/Services/KiloClaw/KiloClawHttpClient.php (new), app/Services/KiloClaw/KiloClawException.php (new), app/Services/KiloClawClientService.php (rewritten from single-line scaffold), tests/Unit/Services/KiloClawClientServiceTest.php (new)
  - Parallel task tdd-telegram-set-webhook owns concurrent edits to app/Services/Telegram/TelegramHttpClient.php, app/Services/TelegramBotRegistrarService.php, and tests/Unit/Services/TelegramBotRegistrarServiceTest.php; this task did NOT touch them
  - composer.json, phpunit.xml.dist, .gitignore untouched
- Gaps: []

### AC31
- Status: PASS
- Proof:
  - Author audit: no em dash characters introduced in any authored file
  - Grep for U+2014 in the four authored files and spec.md returns zero matches
- Gaps: []
