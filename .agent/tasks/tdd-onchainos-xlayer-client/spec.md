# Task Spec: tdd-onchainos-xlayer-client

## Metadata
- Task ID: tdd-onchainos-xlayer-client
- Created: 2026-04-14T15:21:18+00:00
- Frozen: 2026-04-13
- Repo root: /Users/krutovoy/Projects/hosting-platform
- Working directory at init: /Users/krutovoy/Projects/hosting-platform
- Wave: A (Wave A runs after `land-laravel-framework`, which provides `laravel/framework` in composer.json)
- TDD cycle: 6 of N

## Guidance sources
- /Users/krutovoy/Projects/hosting-platform/CLAUDE.md (hackathon rules, "crypto hidden" principle, Laravel 12 + PHP 8.2 mandatory, scaffold from `~/Projects/mandate`)
- /Users/krutovoy/Projects/hosting-platform/docs/integrations.md, section "OnChainOS" (payment rail on X-Layer, `createCharge` is call 1, open questions recorded there)
- /Users/krutovoy/Projects/hosting-platform/app/Services/OnChainOS/OnChainOSClient.php (the frozen interface this task implements)
- /Users/krutovoy/Projects/hosting-platform/app/Services/OnChainOS/OnChainOSException.php (frozen, reused as the transport exception type the interface contract already documents)
- /Users/krutovoy/Projects/hosting-platform/app/Services/OnChainOSPaymentService.php (the consumer; it reads `session_id`, `status`, `expires_at` out of the client response)
- /Users/krutovoy/Projects/hosting-platform/tests/Unit/Services/OnChainOSPaymentServiceTest.php (the 8 existing tests that must stay green; they use an in-file `FakeOnChainOSClient`)
- WebFetch: https://github.com/okx/onchainos-skills (README, top-level tree)
- `gh repo view okx/onchainos-skills` (repo description and README)

### Key findings from WebFetch of okx/onchainos-skills
- Repo language: Rust 97.1%, Shell 1.5%, other 1.4%. Not PHP. Not an npm package.
- Distribution: CLI installer (`install.sh` / `install.ps1`) plus "skills" directories for Claude Code, Cursor, Codex, OpenCode, and an MCP server config. Published at `github.com/okx/onchainos-skills`.
- Available skills include `okx-agentic-wallet`, `okx-x402-payment`, `okx-onchain-gateway`, `okx-dex-swap`, and more. Authentication uses three env vars: `OKX_API_KEY`, `OKX_SECRET_KEY`, `OKX_PASSPHRASE`, applied for at the OKX Developer Portal at `web3.okx.com/onchain-os/dev-portal`.
- Supported chains include XLayer, Solana, Ethereum, Base, BSC, Arbitrum, Polygon, and 20+ others. X-Layer is explicitly in the list.
- No PHP composer package is published by okx for onchainos-skills. There is no `vendor/okx-onchainos-skills` on Packagist, and the Rust CLI is not callable from Laravel via composer.

### Pivot decision
**Case chosen: Case 2 (HTTP transport, no composer dependency).**

Rationale: okx/onchainos-skills is a Rust CLI plus AI coding assistant skill bundle, not a PHP composer package. There is no PHP SDK to require. X-Layer payment flows from a Laravel 12 backend must therefore be implemented as a thin HTTP client around the OKX OnChainOS REST API, authenticated with the three OKX env vars above. This task freezes the shape of that thin client and its transport seam, not the wire format. Real endpoint paths, signing, and exact request bodies are resolved during the implementation phase by reading the `okx-agentic-wallet` and `okx-x402-payment` skill markdown to recover the HTTP calls the Rust CLI makes; the frozen test suite exercises only the adapter layer and keeps mocked transport boundaries.

## Original task statement
Sixth TDD cycle. Implement a production `XLayerOnChainOSClient` that talks to the okx/onchainos-skills system on X-Layer, as the production implementation of the existing `App\Services\OnChainOS\OnChainOSClient` interface. The existing 8 OnChainOS unit tests (against the in-file fake in `OnChainOSPaymentServiceTest.php`) must keep passing. The new production class lives in a new namespace `App\Services\OnChainOS\XLayer` and is NOT the thing the existing tests exercise. The new production class gets its OWN test file that mocks the transport boundary below the adapter.

## Acceptance criteria

- AC1: File `app/Services/OnChainOS/OnChainOSClient.php` is byte-identical to its pre-task state; its sha256 remains `d4c5d7d9babd848e081aefb005d03c326cf38ab0d8c61f5d72b2876eab3694bc`.
- AC2: File `app/Services/OnChainOS/OnChainOSException.php` is byte-identical to its pre-task state; its sha256 remains `ee657f8efa7dd11484972ad1ad7da66738025b745db7622856f532d4a6e13012`.
- AC3: File `app/Services/OnChainOSPaymentService.php` is byte-identical to its pre-task state; its sha256 remains `66ab94621d19770f571e344ad75eb67c4ba4527196992552fbabcc133b993acf`.
- AC4: File `tests/Unit/Services/OnChainOSPaymentServiceTest.php` is byte-identical to its pre-task state; its sha256 remains `0ccace84071c89ab41e599052c905411d196922eadce27bf29dc9b794552d956`, and its 8 test methods still pass after this task lands.
- AC5: New file exists at `app/Services/OnChainOS/XLayer/XLayerHttpTransport.php`. It declares `declare(strict_types=1);`, uses namespace `App\Services\OnChainOS\XLayer`, and defines an `interface XLayerHttpTransport` with exactly one public method: `public function post(string $path, array $body, array $headers): array;`. No other members.
- AC6: New file exists at `app/Services/OnChainOS/XLayer/XLayerHttpException.php`. It declares `declare(strict_types=1);`, uses namespace `App\Services\OnChainOS\XLayer`, and defines `final class XLayerHttpException extends \RuntimeException`. No other members.
- AC7: New file exists at `app/Services/OnChainOS/XLayer/XLayerOnChainOSClient.php`. It declares `declare(strict_types=1);`, uses namespace `App\Services\OnChainOS\XLayer`, and defines `final class XLayerOnChainOSClient` that implements `\App\Services\OnChainOS\OnChainOSClient`.
- AC8: `XLayerOnChainOSClient` has a readonly-promoted constructor taking exactly the `XLayerHttpTransport` transport and three configuration strings: `string $apiKey`, `string $secretKey`, `string $passphrase`. No Laravel facades, no `config()` calls, no `env()` calls anywhere inside the class.
- AC9: `XLayerOnChainOSClient::createCharge(int $amountUsd, string $agentName, string $idempotencyKey): array` matches the signature of the interface exactly, including the `array` return type and identical parameter names and order.
- AC10: On the happy path, `createCharge` returns an array with the three keys the consumer expects: `session_id` (string), `status` (one of the strings `pending`, `failed`, `completed`), and `expires_at` (string in ISO 8601 format, or `null`). No extra top-level keys.
- AC11: `createCharge` passes `$idempotencyKey` through to the transport header map untouched. It does not rehash, prefix, or transform it. The `OnChainOSPaymentService` is the sole owner of idempotency key derivation.
- AC12: The method body of `createCharge` contains no `new ` token except for throwing exceptions (`throw new OnChainOSException(...)` or `throw new XLayerHttpException(...)`). It contains no `::` token except for class constants and static method calls on built-in PHP classes (`DateTimeImmutable`, `DateTimeInterface`, `JSON_THROW_ON_ERROR`); no static factory on application classes is allowed. This is enforced by the test suite or by inspection in `evidence.md`.
- AC13: Any `XLayerHttpException` raised by the transport is caught inside `createCharge` and rethrown as a `\App\Services\OnChainOS\OnChainOSException` that wraps the original as the previous exception, so that `OnChainOSPaymentService` only has to catch one exception type (matching its existing contract).
- AC14: A malformed upstream response (missing `session_id`, or missing `status`, or `status` outside the allowed set) causes `createCharge` to throw `\App\Services\OnChainOS\OnChainOSException` with a message that contains the substring `malformed`. It does NOT return a partial array.
- AC15: An auth failure (HTTP 401 or 403 surfaced by the transport through `XLayerHttpException`) causes `createCharge` to throw `\App\Services\OnChainOS\OnChainOSException` whose message contains the substring `auth`. The OKX API key, secret, and passphrase never appear in the exception message or in any string returned by `createCharge`.
- AC16: New test file exists at `tests/Unit/Services/OnChainOS/XLayerOnChainOSClientTest.php`, namespace `Tests\Unit\Services\OnChainOS`, extending `PHPUnit\Framework\TestCase`. It defines at least 4 test methods and uses an in-file fake class (not a PHPUnit mock object) that implements `\App\Services\OnChainOS\XLayer\XLayerHttpTransport`. The fake records the last `$path`, last `$body`, and last `$headers` it was called with, and allows queuing a response array or a `XLayerHttpException` to throw.
- AC17: The four required test cases in that file are, at minimum: (a) happy path returning canonical shape, (b) transport raises `XLayerHttpException`, client rethrows as `OnChainOSException` with original chained, (c) upstream response missing `session_id`, client throws with `malformed` in message, (d) transport raises auth failure (`XLayerHttpException` constructed with status 401 semantics), client throws with `auth` in message.
- AC18: The test suite asserts that the `$idempotencyKey` passed into `createCharge` appears verbatim in the last recorded `$headers` map on the fake transport (key name is frozen as `Idempotency-Key`).
- AC19: Running `composer test` (PHPUnit) from the repo root exits 0 after this task lands. Total test count across the whole suite is at least 41 (prior baseline 37 plus this task's minimum 4 new tests). No prior test is deleted or skipped.
- AC20: `composer.json` is NOT modified by this task; no new composer require is added. The pre-task sha256 of `composer.json` equals the post-task sha256. Rationale: Case 2 was chosen, so no new composer dependency is introduced. If future work decides to ship a curl-based transport implementation it may add `guzzlehttp/guzzle` in a separate task, but this task only freezes the seam.
- AC21: All 14 prior proof-loop task specs on disk (`bootstrap-proof-loop`, `test-harness`, `scaffold-service-stubs`, `postgres-migrations`, `http-routes-controllers`, `tdd-agent-deployer`, `tdd-kiloclaw-install`, `tdd-telegram-validate-token`, `tdd-telegram-set-webhook`, `tdd-google-auth-socialite`, `tdd-onchainos-create-charge`, `land-laravel-framework`, plus the four mockup tasks `landing-mockup-steve-ive`, `wizard-mockup-steve-ive`, `running-state-mockup-steve-ive`, `agents-list-mockup-steve-ive`, `agent-wallet-disclosure-mockup-steve-ive`, `agents-list-mockup-steve-ive`) that have already produced verdicts keep their existing verdicts; this task does not modify their `verdict.json`, `evidence.json`, or `spec.md`.
- AC22: No file produced or modified by this task contains an em dash (U+2014). Enforced by grep check in `evidence.md`.

## Constraints
- PHP 8.2, Laravel 12. No facades, no `env()`, no `config()` inside `XLayerOnChainOSClient`. Configuration is injected as constructor arguments so the test suite never touches the Laravel container.
- No network calls during `composer test`. The only transport implementation the tests see is the in-file fake. A real curl or Guzzle implementation of `XLayerHttpTransport` is explicitly out of scope for this task.
- Do not edit `app/Services/OnChainOS/OnChainOSClient.php`, `app/Services/OnChainOS/OnChainOSException.php`, `app/Services/OnChainOSPaymentService.php`, or `tests/Unit/Services/OnChainOSPaymentServiceTest.php`. They are frozen by the previous task (`tdd-onchainos-create-charge`).
- The canonical return shape is dictated by `OnChainOSPaymentService::createCharge`, which reads exactly `$response['session_id']`, `$response['status']`, and `$response['expires_at'] ?? null`. Matching that shape is non-negotiable.
- Idempotency key format is owned by `OnChainOSPaymentService` (currently `spawn-` prefix plus sha1 of agent + amount + UTC date). This task must pass the string through untouched.
- Error channel is the existing `App\Services\OnChainOS\OnChainOSException` because the interface docblock already declares `@throws OnChainOSException`. The new `XLayerHttpException` is a lower-level signal internal to the XLayer namespace that is always translated to `OnChainOSException` at the adapter boundary.
- Output must never leak OKX credentials into exception messages, logs, return values, or test fixtures.
- No em dashes anywhere.

## Non-goals
- Implementing a real curl or Guzzle `XLayerHttpTransport` that actually hits OKX. That is a separate task.
- Webhook signature verification for the OnChainOS payment callback (open question #3 in `integrations.md`). Out of scope for this cycle.
- Poll vs webhook waiting strategy for confirmation. Out of scope.
- Wiring `XLayerOnChainOSClient` into the Laravel service container (`AppServiceProvider`). Out of scope; the PaymentService is still wired against the in-file fake in its own test; container binding is a follow-up task once Laravel is fully bootstrapped.
- Adding `OKX_API_KEY`, `OKX_SECRET_KEY`, `OKX_PASSPHRASE` to `.env.example`. Out of scope for this cycle.
- Replacing the in-file fake in `OnChainOSPaymentServiceTest.php` with `XLayerOnChainOSClient`. Out of scope; those tests stay green against their own fake.
- Integrating `okx-x402-payment`, `okx-agentic-wallet`, or any other skill from `okx/onchainos-skills`. The WebFetch output informs design direction only.
- Multi-chain support beyond X-Layer. Chain selection is fixed at X-Layer per `CLAUDE.md`.
- Any changes to `composer.json` or `composer.lock`.

## Verification plan
- Build: none beyond autoload. `composer dump-autoload` as a smoke check is acceptable if required by the local toolchain; it must not add packages.
- Unit tests: `composer test` (PHPUnit) exits 0. Total method count at least 41. The new `tests/Unit/Services/OnChainOS/XLayerOnChainOSClientTest.php` contains at least 4 methods, all green. The existing `OnChainOSPaymentServiceTest` still contains its 8 methods unchanged and all green.
- Integration tests: none. This is a pure unit-level adapter cycle with no network.
- Lint: `./vendor/bin/pint --test` (or project equivalent) exits 0 for the three new files; if the project does not yet expose a Pint binary after `land-laravel-framework` lands, skip with a note in `evidence.md`.
- Invariant file checks: recompute sha256 of the four frozen files listed in AC1..AC4 and confirm they match the pre-task values captured above.
- `composer.json` byte-equality check: sha256 before and after are equal (AC20).
- Static checks on `app/Services/OnChainOS/XLayer/XLayerOnChainOSClient.php`:
  - Contains `implements OnChainOSClient` (or fully qualified equivalent).
  - `createCharge` method body does not contain `new ` except when immediately followed by `OnChainOSException` or `XLayerHttpException`.
  - `createCharge` method body does not contain the substring `env(`, `config(`, or `\Illuminate\`.
- Em dash check: `grep -R $'\u2014' app/Services/OnChainOS/XLayer tests/Unit/Services/OnChainOS` returns zero matches.
- Credential leak check: the three literal tokens `$apiKey`, `$secretKey`, `$passphrase` never appear inside the output of any test assertion message or inside any exception message raised by `createCharge`.
- Manual check: open `XLayerOnChainOSClientTest.php` and confirm the in-file fake implements `XLayerHttpTransport` (not a PHPUnit mock) and records `$path`, `$body`, `$headers` on every `post()` call.
- Regression check: load each prior task's `verdict.json` under `.agent/tasks/*/verdict.json` and confirm verdict strings are unchanged by this cycle.
