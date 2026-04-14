# Task Spec: http-routes-controllers

## Metadata
- Task ID: http-routes-controllers
- Created: 2026-04-14T15:21:19+00:00
- Frozen: 2026-04-13
- Repo root: /Users/krutovoy/Projects/hosting-platform
- Working directory at init: /Users/krutovoy/Projects/hosting-platform
- Wave: 8 (eighth TDD cycle, first HTTP-layer task)
- Runs after: land-laravel-framework (Laravel 12 shell, `routes/api.php`, `app/Http/Controllers/Controller.php` are in place)
- New production files:
  - `app/Http/Controllers/Api/DeployController.php`
  - `app/Http/Controllers/Api/OnChainOSWebhookController.php`
  - `app/Http/Controllers/Api/TelegramWebhookController.php`
  - `app/Services/OnChainOS/WebhookSignatureVerifier.php`
  - `config/onchainos.php` (or `config/services.php` extension, see AC41)
- Modified production files:
  - `routes/api.php` (append three routes)
  - `phpunit.xml.dist` (add `Feature` testsuite)
- New test files:
  - `tests/TestCase.php` (Laravel base test case; if not already created by `land-laravel-framework`)
  - `tests/CreatesApplication.php` (trait; if not already created by `land-laravel-framework`)
  - `tests/Feature/Http/DeployControllerTest.php`
  - `tests/Feature/Http/OnChainOSWebhookControllerTest.php`
  - `tests/Feature/Http/TelegramWebhookControllerTest.php`

## Guidance sources
- AGENTS.md
- CLAUDE.md
- docs/agent_spawn_prd.md (the 7-step walkthrough)
- docs/sprint_v0.1.md Day 1 Tasks 6-7 and Day 2 Tasks 3-4 (`POST /api/deploys`, `POST /api/webhooks/onchainos`, `POST /api/telegram/webhook/{agent_id}`)
- docs/integrations.md (OnChainOS webhook shape, Telegram per-agent webhook pattern)
- app/Services/AgentDeployerService.php (the orchestrator; canonical return shape and `status` values)
- app/Services/OnChainOSPaymentService.php (called transitively via `AgentDeployerService`)
- app/Services/TelegramBotRegistrarService.php (will be wired from the inbound Telegram webhook handler in a later task; this task only registers the route)
- .agent/tasks/tdd-agent-deployer/spec.md (for canonical `status` values and return-shape keys)
- .agent/tasks/land-laravel-framework/spec.md (for the Laravel shell assumptions this task depends on)

## Original task statement
Eighth TDD cycle. Wire the thin HTTP layer on top of the existing domain services. Laravel is already installed by `land-laravel-framework`, so `routes/api.php` exists and `app/Http/Controllers/Controller.php` is present. Create three new controllers under `app/Http/Controllers/Api/` and register their routes in `routes/api.php`. The controllers MUST delegate to existing service classes and MUST NOT contain business logic. Add a `Feature` testsuite to `phpunit.xml.dist` (the only change to that file). Write feature tests using Laravel's HTTP test helpers, binding fakes via the service container rather than PHPUnit mocks. `composer test` after green must report at least nine new tests on top of the prior count. No existing service file or existing unit test file is modified. Strict types. Red then green discipline. No em dashes. No changes to `design/`, `docs/`, `CLAUDE.md`, `AGENTS.md`.

## Acceptance criteria

### DeployController file shape
- AC1: `app/Http/Controllers/Api/DeployController.php` exists, begins with `<?php` followed by `declare(strict_types=1);`, and declares `namespace App\Http\Controllers\Api;`.
- AC2: The class is `final class DeployController extends \App\Http\Controllers\Controller` (or the fully resolved form via `use App\Http\Controllers\Controller;`). It imports `Illuminate\Http\Request` and `Illuminate\Http\JsonResponse` via `use`.
- AC3: The class exposes exactly one public action: `public function store(Request $request, \App\Services\AgentDeployerService $deployer): JsonResponse`. The `AgentDeployerService` parameter is resolved by Laravel's container via method injection.
- AC4: Inside `store()`, the request payload is validated via `$request->validate([...])` with EXACTLY these rules (grepped verbatim): `'agent_name' => 'required|string'`, `'personality' => 'required|string'`, `'telegram_bot_token' => 'required|string'`, `'amount_usd' => 'required|integer|min:1'`, `'allowlist' => 'nullable|string'`.
- AC5: After validation, `store()` calls `$deployer->deploy($request->all())` and captures the result into a local `$result` variable (one call site; grep returns exactly one hit for `$deployer->deploy(`).
- AC6: `store()` maps `$result['status']` to an HTTP status code via the following locked table and returns `response()->json($result, $statusCode)`:
  - `'deployed'` to `201`
  - `'payment_failed'` to `402`
  - `'invalid_request'` to `422`
  - `'telegram_invalid'` to `422`
  - `'install_failed'` to `502`
  - any other value (safety default) to `500`
- AC7: `store()` contains NO business logic beyond validation, the single `deploy()` call, and the status mapping. No database queries. No `new ` (space-new-space) inside the method body. No `try`/`catch`. No calls to any other service class.

### OnChainOSWebhookController file shape
- AC8: `app/Http/Controllers/Api/OnChainOSWebhookController.php` exists, begins with `<?php` followed by `declare(strict_types=1);`, and declares `namespace App\Http\Controllers\Api;`.
- AC9: The class is `final class OnChainOSWebhookController extends \App\Http\Controllers\Controller`. It imports `Illuminate\Http\Request`, `Illuminate\Http\JsonResponse`, and `App\Services\OnChainOS\WebhookSignatureVerifier` via `use`.
- AC10: The class exposes exactly one public action: `public function handle(Request $request, WebhookSignatureVerifier $verifier): JsonResponse`. The verifier is injected via method injection.
- AC11: Inside `handle()`, the `X-OnChainOS-Signature` header is read via `$request->header('X-OnChainOS-Signature')`. If the header is absent or empty, the method returns `response()->json(['status' => 'error', 'error' => 'missing signature'], 400)` and does NOT call the verifier.
- AC12: If the header is present, `handle()` calls `$verifier->verify($signature, $request->getContent())`. If it returns `false`, `handle()` returns `response()->json(['status' => 'error', 'error' => 'invalid signature'], 401)`.
- AC13: If the verifier returns `true`, `handle()` returns `response()->json(['status' => 'ok'], 200)`. It does NOT call `AgentDeployerService`, does NOT hit the database, and does NOT enqueue any job. The actual provisioning-job trigger is a future task.
- AC14: `handle()` contains no `try`/`catch`, no ` new ` inside the method body, and no business logic beyond header read, verifier call, and status mapping.

### TelegramWebhookController file shape
- AC15: `app/Http/Controllers/Api/TelegramWebhookController.php` exists, begins with `<?php` followed by `declare(strict_types=1);`, and declares `namespace App\Http\Controllers\Api;`.
- AC16: The class is `final class TelegramWebhookController extends \App\Http\Controllers\Controller`. It imports `Illuminate\Http\Request` and `Illuminate\Http\JsonResponse` via `use`.
- AC17: The class exposes exactly one public action: `public function handle(int $agentId, Request $request): JsonResponse`. The `$agentId` parameter is bound from the route placeholder.
- AC18: `handle()` returns `response()->json(['ok' => true], 200)`. No database access, no service calls, no signature verification in v0.1. The message-routing-to-KiloClaw implementation is a future task.

### WebhookSignatureVerifier service
- AC19: `app/Services/OnChainOS/WebhookSignatureVerifier.php` exists with `declare(strict_types=1);` and `namespace App\Services\OnChainOS;`.
- AC20: The class is `final class WebhookSignatureVerifier`. The constructor uses PHP 8 promotion with a single `private readonly string $secret` property. The value is read from config via the service provider binding (see AC41) or via a static factory method; the class itself does NOT call `config()` or `env()` inside its body.
- AC21: The class exposes one public method `public function verify(string $signature, string $payload): bool`. The v0.1 implementation uses `hash_equals($this->secret, $signature)` (constant-time compare). The `$payload` parameter is accepted for forward compatibility but is allowed to be unused in v0.1.
- AC22: The class file contains no `new ` inside the method body, no `::` static calls other than `hash_equals(` (which is a function call, not `::`), no `try`/`catch`.

### Routes
- AC23: `routes/api.php` contains a line registering `Route::post('/deploys', [DeployController::class, 'store']);` (locked literal, grepped verbatim).
- AC24: `routes/api.php` contains a line registering `Route::post('/webhooks/onchainos', [OnChainOSWebhookController::class, 'handle']);` (locked literal, grepped verbatim).
- AC25: `routes/api.php` contains a line registering `Route::post('/telegram/webhook/{agentId}', [TelegramWebhookController::class, 'handle']);` (locked literal, grepped verbatim).
- AC26: `routes/api.php` has three `use` imports at the top: `use App\Http\Controllers\Api\DeployController;`, `use App\Http\Controllers\Api\OnChainOSWebhookController;`, `use App\Http\Controllers\Api\TelegramWebhookController;`.
- AC27: The three routes are mounted under the `/api` prefix (Laravel's default for `routes/api.php` via `RouteServiceProvider`). `php artisan route:list --path=api` lists `POST api/deploys`, `POST api/webhooks/onchainos`, and `POST api/telegram/webhook/{agentId}` and maps each to the correct controller action.

### phpunit.xml.dist
- AC28: `phpunit.xml.dist` still contains the existing `<testsuite name="Unit"><directory>./tests/Unit</directory></testsuite>` block, byte-for-byte unchanged except for formatting around the new sibling block.
- AC29: `phpunit.xml.dist` contains a new `<testsuite name="Feature"><directory>./tests/Feature</directory></testsuite>` block as a sibling of the Unit testsuite inside `<testsuites>`. The existing `bootstrap`, `colors`, and XML header attributes are unchanged.

### Laravel test base class
- AC30: `tests/TestCase.php` exists with `declare(strict_types=1);`, `namespace Tests;`, and `abstract class TestCase extends \Illuminate\Foundation\Testing\TestCase { use CreatesApplication; }`.
- AC31: `tests/CreatesApplication.php` exists with `declare(strict_types=1);`, `namespace Tests;`, and a `trait CreatesApplication` that boots the app via `require __DIR__.'/../bootstrap/app.php'` and calls `$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap()` before returning `$app`. If `land-laravel-framework` already created this trait, this task leaves it untouched and this AC is satisfied by the pre-existing file; otherwise this task creates it.

### DeployController feature tests
- AC32: `tests/Feature/Http/DeployControllerTest.php` exists with `declare(strict_types=1);`, `namespace Tests\Feature\Http;`, and the class `final class DeployControllerTest extends \Tests\TestCase`.
- AC33: The test file binds a fake `AgentDeployerService` into the container via `$this->app->bind(AgentDeployerService::class, fn () => new FakeAgentDeployerService($preloadedResult))` (or equivalently via `instance()`); it does NOT use `createMock`, `getMockBuilder`, or `MockObject` anywhere. The fake is an in-file final class that extends `AgentDeployerService` and overrides `deploy(array $request): array` to return a pre-seeded array (constructor-promoted `array $response`). Because the real `AgentDeployerService` constructor requires three service arguments, the fake's constructor accepts `array $response` only and calls `parent::__construct(...)` with three null-object fakes OR declares its own constructor without calling `parent::__construct`, matching whichever pattern is compatible with PHP 8.2 when the parent uses `private readonly` promoted properties (the fake overrides `deploy()` so the parent constructor does not need to run; using `parent::__construct` is NOT required and the fake may skip it as long as `deploy()` is overridden and no parent method depending on the promoted properties is invoked).
- AC34: The test class declares EXACTLY these five public test methods (names grepped verbatim), each annotated with `#[\PHPUnit\Framework\Attributes\Test]` OR prefixed with `test_`:
  - `test_happy_path_deployed_returns_201`
  - `test_missing_personality_returns_422`
  - `test_telegram_invalid_returns_422`
  - `test_payment_failed_returns_402`
  - `test_install_failed_returns_502`
- AC35: Each test uses `$this->postJson('/api/deploys', [...])` (locked literal path, grepped verbatim) and asserts the HTTP status via `$response->assertStatus($code)` where `$code` matches the AC6 mapping table.
- AC36: The happy-path test seeds the fake with `['status' => 'deployed', 'stage' => 'complete', 'agent_name' => 'atlas', 'error' => null, 'kiloclaw_id' => 'kc_abc', 'session_id' => 'sess_abc']` and asserts the JSON response contains exactly these six keys with these values via `$response->assertJson([...])`.

### OnChainOSWebhookController feature tests
- AC37: `tests/Feature/Http/OnChainOSWebhookControllerTest.php` exists with `declare(strict_types=1);`, `namespace Tests\Feature\Http;`, and `final class OnChainOSWebhookControllerTest extends \Tests\TestCase`.
- AC38: The test class declares EXACTLY these three public test methods (names grepped verbatim):
  - `test_missing_signature_header_returns_400`
  - `test_invalid_signature_returns_401`
  - `test_valid_signature_returns_200`
- AC39: Each test posts to `/api/webhooks/onchainos` (locked literal path, grepped verbatim) via `$this->postJson('/api/webhooks/onchainos', [...], [...])` or the equivalent header-bearing variant. The valid-signature test uses the header name `X-OnChainOS-Signature` (locked literal). No PHPUnit mocks are used; the `WebhookSignatureVerifier` is bound into the container via `$this->app->bind(WebhookSignatureVerifier::class, fn () => new WebhookSignatureVerifier('test-secret'))` and the test supplies matching or mismatching header values.

### TelegramWebhookController feature test
- AC40: `tests/Feature/Http/TelegramWebhookControllerTest.php` exists with `declare(strict_types=1);`, `namespace Tests\Feature\Http;`, and `final class TelegramWebhookControllerTest extends \Tests\TestCase`. It declares EXACTLY one public test method named `test_post_returns_ok_true` that issues `$this->postJson('/api/telegram/webhook/42', [])` (agent id 42 is a locked literal used for greppable verification) and asserts status 200 and JSON body `['ok' => true]`.

### Config wiring
- AC41: The OnChainOS webhook secret is bound at the container level. Either `config/services.php` gains an `'onchainos' => ['webhook_secret' => env('ONCHAINOS_WEBHOOK_SECRET', '')]` subarray, OR a new `config/onchainos.php` is added with an equivalent shape. A service-provider binding (inside `app/Providers/AppServiceProvider.php`'s `register()` or a new dedicated provider) constructs `WebhookSignatureVerifier` from `config('services.onchainos.webhook_secret')` (or `config('onchainos.webhook_secret')`, matching whichever file was chosen). The binding must use `$this->app->singleton(WebhookSignatureVerifier::class, fn () => new WebhookSignatureVerifier((string) config('services.onchainos.webhook_secret')));` or the exact equivalent for the other config path.

### Red then green discipline
- AC42: `.agent/tasks/http-routes-controllers/phpunit-red.txt` exists and records a non-zero exit of `composer test` captured BEFORE the three controllers are written (only the three feature test files present and the `Feature` testsuite added to `phpunit.xml.dist`). `.agent/tasks/http-routes-controllers/phpunit-green.txt` exists and records a zero exit of `composer test` captured AFTER the controllers, routes, verifier, config, and service-provider binding are in place.

### Test count and regressions
- AC43: After green, `composer test` reports at least `prior_total + 9` tests (five DeployController + three OnChainOSWebhookController + one TelegramWebhookController = nine new tests). `prior_total` is whatever `composer test` reports after `land-laravel-framework` completes. The exit code is 0 and zero tests are skipped or marked incomplete for the nine new cases.
- AC44: All prior unit tests remain green and byte-for-byte untouched. The ten existing service and unit files listed under Constraints are unchanged.

### Collateral
- AC45: `git status --porcelain` after this task shows ONLY the following paths touched (plus optional `.phpunit.result.cache` and `.agent/tasks/http-routes-controllers/` artifacts):
  - `app/Http/Controllers/Api/DeployController.php` (new)
  - `app/Http/Controllers/Api/OnChainOSWebhookController.php` (new)
  - `app/Http/Controllers/Api/TelegramWebhookController.php` (new)
  - `app/Services/OnChainOS/WebhookSignatureVerifier.php` (new)
  - `app/Providers/AppServiceProvider.php` (modified; container binding)
  - `config/services.php` OR `config/onchainos.php` (modified or new)
  - `routes/api.php` (modified)
  - `phpunit.xml.dist` (modified; Feature testsuite added)
  - `tests/TestCase.php` (new IF not created by `land-laravel-framework`)
  - `tests/CreatesApplication.php` (new IF not created by `land-laravel-framework`)
  - `tests/Feature/Http/DeployControllerTest.php` (new)
  - `tests/Feature/Http/OnChainOSWebhookControllerTest.php` (new)
  - `tests/Feature/Http/TelegramWebhookControllerTest.php` (new)

## Constraints
- PHP 8.2, strict types in every new file.
- Laravel 12 is already installed by `land-laravel-framework`. No new composer dependencies. No `laravel new`.
- No em dashes anywhere in the new or modified files.
- No placeholder-work comments (`T` `O` `D` `O`, `FIXME`, `XXX`) in any new file or in this spec.
- Feature tests MUST extend `Tests\TestCase` (Laravel's Foundation test base). They MUST use Laravel's HTTP test helpers (`postJson`, `getJson`, `assertStatus`, `assertJson`) and MUST NOT use `createMock`, `getMockBuilder`, or `MockObject`.
- Fakes for service classes are wired via `$this->app->bind(...)` or `$this->app->instance(...)`. In-file fake classes are allowed and are the preferred pattern when a fake needs custom behavior per test; they must be `final` and live inside the test file (not extracted to `tests/Support/`).
- Controllers MUST NOT contain business logic. No DB queries, no job dispatch, no calls to any service class other than the one declared in the constructor or method signature of each controller action.
- `phpunit.xml.dist` is modified in exactly ONE place: the addition of the `Feature` testsuite block. The existing `Unit` testsuite is unchanged.
- The following ten files are preserved byte-for-byte:
  - `app/Services/AgentDeployerService.php`
  - `app/Services/OnChainOSPaymentService.php`
  - `app/Services/KiloClawClientService.php`
  - `app/Services/TelegramBotRegistrarService.php`
  - `app/Services/OnChainOS/OnChainOSClient.php`
  - `app/Services/OnChainOS/OnChainOSException.php`
  - `app/Services/KiloClaw/KiloClawHttpClient.php`
  - `app/Services/KiloClaw/KiloClawTransportException.php`
  - `app/Services/Telegram/TelegramHttpClient.php`
  - `app/Services/Telegram/TelegramTransportException.php`
- The following five unit test files are preserved byte-for-byte:
  - `tests/Unit/SmokeTest.php`
  - `tests/Unit/Services/AgentDeployerServiceTest.php`
  - `tests/Unit/Services/OnChainOSPaymentServiceTest.php`
  - `tests/Unit/Services/KiloClawClientServiceTest.php`
  - `tests/Unit/Services/TelegramBotRegistrarServiceTest.php`
- No changes to `design/`, `docs/`, `CLAUDE.md`, `AGENTS.md`.
- Route path literals, HTTP methods, controller class names, and test method names listed in the ACs are LOCKED; the verifier greps `routes/api.php`, the controller files, and the test files for each exact string.
- Red then green discipline is mandatory: write the three feature test files and the `phpunit.xml.dist` edit first, capture `phpunit-red.txt`, THEN write the controllers, routes, verifier, config, and provider binding, and capture `phpunit-green.txt`.

## Non-goals
- Not implementing the OnChainOS webhook side effects (marking a deploy as paid in the DB, enqueuing the provisioning job). `handle()` returns 200 on valid signature and nothing else.
- Not implementing the inbound Telegram message routing to KiloClaw. The Telegram webhook handler returns `{"ok": true}` only. First-message wallet disclosure is a separate task.
- Not creating any migrations, models, or database tables. The `deploys`, `agents`, and `users` tables are handled by `postgres-migrations`.
- Not adding authentication, authorization, rate limiting, or CSRF handling. These are post-v0.1 concerns and are explicitly cut per `docs/sprint_v0.1.md` scope discipline.
- Not wiring Inertia, React, or any frontend code. This task is the HTTP layer only.
- Not touching `design/`, `docs/`, `CLAUDE.md`, `AGENTS.md`.
- Not modifying any existing service class or existing unit test file.
- Not adding a global exception handler beyond Laravel's default `ValidationException` to 422 mapping (which Laravel already provides out of the box).
- Not defining OpenAPI / request resource classes. `$request->validate()` inline is sufficient for v0.1.

## Verification plan

### Build
- `php -l app/Http/Controllers/Api/DeployController.php` exits 0.
- `php -l app/Http/Controllers/Api/OnChainOSWebhookController.php` exits 0.
- `php -l app/Http/Controllers/Api/TelegramWebhookController.php` exits 0.
- `php -l app/Services/OnChainOS/WebhookSignatureVerifier.php` exits 0.
- `php -l routes/api.php` exits 0.
- `php -l tests/Feature/Http/DeployControllerTest.php` exits 0.
- `php -l tests/Feature/Http/OnChainOSWebhookControllerTest.php` exits 0.
- `php -l tests/Feature/Http/TelegramWebhookControllerTest.php` exits 0.
- `php artisan route:list --path=api` lists the three new routes with the correct controller bindings.

### Red phase
- Before writing the three controller files, the route lines, the verifier, and the provider binding, run `composer test`. Capture output to `.agent/tasks/http-routes-controllers/phpunit-red.txt`. The file must show a non-zero exit and failing cases in the three new feature test files (either missing class errors, 404 responses, or 500 responses depending on the Laravel boot order).

### Green phase
- After writing the controllers, routes, verifier, config, and provider binding, run `composer test`. Capture to `phpunit-green.txt`. It must exit 0 and show at least `prior_total + 9` tests.

### Per-AC greps (deterministic)
- AC1-AC3 (DeployController): `grep -n "declare(strict_types=1);" app/Http/Controllers/Api/DeployController.php`, `grep -n "namespace App\\\\Http\\\\Controllers\\\\Api;"`, `grep -n "final class DeployController"`, `grep -n "public function store(Request \\$request, .*AgentDeployerService \\$deployer): JsonResponse"`.
- AC4: `grep -n "'agent_name' => 'required|string'"`, `grep -n "'personality' => 'required|string'"`, `grep -n "'telegram_bot_token' => 'required|string'"`, `grep -n "'amount_usd' => 'required|integer|min:1'"`, `grep -n "'allowlist' => 'nullable|string'"` all present in `DeployController.php`.
- AC5: `grep -cn "\\$deployer->deploy(" app/Http/Controllers/Api/DeployController.php` returns exactly 1.
- AC6: `grep -n "'deployed'.*201\\|201.*'deployed'"`, same for `'payment_failed'` and `402`, `'invalid_request'` and `422`, `'telegram_invalid'` and `422`, `'install_failed'` and `502` in `DeployController.php`.
- AC7: awk-extract `store(` body, assert zero hits for ` new `, `try`, `catch`.
- AC8-AC14 (OnChainOSWebhookController): analogous greps for file header, class declaration, action signature, header read, verifier call, status codes 200/400/401.
- AC15-AC18 (TelegramWebhookController): analogous greps. `grep -n "'ok' => true"` present in the controller; `grep -n "response()->json" ` present exactly once.
- AC19-AC22 (WebhookSignatureVerifier): `grep -n "final class WebhookSignatureVerifier"`, `grep -n "private readonly string \\$secret"`, `grep -n "public function verify(string \\$signature, string \\$payload): bool"`, `grep -n "hash_equals("`.
- AC23: `grep -n "Route::post('/deploys', \\[DeployController::class, 'store'\\]);" routes/api.php` returns exactly 1.
- AC24: `grep -n "Route::post('/webhooks/onchainos', \\[OnChainOSWebhookController::class, 'handle'\\]);" routes/api.php` returns exactly 1.
- AC25: `grep -n "Route::post('/telegram/webhook/{agentId}', \\[TelegramWebhookController::class, 'handle'\\]);" routes/api.php` returns exactly 1.
- AC26: three `use App\Http\Controllers\Api\*Controller;` lines present in `routes/api.php`.
- AC27: `php artisan route:list --path=api --json` parsed; assert three entries with method `POST` and URIs `api/deploys`, `api/webhooks/onchainos`, `api/telegram/webhook/{agentId}`.
- AC28-AC29: `grep -n 'testsuite name="Unit"' phpunit.xml.dist` returns 1. `grep -n 'testsuite name="Feature"' phpunit.xml.dist` returns 1. `grep -n './tests/Feature' phpunit.xml.dist` returns 1.
- AC30-AC31: `test -f tests/TestCase.php && grep -q "namespace Tests;" tests/TestCase.php && grep -q "abstract class TestCase" tests/TestCase.php`. Same for `tests/CreatesApplication.php`.
- AC32-AC36 (DeployControllerTest): `grep -n "final class DeployControllerTest extends"`, each of the five method names grepped verbatim, `grep -cn "\\$this->postJson('/api/deploys'" tests/Feature/Http/DeployControllerTest.php` returns 5, `grep -c "createMock\\|getMockBuilder\\|MockObject"` returns 0.
- AC37-AC39 (OnChainOSWebhookControllerTest): `grep -n "final class OnChainOSWebhookControllerTest extends"`, three method names grepped verbatim, `grep -cn "'/api/webhooks/onchainos'"` returns at least 3, `grep -n "'X-OnChainOS-Signature'"` present, `grep -c "createMock\\|getMockBuilder\\|MockObject"` returns 0.
- AC40 (TelegramWebhookControllerTest): `grep -n "final class TelegramWebhookControllerTest extends"`, `grep -n "test_post_returns_ok_true"`, `grep -n "'/api/telegram/webhook/42'"`, `grep -n "'ok' => true"`.
- AC41: `grep -n "WebhookSignatureVerifier::class" app/Providers/AppServiceProvider.php` returns at least 1, `grep -n "webhook_secret" config/services.php config/onchainos.php 2>/dev/null` returns at least 1.
- AC42: `test -s .agent/tasks/http-routes-controllers/phpunit-red.txt && test -s .agent/tasks/http-routes-controllers/phpunit-green.txt`.
- AC43: parse `phpunit-green.txt` final summary; assert `Tests: >= (prior_total + 9)` and exit 0. `prior_total` is captured from the last line of `.agent/tasks/land-laravel-framework/raw/test-unit.txt` or recomputed from the green output of the previous task.
- AC44: `git diff --name-only` restricted to the ten service files and five unit test files listed in Constraints returns zero lines.
- AC45: `git status --porcelain` output set-difference against the allowlisted paths listed in AC45 is empty.

### Lint
- `./vendor/bin/pint --test app/Http/Controllers/Api/ app/Services/OnChainOS/WebhookSignatureVerifier.php tests/Feature/Http/ routes/api.php` exits 0 if Pint is wired; otherwise skipped.

### Manual checks
- Boot the app locally (`php artisan serve`) and curl each route: `POST /api/deploys` with valid JSON returns 201, `POST /api/webhooks/onchainos` without header returns 400, `POST /api/telegram/webhook/1` returns `{"ok": true}`.
- Eyeball each controller and confirm zero business logic beyond the delegated service call and status mapping.
- Confirm no em dashes in any of the new or modified files.
