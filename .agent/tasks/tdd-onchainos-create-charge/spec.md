# Task Spec: tdd-onchainos-create-charge

## Metadata
- Task ID: tdd-onchainos-create-charge
- Created: 2026-04-14T09:15:00+00:00
- Repo root: /Users/krutovoy/Projects/hosting-platform
- Working directory at init: /Users/krutovoy/Projects/hosting-platform

## Guidance sources
- /Users/krutovoy/Projects/hosting-platform/CLAUDE.md (TDD mandatory, crypto hidden, Laravel 12 stack, walkthrough-first v0.1)
- /Users/krutovoy/Projects/hosting-platform/docs/integrations.md (OnChainOS section: createCharge returns session ID, stub allowed for v0.1, webhook receiver is a separate task)
- /Users/krutovoy/Projects/hosting-platform/docs/sprint_v0.1.md (Day 0 blocker #1 "stub a 'mark paid after 3s' for v0.1 demo, wire real in v0.2"; Day 1 Task 6 calls OnChainOSPaymentService::createCharge)
- /Users/krutovoy/Projects/hosting-platform/app/Services/OnChainOSPaymentService.php (current single-line scaffold stub)
- /Users/krutovoy/Projects/hosting-platform/app/Services/Telegram/TelegramHttpClient.php (prior TDD task reference shape: strict_types, namespaced sub-package, single-method interface, docblock citing upstream URL)
- /Users/krutovoy/Projects/hosting-platform/tests/Unit/Services/TelegramBotRegistrarServiceTest.php (prior TDD test pattern: in-file anonymous class fake, no createMock, call counters, readonly constructor, nullable next-response and next-exception)
- /Users/krutovoy/Projects/hosting-platform/.agent/tasks/tdd-telegram-validate-token/spec.md (AC structure reused, values refreshed for OnChainOS)
- /Users/krutovoy/Projects/hosting-platform/composer.json (PSR-4 `App\\` -> app/, `Tests\\` -> tests/, PHPUnit 11; no HTTP client dependency added)
- /Users/krutovoy/Projects/hosting-platform/phpunit.xml.dist (unchanged by this task; Tests namespace autoload already covers nested folders)
- /Users/krutovoy/Projects/hosting-platform/tests/Unit/SmokeTest.php (baseline test, must keep passing)
- /Users/krutovoy/Projects/hosting-platform/.agent/tasks/ prior six tasks (`bootstrap-proof-loop`, `scaffold-service-stubs`, `test-harness`, `landing-mockup-steve-ive`, `tdd-telegram-validate-token`, `wizard-mockup-steve-ive`) used for the regression-check list

## Original task statement
Second TDD cycle on the hosting-platform harness. Implement OnChainOSPaymentService::createCharge(int $amountUsd, string $agentName): array using strict red-green-refactor discipline. Per docs/integrations.md and sprint_v0.1.md Day 1 Task 6, OnChainOS may be stubbed for the hackathon ("mark paid after 3 seconds"), so the v0.1 implementation creates a deterministic fake session and returns a payment intent shape. Introduce app/Services/OnChainOS/OnChainOSClient interface (single method createCharge(int amountUsd, string agentName, string idempotencyKey): array) and app/Services/OnChainOS/OnChainOSException for transport failures. The service's createCharge method validates inputs (amount > 0, name non-empty), builds a deterministic idempotency key per agent name + amount + UTC date, calls the injected client, normalizes the returned array into a fixed shape with keys session_id, status, amount_usd, agent_name, expires_at, and returns it. On client exception or invalid response shape, return ['status' => 'failed', ...] without throwing. All work via PHPUnit fakes; no real HTTP; full red->green discipline with phpunit-red.txt and phpunit-green.txt artifacts. Strict types. No Guzzle. No Laravel. No new composer deps. No edits to existing TelegramBotRegistrarService, scaffold service stubs other than OnChainOSPaymentService, or design/.

## Acceptance criteria

### Strict TDD red step (tests first, no production code yet)
- AC1: A failing PHPUnit test for the empty-agent-name case is written first. `createCharge(10, '')` is asserted to return an array with `status` key equal to `'invalid'`. The very first phpunit run captured in `.agent/tasks/tdd-onchainos-create-charge/artifacts/phpunit-red.txt` exits nonzero with a clear failure message, and at that point no production code beyond the existing scaffold stub exists. This is the strict red step.
- AC2: A failing PHPUnit test for a whitespace-only agent-name case is included in the same red run. The fake `OnChainOSClient` records every call to `createCharge`. The test asserts `createCharge(10, '   ')` returns `status => 'invalid'` AND that the fake's `callCount` stays at zero. The red artifact shows this test failing.
- AC3: A failing PHPUnit test for a zero-amount case is included in the same red run. The test asserts `createCharge(0, 'spawn-bot')` returns `status => 'invalid'` AND that the fake's `callCount` stays at zero. The red artifact shows this test failing.
- AC4: A failing PHPUnit test for a negative-amount case is included in the same red run. The test asserts `createCharge(-5, 'spawn-bot')` returns `status => 'invalid'` AND that the fake's `callCount` stays at zero. The red artifact shows this test failing.
- AC5: A failing PHPUnit test for the happy path is included in the same red run. The fake `OnChainOSClient` returns `['session_id' => 'sess_abc', 'status' => 'pending', 'expires_at' => '2026-04-14T10:00:00Z']`. The test asserts `createCharge(10, 'spawn-bot')` returns the canonical shape with `status => 'pending'`, `session_id => 'sess_abc'`, `amount_usd => 10`, `agent_name => 'spawn-bot'`, and `expires_at => '2026-04-14T10:00:00Z'`. The red artifact shows this test failing.
- AC6: A failing PHPUnit test for a client-exception case is included in the same red run. The fake `OnChainOSClient` throws `App\Services\OnChainOS\OnChainOSException` from `createCharge`. The test asserts `createCharge(10, 'spawn-bot')` returns `status => 'failed'` with an `error` key, and that the exception does not propagate. The red artifact shows this test failing.
- AC7: A failing PHPUnit test for a malformed-response case is included in the same red run. The fake returns `['status' => 'pending']` with NO `session_id` key. The test asserts `createCharge(10, 'spawn-bot')` returns `status => 'failed'` with an `error` key, and that no exception is thrown. The red artifact shows this test failing.
- AC8: A failing PHPUnit test for idempotency-key stability is included in the same red run. Two calls to `createCharge(10, 'spawn-bot')` happen inside the same UTC day. The fake records the `idempotencyKey` argument from every call in a `lastArgs` / `argsLog` array. The test asserts that the fake saw the SAME idempotency key for both calls, and that the key starts with `'spawn-'`. The red artifact shows this test failing.

### Green step (production code lands, all tests pass)
- AC9: After production code lands, every test from AC1..AC8 turns green. Captured in `.agent/tasks/tdd-onchainos-create-charge/artifacts/phpunit-green.txt`. Each of the eight test methods is listed by name in the green artifact or summarized in the final test count.
- AC10: `composer test` exits 0 from the repo root. Reports at least 14 tests total (1 SmokeTest + 5 telegram tests + 8 onchainos tests) and at least 14 assertions.

### File and structural acceptance
- AC11: `app/Services/OnChainOS/OnChainOSClient.php` exists with:
  - `<?php` opener
  - `declare(strict_types=1);`
  - `namespace App\Services\OnChainOS;`
  - `interface OnChainOSClient`
  - exactly one method: `public function createCharge(int $amountUsd, string $agentName, string $idempotencyKey): array;`
  - Only a docblock or `//` comment is allowed to contain the upstream `https://` URL reference.
- AC12: `app/Services/OnChainOS/OnChainOSException.php` exists with:
  - `<?php` opener
  - `declare(strict_types=1);`
  - `namespace App\Services\OnChainOS;`
  - `final class OnChainOSException extends \RuntimeException`
- AC13: `app/Services/OnChainOSPaymentService.php` has:
  - `<?php` opener
  - `declare(strict_types=1);`
  - `namespace App\Services;`
  - `final class OnChainOSPaymentService`
  - constructor: `public function __construct(private readonly \App\Services\OnChainOS\OnChainOSClient $client)` (FQN or via `use` statement)
  - public method `createCharge(int $amountUsd, string $agentName): array`
  - No other public methods on the class. The original single-line scaffold comment may be removed or repurposed as a short class-level comment about the role.

### Behavioral acceptance
- AC14: `createCharge` short-circuits on `$amountUsd <= 0` or `trim($agentName) === ''` and does NOT call `$this->client->createCharge`. Proven by AC1..AC4 tests asserting the fake's `callCount` stays at zero. On invalid input the method returns `['status' => 'invalid', 'amount_usd' => $amountUsd, 'agent_name' => $agentName, 'session_id' => null, 'expires_at' => null]` and never throws.
- AC15: `createCharge` catches `App\Services\OnChainOS\OnChainOSException` inside a try block around the client call and returns `['status' => 'failed', 'amount_usd' => $amountUsd, 'agent_name' => $agentName, 'session_id' => null, 'expires_at' => null, 'error' => $exception->getMessage()]` rather than letting the exception propagate. Proven by AC6 test.
- AC16: `createCharge` treats a client response missing either `session_id` or `status` as malformed, returns `['status' => 'failed', ...]` with an `error` key noting the malformed response, and does not throw. Proven by AC7 test.
- AC17: The idempotency key passed to the client is deterministic across two calls with the same `($amountUsd, $agentName)` inside the same UTC day. The service MUST build the key as `'spawn-' . sha1($agentName . ':' . $amountUsd . ':' . gmdate('Y-m-d'))` so stability is guaranteed by `gmdate` returning the same UTC date across the two calls of a single test method. Proven by the AC8 test asserting key equality across two recorded calls.
- AC18: No disallowed constructs inside the `createCharge` method body. The method body must not contain the substrings `new ` (spaced), `::`, or ` static ` (spaced). `catch (OnChainOSException $e)` is allowed because it contains no `::`. Class-level `use` statements and constructor property promotion are outside the method body and are unrestricted. Verifier extracts the method body via awk/sed and greps.
- AC19: The test file uses a fake, not a PHPUnit mock. Zero occurrences of `createMock`, `getMockBuilder`, or `MockObject` in `tests/Unit/Services/OnChainOSPaymentServiceTest.php`. The fake is an inline anonymous class or a small in-file helper class implementing `OnChainOSClient`, recording at minimum: `int $callCount`, an `array` of observed `(amountUsd, agentName, idempotencyKey)` tuples, a configurable `?array $nextResponse`, and a configurable `?\Throwable $nextException`.

### Hygiene acceptance
- AC20: `php -l` exits 0 on every authored PHP file:
  - `app/Services/OnChainOS/OnChainOSClient.php`
  - `app/Services/OnChainOS/OnChainOSException.php`
  - `app/Services/OnChainOSPaymentService.php`
  - `tests/Unit/Services/OnChainOSPaymentServiceTest.php`
- AC21: The autoload probe from `test-harness` is extended to cover the two new OnChainOS symbols. After `require vendor/autoload.php`, all eight of the following resolve via `class_exists` or `interface_exists`:
  - `App\Services\AgentDeployerService`
  - `App\Services\KiloClawClientService`
  - `App\Services\OnChainOSPaymentService`
  - `App\Services\TelegramBotRegistrarService`
  - `App\Services\Telegram\TelegramHttpClient`
  - `App\Services\Telegram\TelegramTransportException`
  - `App\Services\OnChainOS\OnChainOSClient`
  - `App\Services\OnChainOS\OnChainOSException`
- AC22: No live network call. Verifier greps the four authored files for `file_get_contents`, `curl_init`, `curl_exec`, `fsockopen`, `fopen('http`, `http://`, and `https://`. The only allowed matches for `https://` are lines that begin with `//`, `*`, or `#` (i.e. a comment or docblock) inside `app/Services/OnChainOS/OnChainOSClient.php`. Production code lines outside comments must have zero matches.

### Artifact acceptance
- AC23: `.agent/tasks/tdd-onchainos-create-charge/artifacts/phpunit-red.txt` exists. Nonzero exit. Shows failing tests for AC1..AC8. Produced BEFORE any production code beyond the scaffold stub landed. The build log records this ordering explicitly.
- AC24: `.agent/tasks/tdd-onchainos-create-charge/artifacts/phpunit-green.txt` exists. Exit 0. Shows all tests passing AFTER production code landed. Reports at least 14 tests total and at least 14 assertions.

### Regression and scope acceptance
- AC25: No regressions on prior tasks. The verifier reruns `python .claude/skills/repo-task-proof-loop/scripts/task_loop.py status --task-id <task>` (or reads the existing `verdict.json` files) for each of `bootstrap-proof-loop`, `scaffold-service-stubs`, `test-harness`, `landing-mockup-steve-ive`, `tdd-telegram-validate-token`, `wizard-mockup-steve-ive`, and confirms each still reports `verdict_overall_status: PASS`.
- AC26: No collateral top-level additions. Repo-root file changes confined to:
  - `app/Services/OnChainOS/OnChainOSClient.php` (new)
  - `app/Services/OnChainOS/OnChainOSException.php` (new)
  - `app/Services/OnChainOSPaymentService.php` (modified)
  - `tests/Unit/Services/OnChainOSPaymentServiceTest.php` (new)
  - `.agent/tasks/tdd-onchainos-create-charge/artifacts/phpunit-red.txt` (new)
  - `.agent/tasks/tdd-onchainos-create-charge/artifacts/phpunit-green.txt` (new)
  - `.agent/tasks/tdd-onchainos-create-charge/spec.md` (this file, finalized)
  - `.agent/tasks/tdd-onchainos-create-charge/evidence.json`, `evidence.md`, `verdict.json`, `problems.md` (workflow files, written by build/verify steps)
  - `.phpunit.result.cache` may update; tolerate.
  - No edits to `tests/Unit/Services/TelegramBotRegistrarServiceTest.php`, `app/Services/Telegram/*`, `app/Services/AgentDeployerService.php`, `app/Services/KiloClawClientService.php`, `composer.json`, `phpunit.xml.dist`, or `.gitignore`.
- AC27: No em dashes (the U+2014 character) in any authored file from this task. Verifier greps for the literal em dash in all touched files and in this spec.

## Constraints
- Strict TDD discipline. Tests are written and observed failing BEFORE any production code change beyond the existing scaffold stub. The phpunit-red.txt artifact must precede any edits to `app/Services/OnChainOSPaymentService.php` body or to the new `OnChainOS/` subdirectory files.
- No live network calls during the build, the test, or the verify step. The fake injected via the constructor is the only path to `createCharge` data.
- No Laravel framework code, no Illuminate facades, no service container resolution. The service must be constructible with a single explicit argument from a plain PHP test.
- No Guzzle, no Symfony HttpClient, no third-party HTTP package added to `composer.json`. The interface is the seam; a real implementation will land in a separate task (a later v0.2 swap from stub to real OnChainOS client).
- No deletes outside scope. If any file removal becomes necessary, use `trash` (not `rm`).
- No em dashes. Use colons, periods, or commas in prose.
- No PHPUnit mock objects. Use a fake (inline anonymous class or small concrete class) implementing `OnChainOSClient`.
- All new PHP files declare `strict_types=1` on line 2.
- No edits to `composer.json`, `phpunit.xml.dist`, `.gitignore`, or any other top-level config file.
- No edits to `tests/Unit/Services/TelegramBotRegistrarServiceTest.php`, `app/Services/Telegram/*`, `app/Services/AgentDeployerService.php`, or `app/Services/KiloClawClientService.php`.
- No new top-level directories outside `app/Services/OnChainOS/` and reuse of the existing `tests/Unit/Services/`.
- No regex parsing of response bodies. Rely on array key access.
- No new composer dependencies.
- The build runs locally; no Coolify deploy, no DNS changes, no remote infra writes.
- Idempotency key format is fixed: `'spawn-' . sha1($agentName . ':' . $amountUsd . ':' . gmdate('Y-m-d'))`. This exact expression must appear in `OnChainOSPaymentService::createCharge`.

## Non-goals
- No real OnChainOS API call in this task. The concrete `OnChainOSClient` implementation that actually hits the OnChainOS HTTP API is a separate downstream task.
- No webhook receiver. `POST /api/webhooks/onchainos` is a separate task.
- No Laravel facade, no Eloquent model, no migration, no controller, no route, no middleware.
- No `.env` file, no environment variable wiring, no config provider.
- No DB schema, no queue, no job, no mailer.
- No Mandate integration.
- No frontend wiring, no Inertia page, no React change.
- No KiloClaw or Telegram integration in this task.
- No refactor of the test harness, `phpunit.xml.dist`, or `composer.json`.
- No changes to the prior telegram TDD task files.

## Verification plan

The verifier runs each of the following and pins each command to the AC it satisfies.

### Build presence and shape
- `test -f app/Services/OnChainOS/OnChainOSClient.php` (AC11)
- `test -f app/Services/OnChainOS/OnChainOSException.php` (AC12)
- `test -f app/Services/OnChainOSPaymentService.php` (AC13)
- `test -f tests/Unit/Services/OnChainOSPaymentServiceTest.php` (AC19, AC26)
- `grep -n "declare(strict_types=1);" app/Services/OnChainOS/OnChainOSClient.php` (AC11, AC20)
- `grep -n "declare(strict_types=1);" app/Services/OnChainOS/OnChainOSException.php` (AC12, AC20)
- `grep -n "declare(strict_types=1);" app/Services/OnChainOSPaymentService.php` (AC13, AC20)
- `grep -n "declare(strict_types=1);" tests/Unit/Services/OnChainOSPaymentServiceTest.php` (AC20)
- `grep -n "namespace App\\\\Services\\\\OnChainOS" app/Services/OnChainOS/OnChainOSClient.php` (AC11)
- `grep -n "interface OnChainOSClient" app/Services/OnChainOS/OnChainOSClient.php` (AC11)
- `grep -nE "public function createCharge\\(int \\\$amountUsd, string \\\$agentName, string \\\$idempotencyKey\\): array" app/Services/OnChainOS/OnChainOSClient.php` (AC11)
- `grep -nE "final class OnChainOSException extends .*RuntimeException" app/Services/OnChainOS/OnChainOSException.php` (AC12)
- `grep -n "final class OnChainOSPaymentService" app/Services/OnChainOSPaymentService.php` (AC13)
- `grep -nE "private readonly .*OnChainOSClient \\\$client" app/Services/OnChainOSPaymentService.php` (AC13)
- `grep -nE "public function createCharge\\(int \\\$amountUsd, string \\\$agentName\\): array" app/Services/OnChainOSPaymentService.php` (AC13)
- `grep -cE "^\\s*public function " app/Services/OnChainOSPaymentService.php` returns at most 2 (constructor plus `createCharge`) (AC13)

### Lint
- `php -l app/Services/OnChainOS/OnChainOSClient.php` (AC20)
- `php -l app/Services/OnChainOS/OnChainOSException.php` (AC20)
- `php -l app/Services/OnChainOSPaymentService.php` (AC20)
- `php -l tests/Unit/Services/OnChainOSPaymentServiceTest.php` (AC20)

### Autoload probe
- `php -r 'require "vendor/autoload.php"; foreach (["App\\\\Services\\\\AgentDeployerService","App\\\\Services\\\\KiloClawClientService","App\\\\Services\\\\OnChainOSPaymentService","App\\\\Services\\\\TelegramBotRegistrarService","App\\\\Services\\\\Telegram\\\\TelegramHttpClient","App\\\\Services\\\\Telegram\\\\TelegramTransportException","App\\\\Services\\\\OnChainOS\\\\OnChainOSClient","App\\\\Services\\\\OnChainOS\\\\OnChainOSException"] as $c) { if (!class_exists($c) && !interface_exists($c)) { fwrite(STDERR, "MISSING: $c\n"); exit(1); } } echo "OK\n";'` (AC21)

### Test execution and artifacts
- `test -f .agent/tasks/tdd-onchainos-create-charge/artifacts/phpunit-red.txt` (AC23)
- `test -f .agent/tasks/tdd-onchainos-create-charge/artifacts/phpunit-green.txt` (AC24)
- `grep -E "FAIL|Errors|Failures" .agent/tasks/tdd-onchainos-create-charge/artifacts/phpunit-red.txt` (AC23)
- `grep -E "OK|Tests: [0-9]+, Assertions: [0-9]+" .agent/tasks/tdd-onchainos-create-charge/artifacts/phpunit-green.txt` (AC24)
- `composer test` exits 0 (AC9, AC10)
- Parse final `composer test` output for `Tests: N` where `N >= 14` and `Assertions: M` where `M >= 14` (AC10, AC24)

### Behavioral grep checks
- `grep -nE "createMock|getMockBuilder|MockObject" tests/Unit/Services/OnChainOSPaymentServiceTest.php` returns no matches (AC19)
- `grep -nE "implements .*OnChainOSClient|new class.*OnChainOSClient" tests/Unit/Services/OnChainOSPaymentServiceTest.php` returns at least one match (AC19)
- `grep -nE "public int \\\$callCount" tests/Unit/Services/OnChainOSPaymentServiceTest.php` returns at least one match (AC19)
- `grep -nE "function test_.*empty.*(name|agent)" tests/Unit/Services/OnChainOSPaymentServiceTest.php` returns at least one match (AC1)
- `grep -nE "function test_.*whitespace" tests/Unit/Services/OnChainOSPaymentServiceTest.php` returns at least one match (AC2)
- `grep -nE "function test_.*zero.*amount|function test_.*amount.*zero" tests/Unit/Services/OnChainOSPaymentServiceTest.php` returns at least one match (AC3)
- `grep -nE "function test_.*negative" tests/Unit/Services/OnChainOSPaymentServiceTest.php` returns at least one match (AC4)
- `grep -nE "function test_.*happy|function test_.*canonical|function test_.*returns_canonical" tests/Unit/Services/OnChainOSPaymentServiceTest.php` returns at least one match (AC5)
- `grep -nE "function test_.*exception|function test_.*transport|function test_.*client.*fail" tests/Unit/Services/OnChainOSPaymentServiceTest.php` returns at least one match (AC6)
- `grep -nE "function test_.*malformed|function test_.*missing" tests/Unit/Services/OnChainOSPaymentServiceTest.php` returns at least one match (AC7)
- `grep -nE "function test_.*idempotenc" tests/Unit/Services/OnChainOSPaymentServiceTest.php` returns at least one match (AC8)
- Verifier extracts the body of `createCharge` from `app/Services/OnChainOSPaymentService.php` via awk between the method signature and its closing brace. Inside that body:
  - `grep -nE "new "` returns no matches (AC18)
  - `grep -nE "::"` returns no matches (AC18)
  - `grep -nE " static "` returns no matches (AC18)
- `grep -nE "sha1\\(\\\$agentName \\. ':' \\. \\\$amountUsd \\. ':' \\. gmdate\\('Y-m-d'\\)\\)" app/Services/OnChainOSPaymentService.php` returns at least one match (AC17)
- `grep -nE "'spawn-'" app/Services/OnChainOSPaymentService.php` returns at least one match (AC17)
- `grep -nE "file_get_contents|curl_init|curl_exec|fsockopen|fopen\\('http" app/Services/OnChainOSPaymentService.php app/Services/OnChainOS/*.php tests/Unit/Services/OnChainOSPaymentServiceTest.php` returns no matches (AC22)
- `grep -nE "https?://[^ )]" app/Services/OnChainOSPaymentService.php app/Services/OnChainOS/*.php tests/Unit/Services/OnChainOSPaymentServiceTest.php` only matches lines that begin with `//`, `*`, or `#` (AC22)

### Em dash check
- `grep -nP "\xE2\x80\x94" app/Services/OnChainOS/OnChainOSClient.php app/Services/OnChainOS/OnChainOSException.php app/Services/OnChainOSPaymentService.php tests/Unit/Services/OnChainOSPaymentServiceTest.php .agent/tasks/tdd-onchainos-create-charge/spec.md` returns no matches (AC27)

### Regression check
- For each prior task in `bootstrap-proof-loop`, `scaffold-service-stubs`, `test-harness`, `landing-mockup-steve-ive`, `tdd-telegram-validate-token`, `wizard-mockup-steve-ive`: read `.agent/tasks/<task>/verdict.json` and confirm `verdict_overall_status` is `PASS` (AC25)
- `python .claude/skills/repo-task-proof-loop/scripts/task_loop.py status --task-id <task>` for each of the six prior tasks (AC25)

### Scope check
- `git status --porcelain` (or equivalent file enumeration) shows only the files listed in AC26. No new top-level directories. No edits to `composer.json`, `phpunit.xml.dist`, `.gitignore`, `tests/Unit/Services/TelegramBotRegistrarServiceTest.php`, `app/Services/Telegram/*`, `app/Services/AgentDeployerService.php`, or `app/Services/KiloClawClientService.php`. (AC26)
- `test -f tests/Unit/Services/TelegramBotRegistrarServiceTest.php` AND its content hash matches the prior task's committed version (AC26)
