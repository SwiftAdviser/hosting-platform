# Evidence Bundle: tdd-agent-deployer

## Summary
- Overall status: PASS
- Last updated: 2026-04-13
- Baseline test result: `OK (30 tests, 87 assertions)`, composer test EXIT=0
- RED test result: 37 tests, 7 errors on `AgentDeployerService::deploy()` undefined, 30 prior pass, EXIT=2
- GREEN test result: `OK (37 tests, 142 assertions)`, composer test EXIT=0
- Wave 5 of the TDD cycle sequence.

## Files modified
- `app/Services/AgentDeployerService.php` (rewritten from one-line stub into full orchestrator, strict types, constructor-promoted deps)
- `tests/Unit/Services/AgentDeployerServiceTest.php` (new, 7 locked-name tests, 3 inline fakes prefixed `FakeAgentDeployer*`)

No other production or test file was modified.

## Acceptance criteria evidence

### AC1
- Status: PASS
- Proof: `grep -n "declare(strict_types=1);" app/Services/AgentDeployerService.php` returns line 2. File starts with `<?php` then `declare(strict_types=1);`.

### AC2
- Status: PASS
- Proof: `grep -n "namespace App\\Services;" app/Services/AgentDeployerService.php` present; `grep -n "final class AgentDeployerService" app/Services/AgentDeployerService.php` present on line 10.

### AC3
- Status: PASS
- Proof: File contains `use App\Services\OnChainOSPaymentService;`, `use App\Services\KiloClawClientService;`, `use App\Services\TelegramBotRegistrarService;`. Constructor has three `private readonly` parameters in order: `OnChainOSPaymentService $payment`, `KiloClawClientService $kiloclaw`, `TelegramBotRegistrarService $telegram`. No fully qualified names in the signature.

### AC4
- Status: PASS
- Proof: `public function deploy(array $request): array` declared once on line 20.

### AC5
- Status: PASS
- Proof: Every return in the service file contains the six canonical keys `status`, `stage`, `agent_name`, `error`, `kiloclaw_id`, `session_id`. Five return branches: invalid_request, telegram_invalid, payment_failed, install_failed, deployed. The happy-path test asserts the exact six-key shape via `assertSame`.

### AC6
- Status: PASS
- Proof: Validation loop iterates `['agent_name', 'personality', 'telegram_bot_token', 'amount_usd']` in canonical order, checks non-empty trimmed string for first three and positive int for `amount_usd`. If any fail, returns `['status' => 'invalid_request', 'stage' => 'validate', 'agent_name' => <name or empty string>, 'error' => 'missing or invalid: <keys>', 'kiloclaw_id' => null, 'session_id' => null]`. Tests `test_missing_required_field_returns_invalid_request`, `test_empty_agent_name_returns_invalid_request`, `test_zero_amount_returns_invalid_request` assert the status, stage, error substring, and `callCount === 0` on all three downstream fakes.

### AC7
- Status: PASS
- Proof: `allowlist` is not named in the validation loop. The happy-path test passes `'allowlist' => ''` and reaches the `deployed` branch; the missing-field test drops `personality` but leaves `allowlist` absent implicitly without error. Validation ignores `allowlist` entirely.

### AC8
- Status: PASS
- Proof: The first downstream call in `deploy()` after validation is `$this->telegram->validateToken($request['telegram_bot_token'])`. Test `test_telegram_token_invalid_short_circuits_at_telegram_validate` asserts `$telegramFake->getMeCallCount === 1` while `$onchainosFake->callCount === 0` and `$kiloclawFake->callCount === 0`.

### AC9
- Status: PASS
- Proof: When `validateToken()` returns false, `deploy()` returns `['status' => 'telegram_invalid', 'stage' => 'telegram_validate', 'agent_name' => $agentName, 'error' => 'telegram token invalid', 'kiloclaw_id' => null, 'session_id' => null]`. The short-circuit test verifies both the return shape and that `createCharge()` and `install()` were not called.

### AC10
- Status: PASS
- Proof: After Telegram validation succeeds, `$this->payment->createCharge($request['amount_usd'], $request['agent_name'])` is invoked. Tests confirm ordering via call counts.

### AC11
- Status: PASS
- Proof: `if (($charge['status'] ?? null) !== 'pending')` returns `['status' => 'payment_failed', 'stage' => 'payment', 'agent_name' => $agentName, 'error' => $charge['error'] ?? 'payment not pending', 'kiloclaw_id' => null, 'session_id' => $charge['session_id'] ?? null]` and does not call `install()`. Test `test_payment_not_pending_short_circuits_at_payment` preloads the OnChainOS fake with an exception, asserts status `payment_failed`, substring `transport boom` in error, and `$kiloclawFake->callCount === 0`.

### AC12
- Status: PASS
- Proof: `grep -n "'spawn\.' \. strtolower(\$request\['agent_name'\])" app/Services/AgentDeployerService.php` returns exactly one hit (line 76). The manifest is an array literal with keys `id`, `name`, `version` (`'0.1.0'`), `skills` (`[]`), built inline, then passed to `$this->kiloclaw->install($manifest)`.

### AC13
- Status: PASS
- Proof: `!in_array($install['status'] ?? null, ['ready', 'booting'], true)` triggers `['status' => 'install_failed', 'stage' => 'install', 'agent_name' => $agentName, 'error' => $install['error'] ?? 'install failed', 'kiloclaw_id' => $install['kiloclaw_id'] ?? null, 'session_id' => $charge['session_id']]`. Test `test_install_not_ready_short_circuits_at_install` preloads a `KiloClawException`, asserts status `install_failed`, error contains `transport boom`, and `session_id === 'sess_abc'` (propagated from charge).

### AC14
- Status: PASS
- Proof: Happy-path return is `['status' => 'deployed', 'stage' => 'complete', 'agent_name' => $request['agent_name'], 'error' => null, 'kiloclaw_id' => $install['kiloclaw_id'], 'session_id' => $charge['session_id']]`. Test `test_happy_path_deployed_returns_canonical_shape` asserts exact pinned shape via `assertSame` and verifies all three call counts equal 1.

### AC15
- Status: PASS
- Proof: `awk '/public function deploy/,/^    }$/' app/Services/AgentDeployerService.php | grep -c " new "` returns 0. No instantiation inside the method body.

### AC16
- Status: PASS
- Proof: `awk ... | grep -c "::"` returns 0. No static calls or class constants inside `deploy()`.

### AC17
- Status: PASS
- Proof: `awk ... | grep -c " static "` returns 0. No late static binding.

### AC18
- Status: PASS
- Proof: `grep -cE "(^|\s)(try|catch)(\s|\()" app/Services/AgentDeployerService.php` returns 0. Downstream services already catch their own transport exceptions and return arrays, so the deployer does not need `try/catch`.

### AC19
- Status: PASS
- Proof: `tests/Unit/Services/AgentDeployerServiceTest.php` starts with `<?php`, `declare(strict_types=1);`, `namespace Tests\Unit\Services;`, and declares `final class AgentDeployerServiceTest extends TestCase` where `TestCase` is `PHPUnit\Framework\TestCase` (imported via `use`).

### AC20
- Status: PASS
- Proof: The file declares three in-file fakes (per the parent-instruction override on fake naming to avoid collision with existing global test-namespace classes `FakeOnChainOSClient`, `FakeKiloClawHttpClient`, `FakeTelegramHttpClient` defined in the sibling test files): `final class FakeAgentDeployerOnChainOSClient implements OnChainOSClient`, `final class FakeAgentDeployerKiloClawHttpClient implements KiloClawHttpClient`, `final class FakeAgentDeployerTelegramHttpClient implements TelegramHttpClient`. Each fake exposes public `nextResponse` (or per-method equivalents for Telegram: `getMeNextResponse`, `setWebhookNextResponse`) that tests mutate per case. Spec AC20 explicitly allows "equivalently named per existing test files"; the `FakeAgentDeployer*` prefix is mandatory because redeclaring the exact names would trigger a PHP fatal redeclaration error at test load time.

### AC21
- Status: PASS
- Proof: `grep -c "createMock\|getMockBuilder\|MockObject" tests/Unit/Services/AgentDeployerServiceTest.php` returns 0. Every test constructs one fake of each of the three types, passes them into the three real services (`OnChainOSPaymentService`, `KiloClawClientService`, `TelegramBotRegistrarService`), then passes those three services into `new AgentDeployerService(...)`. Factory `makeStack()` centralizes the wiring.

### AC22
- Status: PASS
- Proof: The seven locked test method names appear verbatim in the file: `test_missing_required_field_returns_invalid_request` (line 46), `test_empty_agent_name_returns_invalid_request` (line 66), `test_zero_amount_returns_invalid_request` (line 85), `test_telegram_token_invalid_short_circuits_at_telegram_validate` (line 104), `test_payment_not_pending_short_circuits_at_payment` (line 122), `test_install_not_ready_short_circuits_at_install` (line 141), `test_happy_path_deployed_returns_canonical_shape` (line 164).

### AC23
- Status: PASS
- Proof: `test_happy_path_deployed_returns_canonical_shape` uses the exact pinned fixture: request `['agent_name' => 'atlas', 'personality' => 'A laconic agent that ships code.', 'telegram_bot_token' => '123:abc', 'amount_usd' => 10, 'allowlist' => '']`, Telegram preload `['ok' => true, 'result' => ['id' => 1]]`, OnChainOS preload `['session_id' => 'sess_abc', 'status' => 'pending', 'expires_at' => '2026-04-14T10:00:00Z']`, KiloClaw preload `['kiloclaw_id' => 'kc_abc', 'status' => 'ready']`, and asserts via `assertSame` against `['status' => 'deployed', 'stage' => 'complete', 'agent_name' => 'atlas', 'error' => null, 'kiloclaw_id' => 'kc_abc', 'session_id' => 'sess_abc']`.

### AC24
- Status: PASS
- Proof: `.agent/tasks/tdd-agent-deployer/artifacts/phpunit-red.txt` EXIT=2 with 7 errors on undefined method `deploy()`, captured while the production file was still the one-line stub (see `artifacts/pre-build-stub.txt`). `.agent/tasks/tdd-agent-deployer/artifacts/phpunit-green.txt` EXIT=0 with `OK (37 tests, 142 assertions)`, captured after the service rewrite.

### AC25
- Status: PASS
- Proof: Final `composer test` reports `OK (37 tests, 142 assertions)` and EXIT=0 (`artifacts/composer-test-final.txt`). 37 >= 37 tests, 142 >= 40 assertions. All 10 prior task status checks (via `task_loop.py status`) report `verdict_overall_status: PASS`: `bootstrap-proof-loop`, `scaffold-service-stubs`, `test-harness`, `landing-mockup-steve-ive`, `tdd-telegram-validate-token`, `wizard-mockup-steve-ive`, `tdd-onchainos-create-charge`, `agents-list-mockup-steve-ive`, `tdd-kiloclaw-install`, `tdd-telegram-set-webhook`. See `artifacts/status-*.txt`.

### AC26
- Status: PASS
- Proof: Only two code files touched in the source tree: `app/Services/AgentDeployerService.php` (rewritten) and `tests/Unit/Services/AgentDeployerServiceTest.php` (new). `phpunit.xml.dist`, `composer.json`, `composer.lock`, `app/Services/OnChainOSPaymentService.php`, `app/Services/KiloClawClientService.php`, `app/Services/TelegramBotRegistrarService.php`, and all sibling test files are unchanged. `.agent/tasks/tdd-agent-deployer/` artifacts and evidence bundles are the only other modifications.

## Commands run
- `composer test` (baseline, red, green, final)
- `php -l` on AgentDeployerService, AgentDeployerServiceTest, and the three sibling services
- `composer dump-autoload -o`
- `python3 .claude/skills/repo-task-proof-loop/scripts/task_loop.py status --task-id <prior>` for all 10 prior tasks
- `python3 .claude/skills/repo-task-proof-loop/scripts/task_loop.py validate --task-id tdd-agent-deployer`
- `python3 .claude/skills/repo-task-proof-loop/scripts/task_loop.py status --task-id tdd-agent-deployer`

## Raw artifacts
- `.agent/tasks/tdd-agent-deployer/raw/build.txt`
- `.agent/tasks/tdd-agent-deployer/raw/test-unit.txt`
- `.agent/tasks/tdd-agent-deployer/raw/lint.txt`
- `.agent/tasks/tdd-agent-deployer/raw/test-integration.txt`
- `.agent/tasks/tdd-agent-deployer/artifacts/baseline-test.txt`
- `.agent/tasks/tdd-agent-deployer/artifacts/pre-build-stub.txt`
- `.agent/tasks/tdd-agent-deployer/artifacts/phpunit-red.txt`
- `.agent/tasks/tdd-agent-deployer/artifacts/phpunit-green.txt`
- `.agent/tasks/tdd-agent-deployer/artifacts/php-lint.txt`
- `.agent/tasks/tdd-agent-deployer/artifacts/composer-dump.txt`
- `.agent/tasks/tdd-agent-deployer/artifacts/composer-test-final.txt`
- `.agent/tasks/tdd-agent-deployer/artifacts/post-build-ls.txt`
- `.agent/tasks/tdd-agent-deployer/artifacts/status-*.txt` (10 prior tasks)
