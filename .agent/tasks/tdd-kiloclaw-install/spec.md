# Task Spec: tdd-kiloclaw-install

## Metadata
- Task ID: tdd-kiloclaw-install
- Created: 2026-04-14T09:42:45+00:00
- Frozen: 2026-04-13T00:00:00+00:00
- Repo root: /Users/krutovoy/Projects/hosting-platform
- Working directory at init: /Users/krutovoy/Projects/hosting-platform

## Guidance sources
- /Users/krutovoy/Projects/hosting-platform/CLAUDE.md (TDD mandatory, crypto hidden, Laravel 12 stack, walkthrough-first v0.1)
- /Users/krutovoy/Projects/hosting-platform/docs/integrations.md §KiloClaw / OpenClaw (manifest shape: id, name, version, description, skills, configSchema, postInstall, activation, security; two v0.1 calls: register agent, check status; auth and API-vs-CLI open questions)
- /Users/krutovoy/Projects/hosting-platform/docs/sprint_v0.1.md Day 2 Task 1 (KiloClaw install job packages manifest, calls KiloClawClientService::install, polls status until ready)
- /Users/krutovoy/Projects/hosting-platform/app/Services/KiloClawClientService.php (current single-line scaffold stub, to be rewritten)
- /Users/krutovoy/Projects/hosting-platform/app/Services/Telegram/TelegramHttpClient.php (prior TDD reference: strict_types, namespaced sub-package, single-method interface, docblock citing upstream URL)
- /Users/krutovoy/Projects/hosting-platform/app/Services/OnChainOS/OnChainOSClient.php (prior TDD reference: sub-package interface shape)
- /Users/krutovoy/Projects/hosting-platform/app/Services/OnChainOSPaymentService.php (service class pattern reference: final class, readonly ctor, short-circuit validation, try/catch, canonical return shape)
- /Users/krutovoy/Projects/hosting-platform/tests/Unit/Services/OnChainOSPaymentServiceTest.php (test-with-named-fake pattern; COPY THIS STYLE: in-file final fake class, no mocks, callCount, lastArgs, nextResponse, nextException fields)
- /Users/krutovoy/Projects/hosting-platform/.agent/tasks/tdd-onchainos-create-charge/spec.md (AC structure reused, values refreshed for KiloClaw)
- /Users/krutovoy/Projects/hosting-platform/composer.json (PSR-4 App\ -> app/, Tests\ -> tests/, PHPUnit 11; no HTTP client added)
- /Users/krutovoy/Projects/hosting-platform/phpunit.xml.dist (unchanged by this task)
- /Users/krutovoy/Projects/hosting-platform/.agent/tasks/ prior eight tasks used for the regression-check list: bootstrap-proof-loop, scaffold-service-stubs, test-harness, landing-mockup-steve-ive, tdd-telegram-validate-token, wizard-mockup-steve-ive, tdd-onchainos-create-charge, agents-list-mockup-steve-ive

## Original task statement
Third TDD cycle. Implement KiloClawClientService::install(array $manifest): array using strict red-green-refactor. KiloClaw hosts agent plugins per openclaw.plugin.json manifest (see ~/Projects/mandate/packages/openclaw-plugin/openclaw.plugin.json and docs/integrations.md). Method accepts a manifest array, calls an injected KiloClawHttpClient interface, and returns a canonical shape: ['status' => 'ready'|'booting'|'failed'|'invalid', 'kiloclaw_id' => string|null, 'manifest_id' => string|null, 'error' => string|null]. Validates manifest has required keys (id, name, version, skills). Retries only via the client; service does not retry. Introduces app/Services/KiloClaw/KiloClawHttpClient interface (single method install(array manifest, string idempotencyKey): array) and KiloClawException extending RuntimeException. Uses in-file fake not mock. Strict types on all files. Full red->green artifacts. No Laravel, no composer deps added, no network calls. No edits outside app/Services/KiloClaw/, app/Services/KiloClawClientService.php, tests/Unit/Services/KiloClawClientServiceTest.php, and the task artifacts.

## Acceptance criteria

### Strict TDD red step (tests first, no production code yet)
- AC1: A failing PHPUnit test named `test_manifest_missing_id_returns_invalid_and_does_not_call_client` is written first. Manifest `['name' => 'atlas', 'version' => '0.1.0', 'skills' => []]` (no `id`). The fake `KiloClawHttpClient` records every call to `install`. The test asserts `install($manifest)` returns an array with `status` key equal to `'invalid'` AND that the fake's `callCount` stays at zero. The very first phpunit run captured in `.agent/tasks/tdd-kiloclaw-install/artifacts/phpunit-red.txt` exits nonzero with a clear failure message, and at that point no production code beyond the existing scaffold stub exists. This is the strict red step.
- AC2: A failing PHPUnit test named `test_manifest_missing_name_returns_invalid_and_does_not_call_client` is included in the same red run. Manifest `['id' => 'plugin.atlas', 'version' => '0.1.0', 'skills' => []]` (no `name`). The test asserts `status => 'invalid'` AND that the fake's `callCount` stays at zero. The red artifact shows this test failing.
- AC3: A failing PHPUnit test named `test_manifest_missing_version_returns_invalid_and_does_not_call_client` is included in the same red run. Manifest `['id' => 'plugin.atlas', 'name' => 'atlas', 'skills' => []]` (no `version`). The test asserts `status => 'invalid'` AND that the fake's `callCount` stays at zero. The red artifact shows this test failing.
- AC4: A failing PHPUnit test named `test_manifest_missing_skills_returns_invalid_and_does_not_call_client` is included in the same red run. Manifest `['id' => 'plugin.atlas', 'name' => 'atlas', 'version' => '0.1.0']` (no `skills`). The test asserts `status => 'invalid'` AND that the fake's `callCount` stays at zero. The red artifact shows this test failing.
- AC5: A failing PHPUnit test named `test_happy_path_ready_returns_canonical_shape` is included in the same red run. The fake `KiloClawHttpClient` returns `['kiloclaw_id' => 'kc_abc', 'status' => 'ready']` and receives manifest `['id' => 'plugin.atlas', 'name' => 'atlas', 'version' => '0.1.0', 'skills' => []]`. The test asserts `install($manifest)` returns the canonical shape `['status' => 'ready', 'kiloclaw_id' => 'kc_abc', 'manifest_id' => 'plugin.atlas', 'error' => null]`. The red artifact shows this test failing.
- AC6: A failing PHPUnit test named `test_happy_path_booting_returns_canonical_shape` is included in the same red run. The fake returns `['kiloclaw_id' => 'kc_xyz', 'status' => 'booting']` for manifest `['id' => 'plugin.atlas', 'name' => 'atlas', 'version' => '0.1.0', 'skills' => []]`. The test asserts `install($manifest)` returns `['status' => 'booting', 'kiloclaw_id' => 'kc_xyz', 'manifest_id' => 'plugin.atlas', 'error' => null]`. The red artifact shows this test failing.
- AC7: A failing PHPUnit test named `test_client_exception_returns_failed_without_propagating` is included in the same red run. The fake throws `App\Services\KiloClaw\KiloClawException('boom')` from `install`. The test asserts `install($manifest)` returns `status => 'failed'`, `kiloclaw_id => null`, `manifest_id => 'plugin.atlas'`, and an `error` key containing `'boom'`. The exception does NOT propagate. Wrapped in `try { ... } catch (\Throwable $e) { $this->fail(...); }` to prove no propagation. The red artifact shows this test failing.
- AC8: A failing PHPUnit test named `test_malformed_response_missing_kiloclaw_id_returns_failed` is included in the same red run. The fake returns `['status' => 'ready']` with NO `kiloclaw_id` key. The test asserts `install($manifest)` returns `status => 'failed'`, `kiloclaw_id => null`, `manifest_id => 'plugin.atlas'`, and an `error` key with message `'malformed kiloclaw response'`. No exception is thrown. The red artifact shows this test failing.
- AC9: A failing PHPUnit test named `test_malformed_response_invalid_status_returns_failed` is included in the same red run. The fake returns `['kiloclaw_id' => 'kc_abc', 'status' => 'crashing']` (status not in the `ready|booting|failed` allowlist). The test asserts `install($manifest)` returns `status => 'failed'`, `kiloclaw_id => null`, `manifest_id => 'plugin.atlas'`, and an `error` key with message `'malformed kiloclaw response'`. The red artifact shows this test failing.
- AC10: A failing PHPUnit test named `test_idempotency_key_is_stable_across_two_calls_same_manifest` is included in the same red run. Two calls to `install($manifest)` happen with the same manifest `['id' => 'plugin.atlas', 'name' => 'atlas', 'version' => '0.1.0', 'skills' => []]`. The fake records the `$idempotencyKey` argument from every call in its `lastArgs` field. The test asserts the fake saw the SAME idempotency key for both calls, that the key starts with `'kiloclaw-'`, and that `callCount === 2`. The red artifact shows this test failing.

### Green step (production code lands, all tests pass)
- AC11: After production code lands, every test from AC1..AC10 turns green. Captured in `.agent/tasks/tdd-kiloclaw-install/artifacts/phpunit-green.txt`. Each of the ten test methods is listed by name in the green artifact or summarized in the final test count.
- AC12: `composer test` exits 0 from the repo root. Reports at least 24 tests total (14 prior tests + 10 new kiloclaw tests) and at least 24 assertions.

### File and structural acceptance
- AC13: `app/Services/KiloClaw/KiloClawHttpClient.php` exists with:
  - `<?php` opener
  - `declare(strict_types=1);` on line 2
  - `namespace App\Services\KiloClaw;`
  - `interface KiloClawHttpClient`
  - exactly one method: `public function install(array $manifest, string $idempotencyKey): array;`
  - Only a docblock or `//` comment is allowed to contain the upstream `https://kiloclaw.example/api/install` URL reference.
- AC14: `app/Services/KiloClaw/KiloClawException.php` exists with:
  - `<?php` opener
  - `declare(strict_types=1);` on line 2
  - `namespace App\Services\KiloClaw;`
  - `final class KiloClawException extends \RuntimeException`
- AC15: `app/Services/KiloClawClientService.php` is rewritten with:
  - `<?php` opener
  - `declare(strict_types=1);` on line 2
  - `namespace App\Services;`
  - `final class KiloClawClientService`
  - constructor: `public function __construct(private readonly \App\Services\KiloClaw\KiloClawHttpClient $client)` (FQN or via `use` statement acceptable)
  - public method `install(array $manifest): array`
  - No other public methods on the class besides the constructor and `install`. The original single-line scaffold comment may be removed or repurposed as a short class-level comment.
- AC16: `tests/Unit/Services/KiloClawClientServiceTest.php` exists with:
  - `<?php` opener
  - `declare(strict_types=1);` on line 2
  - `namespace Tests\Unit\Services;`
  - `final class KiloClawClientServiceTest extends \PHPUnit\Framework\TestCase`
  - An in-file helper class `final class FakeKiloClawHttpClient implements \App\Services\KiloClaw\KiloClawHttpClient` with public fields `int $callCount`, `array $lastArgs`, `array $nextResponse`, `?\Throwable $nextException`.

### Behavioral acceptance
- AC17: `install` validates the manifest has the four required keys `id`, `name`, `version`, `skills`. On any missing key, or if `id` is not a non-empty string, or if `name` is not a non-empty string, or if `version` is not a non-empty string, or if `skills` is not an array, the method returns `['status' => 'invalid', 'kiloclaw_id' => null, 'manifest_id' => null, 'error' => 'missing or invalid manifest keys: <listed keys>']` WITHOUT calling `$this->client->install`. Proven by AC1..AC4 tests asserting the fake's `callCount` stays at zero. The `<listed keys>` part is a comma-separated list of the offending key names in the fixed order `id, name, version, skills` (only those that actually failed).
- AC18: `install` builds the idempotency key with the EXACT expression `'kiloclaw-' . sha1($manifest['id'] . ':' . $manifest['version'])`. This exact expression must appear verbatim in `app/Services/KiloClawClientService.php`. The verifier greps for it.
- AC19: `install` calls `$this->client->install($manifest, $idempotencyKey)` inside a try block. On `KiloClawException`, it returns `['status' => 'failed', 'kiloclaw_id' => null, 'manifest_id' => $manifest['id'] ?? null, 'error' => $e->getMessage()]` and does NOT propagate the exception. Proven by AC7 test.
- AC20: `install` treats a client response as malformed if it is missing the `kiloclaw_id` key, OR its `kiloclaw_id` is not a string, OR its `status` key is missing, OR its `status` value is not one of the allowlist `'ready'`, `'booting'`, `'failed'`. On malformed response, it returns `['status' => 'failed', 'kiloclaw_id' => null, 'manifest_id' => $manifest['id'], 'error' => 'malformed kiloclaw response']` and does not throw. Proven by AC8 and AC9 tests.
- AC21: On a valid response, `install` returns `['status' => $response['status'], 'kiloclaw_id' => $response['kiloclaw_id'], 'manifest_id' => $manifest['id'], 'error' => null]`. Proven by AC5 and AC6 tests. Happy-path canonical manifest fixture: `['id' => 'plugin.atlas', 'name' => 'atlas', 'version' => '0.1.0', 'skills' => []]`. Happy-path fake response: `['kiloclaw_id' => 'kc_abc', 'status' => 'ready']`. Expected canonical return: `['status' => 'ready', 'kiloclaw_id' => 'kc_abc', 'manifest_id' => 'plugin.atlas', 'error' => null]`.
- AC22: No disallowed constructs inside the `install` method body. The body must not contain the substrings ` new ` (spaced), `::`, or ` static ` (spaced). `catch (KiloClawException $e)` is allowed because it contains no `::`. Class-level `use` statements, the class signature, and constructor property promotion are outside the method body and are unrestricted. Verifier extracts the method body via awk between the `install` signature and its matching closing brace and greps.
- AC23: The test file uses a fake, not a PHPUnit mock. Zero occurrences of `createMock`, `getMockBuilder`, or `MockObject` in `tests/Unit/Services/KiloClawClientServiceTest.php`. The fake is a small in-file final helper class `FakeKiloClawHttpClient` implementing `KiloClawHttpClient`, recording at minimum: `public int $callCount`, `public array $lastArgs`, `public array $nextResponse`, `public ?\Throwable $nextException`.

### Hygiene acceptance
- AC24: `php -l` exits 0 on every authored PHP file:
  - `app/Services/KiloClaw/KiloClawHttpClient.php`
  - `app/Services/KiloClaw/KiloClawException.php`
  - `app/Services/KiloClawClientService.php`
  - `tests/Unit/Services/KiloClawClientServiceTest.php`
- AC25: The autoload probe is extended to cover the two new KiloClaw symbols. After `require vendor/autoload.php`, all ten of the following resolve via `class_exists` or `interface_exists`:
  - `App\Services\AgentDeployerService`
  - `App\Services\KiloClawClientService`
  - `App\Services\OnChainOSPaymentService`
  - `App\Services\TelegramBotRegistrarService`
  - `App\Services\Telegram\TelegramHttpClient`
  - `App\Services\Telegram\TelegramTransportException`
  - `App\Services\OnChainOS\OnChainOSClient`
  - `App\Services\OnChainOS\OnChainOSException`
  - `App\Services\KiloClaw\KiloClawHttpClient`
  - `App\Services\KiloClaw\KiloClawException`
- AC26: No live network call. Verifier greps the four authored files for `file_get_contents`, `curl_init`, `curl_exec`, `fsockopen`, `fopen('http`, and `https?://`. The only allowed match for `https://` is a single line inside `app/Services/KiloClaw/KiloClawHttpClient.php` that begins with `//`, `*`, or `#` (a comment or docblock). Production code lines outside comments must have zero matches.

### Artifact acceptance
- AC27: `.agent/tasks/tdd-kiloclaw-install/artifacts/phpunit-red.txt` exists. Nonzero exit. Shows failing tests for AC1..AC10. Produced BEFORE any production code beyond the existing scaffold stub landed. The build log records this ordering explicitly.
- AC28: `.agent/tasks/tdd-kiloclaw-install/artifacts/phpunit-green.txt` exists. Exit 0. Shows all tests passing AFTER production code landed. Reports at least 24 tests total and at least 24 assertions.

### Regression and scope acceptance
- AC29: No regressions on prior tasks. The verifier reads `.agent/tasks/<task>/verdict.json` for each of `bootstrap-proof-loop`, `scaffold-service-stubs`, `test-harness`, `landing-mockup-steve-ive`, `tdd-telegram-validate-token`, `wizard-mockup-steve-ive`, `tdd-onchainos-create-charge`, `agents-list-mockup-steve-ive` and confirms each still reports `verdict_overall_status: PASS`. If any prior verdict file is missing or not PASS at freeze time, this AC is waived for that task only and the waiver is recorded in evidence at verify time.
- AC30: No collateral file changes. Repo file changes confined to:
  - `app/Services/KiloClaw/KiloClawHttpClient.php` (new)
  - `app/Services/KiloClaw/KiloClawException.php` (new)
  - `app/Services/KiloClawClientService.php` (rewritten)
  - `tests/Unit/Services/KiloClawClientServiceTest.php` (new)
  - `.agent/tasks/tdd-kiloclaw-install/artifacts/phpunit-red.txt` (new)
  - `.agent/tasks/tdd-kiloclaw-install/artifacts/phpunit-green.txt` (new)
  - `.agent/tasks/tdd-kiloclaw-install/spec.md` (this file, finalized)
  - `.agent/tasks/tdd-kiloclaw-install/evidence.json`, `evidence.md`, `verdict.json`, `problems.md` (workflow files, written by build/verify steps)
  - `.phpunit.result.cache` may update; tolerate.
  - No edits to `tests/Unit/Services/TelegramBotRegistrarServiceTest.php`, `tests/Unit/Services/OnChainOSPaymentServiceTest.php`, `app/Services/Telegram/*`, `app/Services/OnChainOS/*`, `app/Services/OnChainOSPaymentService.php`, `app/Services/AgentDeployerService.php`, `app/Services/TelegramBotRegistrarService.php`, `composer.json`, `phpunit.xml.dist`, or `.gitignore`.
- AC31: No em dashes (the U+2014 character) in any authored file from this task. Verifier greps for the literal em dash in all touched files and in this spec.

## Constraints
- Strict TDD discipline. Tests are written and observed failing BEFORE any production code change beyond the existing scaffold stub. The `phpunit-red.txt` artifact must precede any edits to `app/Services/KiloClawClientService.php` body or to the new `KiloClaw/` subdirectory files.
- RED phase writes ONLY the test file first. GREEN phase adds the interface, the exception, and the service rewrite.
- No live network calls during the build, the test, or the verify step. The injected fake is the only path to `install` data.
- No Laravel framework code, no Illuminate facades, no service container resolution. The service must be constructible with a single explicit argument from a plain PHP test.
- No Guzzle, no Symfony HttpClient, no third-party HTTP package added to `composer.json`. The interface is the seam; a real HTTP or CLI implementation lands in a separate downstream task.
- No deletes outside scope. If any file removal becomes necessary, use `trash` (not `rm`).
- No em dashes anywhere. Use colons, periods, or commas in prose.
- No PHPUnit mock objects. Use a fake (small in-file final concrete class) implementing `KiloClawHttpClient`.
- All new PHP files declare `strict_types=1` on line 2.
- No edits to `composer.json`, `phpunit.xml.dist`, `.gitignore`, or any other top-level config file.
- No edits to prior TDD files: `tests/Unit/Services/TelegramBotRegistrarServiceTest.php`, `tests/Unit/Services/OnChainOSPaymentServiceTest.php`, `app/Services/Telegram/*`, `app/Services/OnChainOS/*`, `app/Services/OnChainOSPaymentService.php`, `app/Services/TelegramBotRegistrarService.php`, `app/Services/AgentDeployerService.php`.
- No new top-level directories outside `app/Services/KiloClaw/` and reuse of the existing `tests/Unit/Services/`.
- No regex parsing of response bodies. Rely on array key access and simple scalar type checks.
- No new composer dependencies.
- The build runs locally; no Coolify deploy, no DNS changes, no remote infra writes.
- Idempotency key format is fixed: `'kiloclaw-' . sha1($manifest['id'] . ':' . $manifest['version'])`. This exact expression must appear in `KiloClawClientService::install`.
- Canonical return-shape keys are fixed: `status`, `kiloclaw_id`, `manifest_id`, `error`. Key order in the final return statement SHOULD follow this order for readability but is not strictly enforced.
- Allowed `status` values in a successful response: `'ready'`, `'booting'`, `'failed'`. Any other value is treated as malformed.

## Non-goals
- No real KiloClaw HTTP API call or CLI shell-out in this task. The concrete `KiloClawHttpClient` implementation is a separate downstream task.
- No status polling loop. The service does a single `install` call and returns whatever the client yields, normalized. Polling is a separate queue-job task.
- No webhook receiver, no queue job wiring, no controller, no route, no middleware.
- No Laravel facade, no Eloquent model, no migration, no mailer.
- No `.env` file, no environment variable wiring, no config provider.
- No DB schema, no queue job, no Bus dispatch.
- No Mandate integration.
- No frontend wiring, no Inertia page, no React change.
- No OnChainOS or Telegram code changes in this task.
- No refactor of the test harness, `phpunit.xml.dist`, or `composer.json`.
- No changes to prior TDD task files.
- No retry logic inside the service; retries belong to the client implementation that lands later.

## Verification plan

The verifier runs each of the following and pins each command to the AC it satisfies.

### Build presence and shape
- `test -f app/Services/KiloClaw/KiloClawHttpClient.php` (AC13)
- `test -f app/Services/KiloClaw/KiloClawException.php` (AC14)
- `test -f app/Services/KiloClawClientService.php` (AC15)
- `test -f tests/Unit/Services/KiloClawClientServiceTest.php` (AC16, AC30)
- `grep -n "declare(strict_types=1);" app/Services/KiloClaw/KiloClawHttpClient.php` (AC13, AC24)
- `grep -n "declare(strict_types=1);" app/Services/KiloClaw/KiloClawException.php` (AC14, AC24)
- `grep -n "declare(strict_types=1);" app/Services/KiloClawClientService.php` (AC15, AC24)
- `grep -n "declare(strict_types=1);" tests/Unit/Services/KiloClawClientServiceTest.php` (AC16, AC24)
- `grep -n "namespace App\\\\Services\\\\KiloClaw" app/Services/KiloClaw/KiloClawHttpClient.php` (AC13)
- `grep -n "interface KiloClawHttpClient" app/Services/KiloClaw/KiloClawHttpClient.php` (AC13)
- `grep -nE "public function install\\(array \\\$manifest, string \\\$idempotencyKey\\): array" app/Services/KiloClaw/KiloClawHttpClient.php` (AC13)
- `grep -nE "final class KiloClawException extends .*RuntimeException" app/Services/KiloClaw/KiloClawException.php` (AC14)
- `grep -n "final class KiloClawClientService" app/Services/KiloClawClientService.php` (AC15)
- `grep -nE "private readonly .*KiloClawHttpClient \\\$client" app/Services/KiloClawClientService.php` (AC15)
- `grep -nE "public function install\\(array \\\$manifest\\): array" app/Services/KiloClawClientService.php` (AC15)
- `grep -cE "^\\s*public function " app/Services/KiloClawClientService.php` returns at most 2 (constructor plus `install`) (AC15)
- `grep -nE "final class FakeKiloClawHttpClient implements .*KiloClawHttpClient" tests/Unit/Services/KiloClawClientServiceTest.php` (AC16, AC23)
- `grep -nE "public int \\\$callCount" tests/Unit/Services/KiloClawClientServiceTest.php` (AC16, AC23)
- `grep -nE "public array \\\$lastArgs" tests/Unit/Services/KiloClawClientServiceTest.php` (AC16, AC23)
- `grep -nE "public array \\\$nextResponse" tests/Unit/Services/KiloClawClientServiceTest.php` (AC16, AC23)
- `grep -nE "public \\?\\\\Throwable \\\$nextException" tests/Unit/Services/KiloClawClientServiceTest.php` (AC16, AC23)

### Lint
- `php -l app/Services/KiloClaw/KiloClawHttpClient.php` (AC24)
- `php -l app/Services/KiloClaw/KiloClawException.php` (AC24)
- `php -l app/Services/KiloClawClientService.php` (AC24)
- `php -l tests/Unit/Services/KiloClawClientServiceTest.php` (AC24)

### Autoload probe
- `php -r 'require "vendor/autoload.php"; foreach (["App\\\\Services\\\\AgentDeployerService","App\\\\Services\\\\KiloClawClientService","App\\\\Services\\\\OnChainOSPaymentService","App\\\\Services\\\\TelegramBotRegistrarService","App\\\\Services\\\\Telegram\\\\TelegramHttpClient","App\\\\Services\\\\Telegram\\\\TelegramTransportException","App\\\\Services\\\\OnChainOS\\\\OnChainOSClient","App\\\\Services\\\\OnChainOS\\\\OnChainOSException","App\\\\Services\\\\KiloClaw\\\\KiloClawHttpClient","App\\\\Services\\\\KiloClaw\\\\KiloClawException"] as $c) { if (!class_exists($c) && !interface_exists($c)) { fwrite(STDERR, "MISSING: $c\n"); exit(1); } } echo "OK\n";'` (AC25)

### Test execution and artifacts
- `test -f .agent/tasks/tdd-kiloclaw-install/artifacts/phpunit-red.txt` (AC27)
- `test -f .agent/tasks/tdd-kiloclaw-install/artifacts/phpunit-green.txt` (AC28)
- `grep -E "FAIL|Errors|Failures" .agent/tasks/tdd-kiloclaw-install/artifacts/phpunit-red.txt` (AC27)
- `grep -E "OK|Tests: [0-9]+, Assertions: [0-9]+" .agent/tasks/tdd-kiloclaw-install/artifacts/phpunit-green.txt` (AC28)
- `composer test` exits 0 (AC11, AC12)
- Parse final `composer test` output for `Tests: N` where `N >= 24` and `Assertions: M` where `M >= 24` (AC12, AC28)

### Behavioral grep checks
- `grep -nE "createMock|getMockBuilder|MockObject" tests/Unit/Services/KiloClawClientServiceTest.php` returns no matches (AC23)
- `grep -n "function test_manifest_missing_id_returns_invalid_and_does_not_call_client" tests/Unit/Services/KiloClawClientServiceTest.php` returns at least one match (AC1)
- `grep -n "function test_manifest_missing_name_returns_invalid_and_does_not_call_client" tests/Unit/Services/KiloClawClientServiceTest.php` returns at least one match (AC2)
- `grep -n "function test_manifest_missing_version_returns_invalid_and_does_not_call_client" tests/Unit/Services/KiloClawClientServiceTest.php` returns at least one match (AC3)
- `grep -n "function test_manifest_missing_skills_returns_invalid_and_does_not_call_client" tests/Unit/Services/KiloClawClientServiceTest.php` returns at least one match (AC4)
- `grep -n "function test_happy_path_ready_returns_canonical_shape" tests/Unit/Services/KiloClawClientServiceTest.php` returns at least one match (AC5)
- `grep -n "function test_happy_path_booting_returns_canonical_shape" tests/Unit/Services/KiloClawClientServiceTest.php` returns at least one match (AC6)
- `grep -n "function test_client_exception_returns_failed_without_propagating" tests/Unit/Services/KiloClawClientServiceTest.php` returns at least one match (AC7)
- `grep -n "function test_malformed_response_missing_kiloclaw_id_returns_failed" tests/Unit/Services/KiloClawClientServiceTest.php` returns at least one match (AC8)
- `grep -n "function test_malformed_response_invalid_status_returns_failed" tests/Unit/Services/KiloClawClientServiceTest.php` returns at least one match (AC9)
- `grep -n "function test_idempotency_key_is_stable_across_two_calls_same_manifest" tests/Unit/Services/KiloClawClientServiceTest.php` returns at least one match (AC10)
- `grep -nF "'plugin.atlas'" tests/Unit/Services/KiloClawClientServiceTest.php` returns at least one match (AC5, AC21)
- `grep -nF "'kc_abc'" tests/Unit/Services/KiloClawClientServiceTest.php` returns at least one match (AC5, AC21)
- `grep -nF "'kiloclaw-'" app/Services/KiloClawClientService.php` returns at least one match (AC18)
- `grep -nF "sha1(\$manifest['id'] . ':' . \$manifest['version'])" app/Services/KiloClawClientService.php` returns at least one match (AC18)
- Verifier extracts the body of `install` from `app/Services/KiloClawClientService.php` via awk between the method signature and its matching closing brace. Inside that body:
  - `grep -nE " new "` returns no matches (AC22)
  - `grep -nF "::"` returns no matches (AC22)
  - `grep -nE " static "` returns no matches (AC22)
- `grep -nE "file_get_contents|curl_init|curl_exec|fsockopen|fopen\\('http" app/Services/KiloClawClientService.php app/Services/KiloClaw/*.php tests/Unit/Services/KiloClawClientServiceTest.php` returns no matches (AC26)
- `grep -nE "https?://[^ )]" app/Services/KiloClawClientService.php app/Services/KiloClaw/*.php tests/Unit/Services/KiloClawClientServiceTest.php` only matches lines that begin with `//`, `*`, or `#` (AC26)

### Em dash check
- `grep -nP "\xE2\x80\x94" app/Services/KiloClaw/KiloClawHttpClient.php app/Services/KiloClaw/KiloClawException.php app/Services/KiloClawClientService.php tests/Unit/Services/KiloClawClientServiceTest.php .agent/tasks/tdd-kiloclaw-install/spec.md` returns no matches (AC31)

### Regression check
- For each prior task in `bootstrap-proof-loop`, `scaffold-service-stubs`, `test-harness`, `landing-mockup-steve-ive`, `tdd-telegram-validate-token`, `wizard-mockup-steve-ive`, `tdd-onchainos-create-charge`, `agents-list-mockup-steve-ive`: read `.agent/tasks/<task>/verdict.json` and confirm `verdict_overall_status` is `PASS` (AC29)
- `python .claude/skills/repo-task-proof-loop/scripts/task_loop.py status --task-id <task>` for each of the eight prior tasks (AC29)

### Scope check
- `git status --porcelain` (or equivalent file enumeration) shows only the files listed in AC30. No new top-level directories beyond `app/Services/KiloClaw/`. No edits to `composer.json`, `phpunit.xml.dist`, `.gitignore`, `tests/Unit/Services/TelegramBotRegistrarServiceTest.php`, `tests/Unit/Services/OnChainOSPaymentServiceTest.php`, `app/Services/Telegram/*`, `app/Services/OnChainOS/*`, `app/Services/OnChainOSPaymentService.php`, `app/Services/AgentDeployerService.php`, or `app/Services/TelegramBotRegistrarService.php`. (AC30)
- `test -f tests/Unit/Services/TelegramBotRegistrarServiceTest.php` AND its content hash matches the prior task's committed version (AC30)
- `test -f tests/Unit/Services/OnChainOSPaymentServiceTest.php` AND its content hash matches the prior task's committed version (AC30)
