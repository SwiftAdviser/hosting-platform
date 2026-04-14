# Evidence Bundle: tdd-onchainos-xlayer-client

## Summary
- Overall status: PASS
- TDD cycle 6 of N, Case 2 (HTTP transport seam, no composer dependency).
- Baseline: 37 tests. Green: 41 tests, 161 assertions, exit 0.
- Red artifact: artifacts/phpunit-red.txt EXIT=255 ("Interface App\\Services\\OnChainOS\\XLayer\\XLayerHttpTransport not found" captured before production files existed).
- Four protected files byte-identical pre and post (AC1..AC4).
- composer.json byte-identical pre and post (AC20).

## Acceptance criteria evidence

### AC1 OnChainOSClient.php byte-identical
- Status: PASS
- Proof:
  - pre-sha: d4c5d7d9babd848e081aefb005d03c326cf38ab0d8c61f5d72b2876eab3694bc
  - post-sha matches; diff pre-sha.txt post-sha.txt returned 0.

### AC2 OnChainOSException.php byte-identical
- Status: PASS
- Proof:
  - pre-sha: ee657f8efa7dd11484972ad1ad7da66738025b745db7622856f532d4a6e13012
  - post-sha matches.

### AC3 OnChainOSPaymentService.php byte-identical
- Status: PASS
- Proof:
  - pre-sha: 66ab94621d19770f571e344ad75eb67c4ba4527196992552fbabcc133b993acf
  - post-sha matches.

### AC4 OnChainOSPaymentServiceTest.php byte-identical and 8 tests green
- Status: PASS
- Proof:
  - pre-sha: 0ccace84071c89ab41e599052c905411d196922eadce27bf29dc9b794552d956
  - green run shows 41 tests total (37 prior + 4 new). The 8 OnChainOSPaymentService methods still run.

### AC5 XLayerHttpTransport interface file
- Status: PASS
- Proof:
  - app/Services/OnChainOS/XLayer/XLayerHttpTransport.php declares strict types, namespace App\Services\OnChainOS\XLayer, interface XLayerHttpTransport with single `post(string,array,array): array` method.

### AC6 XLayerHttpException final class
- Status: PASS
- Proof:
  - app/Services/OnChainOS/XLayer/XLayerHttpException.php declares `final class XLayerHttpException extends \RuntimeException` with empty body.

### AC7 XLayerOnChainOSClient implements OnChainOSClient
- Status: PASS
- Proof:
  - app/Services/OnChainOS/XLayer/XLayerOnChainOSClient.php declares `final class XLayerOnChainOSClient implements OnChainOSClient`.

### AC8 Readonly promoted constructor, no facades/env/config
- Status: PASS
- Proof:
  - Constructor takes `XLayerHttpTransport $transport, string $apiKey, string $secretKey, string $passphrase`, all `private readonly`.
  - grep for `env(|config(|Illuminate` returns zero matches in XLayerOnChainOSClient.php.

### AC9 createCharge signature matches interface
- Status: PASS
- Proof:
  - `public function createCharge(int $amountUsd, string $agentName, string $idempotencyKey): array` identical to OnChainOSClient::createCharge.

### AC10 Canonical happy path shape
- Status: PASS
- Proof:
  - Happy path returns `['session_id'=>..., 'status'=>..., 'expires_at'=>...]` only. Test `test_happy_path_returns_canonical_shape` asserts exact shape with assertSame.

### AC11 Idempotency key passes through untouched
- Status: PASS
- Proof:
  - Happy path test asserts `$fake->lastArgs[2]['Idempotency-Key'] === 'spawn-abc123'` verbatim. No transformation code present.

### AC12 Method body has no `new` except throws, no `::` except built-ins
- Status: PASS
- Proof:
  - grep `new ` in XLayerOnChainOSClient.php shows 4 occurrences, all `throw new OnChainOSException(...)` on lines 39/41/45/50.
  - No `::` tokens present in the method body (only `str_contains`, `stripos`, `isset`, `str_contains` function calls).
  - Allowed whitelist per AC12: `new OnChainOSException(...)` used exclusively for thrown exceptions.

### AC13 XLayerHttpException rethrown as OnChainOSException with previous
- Status: PASS
- Proof:
  - Catch block constructs `new OnChainOSException('...', 0, $e)` passing original as third arg.
  - Test `test_transport_exception_translates_to_onchainos_exception` asserts `$e->getPrevious() === $original`.

### AC14 Malformed response throws with "malformed"
- Status: PASS
- Proof:
  - Missing `session_id` path throws `OnChainOSException('malformed xlayer response')`.
  - Missing `status` same. Status outside {pending,failed,completed} throws `OnChainOSException('malformed xlayer response status')`.
  - Test `test_malformed_upstream_throws_with_malformed_marker` uses `expectExceptionMessageMatches('/malformed/i')`.

### AC15 Auth failure throws with "auth", no credential leak
- Status: PASS
- Proof:
  - Catch branch detects 401/403 or unauthorized/forbidden and throws `OnChainOSException('auth failure', 0, $e)`.
  - Test `test_auth_failure_throws_without_leaking_credentials` asserts message matches `/auth/i` and walks `getPrevious()` chain asserting redacted tokens absent. Client constructor never embeds credentials in any exception message.

### AC16 Test file with in-file fake implementing XLayerHttpTransport
- Status: PASS
- Proof:
  - tests/Unit/Services/OnChainOS/XLayerOnChainOSClientTest.php namespace `Tests\Unit\Services\OnChainOS`, extends `PHPUnit\Framework\TestCase`, 4 test methods, in-file `FakeXLayerHttpTransport implements XLayerHttpTransport` with `$callCount`, `$lastArgs`, `$nextResponse`, `$nextException`, and `post(...)` recording `[$path,$body,$headers]`.
  - No `createMock`, `getMockBuilder`, or `MockObject` used.

### AC17 Four required test cases
- Status: PASS
- Proof:
  - `test_happy_path_returns_canonical_shape`
  - `test_transport_exception_translates_to_onchainos_exception`
  - `test_malformed_upstream_throws_with_malformed_marker`
  - `test_auth_failure_throws_without_leaking_credentials`

### AC18 Header map contains Idempotency-Key verbatim
- Status: PASS
- Proof:
  - Happy path test: `$this->assertSame('spawn-abc123', $headers['Idempotency-Key']);`

### AC19 composer test exits 0, suite >= 41, no deletions
- Status: PASS
- Proof:
  - artifacts/phpunit-green.txt: "OK (41 tests, 161 assertions)" EXIT=0. Prior 37 all green; 4 new all green.

### AC20 composer.json byte-identical
- Status: PASS
- Proof:
  - pre-sha: c3edf159cf70ba3cc9f2d931a9be4ac0ca89eba53092d6ca93367fa753354de2 (composer.json line in pre-sha.txt).
  - post-sha matches.

### AC21 14 prior task verdicts untouched
- Status: PASS
- Proof:
  - artifacts/status-*.txt shows verdict_overall_status PASS for: bootstrap-proof-loop, test-harness, scaffold-service-stubs, land-laravel-framework, tdd-agent-deployer, tdd-kiloclaw-install, tdd-telegram-validate-token, tdd-telegram-set-webhook, tdd-onchainos-create-charge, landing-mockup-steve-ive, wizard-mockup-steve-ive, running-state-mockup-steve-ive, agents-list-mockup-steve-ive, agent-wallet-disclosure-mockup-steve-ive. This task did not edit any of their verdict.json, evidence.json, or spec.md.

### AC22 No em dash in authored files
- Status: PASS
- Proof:
  - Python grep for `\u2014` across the 3 production files and 1 test file returns zero matches.

## Commands run
- composer test > artifacts/baseline-test.txt
- shasum -a 256 ... > artifacts/pre-sha.txt
- composer test > artifacts/phpunit-red.txt (EXIT=255 before production existed)
- composer test > artifacts/phpunit-green.txt (EXIT=0, 41 tests)
- php -l on the 4 authored files > artifacts/php-lint.txt
- php .../autoload-check.php > artifacts/autoload-check.txt (13 ok)
- shasum -a 256 ... > artifacts/post-sha.txt; diff pre-sha post-sha (empty)
- task_loop.py status for each of 14 prior tasks > artifacts/status-*.txt

## Raw artifacts
- .agent/tasks/tdd-onchainos-xlayer-client/raw/build.txt
- .agent/tasks/tdd-onchainos-xlayer-client/raw/test-unit.txt
- .agent/tasks/tdd-onchainos-xlayer-client/raw/test-integration.txt
- .agent/tasks/tdd-onchainos-xlayer-client/raw/lint.txt
- .agent/tasks/tdd-onchainos-xlayer-client/raw/screenshot-1.png

## Known gaps
- None. A real curl/Guzzle XLayerHttpTransport is explicitly out of scope for this cycle (spec non-goals).
