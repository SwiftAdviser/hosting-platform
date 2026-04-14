# Task Spec: tdd-agent-deployer

## Metadata
- Task ID: tdd-agent-deployer
- Created: 2026-04-14T09:43:15+00:00
- Frozen: 2026-04-13
- Repo root: /Users/krutovoy/Projects/hosting-platform
- Working directory at init: /Users/krutovoy/Projects/hosting-platform
- Wave: 5 (fifth TDD cycle)
- Production file: app/Services/AgentDeployerService.php (rewrite)
- Test file: tests/Unit/Services/AgentDeployerServiceTest.php (new)

## Guidance sources
- AGENTS.md
- CLAUDE.md
- docs/agent_spawn_prd.md (7-step walkthrough)
- docs/sprint_v0.1.md Day 1 Tasks 4-7 and Day 2 Tasks 1-4
- .agent/tasks/tdd-onchainos-create-charge/spec.md (AC structure reference)
- .agent/tasks/tdd-telegram-set-webhook/spec.md (AC structure reference)
- app/Services/OnChainOSPaymentService.php (createCharge(int, string): array)
- app/Services/KiloClawClientService.php (install(array): array)
- app/Services/TelegramBotRegistrarService.php (validateToken(string): bool, registerWebhook(string, string): array)
- tests/Unit/Services/OnChainOSPaymentServiceTest.php (FakeOnChainOSClient pattern)
- tests/Unit/Services/KiloClawClientServiceTest.php (FakeKiloClawHttpClient pattern)
- tests/Unit/Services/TelegramBotRegistrarServiceTest.php (FakeTelegramHttpClient pattern)

## Original task statement
Fifth TDD cycle. Implement `App\Services\AgentDeployerService::deploy(array $request): array` as the orchestrator that ties `OnChainOSPaymentService`, `KiloClawClientService`, and `TelegramBotRegistrarService` together. Accepts `$request` with keys: `agent_name`, `personality`, `telegram_bot_token`, `allowlist` (optional), `amount_usd`. Orchestration short-circuits on each failure and returns a canonical shape on success. The deployer is injected with the three services via constructor promotion. Each service still uses its own client fake in tests. The deployer test uses three in-file fake clients that conform to each dependent service's public API. All prior tests must stay green. Final `composer test` must report at least 37 tests (prior 30 + at least 7 new deployer tests). Strict types. Red then green discipline. No Laravel. No new composer deps. Do not edit design/, docs/, CLAUDE.md, AGENTS.md. May add app/Services/AgentDeployerService.php (rewrite) and tests/Unit/Services/AgentDeployerServiceTest.php (new).

## Acceptance criteria

### File layout
- AC1: `app/Services/AgentDeployerService.php` is rewritten and starts with `<?php` followed by `declare(strict_types=1);`.
- AC2: The file declares `namespace App\Services;` and the class is `final class AgentDeployerService`.
- AC3: The class constructor uses PHP 8 constructor promotion with exactly three `private readonly` properties, in order: `OnChainOSPaymentService $payment`, `KiloClawClientService $kiloclaw`, `TelegramBotRegistrarService $telegram`. The three dependency types are imported via `use` statements at the top of the file (no fully qualified names in the constructor signature).

### Public API
- AC4: The class exposes a single public method `public function deploy(array $request): array`.
- AC5: `deploy()` returns an associative array that always contains the keys `status`, `stage`, `agent_name`, `error`, `kiloclaw_id`, `session_id` (in every branch, success or failure).

### Request validation (step 0)
- AC6: If `$request` is missing any of the required keys `agent_name`, `personality`, `telegram_bot_token`, `amount_usd`, or if `agent_name`, `personality`, or `telegram_bot_token` are not non-empty strings after `trim()`, or `amount_usd` is not an int `> 0`, the method returns `['status' => 'invalid_request', 'stage' => 'validate', 'agent_name' => $request['agent_name'] ?? null, 'error' => 'missing or invalid: <listed keys>', 'kiloclaw_id' => null, 'session_id' => null]` WITHOUT calling any downstream service. `<listed keys>` is the comma-separated list of offending keys in the canonical order `agent_name, personality, telegram_bot_token, amount_usd`.
- AC7: The optional `allowlist` key (string, possibly empty, comma-separated) is accepted but not parsed. Missing `allowlist` is not a validation error.

### Orchestration order
- AC8: Step 1 is Telegram token validation via `$this->telegram->validateToken($request['telegram_bot_token'])`. It runs before payment and before install. Rationale: cheapest check, fails without money movement.
- AC9: If `validateToken()` returns `false`, `deploy()` returns `['status' => 'telegram_invalid', 'stage' => 'telegram_validate', 'agent_name' => $request['agent_name'], 'error' => 'telegram token invalid', 'kiloclaw_id' => null, 'session_id' => null]` and does NOT call `createCharge()` or `install()`.
- AC10: Step 2 is payment via `$this->payment->createCharge($request['amount_usd'], $request['agent_name'])`. It runs only after Telegram validation passes.
- AC11: If the returned `$charge['status']` is NOT exactly `'pending'`, `deploy()` returns `['status' => 'payment_failed', 'stage' => 'payment', 'agent_name' => $request['agent_name'], 'error' => $charge['error'] ?? 'payment not pending', 'kiloclaw_id' => null, 'session_id' => $charge['session_id'] ?? null]` and does NOT call `install()`.
- AC12: Step 3 is KiloClaw install via `$this->kiloclaw->install($manifest)`. The manifest is built inline (no helper method) as `$manifest = ['id' => 'spawn.' . strtolower($request['agent_name']), 'name' => $request['agent_name'], 'version' => '0.1.0', 'skills' => []]`.
- AC13: If the returned `$install['status']` is NOT one of `'ready'` or `'booting'`, `deploy()` returns `['status' => 'install_failed', 'stage' => 'install', 'agent_name' => $request['agent_name'], 'error' => $install['error'] ?? 'install failed', 'kiloclaw_id' => $install['kiloclaw_id'] ?? null, 'session_id' => $charge['session_id']]`.
- AC14: Step 4 (success) returns `['status' => 'deployed', 'stage' => 'complete', 'agent_name' => $request['agent_name'], 'error' => null, 'kiloclaw_id' => $install['kiloclaw_id'], 'session_id' => $charge['session_id']]`.

### Method-body hygiene
- AC15: Inside the body of `deploy()`, there is no occurrence of ` new ` (space-new-space). The manifest is an array literal, not an instantiation.
- AC16: Inside the body of `deploy()`, there is no occurrence of `::` (no static calls, no class constants).
- AC17: Inside the body of `deploy()`, there is no occurrence of ` static ` (no late static binding).
- AC18: `deploy()` does not contain a `try` or `catch` keyword; downstream services swallow their own exceptions and return arrays.

### Test file
- AC19: `tests/Unit/Services/AgentDeployerServiceTest.php` exists with `declare(strict_types=1);`, `namespace Tests\Unit\Services;`, and extends `PHPUnit\Framework\TestCase`.
- AC20: The test file declares three in-file fake classes (self-contained duplication, by deliberate choice documented in Constraints): `FakeOnChainOSClient` implementing `App\Services\OnChainOS\OnChainOSClient`, `FakeKiloClawHttpClient` implementing `App\Services\KiloClaw\KiloClawHttpClient`, `FakeTelegramHttpClient` implementing `App\Services\Telegram\TelegramHttpClient`. Each fake exposes a public `nextResponse` (or equivalently named per existing test files) that the test body mutates per case.
- AC21: Each test constructs the three real services (`OnChainOSPaymentService`, `KiloClawClientService`, `TelegramBotRegistrarService`) by passing one fake client into each, then constructs `AgentDeployerService` by passing the three real services into its constructor. No PHPUnit mocks (`createMock`, `getMockBuilder`, `MockObject`) appear anywhere in the file.
- AC22: The test file declares at least these seven test methods (names grepped verbatim): `test_missing_required_field_returns_invalid_request`, `test_empty_agent_name_returns_invalid_request`, `test_zero_amount_returns_invalid_request`, `test_telegram_token_invalid_short_circuits_at_telegram_validate`, `test_payment_not_pending_short_circuits_at_payment`, `test_install_not_ready_short_circuits_at_install`, `test_happy_path_deployed_returns_canonical_shape`.
- AC23: `test_happy_path_deployed_returns_canonical_shape` uses the pinned canonical fixture below and asserts the pinned canonical return shape. The request array `['agent_name' => 'atlas', 'personality' => 'A laconic agent that ships code.', 'telegram_bot_token' => '123:abc', 'amount_usd' => 10, 'allowlist' => '']`, Telegram fake preload `['ok' => true, 'result' => ['id' => 1]]`, OnChainOS fake preload `['session_id' => 'sess_abc', 'status' => 'pending', 'expires_at' => '2026-04-14T10:00:00Z']`, KiloClaw fake preload `['kiloclaw_id' => 'kc_abc', 'status' => 'ready']`, expected return `['status' => 'deployed', 'stage' => 'complete', 'agent_name' => 'atlas', 'error' => null, 'kiloclaw_id' => 'kc_abc', 'session_id' => 'sess_abc']`.

### Red then green discipline
- AC24: `phpunit-red.txt` exists in `.agent/tasks/tdd-agent-deployer/` and records a non-zero exit of `composer test` captured BEFORE the service rewrite (only the test file written; production file still the one-line stub). `phpunit-green.txt` exists in the same directory and records a zero exit of `composer test` captured AFTER the service rewrite.

### Test count and regressions
- AC25: After the service rewrite, `composer test` reports at least 37 tests and at least 40 assertions, and exits 0. All prior ten tasks remain green: `bootstrap-proof-loop`, `scaffold-service-stubs`, `test-harness`, `landing-mockup-steve-ive`, `tdd-telegram-validate-token`, `wizard-mockup-steve-ive`, `tdd-onchainos-create-charge`, `agents-list-mockup-steve-ive`, `tdd-kiloclaw-install`, `tdd-telegram-set-webhook`.

### Collateral
- AC26: Only two code files are touched in this task: `app/Services/AgentDeployerService.php` (rewritten) and `tests/Unit/Services/AgentDeployerServiceTest.php` (new). `phpunit.xml.dist`, composer files, `app/Services/OnChainOSPaymentService.php`, `app/Services/KiloClawClientService.php`, `app/Services/TelegramBotRegistrarService.php`, and all existing test files are unchanged. `.phpunit.result.cache` may appear or change.

## Constraints
- PHP 8.2, strict types.
- No Laravel. No Guzzle. No new composer dependencies.
- No em dashes anywhere in the new files.
- No TODO comments in the spec or the new files.
- Constructor promotion with three `private readonly` dependencies.
- The deploy method body must be free of ` new `, `::`, and ` static `.
- No PHPUnit mocks (`createMock`, `getMockBuilder`, `MockObject`). Only hand-written in-file fakes.
- Self-contained duplication of fakes: `FakeOnChainOSClient`, `FakeKiloClawHttpClient`, `FakeTelegramHttpClient` are re-declared inline in `AgentDeployerServiceTest.php` rather than extracted to `tests/Support/`. Deliberate short-term choice: keeps wave 5 blast radius small; extraction can happen in a later refactor when multiple test files share them.
- `phpunit.xml.dist` is unchanged.
- Red then green discipline is mandatory: write the failing test first, capture `phpunit-red.txt`, only then rewrite the service and capture `phpunit-green.txt`.
- Prior ten tasks must stay green.
- Walkthrough order (Telegram validate first, then payment, then install, then success) is locked to match the PRD 7-step flow and the cheapest-check-first principle.

## Non-goals
- Not calling `registerWebhook()` in this task. Webhook registration belongs to a later task that runs after install and knows the agent URL.
- Not parsing `allowlist`. Stored raw.
- Not integrating a queue, HTTP controller, or Inertia page.
- Not touching design, docs, AGENTS.md, or CLAUDE.md.
- Not extracting fakes to a shared `tests/Support/` namespace.
- Not defining narrow port interfaces (`AgentPaymentPort`, etc.). We inject the concrete services.
- Not adding exception handling in the deployer; downstream services already swallow their own exceptions and return arrays.

## Verification plan

### Build
- `php -l app/Services/AgentDeployerService.php` exits 0.
- `php -l tests/Unit/Services/AgentDeployerServiceTest.php` exits 0.

### Unit tests
- Red phase: `cat .agent/tasks/tdd-agent-deployer/phpunit-red.txt` shows a non-zero exit and FAIL/ERROR lines from the seven new tests, while the production file still contains the one-line stub.
- Green phase: `composer test` exits 0. Output reports at least 37 tests and at least 40 assertions. `cat .agent/tasks/tdd-agent-deployer/phpunit-green.txt` matches.

### Per-AC greps (deterministic)
- AC1: `grep -n "declare(strict_types=1);" app/Services/AgentDeployerService.php`.
- AC2: `grep -n "namespace App\\\\Services;" app/Services/AgentDeployerService.php` and `grep -n "final class AgentDeployerService" app/Services/AgentDeployerService.php`.
- AC3: `grep -n "use App\\\\Services\\\\OnChainOSPaymentService;" app/Services/AgentDeployerService.php`, same for `KiloClawClientService` and `TelegramBotRegistrarService`; `grep -n "private readonly OnChainOSPaymentService \\$payment" app/Services/AgentDeployerService.php`, same for the other two.
- AC4: `grep -n "public function deploy(array \\$request): array" app/Services/AgentDeployerService.php`.
- AC5, AC6, AC9, AC11, AC13, AC14: `grep -n "'status' => 'invalid_request'"`, `'stage' => 'validate'`, `'status' => 'telegram_invalid'`, `'stage' => 'telegram_validate'`, `'status' => 'payment_failed'`, `'stage' => 'payment'`, `'status' => 'install_failed'`, `'stage' => 'install'`, `'status' => 'deployed'`, `'stage' => 'complete'` all present in the service file.
- AC8, AC10, AC12: `grep -n "\\$this->telegram->validateToken("`, `grep -n "\\$this->payment->createCharge("`, `grep -n "\\$this->kiloclaw->install(" app/Services/AgentDeployerService.php`.
- AC12 manifest literal: `grep -n "'spawn.' . strtolower(\\$request\\['agent_name'\\])" app/Services/AgentDeployerService.php` returns exactly one hit.
- AC15/AC16/AC17 body bans: extract the `deploy(` body with `awk '/public function deploy/,/^    }$/'` and assert zero hits for ` new `, `::`, ` static `.
- AC18: `grep -cE "(^|\\s)(try|catch)(\\s|\\()" app/Services/AgentDeployerService.php` returns 0.
- AC19: `grep -n "declare(strict_types=1);" tests/Unit/Services/AgentDeployerServiceTest.php` and `grep -n "namespace Tests\\\\Unit\\\\Services;"` and `grep -n "extends TestCase"`.
- AC20: `grep -n "class FakeOnChainOSClient"`, `class FakeKiloClawHttpClient`, `class FakeTelegramHttpClient` each present once in the test file; `grep -n "implements OnChainOSClient"`, same for `KiloClawHttpClient` and `TelegramHttpClient`.
- AC21: `grep -c "createMock\\|getMockBuilder\\|MockObject" tests/Unit/Services/AgentDeployerServiceTest.php` returns 0; `grep -n "new AgentDeployerService("` present.
- AC22: each of the seven test method names is grepped verbatim.
- AC23 happy-path return: `grep -n "'status' => 'deployed'" tests/Unit/Services/AgentDeployerServiceTest.php` AND `grep -n "'kiloclaw_id' => 'kc_abc'"` AND `grep -n "'session_id' => 'sess_abc'"` AND `grep -n "'agent_name' => 'atlas'"` all present.
- AC24: `test -s .agent/tasks/tdd-agent-deployer/phpunit-red.txt` and `test -s .agent/tasks/tdd-agent-deployer/phpunit-green.txt`.
- AC25: parse the final `composer test` summary: `Tests: >= 37`, `Assertions: >= 40`, exit 0.
- AC26: `git status --porcelain` shows only `app/Services/AgentDeployerService.php`, `tests/Unit/Services/AgentDeployerServiceTest.php`, `.agent/tasks/tdd-agent-deployer/` artifacts, and optionally `.phpunit.result.cache`. No other paths.

### Lint
- `./vendor/bin/pint --test app/Services/AgentDeployerService.php tests/Unit/Services/AgentDeployerServiceTest.php` exits 0 if Pint is wired up; otherwise skipped.

### Manual checks
- Eyeball the four return-shape branches in the service body and confirm the six canonical keys are present in every path.
- Confirm no em dashes in either new file.
