# Task Spec: tdd-telegram-set-webhook

## Metadata
- Task ID: tdd-telegram-set-webhook
- Created: 2026-04-14T09:42:59+00:00
- Frozen: 2026-04-13
- Repo root: /Users/krutovoy/Projects/hosting-platform
- Working directory at init: /Users/krutovoy/Projects/hosting-platform
- Prior task: tdd-telegram-validate-token (5 tests, PASS)

## Guidance sources
- /Users/krutovoy/Projects/hosting-platform/CLAUDE.md (TDD mandatory, crypto hidden, no em dashes, no Laravel facades in unit layer)
- /Users/krutovoy/Projects/hosting-platform/docs/integrations.md Telegram Bot API section (setWebhook: POST https://api.telegram.org/bot<TOKEN>/setWebhook with body {url: ...}; success response {ok: true, result: true})
- /Users/krutovoy/Projects/hosting-platform/app/Services/TelegramBotRegistrarService.php (current service, validateToken method must stay byte identical)
- /Users/krutovoy/Projects/hosting-platform/app/Services/Telegram/TelegramHttpClient.php (current interface, 1 method getMe)
- /Users/krutovoy/Projects/hosting-platform/app/Services/Telegram/TelegramTransportException.php (final RuntimeException, unchanged)
- /Users/krutovoy/Projects/hosting-platform/tests/Unit/Services/TelegramBotRegistrarServiceTest.php (current 5-test file with inline anonymous fake class)
- /Users/krutovoy/Projects/hosting-platform/.agent/tasks/tdd-telegram-validate-token/spec.md (prior spec shape reused)
- /Users/krutovoy/Projects/hosting-platform/.agent/tasks/tdd-onchainos-charge-wallet/spec.md (OnChainOS 8 tests, no-regression baseline)
- /Users/krutovoy/Projects/hosting-platform/composer.json (PSR-4 App\\ -> app/, Tests\\ -> tests/, phpunit ^11)

## Original task statement
Fourth TDD cycle. Extend `App\Services\TelegramBotRegistrarService` with a new public method `registerWebhook(string $token, string $webhookUrl): array`. This method is separate from `validateToken` (which remains unchanged and must keep its 5 existing tests green). The method posts to `POST https://api.telegram.org/bot<TOKEN>/setWebhook` with body `{ url }` via an injected `TelegramHttpClient`. Extend the existing `TelegramHttpClient` interface with one new method `setWebhook(string $token, string $webhookUrl): array`. `registerWebhook` returns `['status' => 'registered'|'invalid'|'failed', 'webhook_url' => string|null, 'error' => string|null]`. Validates: non-empty trimmed token, https webhookUrl prefix, host is not empty. If invalid, return `'invalid'` without calling client. If client throws `TelegramTransportException`, return `'failed'`. If client returns `ok=false`, return `'failed'`. Existing fake class pattern (not mocks). All 5 existing TelegramBotRegistrarService tests and the 8 existing OnChainOS tests and the 1 smoke test must remain green (14 total). Adds at least 6 new tests for `registerWebhook`. Final `composer test` must report at least 20 tests. Strict types. No Laravel. No new composer deps. Red then green discipline with artifacts. No edits outside `app/Services/Telegram/`, `app/Services/TelegramBotRegistrarService.php`, `tests/Unit/Services/TelegramBotRegistrarServiceTest.php`, and task artifacts.

## Acceptance criteria

### Strict TDD red step (tests first, no new production behavior)
- AC1: The RED artifact `.agent/tasks/tdd-telegram-set-webhook/artifacts/phpunit-red.txt` exists, exit nonzero, and contains exactly 6 failing test methods with these locked names:
  1. `test_register_webhook_empty_token_returns_invalid_no_client_call`
  2. `test_register_webhook_non_https_url_returns_invalid_no_client_call`
  3. `test_register_webhook_invalid_host_returns_invalid_no_client_call`
  4. `test_register_webhook_happy_path_returns_registered`
  5. `test_register_webhook_client_exception_returns_failed`
  6. `test_register_webhook_telegram_rejected_returns_failed`
  The RED run is observable before any `registerWebhook` body is implemented in `TelegramBotRegistrarService`.
- AC2: In the same RED run, the 5 existing validateToken tests still PASS in parallel (they do not depend on `registerWebhook` or `setWebhook`). The RED artifact shows 11 or more tests executed (5 validateToken + 6 new webhook + any others from the file) with exactly the 6 new failing.
- AC3: The RED phase adds `setWebhook(string $token, string $webhookUrl): array;` to the `TelegramHttpClient` interface AND updates the existing in-file fake in `tests/Unit/Services/TelegramBotRegistrarServiceTest.php` so the fake still implements the interface contract (a no-op `setWebhook` stub returning `[]` is acceptable). Without this dual change, the test file would fatal on load and every test would fail, violating AC2. The RED artifact must show the 5 validateToken tests green, proving the interface extension did not break them.

### Green step (production code lands, all tests pass)
- AC4: After `registerWebhook` production code lands, every one of the 6 new tests from AC1 turns green. Captured in `.agent/tasks/tdd-telegram-set-webhook/artifacts/phpunit-green.txt`. All 6 test method names visible in the green artifact.
- AC5: `composer test` exits 0 from the repo root. Reports at least 20 tests (1 smoke + 8 onchainos + 5 validateToken + 6 new webhook) and at least 20 assertions.

### File and structural acceptance (interface)
- AC6: `app/Services/Telegram/TelegramHttpClient.php` has `<?php`, `declare(strict_types=1);`, `namespace App\Services\Telegram;`, `interface TelegramHttpClient`, and exactly two methods: `public function getMe(string $token): array;` AND `public function setWebhook(string $token, string $webhookUrl): array;`. The `getMe` signature line is byte identical to its pre-task form.
- AC7: `app/Services/Telegram/TelegramTransportException.php` is unchanged from the tdd-telegram-validate-token baseline.

### File and structural acceptance (service)
- AC8: `app/Services/TelegramBotRegistrarService.php` has:
  - `<?php` opener
  - `declare(strict_types=1);`
  - `namespace App\Services;`
  - `final class TelegramBotRegistrarService`
  - constructor unchanged: `public function __construct(private readonly TelegramHttpClient $client)`
  - existing `validateToken(string $token): bool` method present
  - new method `public function registerWebhook(string $token, string $webhookUrl): array`
- AC9: `validateToken` method body is byte identical to its tdd-telegram-validate-token post-green form. Verifier extracts the method (opening `public function validateToken` through its closing `}`) and diffs against a pinned snapshot. Any whitespace or code change inside the method body fails this AC. Allowed: changes to `use` statements at the top of the file, changes outside the `validateToken` body, and reordering of methods relative to each other.

### Behavioral acceptance for registerWebhook
- AC10: `registerWebhook('', 'https://example.com/hook')` returns `['status' => 'invalid', 'webhook_url' => null, 'error' => 'empty token']` and does NOT call `$this->client->setWebhook`. Proven by the test `test_register_webhook_empty_token_returns_invalid_no_client_call` asserting the fake's `$setWebhookCallCount` stays at 0.
- AC11: `registerWebhook('   ', 'https://example.com/hook')` (whitespace token) also returns `['status' => 'invalid', 'webhook_url' => null, 'error' => 'empty token']` without calling the client. This is covered by the empty-token test OR by an inline second assertion within it. Verifier greps the test for both `''` and `'   '` token literals.
- AC12: `registerWebhook('123:abc', 'http://example.com/hook')` returns `['status' => 'invalid', 'webhook_url' => null, 'error' => 'webhook url must start with https://']` and does NOT call the client. Proven by `test_register_webhook_non_https_url_returns_invalid_no_client_call`.
- AC13: `registerWebhook('123:abc', 'https://nohostdot')` returns `['status' => 'invalid', 'webhook_url' => null, 'error' => 'webhook url host invalid']` and does NOT call the client. Proven by `test_register_webhook_invalid_host_returns_invalid_no_client_call`. The invalid-host rule: after stripping the `https://` prefix, the remaining string must be non-empty AND contain at least one `.` character.
- AC14: Happy path: token `'123:abc'`, url `'https://example.com/hook'`, fake response `['ok' => true, 'result' => true]`. `registerWebhook` returns exactly `['status' => 'registered', 'webhook_url' => 'https://example.com/hook', 'error' => null]`. The fake records `$setWebhookCallCount === 1` and `$setWebhookLastArgs === ['123:abc', 'https://example.com/hook']`. Proven by `test_register_webhook_happy_path_returns_registered`.
- AC15: Client throws `TelegramTransportException('network down')`. `registerWebhook('123:abc', 'https://example.com/hook')` returns `['status' => 'failed', 'webhook_url' => 'https://example.com/hook', 'error' => 'network down']`. Exception does not propagate. Proven by `test_register_webhook_client_exception_returns_failed`.
- AC16: Telegram-rejected: fake response `['ok' => false, 'error_code' => 400, 'description' => 'Bad Request: bad webhook']`. `registerWebhook('123:abc', 'https://example.com/hook')` returns `['status' => 'failed', 'webhook_url' => 'https://example.com/hook', 'error' => 'Bad Request: bad webhook']`. Proven by `test_register_webhook_telegram_rejected_returns_failed`.

### Code shape acceptance
- AC17: The body of `registerWebhook` does NOT contain the substrings ` new `, `::`, or ` static `. Catch clauses (which use a class name without `::`) are fine. Verifier extracts the function body and greps. The only permitted `https://` string literal in the production file is the prefix check `'https://'` inside `registerWebhook`, and any docblock comments. No other `https://` or `http://` literals allowed in the production file.
- AC18: The test file at `tests/Unit/Services/TelegramBotRegistrarServiceTest.php` contains NO `createMock`, `getMockBuilder`, or `MockObject` references. The fake is a plain concrete/anonymous class implementing `TelegramHttpClient`. Verifier greps and asserts absence.
- AC19: The test file fake (whether the existing in-file anonymous fake extended with `setWebhook`, or a new named helper class) implements BOTH `getMe` and `setWebhook`. Verifier greps the test file for `public function getMe` and `public function setWebhook` and asserts at least one occurrence each.
- AC20: The test file defines the recorder fields used by the new tests: `setWebhookCallCount`, `setWebhookLastArgs`, and supports a configurable `setWebhookNextResponse` and `setWebhookNextException` path. Verifier greps the test file for the substrings `setWebhookCallCount`, `setWebhookLastArgs`, `setWebhookNextResponse`, `setWebhookNextException`.

### Hygiene acceptance
- AC21: `php -l` exits 0 on every modified PHP file:
  - `app/Services/Telegram/TelegramHttpClient.php`
  - `app/Services/TelegramBotRegistrarService.php`
  - `tests/Unit/Services/TelegramBotRegistrarServiceTest.php`
- AC22: Autoload probe unchanged: `App\Services\AgentDeployerService`, `App\Services\KiloClawClientService`, `App\Services\OnChainOSPaymentService`, `App\Services\TelegramBotRegistrarService`, `App\Services\Telegram\TelegramHttpClient`, `App\Services\Telegram\TelegramTransportException` all resolve via `class_exists`/`interface_exists` after `vendor/autoload.php` is required.
- AC23: No live network call during `composer test`. Grep the modified production and test files for `file_get_contents`, `curl_init`, `curl_exec`, `fsockopen`, `fopen('http`; must be absent. Allowed https strings: the `'https://'` prefix literal in `registerWebhook`, docblock URLs, and test fixture URL strings (`'https://example.com/hook'`, `'http://example.com/hook'`, `'https://nohostdot'`).
- AC24: No em dashes (U+2014) in any touched file including this spec.

### Regression and scope acceptance
- AC25: No regressions on prior tasks. Each of the 8 prior tasks still reports PASS in its `verdict.json`: `bootstrap-proof-loop`, `scaffold-service-stubs`, `test-harness`, `landing-mockup-steve-ive`, `tdd-onchainos-charge-wallet`, `tdd-telegram-validate-token`, plus any others present at verify time (verifier enumerates `.agent/tasks/*/verdict.json` and requires all PASS).
- AC26: Collateral scope is strictly limited. The only files modified (M), added (A), or touched are:
  - M `app/Services/Telegram/TelegramHttpClient.php`
  - M `app/Services/TelegramBotRegistrarService.php`
  - M `tests/Unit/Services/TelegramBotRegistrarServiceTest.php`
  - A `.agent/tasks/tdd-telegram-set-webhook/artifacts/phpunit-red.txt`
  - A `.agent/tasks/tdd-telegram-set-webhook/artifacts/phpunit-green.txt`
  - M `.agent/tasks/tdd-telegram-set-webhook/spec.md` (this file)
  - A `.agent/tasks/tdd-telegram-set-webhook/evidence.json`, `evidence.md`, `verdict.json`, `problems.md` (workflow files)
  - `.phpunit.result.cache` may update; tolerate.
  No other file changes. No `composer.json`, `phpunit.xml.dist`, `.gitignore`, or other top-level config changes. No new top-level directories.

## Constraints
- Strict TDD discipline. The RED artifact MUST be captured before the `registerWebhook` method body is implemented. The ordering check: the RED artifact timestamp and git-state ordering must precede any change to `TelegramBotRegistrarService` that adds the `registerWebhook` body.
- The RED step extends the `TelegramHttpClient` interface AND simultaneously updates the existing fake (in the test file) with a no-op `setWebhook(...) { return []; }` stub. Without this, the 5 existing validateToken tests would fatal on load and AC2 would fail. This is a legitimate collateral edit, not a regression.
- No live network calls. The only path to `setWebhook` data is the injected fake.
- No Laravel framework code, no Illuminate facades, no service container resolution.
- No Guzzle, Symfony HttpClient, or any third-party HTTP package added to `composer.json`.
- No PHPUnit `createMock`, `getMockBuilder`, or `MockObject` usage.
- Strict types enabled on every modified PHP file.
- No em dashes anywhere in the touched files.
- No edits to `composer.json`, `phpunit.xml.dist`, `.gitignore`, or any other top-level config.
- No edits to other service stubs (`AgentDeployerService`, `KiloClawClientService`, `OnChainOSPaymentService`).
- `validateToken` method body is byte identical pre and post. Verifier diffs.
- No deletes outside scope. If removal is needed, use `trash` not `rm`.
- No TODO placeholders in any authored file.
- The build runs locally; no Coolify deploy, no DNS changes, no remote infra writes.
- The `https://` string literal is permitted exactly once in `TelegramBotRegistrarService.php` production code (inside the `registerWebhook` prefix check). Any other `https://` or `http://` literals in production files fail AC17.

## Non-goals
- No real Telegram API call in this task. The concrete `TelegramHttpClient` implementation hitting `api.telegram.org` is a separate downstream task.
- No re-implementation of `validateToken`. It stays byte identical.
- No retry, exponential backoff, or circuit breaker logic on `setWebhook` failure.
- No webhook secret token, allowed_updates filter, or drop_pending_updates flag in v0.1.
- No Laravel facade, Eloquent model, migration, controller, route, or middleware.
- No `.env` or environment variable wiring.
- No DB schema, queue, or job.
- No Mandate integration.
- No frontend wiring, Inertia page, or React change.
- No KiloClaw or OnChainOS change in this task.
- No refactor of the test harness or `phpunit.xml.dist`.

## Verification plan

The verifier runs each of the following and pins each command to the AC it satisfies.

### Pre-RED ordering
- Confirm `git diff app/Services/TelegramBotRegistrarService.php` at RED artifact capture time shows NO `registerWebhook` body (stub allowed, body empty or throwing `\LogicException` allowed). (AC1, AC3)

### Build presence and shape
- `test -f app/Services/Telegram/TelegramHttpClient.php` (AC6)
- `test -f app/Services/TelegramBotRegistrarService.php` (AC8)
- `test -f tests/Unit/Services/TelegramBotRegistrarServiceTest.php` (AC18)
- `grep -n "declare(strict_types=1);" app/Services/Telegram/TelegramHttpClient.php` (AC6, AC21)
- `grep -n "declare(strict_types=1);" app/Services/TelegramBotRegistrarService.php` (AC8, AC21)
- `grep -n "declare(strict_types=1);" tests/Unit/Services/TelegramBotRegistrarServiceTest.php` (AC21)
- `grep -n "interface TelegramHttpClient" app/Services/Telegram/TelegramHttpClient.php` (AC6)
- `grep -n "public function getMe(string \$token): array" app/Services/Telegram/TelegramHttpClient.php` (AC6)
- `grep -n "public function setWebhook(string \$token, string \$webhookUrl): array" app/Services/Telegram/TelegramHttpClient.php` (AC6)
- `grep -n "final class TelegramBotRegistrarService" app/Services/TelegramBotRegistrarService.php` (AC8)
- `grep -n "public function validateToken(string \$token): bool" app/Services/TelegramBotRegistrarService.php` (AC8, AC9)
- `grep -n "public function registerWebhook(string \$token, string \$webhookUrl): array" app/Services/TelegramBotRegistrarService.php` (AC8)

### validateToken byte-identity
- Extract the `validateToken` method body via awk from the post-green file. Diff against the pinned snapshot (the tdd-telegram-validate-token post-green byte range). Any non-empty diff fails AC9.
- Pinned snapshot (the exact body the verifier diffs against):
  ```
  public function validateToken(string $token): bool
  {
      if (trim($token) === '') {
          return false;
      }

      try {
          $response = $this->client->getMe($token);
      } catch (TelegramTransportException) {
          return false;
      }

      return ($response['ok'] ?? false) === true;
  }
  ```

### Lint
- `php -l app/Services/Telegram/TelegramHttpClient.php` (AC21)
- `php -l app/Services/TelegramBotRegistrarService.php` (AC21)
- `php -l tests/Unit/Services/TelegramBotRegistrarServiceTest.php` (AC21)

### Autoload probe
- `php -r 'require "vendor/autoload.php"; foreach (["App\\\\Services\\\\AgentDeployerService","App\\\\Services\\\\KiloClawClientService","App\\\\Services\\\\OnChainOSPaymentService","App\\\\Services\\\\TelegramBotRegistrarService","App\\\\Services\\\\Telegram\\\\TelegramHttpClient","App\\\\Services\\\\Telegram\\\\TelegramTransportException"] as $c) { if (!class_exists($c) && !interface_exists($c)) { fwrite(STDERR, "MISSING: $c\n"); exit(1); } } echo "OK\n";'` (AC22)

### Test execution and artifacts
- `test -f .agent/tasks/tdd-telegram-set-webhook/artifacts/phpunit-red.txt` (AC1)
- `test -f .agent/tasks/tdd-telegram-set-webhook/artifacts/phpunit-green.txt` (AC4)
- `grep -E "FAIL|Errors|Failures" .agent/tasks/tdd-telegram-set-webhook/artifacts/phpunit-red.txt` matches (AC1)
- RED artifact shows exactly the 6 new test names failing AND the 5 validateToken tests passing. (AC1, AC2, AC3)
- `grep -E "OK|Tests: [0-9]+, Assertions: [0-9]+" .agent/tasks/tdd-telegram-set-webhook/artifacts/phpunit-green.txt` matches (AC4)
- `composer test` exits 0 and reports `Tests: N, Assertions: M` where N >= 20 and M >= 20 (AC5)

### Test name presence
- `grep -n "function test_register_webhook_empty_token_returns_invalid_no_client_call" tests/Unit/Services/TelegramBotRegistrarServiceTest.php` (AC10, AC11)
- `grep -n "function test_register_webhook_non_https_url_returns_invalid_no_client_call" tests/Unit/Services/TelegramBotRegistrarServiceTest.php` (AC12)
- `grep -n "function test_register_webhook_invalid_host_returns_invalid_no_client_call" tests/Unit/Services/TelegramBotRegistrarServiceTest.php` (AC13)
- `grep -n "function test_register_webhook_happy_path_returns_registered" tests/Unit/Services/TelegramBotRegistrarServiceTest.php` (AC14)
- `grep -n "function test_register_webhook_client_exception_returns_failed" tests/Unit/Services/TelegramBotRegistrarServiceTest.php` (AC15)
- `grep -n "function test_register_webhook_telegram_rejected_returns_failed" tests/Unit/Services/TelegramBotRegistrarServiceTest.php` (AC16)

### Test fake shape
- `grep -nE "createMock|getMockBuilder|MockObject" tests/Unit/Services/TelegramBotRegistrarServiceTest.php` returns no matches (AC18)
- `grep -nE "public function getMe" tests/Unit/Services/TelegramBotRegistrarServiceTest.php` returns at least one match (AC19)
- `grep -nE "public function setWebhook" tests/Unit/Services/TelegramBotRegistrarServiceTest.php` returns at least one match (AC19)
- `grep -n "setWebhookCallCount" tests/Unit/Services/TelegramBotRegistrarServiceTest.php` returns at least one match (AC20)
- `grep -n "setWebhookLastArgs" tests/Unit/Services/TelegramBotRegistrarServiceTest.php` returns at least one match (AC20)
- `grep -n "setWebhookNextResponse" tests/Unit/Services/TelegramBotRegistrarServiceTest.php` returns at least one match (AC20)
- `grep -n "setWebhookNextException" tests/Unit/Services/TelegramBotRegistrarServiceTest.php` returns at least one match (AC20)

### registerWebhook body grep
- Extract the `registerWebhook` method body via awk. Grep for ` new `, `::`, ` static `. Must return no matches. (AC17)
- Grep production files for `http://`: no matches. (AC17, AC23)
- Grep `TelegramBotRegistrarService.php` for `'https://'` literal: at most one match (the prefix check). (AC17)

### Network hygiene
- `grep -nE "file_get_contents|curl_init|curl_exec|fsockopen|fopen\\('http" app/Services/TelegramBotRegistrarService.php app/Services/Telegram/*.php tests/Unit/Services/TelegramBotRegistrarServiceTest.php` returns no matches (AC23)

### Em dash check
- `grep -nP "\xE2\x80\x94" app/Services/Telegram/TelegramHttpClient.php app/Services/TelegramBotRegistrarService.php tests/Unit/Services/TelegramBotRegistrarServiceTest.php .agent/tasks/tdd-telegram-set-webhook/spec.md` returns no matches (AC24)

### Regression check
- For each directory under `.agent/tasks/` that has a `verdict.json`, confirm verdict is PASS except for the current task. In particular: `bootstrap-proof-loop`, `scaffold-service-stubs`, `test-harness`, `landing-mockup-steve-ive`, `tdd-onchainos-charge-wallet`, `tdd-telegram-validate-token`. (AC25)

### Scope check
- `git status --porcelain` shows only files from AC26. No new top-level directories. No edits to `composer.json`, `phpunit.xml.dist`, `.gitignore`. `.phpunit.result.cache` is tolerated. (AC26)
