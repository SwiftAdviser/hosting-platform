# Task Spec: tdd-telegram-validate-token

## Metadata
- Task ID: tdd-telegram-validate-token
- Created: 2026-04-14T08:54:48+00:00
- Repo root: /Users/krutovoy/Projects/hosting-platform
- Working directory at init: /Users/krutovoy/Projects/hosting-platform

## Guidance sources
- /Users/krutovoy/Projects/hosting-platform/CLAUDE.md (TDD mandatory principle, crypto hidden, Laravel 12 stack)
- /Users/krutovoy/Projects/hosting-platform/docs/integrations.md (Telegram Bot API section: GET https://api.telegram.org/bot<TOKEN>/getMe, ok flag semantics)
- /Users/krutovoy/Projects/hosting-platform/app/Services/TelegramBotRegistrarService.php (current scaffold stub)
- /Users/krutovoy/Projects/hosting-platform/composer.json (PSR-4 App\\ -> app/, Tests\\ -> tests/, phpunit ^11)
- /Users/krutovoy/Projects/hosting-platform/tests/Unit/SmokeTest.php (existing baseline test, must keep passing)
- /Users/krutovoy/Projects/hosting-platform/.agent/tasks/tdd-telegram-validate-token/spec.md (placeholder)
- /Users/krutovoy/Projects/hosting-platform/.agent/tasks/test-harness/ (autoload class_exists probe pattern, AC20)
- /Users/krutovoy/Projects/hosting-platform/.agent/tasks/scaffold-service-stubs/ (origin of the four service stubs)

## Original task statement
First real TDD cycle on the hosting-platform test harness. Implement TelegramBotRegistrarService::validateToken(string $token): bool using strict red->green->refactor discipline. The method calls GET https://api.telegram.org/bot<TOKEN>/getMe and returns true when the response ok flag is true, false otherwise. Use dependency injection for the HTTP client so tests are deterministic and never hit the network. Introduce a tiny TelegramHttpClient interface with one method getMe(string $token): array. Use a PHPUnit double as the test implementation. The service class currently contains only a doc-comment stub; this task replaces it. All work must go through PHPUnit tests; no live HTTP calls during the build. Strict TDD: write failing tests first, observe red via vendor/bin/phpunit, implement to green, run all tests green at the end. Capture both red and green phpunit outputs as raw artifacts.

## Acceptance criteria

### Strict TDD red step (tests first, no production code yet)
- AC1: A failing PHPUnit test for the empty-token case is written first. `validateToken('')` is asserted to return false. The very first phpunit run captured in `phpunit-red.txt` exits nonzero with a clear failure message, and at that point no production code beyond the existing scaffold stub exists. This is the strict red step.
- AC2: A failing PHPUnit test for a valid-token case is included in the same red run. The fake `TelegramHttpClient` returns `['ok' => true, 'result' => ['id' => 1]]` for token `'123:abc'`. The test asserts `validateToken('123:abc')` returns true. The red artifact shows this test failing.
- AC3: A failing PHPUnit test for an invalid-token case is included in the same red run. The fake `TelegramHttpClient` returns `['ok' => false, 'error_code' => 401, 'description' => 'Unauthorized']`. The test asserts `validateToken('bad:token')` returns false. The red artifact shows this test failing.
- AC4: A failing PHPUnit test for a transport exception case is included in the same red run. The fake `TelegramHttpClient` throws `App\Services\Telegram\TelegramTransportException` from `getMe`. The test asserts `validateToken('123:abc')` returns false and that the exception does not propagate. The red artifact shows this test failing.
- AC5: A failing PHPUnit test for a whitespace-only token case is included in the same red run. The fake `TelegramHttpClient` records every call to `getMe`. The test asserts `validateToken('   ')` returns false AND that `getMe` was never called on the fake. The red artifact shows this test failing.

### Green step (production code lands, all tests pass)
- AC6: After production code lands, every test from AC1..AC5 turns green. Captured in `phpunit-green.txt`. Each of the five test methods listed by name in the green artifact.
- AC7: `composer test` exits 0 from the repo root. Reports at least 6 tests total (1 SmokeTest + 5 telegram tests) and at least 6 assertions.

### File and structural acceptance
- AC8: `app/Services/Telegram/TelegramHttpClient.php` exists with:
  - `<?php` opener
  - `declare(strict_types=1);`
  - `namespace App\Services\Telegram;`
  - `interface TelegramHttpClient`
  - exactly one method: `public function getMe(string $token): array;`
- AC9: `app/Services/Telegram/TelegramTransportException.php` exists with:
  - `<?php` opener
  - `declare(strict_types=1);`
  - `namespace App\Services\Telegram;`
  - `final class TelegramTransportException extends \RuntimeException`
- AC10: `app/Services/TelegramBotRegistrarService.php` has:
  - `<?php` opener
  - `declare(strict_types=1);`
  - `namespace App\Services;`
  - `final class TelegramBotRegistrarService`
  - constructor: `public function __construct(private readonly \App\Services\Telegram\TelegramHttpClient $client)` (or equivalent FQN via `use` statement)
  - public method `validateToken(string $token): bool`
  - The original single-line scaffold comment may be removed or repurposed as a short class-level comment about the role.

### Behavioral acceptance
- AC11: `validateToken` short-circuits on empty or whitespace-only input and does NOT call `$this->client->getMe`. Proven by the AC5 test asserting the fake's call counter stays at zero.
- AC12: `validateToken` catches `App\Services\Telegram\TelegramTransportException` and returns false rather than letting it propagate. Proven by the AC4 test.
- AC13: No global state in the service. The body of `validateToken` does not contain the substrings `new `, `::`, or ` static `. Verifier greps the method body. Single allowed exception: `::class` is not used and `parent::` is not used.
- AC14: The test file uses an inline anonymous class or a small named fake class implementing `TelegramHttpClient`, not a PHPUnit `createMock` or `getMockBuilder`. Stylistic lock: fake, not mock. Verifier greps the test file for `createMock` and `getMockBuilder` and asserts they are absent.

### Hygiene acceptance
- AC15: `php -l` exits 0 on every new and modified PHP file:
  - `app/Services/Telegram/TelegramHttpClient.php`
  - `app/Services/Telegram/TelegramTransportException.php`
  - `app/Services/TelegramBotRegistrarService.php`
  - `tests/Unit/Services/TelegramBotRegistrarServiceTest.php`
- AC16: All four scaffold service stubs still autoload via the class_exists probe pattern from the test-harness task: `App\Services\AgentDeployerService`, `App\Services\KiloClawClientService`, `App\Services\OnChainOSPaymentService`, `App\Services\TelegramBotRegistrarService` all return true from `class_exists()` after `vendor/autoload.php` is required.
- AC17: No live network call during `composer test`. Verifier greps the test file and the three production PHP files for `file_get_contents`, `curl_init`, `curl_exec`, `fsockopen`, `fopen('http`, and bare `http://` / `https://` references in non-comment positions. The only allowed `https://api.telegram.org` reference is inside a PHP comment or docblock citing the upstream URL.

### Artifact acceptance
- AC18: `.agent/tasks/tdd-telegram-validate-token/artifacts/phpunit-red.txt` exists. Nonzero exit. Shows failing tests for AC1..AC5. Produced BEFORE any production code beyond the scaffold stub landed. The build log records this ordering explicitly.
- AC19: `.agent/tasks/tdd-telegram-validate-token/artifacts/phpunit-green.txt` exists. Exit 0. Shows all tests passing AFTER production code landed.

### Regression and scope acceptance
- AC20: No regressions on prior tasks. The verifier reruns `task_loop.py status` (or reads the existing `verdict.json` files) for `bootstrap-proof-loop`, `scaffold-service-stubs`, `test-harness`, and `landing-mockup-steve-ive`, and confirms each still reports PASS.
- AC21: No collateral top-level additions. Repo-root file changes confined to:
  - `app/Services/Telegram/TelegramHttpClient.php` (new)
  - `app/Services/Telegram/TelegramTransportException.php` (new)
  - `app/Services/TelegramBotRegistrarService.php` (modified)
  - `tests/Unit/Services/TelegramBotRegistrarServiceTest.php` (new)
  - `.agent/tasks/tdd-telegram-validate-token/artifacts/phpunit-red.txt` (new)
  - `.agent/tasks/tdd-telegram-validate-token/artifacts/phpunit-green.txt` (new)
  - `.agent/tasks/tdd-telegram-validate-token/spec.md` (this file, finalized)
  - `.agent/tasks/tdd-telegram-validate-token/evidence.json`, `evidence.md`, `verdict.json`, `problems.md` (workflow files, written by build/verify steps)
  - `.phpunit.result.cache` may update; tolerate.
- AC22: No em dashes (the U+2014 character) in any authored file from this task. Verifier greps for the literal em dash character in all touched files.
- AC23: The test class lives at `tests/Unit/Services/TelegramBotRegistrarServiceTest.php` with namespace `Tests\Unit\Services` and class `TelegramBotRegistrarServiceTest`. The `Tests\\` PSR-4 prefix maps to `tests/`, so the deeper namespace autoloads without any change to `phpunit.xml.dist` or `composer.json`. Verifier confirms `phpunit.xml.dist` is unchanged from prior task.

## Constraints
- Strict TDD discipline. Tests are written and observed failing BEFORE any production code change beyond the existing scaffold stub. The phpunit-red.txt artifact must precede any edits to `app/Services/TelegramBotRegistrarService.php` body or to the new `Telegram/` subdirectory files.
- No live network calls during the build, the test, or the verify step. The fake injected via the constructor is the only path to `getMe` data.
- No Laravel framework code, no Illuminate facades, no service container resolution. The service must be constructible with a single explicit argument from a plain PHP test.
- No Guzzle, no Symfony HttpClient, no third-party HTTP package added to `composer.json`. The interface is the seam; the real implementation will land in a separate task.
- No deletes outside scope. If any file removal becomes necessary, use `trash` (not `rm`).
- No em dashes. Use colons, periods, or commas in prose.
- No PHPUnit mock objects. Use a fake (anonymous class or small concrete class) implementing `TelegramHttpClient`.
- All new PHP files declare `strict_types=1`.
- No edits to `composer.json`, `phpunit.xml.dist`, `.gitignore`, or any other top-level config file.
- No edits to other service stubs (`AgentDeployerService`, `KiloClawClientService`, `OnChainOSPaymentService`).
- No new top-level directories outside `app/Services/Telegram/` and `tests/Unit/Services/`.
- The build runs locally; no Coolify deploy, no DNS changes, no remote infra writes.

## Non-goals
- No real Telegram API call in this task. The concrete `TelegramHttpClient` implementation that actually hits `api.telegram.org` is a separate downstream task.
- No webhook registration. `setWebhook` is a separate task.
- No Laravel facade, no Eloquent model, no migration, no controller, no route, no middleware.
- No `.env` file, no environment variable wiring, no config provider.
- No DB schema, no queue, no job.
- No Mandate integration.
- No frontend wiring, no Inertia page, no React change.
- No KiloClaw or OnChainOS integration in this task.
- No refactor of the test harness or `phpunit.xml.dist`.

## Verification plan

The verifier runs each of the following and pins each command to the AC it satisfies.

### Build presence and shape
- `test -f app/Services/Telegram/TelegramHttpClient.php` (AC8)
- `test -f app/Services/Telegram/TelegramTransportException.php` (AC9)
- `test -f app/Services/TelegramBotRegistrarService.php` (AC10)
- `test -f tests/Unit/Services/TelegramBotRegistrarServiceTest.php` (AC23)
- `grep -n "declare(strict_types=1);" app/Services/Telegram/TelegramHttpClient.php` (AC8, AC15)
- `grep -n "declare(strict_types=1);" app/Services/Telegram/TelegramTransportException.php` (AC9, AC15)
- `grep -n "declare(strict_types=1);" app/Services/TelegramBotRegistrarService.php` (AC10, AC15)
- `grep -n "declare(strict_types=1);" tests/Unit/Services/TelegramBotRegistrarServiceTest.php` (AC15)
- `grep -n "namespace App\\\\Services\\\\Telegram" app/Services/Telegram/TelegramHttpClient.php` (AC8)
- `grep -n "interface TelegramHttpClient" app/Services/Telegram/TelegramHttpClient.php` (AC8)
- `grep -n "public function getMe(string \$token): array" app/Services/Telegram/TelegramHttpClient.php` (AC8)
- `grep -n "final class TelegramTransportException extends" app/Services/Telegram/TelegramTransportException.php` (AC9)
- `grep -n "final class TelegramBotRegistrarService" app/Services/TelegramBotRegistrarService.php` (AC10)
- `grep -n "private readonly .*TelegramHttpClient \$client" app/Services/TelegramBotRegistrarService.php` (AC10)
- `grep -n "public function validateToken(string \$token): bool" app/Services/TelegramBotRegistrarService.php` (AC10)

### Lint
- `php -l app/Services/Telegram/TelegramHttpClient.php` (AC15)
- `php -l app/Services/Telegram/TelegramTransportException.php` (AC15)
- `php -l app/Services/TelegramBotRegistrarService.php` (AC15)
- `php -l tests/Unit/Services/TelegramBotRegistrarServiceTest.php` (AC15)

### Autoload probe
- `php -r 'require "vendor/autoload.php"; foreach (["App\\\\Services\\\\AgentDeployerService","App\\\\Services\\\\KiloClawClientService","App\\\\Services\\\\OnChainOSPaymentService","App\\\\Services\\\\TelegramBotRegistrarService","App\\\\Services\\\\Telegram\\\\TelegramHttpClient","App\\\\Services\\\\Telegram\\\\TelegramTransportException"] as $c) { if (!class_exists($c) && !interface_exists($c)) { fwrite(STDERR, "MISSING: $c\n"); exit(1); } } echo "OK\n";'` (AC16)

### Test execution and artifacts
- `test -f .agent/tasks/tdd-telegram-validate-token/artifacts/phpunit-red.txt` (AC18)
- `test -f .agent/tasks/tdd-telegram-validate-token/artifacts/phpunit-green.txt` (AC19)
- `grep -E "FAIL|Errors|Failures" .agent/tasks/tdd-telegram-validate-token/artifacts/phpunit-red.txt` (AC18)
- `grep -E "OK|Tests: [0-9]+, Assertions: [0-9]+" .agent/tasks/tdd-telegram-validate-token/artifacts/phpunit-green.txt` (AC19)
- `composer test` exits 0 (AC6, AC7)
- Parse final `composer test` output for `Tests: N` where N >= 6 and `Assertions: M` where M >= 6 (AC7)

### Behavioral grep checks
- `grep -nE "createMock|getMockBuilder" tests/Unit/Services/TelegramBotRegistrarServiceTest.php` returns no matches (AC14)
- `grep -nE "implements .*TelegramHttpClient|new class.*TelegramHttpClient" tests/Unit/Services/TelegramBotRegistrarServiceTest.php` returns at least one match (AC14)
- Inspect the body of `validateToken` in `app/Services/TelegramBotRegistrarService.php`. The method body must NOT contain `new `, ` static `, or `::` (other than typed exception class reference in `catch`, which uses a class name not `::`). Verifier extracts the function via awk/sed and greps. (AC13)
- `grep -nE "file_get_contents|curl_init|curl_exec|fsockopen|fopen\\('http" app/Services/TelegramBotRegistrarService.php app/Services/Telegram/*.php tests/Unit/Services/TelegramBotRegistrarServiceTest.php` returns no matches (AC17)
- `grep -nE "https?://[^ )]" app/Services/TelegramBotRegistrarService.php app/Services/Telegram/*.php tests/Unit/Services/TelegramBotRegistrarServiceTest.php` only matches inside lines that begin with `//`, `*`, or `#` (i.e. comments) (AC17)

### Em dash check
- `grep -nP "\xE2\x80\x94" app/Services/Telegram/TelegramHttpClient.php app/Services/Telegram/TelegramTransportException.php app/Services/TelegramBotRegistrarService.php tests/Unit/Services/TelegramBotRegistrarServiceTest.php .agent/tasks/tdd-telegram-validate-token/spec.md` returns no matches (AC22)

### Regression check
- For each prior task in `bootstrap-proof-loop`, `scaffold-service-stubs`, `test-harness`, `landing-mockup-steve-ive`: read `.agent/tasks/<task>/verdict.json` and confirm verdict is PASS (AC20)
- `python .claude/skills/repo-task-proof-loop/scripts/task_loop.py status --task-id <task>` for each (AC20)

### Scope check
- `git status --porcelain` (or equivalent file enumeration) shows only the files listed in AC21. No new top-level directories. No edits to `composer.json`, `phpunit.xml.dist`, `.gitignore`. (AC21)

### Test name presence
- `grep -nE "function test_.*empty_token" tests/Unit/Services/TelegramBotRegistrarServiceTest.php` (AC1)
- `grep -nE "function test_.*valid_token|function test_.*returns_true" tests/Unit/Services/TelegramBotRegistrarServiceTest.php` (AC2)
- `grep -nE "function test_.*invalid_token|function test_.*ok_false" tests/Unit/Services/TelegramBotRegistrarServiceTest.php` (AC3)
- `grep -nE "function test_.*transport|function test_.*exception" tests/Unit/Services/TelegramBotRegistrarServiceTest.php` (AC4)
- `grep -nE "function test_.*whitespace|function test_.*blank" tests/Unit/Services/TelegramBotRegistrarServiceTest.php` (AC5)
