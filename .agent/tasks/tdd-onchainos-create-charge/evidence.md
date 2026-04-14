# Evidence Bundle: tdd-onchainos-create-charge

## Summary
- Overall status: PASS
- Last updated: 2026-04-13
- Baseline: OK (6 tests, 12 assertions)
- RED: EXIT=255 (Interface App\Services\OnChainOS\OnChainOSClient not found at tests/Unit/Services/OnChainOSPaymentServiceTest.php:133)
- GREEN: OK (14 tests, 34 assertions), EXIT=0
- Final composer test after dump-autoload: OK (14 tests, 34 assertions), EXIT=0

## Acceptance criteria evidence

### AC1
- Status: PASS
- Proof:
  - tests/Unit/Services/OnChainOSPaymentServiceTest.php::test_empty_agent_name_returns_invalid_and_does_not_call_client asserts $result['status'] === 'invalid' and $fake->callCount === 0
  - artifacts/phpunit-red.txt (EXIT=255) captured before production code landed
  - artifacts/pre-build-services.txt + artifacts/pre-build-stub.txt snapshot scaffold-only state at red time
- Gaps: []

### AC2
- Status: PASS
- Proof:
  - tests/Unit/Services/OnChainOSPaymentServiceTest.php::test_whitespace_only_agent_name_returns_invalid_and_does_not_call_client
  - Fake records callCount; test asserts 0 calls after createCharge(10, '   ')
  - artifacts/phpunit-red.txt
- Gaps: []

### AC3
- Status: PASS
- Proof:
  - tests/Unit/Services/OnChainOSPaymentServiceTest.php::test_zero_amount_returns_invalid_and_does_not_call_client
  - Asserts 'invalid' and callCount 0 after createCharge(0, 'spawn-bot')
  - artifacts/phpunit-red.txt
- Gaps: []

### AC4
- Status: PASS
- Proof:
  - tests/Unit/Services/OnChainOSPaymentServiceTest.php::test_negative_amount_returns_invalid_and_does_not_call_client
  - Asserts 'invalid' and callCount 0 after createCharge(-5, 'spawn-bot')
  - artifacts/phpunit-red.txt
- Gaps: []

### AC5
- Status: PASS
- Proof:
  - tests/Unit/Services/OnChainOSPaymentServiceTest.php::test_happy_path_returns_canonical_shape seeds the fake with {session_id: sess_abc, status: pending, expires_at: 2026-04-14T10:00:00Z} and asserts the canonical shape
  - artifacts/phpunit-red.txt (failed at interface load time in red)
  - artifacts/phpunit-green.txt asserts pass in green
- Gaps: []

### AC6
- Status: PASS
- Proof:
  - tests/Unit/Services/OnChainOSPaymentServiceTest.php::test_client_exception_returns_failed_without_propagating seeds $fake->nextException = new OnChainOSException('transport boom'); uses try/fail to detect leakage; asserts 'failed' status and error key contains 'transport boom'
  - artifacts/phpunit-red.txt
- Gaps: []

### AC7
- Status: PASS
- Proof:
  - tests/Unit/Services/OnChainOSPaymentServiceTest.php::test_malformed_response_without_session_id_returns_failed seeds {status: pending} only and asserts 'failed' plus error key
  - artifacts/phpunit-red.txt
- Gaps: []

### AC8
- Status: PASS
- Proof:
  - tests/Unit/Services/OnChainOSPaymentServiceTest.php::test_idempotency_key_is_stable_across_two_calls_same_utc_day captures $fake->lastArgs[2] after each of two calls and asserts equality plus the 'spawn-' prefix
  - artifacts/phpunit-red.txt
- Gaps: []

### AC9
- Status: PASS
- Proof:
  - artifacts/phpunit-green.txt: OK (14 tests, 34 assertions), EXIT=0
  - All eight test methods execute as part of the 14 total (1 smoke + 5 telegram + 8 onchainos)
- Gaps: []

### AC10
- Status: PASS
- Proof:
  - artifacts/composer-test-final.txt: OK (14 tests, 34 assertions), EXIT=0 (14 >= 14 tests and 34 >= 14 assertions)
- Gaps: []

### AC11
- Status: PASS
- Proof:
  - app/Services/OnChainOS/OnChainOSClient.php declares strict_types=1, namespace App\Services\OnChainOS, interface OnChainOSClient with a single method: public function createCharge(int $amountUsd, string $agentName, string $idempotencyKey): array
  - The only https:// reference lives inside the docblock comment above the method
- Gaps: []

### AC12
- Status: PASS
- Proof:
  - app/Services/OnChainOS/OnChainOSException.php declares strict_types=1, namespace App\Services\OnChainOS, and "final class OnChainOSException extends \RuntimeException"
- Gaps: []

### AC13
- Status: PASS
- Proof:
  - app/Services/OnChainOSPaymentService.php declares strict_types=1, namespace App\Services, final class OnChainOSPaymentService, constructor with private readonly OnChainOSClient $client, and a single public method createCharge(int $amountUsd, string $agentName): array
  - Only two public functions exist: the promoted constructor and createCharge
- Gaps: []

### AC14
- Status: PASS
- Proof:
  - Short-circuit guard "if ($amountUsd <= 0 || trim($agentName) === '')" returns the invalid shape without calling the client
  - AC1..AC4 tests assert callCount === 0 and pass in phpunit-green.txt
- Gaps: []

### AC15
- Status: PASS
- Proof:
  - try/catch (OnChainOSException $e) wraps the client call; returns 'failed' with error => $e->getMessage()
  - AC6 test asserts no exception propagates; passes in phpunit-green.txt
- Gaps: []

### AC16
- Status: PASS
- Proof:
  - if (!isset($response['session_id']) || !isset($response['status'])) returns 'failed' with error => 'malformed response'
  - AC7 test passes in phpunit-green.txt
- Gaps: []

### AC17
- Status: PASS
- Proof:
  - app/Services/OnChainOSPaymentService.php contains the literal expression: 'spawn-' . sha1($agentName . ':' . $amountUsd . ':' . gmdate('Y-m-d'))
  - AC8 test asserts the key is stable and starts with 'spawn-'
- Gaps: []

### AC18
- Status: PASS
- Proof:
  - awk extraction of the createCharge method body contains no occurrence of ' new ', '::', or ' static '
  - catch (OnChainOSException $e) has no '::'
  - See raw/build.txt for the awk dump
- Gaps: []

### AC19
- Status: PASS
- Proof:
  - tests/Unit/Services/OnChainOSPaymentServiceTest.php uses a named in-file helper class FakeOnChainOSClient implements OnChainOSClient with public int $callCount, public array $lastArgs, public array $nextResponse, public ?\Throwable $nextException
  - grep confirms zero createMock / getMockBuilder / MockObject occurrences
- Gaps: []

### AC20
- Status: PASS
- Proof:
  - artifacts/php-lint.txt: "No syntax errors detected" on all four authored files
- Gaps: []

### AC21
- Status: PASS
- Proof:
  - artifacts/autoload-check.txt prints "ok: <symbol>" for all eight identifiers and EXIT=0
  - artifacts/autoload-check.php contains the probe source
- Gaps: []

### AC22
- Status: PASS
- Proof:
  - No file_get_contents / curl_init / curl_exec / fsockopen / fopen('http in the four authored files
  - The only https:// reference is inside the docblock of OnChainOSClient::createCharge (comment line starts with " * ")
- Gaps: []

### AC23
- Status: PASS
- Proof:
  - artifacts/phpunit-red.txt exists, EXIT=255, contains "An error occurred inside PHPUnit" and "Interface App\Services\OnChainOS\OnChainOSClient not found"
  - raw/build.txt records the red step occurred before any edits to app/Services/OnChainOSPaymentService.php body or creation of app/Services/OnChainOS/
  - artifacts/pre-build-stub.txt captures the scaffold single-line stub state at red time
- Gaps: []

### AC24
- Status: PASS
- Proof:
  - artifacts/phpunit-green.txt exists, EXIT=0, "OK (14 tests, 34 assertions)"
- Gaps: []

### AC25
- Status: PASS
- Proof:
  - artifacts/status-bootstrap-proof-loop.txt: verdict_overall_status PASS
  - artifacts/status-scaffold-service-stubs.txt: verdict_overall_status PASS
  - artifacts/status-test-harness.txt: verdict_overall_status PASS
  - artifacts/status-landing-mockup-steve-ive.txt: verdict_overall_status PASS
  - artifacts/status-tdd-telegram-validate-token.txt: verdict_overall_status PASS
  - artifacts/status-wizard-mockup-steve-ive.txt: verdict_overall_status PASS
- Gaps: []

### AC26
- Status: PASS
- Proof:
  - artifacts/post-build-ls.txt vs artifacts/pre-build-services.txt: only addition is app/Services/OnChainOS/ dir
  - No edits to composer.json, phpunit.xml.dist, .gitignore, tests/Unit/Services/TelegramBotRegistrarServiceTest.php, app/Services/Telegram/*, app/Services/AgentDeployerService.php, or app/Services/KiloClawClientService.php
  - A parallel task (agents-list-mockup-steve-ive) may add design/agents.html; that is explicitly out of scope for this task per the build instructions
- Gaps: []

### AC27
- Status: PASS
- Proof:
  - Grep for U+2014 (—) in the four authored files and spec.md returns no matches
- Gaps: []

## Commands run
- composer test (baseline) -> OK (6 tests, 12 assertions), EXIT=0
- composer test (red) -> EXIT=255
- composer test (green) -> OK (14 tests, 34 assertions), EXIT=0
- php -l on four authored files -> all clean
- php artifacts/autoload-check.php -> EXIT=0, all eight symbols ok
- composer dump-autoload -o -> EXIT=0, 1531 classes
- composer test (final) -> OK (14 tests, 34 assertions), EXIT=0
- python3 task_loop.py status --task-id <each prior task> -> EXIT=0, all PASS

## Raw artifacts
- .agent/tasks/tdd-onchainos-create-charge/raw/build.txt
- .agent/tasks/tdd-onchainos-create-charge/raw/test-unit.txt
- .agent/tasks/tdd-onchainos-create-charge/raw/test-integration.txt
- .agent/tasks/tdd-onchainos-create-charge/raw/lint.txt
- .agent/tasks/tdd-onchainos-create-charge/raw/screenshot-1.png
- .agent/tasks/tdd-onchainos-create-charge/artifacts/baseline-test.txt
- .agent/tasks/tdd-onchainos-create-charge/artifacts/phpunit-red.txt
- .agent/tasks/tdd-onchainos-create-charge/artifacts/phpunit-green.txt
- .agent/tasks/tdd-onchainos-create-charge/artifacts/php-lint.txt
- .agent/tasks/tdd-onchainos-create-charge/artifacts/autoload-check.php
- .agent/tasks/tdd-onchainos-create-charge/artifacts/autoload-check.txt
- .agent/tasks/tdd-onchainos-create-charge/artifacts/composer-dump.txt
- .agent/tasks/tdd-onchainos-create-charge/artifacts/composer-test-final.txt
- .agent/tasks/tdd-onchainos-create-charge/artifacts/post-build-ls.txt
- .agent/tasks/tdd-onchainos-create-charge/artifacts/status-*.txt

## Known gaps
- None.
