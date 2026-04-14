# Task Spec: land-laravel-framework

## Metadata
- Task ID: land-laravel-framework
- Created: 2026-04-14T15:21:18+00:00
- Frozen: 2026-04-13
- Repo root: /Users/krutovoy/Projects/hosting-platform
- Wave: B, chokepoint (runs SOLO ahead of other Wave B tasks)
- Authored by: task-spec-freezer subagent

## Guidance sources
- /Users/krutovoy/Projects/hosting-platform/CLAUDE.md (locked decisions: Laravel 12, PHP 8.2, mandate shell, crypto hidden)
- /Users/krutovoy/Projects/hosting-platform/AGENTS.md
- /Users/krutovoy/Projects/hosting-platform/docs/scaffold.md (authoritative step list: section 1 rsync, section 2 prune, section 4 env, section 5 install)
- /Users/krutovoy/Projects/hosting-platform/docs/integrations.md (context for what the preserved services talk to)
- /Users/krutovoy/Projects/hosting-platform/docs/sprint_v0.1.md Day 1 Task 1 "Scaffold"
- /Users/krutovoy/Projects/hosting-platform/composer.json (current minimal harness)
- /Users/krutovoy/Projects/hosting-platform/phpunit.xml.dist (current test runner config)
- ~/Projects/mandate/composer.json, ~/Projects/mandate/bootstrap/app.php, ~/Projects/mandate/config/ (reference Laravel 12 shell)

## Original task statement
Land a working Laravel 12 framework in the hosting-platform repo so that downstream Wave B tasks (`postgres-migrations`, `http-routes-controllers`, `tdd-google-auth-socialite`, `tdd-onchainos-xlayer-client`) can add migrations, routes, controllers, and Google OAuth via Laravel Socialite on top of a booted Laravel application. Preserve byte-for-byte the four existing service classes (`AgentDeployerService`, `OnChainOSPaymentService`, `KiloClawClientService`, `TelegramBotRegistrarService`) plus their six helper/exception files under `app/Services/Telegram/`, `app/Services/OnChainOS/`, and `app/Services/KiloClaw/`, and the five existing test files (`tests/Unit/SmokeTest.php` plus the four `tests/Unit/Services/*ServiceTest.php`). `composer test` must still report at least `OK (37 tests, 142 assertions)` after the task lands.

## Acceptance criteria

### Composer manifest and dependencies
- AC1: `composer.json` `require` block contains `"laravel/framework": "^12.0"`.
- AC2: `composer.json` `require` block contains `"laravel/socialite": "^5.0"`.
- AC3: `composer.json` `require` block still contains `"php": "^8.2"`.
- AC4: `composer.json` `require-dev` block still contains `"phpunit/phpunit": "^11.0"`.
- AC5: `composer.json` `autoload.psr-4` maps `App\\` to `app/` (unchanged key/value).
- AC6: `composer.json` `autoload-dev.psr-4` maps `Tests\\` to `tests/` (unchanged).
- AC7: `composer.json` `scripts.test` runs `vendor/bin/phpunit` (unchanged intent; value may be a string or array entry that invokes phpunit).
- AC8: `composer.json` `scripts` contains a `post-autoload-dump` hook with `Illuminate\\Foundation\\ComposerScripts::postAutoloadDump` and `@php artisan package:discover --ansi` so Laravel package discovery runs after install.
- AC9: `composer.json` `config.allow-plugins` trusts at least the `pestphp/pest-plugin` key set to false OR explicitly allows Laravel-related plugins without blocking install; at minimum the key `allow-plugins` is a non-empty object so composer does not prompt interactively.

### Vendor install and Laravel presence
- AC10: `vendor/laravel/framework/src/Illuminate/Foundation/Application.php` exists after `composer install`.
- AC11: `vendor/laravel/socialite/src/SocialiteManager.php` exists after `composer install`.
- AC12: `vendor/bin/phpunit` exists and is executable.
- AC13: `vendor/autoload.php` exists.

### Laravel bootstrap and application skeleton
- AC14: `bootstrap/app.php` exists and its contents contain the substring `return Application::configure(`.
- AC15: `bootstrap/cache/` directory exists and is writable.
- AC16: `public/index.php` exists and contains `require_once __DIR__.'/../vendor/autoload.php'` or a Laravel 12 equivalent loading `bootstrap/app.php`.
- AC17: `config/app.php` exists.
- AC18: `config/auth.php` exists (Socialite expects a user provider).
- AC19: `config/services.php` exists (Socialite reads Google creds from here).
- AC20: `config/database.php` exists.
- AC21: `config/session.php` exists.
- AC22: `routes/web.php` exists (may be empty or default Laravel welcome route).
- AC23: `routes/api.php` exists (may be empty; `http-routes-controllers` will populate).
- AC24: `routes/console.php` exists.
- AC25: `artisan` executable exists at repo root.
- AC26: `storage/framework/cache/`, `storage/framework/sessions/`, `storage/framework/views/`, and `storage/logs/` directories all exist and are writable.

### Env example
- AC27: `.env.example` exists at repo root.
- AC28: `.env.example` contains `DB_CONNECTION=pgsql` (NOT `sqlite`).
- AC29: `.env.example` contains all eleven placeholder keys: `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI`, `KILOCLAW_API_URL`, `KILOCLAW_API_KEY`, `ONCHAINOS_API_URL`, `ONCHAINOS_MERCHANT_ID`, `ONCHAINOS_WEBHOOK_SECRET`, `XLAYER_RPC_URL`, `XLAYER_CHAIN_ID`, `TELEGRAM_WEBHOOK_BASE`.
- AC30: `.env.example` also contains `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL` (standard Laravel keys needed by `php artisan` to boot).

### Artisan must work
- AC31: `php -d memory_limit=1G artisan --version` exits 0 and stdout contains the string `Laravel Framework 12`.
- AC32: `php -d memory_limit=1G artisan list` exits 0.

### Test suite preservation (strict byte identity via sha256)
The builder MUST capture sha256 of each of the following 15 files BEFORE any rsync or edit, and confirm the sha256 is unchanged AFTER the task completes.

- AC33: `app/Services/AgentDeployerService.php` sha256 unchanged.
- AC34: `app/Services/OnChainOSPaymentService.php` sha256 unchanged.
- AC35: `app/Services/KiloClawClientService.php` sha256 unchanged.
- AC36: `app/Services/TelegramBotRegistrarService.php` sha256 unchanged.
- AC37: `app/Services/Telegram/TelegramHttpClient.php` sha256 unchanged.
- AC38: `app/Services/Telegram/TelegramTransportException.php` sha256 unchanged.
- AC39: `app/Services/OnChainOS/OnChainOSClient.php` sha256 unchanged.
- AC40: `app/Services/OnChainOS/OnChainOSException.php` sha256 unchanged.
- AC41: `app/Services/KiloClaw/KiloClawHttpClient.php` sha256 unchanged.
- AC42: `app/Services/KiloClaw/KiloClawException.php` sha256 unchanged.
- AC43: `tests/Unit/SmokeTest.php` sha256 unchanged.
- AC44: `tests/Unit/Services/AgentDeployerServiceTest.php` sha256 unchanged.
- AC45: `tests/Unit/Services/OnChainOSPaymentServiceTest.php` sha256 unchanged.
- AC46: `tests/Unit/Services/KiloClawClientServiceTest.php` sha256 unchanged.
- AC47: `tests/Unit/Services/TelegramBotRegistrarServiceTest.php` sha256 unchanged.

### Test runner still green
- AC48: `phpunit.xml.dist` still exists and still contains a `<testsuite name="Unit">` element pointing at `./tests/Unit`.
- AC49: `composer test` exits 0 and stdout reports at least `OK (37 tests, 142 assertions)` (more is fine, fewer is a FAIL).

### Governance artifacts unchanged
- AC50: `CLAUDE.md`, `AGENTS.md` sha256 unchanged.
- AC51: All five files under `docs/` (`agent_spawn_prd.md`, `detailed_spec_draft.md`, `integrations.md`, `scaffold.md`, `sprint_v0.1.md`) sha256 unchanged.
- AC52: All five files under `design/` (`agent-wallet.html`, `agents.html`, `landing.html`, `running.html`, `wizard.html`) sha256 unchanged.
- AC53: All 13 prior proof-loop tasks whose `verdict.json` currently reports `overall_verdict: PASS` still report `overall_verdict: PASS` after the task lands. The 13 tasks are: `bootstrap-proof-loop`, `test-harness`, `scaffold-service-stubs`, `landing-mockup-steve-ive`, `wizard-mockup-steve-ive`, `running-state-mockup-steve-ive`, `agents-list-mockup-steve-ive`, `agent-wallet-disclosure-mockup-steve-ive`, `tdd-agent-deployer`, `tdd-kiloclaw-install`, `tdd-onchainos-create-charge`, `tdd-telegram-set-webhook`, `tdd-telegram-validate-token`.
- AC54: No new top-level directory is created except for the Laravel shell set {`bootstrap/`, `config/`, `public/`, `resources/`, `routes/`, `storage/`, `database/`, `vendor/`}. Any other new top-level entry is a FAIL.
- AC55: `.env` file is NOT created at the repo root (only `.env.example`). The verifier MUST NOT require `.env`.

## Constraints
- Laravel version locked at `^12.0` per CLAUDE.md; do not substitute 11 or 10.
- PHP locked at `^8.2`.
- `composer install` is AUTHORIZED and EXPECTED; it may download several hundred MB and take minutes. Do not abort on slow install. Do not run with `--no-dev` (PHPUnit lives in require-dev).
- Rsync safety: when copying the Laravel shell from `~/Projects/mandate/`, ALWAYS use `rsync --ignore-existing` so existing files in `hosting-platform` are never clobbered. Include only the directories we need from mandate: `bootstrap/`, `config/`, `public/`, `routes/`, `resources/` (minimal), `database/migrations/` (empty or skip), `storage/` skeleton, `artisan`, and the non-conflicting subdirs of `app/` (`Http/`, `Models/`, `Providers/`, `Console/`, `Exceptions/`). EXCLUDE `app/Services/`, `tests/`, `composer.json`, `composer.lock`, `phpunit.xml`, `phpunit.xml.dist`, `.env`, `.env.example`, `CLAUDE.md`, `AGENTS.md`, `docs/`, `design/`, `.agent/`, `.claude/`, `.codex/`, `.git/`, `vendor/`, `node_modules/`, `public/build/`, `bootstrap/cache/*.php`, `storage/logs/*`, `storage/framework/cache/*`, `storage/framework/sessions/*`, `storage/framework/views/*`.
- `composer.json`: do NOT copy mandate's. Extend the existing minimal `composer.json` in place with the required new keys (add `laravel/framework`, `laravel/socialite`, `post-autoload-dump` script, `allow-plugins` entries). Keep `phpunit/phpunit ^11.0` in require-dev. Keep PSR-4 and `scripts.test` intact.
- `phpunit.xml.dist`: do NOT overwrite. If Laravel's `phpunit.xml` is rsynced accidentally, delete it via `trash`, not `rm -rf`. The existing `<testsuite name="Unit">` pointing at `./tests/Unit` is load-bearing.
- `.env.example`: synthesize fresh from scaffold.md section 4 with `DB_CONNECTION=pgsql` and the eleven required keys. Do NOT copy mandate's `.env.example`.
- Mandate files that sneak in and must be stripped after rsync: any `app/Http/Controllers/Api/*` file that references mandate-specific services (`PolicyEngineService`, `QuotaManagerService`, `IntentStateMachineService`, `EnvelopeVerifierService`, `CircuitBreakerService`, etc.), any `app/Console/Commands/Mandate*` commands, any `config/mandate.php`, any `config/horizon.php`, `config/sanctum.php` if it pulls in deps we do not list. Laravel 12 MUST still boot after stripping; confirm via `php artisan --version`.
- Do NOT add `inertiajs/inertia-laravel`, `laravel/sanctum`, `laravel/horizon`, `predis/predis`, `tightenco/ziggy`, or any other mandate dep to composer.json. Only `laravel/framework` and `laravel/socialite`.
- Do NOT run `git init`, `gh`, DNS, or Coolify commands. This is a local-only task.
- Do NOT run `php artisan migrate` (Postgres is not configured yet; `postgres-migrations` task will wire it).
- Deletions MUST go through `trash`, not `rm -rf`, per CLAUDE.md guardrails.
- No em dashes in any authored file.
- MUST NOT touch the 15 preserved files (10 services, 5 tests). Any edit, even whitespace, fails AC33 through AC47.
- MUST NOT touch `CLAUDE.md`, `AGENTS.md`, `docs/*`, `design/*`, `.claude/`, `.codex/`, any folder under `.agent/tasks/` except `land-laravel-framework/` itself.
- Parallel spec-freezers for `postgres-migrations`, `http-routes-controllers`, `tdd-google-auth-socialite`, `tdd-onchainos-xlayer-client` are running concurrently. Do not touch their artifacts.

## Non-goals
- Google OAuth controller, route, or test implementation (owned by `tdd-google-auth-socialite`).
- Any database migration beyond what Laravel's default scaffold ships (owned by `postgres-migrations`).
- Any API or web controller beyond what Laravel's default scaffold ships (owned by `http-routes-controllers`).
- OKX / X-Layer RPC client (owned by `tdd-onchainos-xlayer-client`).
- Frontend (Inertia, React, Tailwind). Not in scope for this task.
- Deploy to Coolify, DNS, TLS (owned by a later ops task).
- Running `php artisan migrate` or provisioning a Postgres instance.
- Creating a git repo, initial commit, or pushing to GitHub.
- Any edits to the 15 preserved files or any governance doc.

## Verification plan

### Composer manifest checks (AC1 to AC9)
```bash
cd /Users/krutovoy/Projects/hosting-platform
python3 -c "import json; c = json.load(open('composer.json')); print('laravel/framework', c['require'].get('laravel/framework')); print('laravel/socialite', c['require'].get('laravel/socialite')); print('php', c['require'].get('php')); print('phpunit', c['require-dev'].get('phpunit/phpunit')); print('psr4', c['autoload']['psr-4']); print('psr4-dev', c['autoload-dev']['psr-4']); print('scripts.test', c['scripts'].get('test')); print('post-autoload-dump', c['scripts'].get('post-autoload-dump')); print('allow-plugins', c['config'].get('allow-plugins'))"
```

### Vendor install (AC10 to AC13)
```bash
cd /Users/krutovoy/Projects/hosting-platform
composer install 2>&1 | tail -20
test -f vendor/laravel/framework/src/Illuminate/Foundation/Application.php && echo AC10_OK
test -f vendor/laravel/socialite/src/SocialiteManager.php && echo AC11_OK
test -x vendor/bin/phpunit && echo AC12_OK
test -f vendor/autoload.php && echo AC13_OK
```

### Laravel skeleton (AC14 to AC26)
```bash
cd /Users/krutovoy/Projects/hosting-platform
grep -q 'return Application::configure(' bootstrap/app.php && echo AC14_OK
test -d bootstrap/cache && test -w bootstrap/cache && echo AC15_OK
test -f public/index.php && echo AC16_OK
test -f config/app.php && echo AC17_OK
test -f config/auth.php && echo AC18_OK
test -f config/services.php && echo AC19_OK
test -f config/database.php && echo AC20_OK
test -f config/session.php && echo AC21_OK
test -f routes/web.php && echo AC22_OK
test -f routes/api.php && echo AC23_OK
test -f routes/console.php && echo AC24_OK
test -f artisan && echo AC25_OK
for d in storage/framework/cache storage/framework/sessions storage/framework/views storage/logs; do test -d "$d" && test -w "$d" && echo "$d_OK"; done
```

### Env example (AC27 to AC30)
```bash
cd /Users/krutovoy/Projects/hosting-platform
test -f .env.example && echo AC27_OK
grep -q '^DB_CONNECTION=pgsql' .env.example && echo AC28_OK
for k in GOOGLE_CLIENT_ID GOOGLE_CLIENT_SECRET GOOGLE_REDIRECT_URI KILOCLAW_API_URL KILOCLAW_API_KEY ONCHAINOS_API_URL ONCHAINOS_MERCHANT_ID ONCHAINOS_WEBHOOK_SECRET XLAYER_RPC_URL XLAYER_CHAIN_ID TELEGRAM_WEBHOOK_BASE; do grep -q "^$k=" .env.example && echo "$k OK"; done
for k in APP_NAME APP_ENV APP_KEY APP_DEBUG APP_URL; do grep -q "^$k=" .env.example && echo "$k OK"; done
```

### Artisan boot (AC31, AC32)
```bash
cd /Users/krutovoy/Projects/hosting-platform
php -d memory_limit=1G artisan --version | tee /tmp/artisan-version.txt
grep -q 'Laravel Framework 12' /tmp/artisan-version.txt && echo AC31_OK
php -d memory_limit=1G artisan list > /dev/null && echo AC32_OK
```

### Preserved-file sha256 identity (AC33 to AC47)
The builder MUST write a manifest of expected hashes BEFORE any work:
```bash
cd /Users/krutovoy/Projects/hosting-platform
shasum -a 256 \
  app/Services/AgentDeployerService.php \
  app/Services/OnChainOSPaymentService.php \
  app/Services/KiloClawClientService.php \
  app/Services/TelegramBotRegistrarService.php \
  app/Services/Telegram/TelegramHttpClient.php \
  app/Services/Telegram/TelegramTransportException.php \
  app/Services/OnChainOS/OnChainOSClient.php \
  app/Services/OnChainOS/OnChainOSException.php \
  app/Services/KiloClaw/KiloClawHttpClient.php \
  app/Services/KiloClaw/KiloClawException.php \
  tests/Unit/SmokeTest.php \
  tests/Unit/Services/AgentDeployerServiceTest.php \
  tests/Unit/Services/OnChainOSPaymentServiceTest.php \
  tests/Unit/Services/KiloClawClientServiceTest.php \
  tests/Unit/Services/TelegramBotRegistrarServiceTest.php \
  > /tmp/preserved-before.txt
```
After the task lands, re-hash and diff:
```bash
shasum -a 256 -c /tmp/preserved-before.txt
```
Exit 0 means all 15 files unchanged.

### Test runner (AC48, AC49)
```bash
cd /Users/krutovoy/Projects/hosting-platform
grep -q 'testsuite name="Unit"' phpunit.xml.dist && echo AC48_OK
composer test 2>&1 | tee /tmp/composer-test.txt
grep -E 'OK \(([0-9]+) tests, ([0-9]+) assertions\)' /tmp/composer-test.txt
python3 -c "import re; m = re.search(r'OK \\((\\d+) tests, (\\d+) assertions\\)', open('/tmp/composer-test.txt').read()); assert m and int(m.group(1)) >= 37 and int(m.group(2)) >= 142, 'FAIL'; print('AC49_OK', m.group(0))"
```

### Governance unchanged (AC50 to AC52)
```bash
cd /Users/krutovoy/Projects/hosting-platform
shasum -a 256 CLAUDE.md AGENTS.md docs/*.md design/*.html > /tmp/gov-after.txt
diff /tmp/gov-before.txt /tmp/gov-after.txt && echo GOVERNANCE_OK
```
(The builder captures `gov-before.txt` at task start.)

### Prior tasks still PASS (AC53)
```bash
cd /Users/krutovoy/Projects/hosting-platform
for t in bootstrap-proof-loop test-harness scaffold-service-stubs landing-mockup-steve-ive wizard-mockup-steve-ive running-state-mockup-steve-ive agents-list-mockup-steve-ive agent-wallet-disclosure-mockup-steve-ive tdd-agent-deployer tdd-kiloclaw-install tdd-onchainos-create-charge tdd-telegram-set-webhook tdd-telegram-validate-token; do python3 -c "import json; v = json.load(open('.agent/tasks/$t/verdict.json'))['overall_verdict']; assert v == 'PASS', '$t: ' + v; print('$t PASS')"; done
```

### No stray new top-level dirs (AC54)
```bash
cd /Users/krutovoy/Projects/hosting-platform
ls -1 | sort > /tmp/top-after.txt
diff /tmp/top-before.txt /tmp/top-after.txt
```
Only these lines may appear as additions: `bootstrap`, `config`, `public`, `resources`, `routes`, `storage`, `database`, `.env.example`, `artisan`, `vendor`, `composer.lock`.

### Env file absence (AC55)
```bash
test ! -f /Users/krutovoy/Projects/hosting-platform/.env && echo AC55_OK
```

## Assumptions recorded
- Mandate's `~/Projects/mandate/` is the reference Laravel 12 shell and contains `bootstrap/app.php`, full `config/`, `public/index.php`, `routes/*`, and `artisan`. Confirmed by `ls`.
- `phpunit/phpunit ^11.0` is compatible with Laravel 12 (mandate uses `^11.5.3` against `laravel/framework ^12.0`, same minor).
- Mandate's top-level `app/Services/` will not be rsynced (excluded). Mandate's `app/Http/` will be rsynced but stripped of mandate-specific controllers after the copy.
- The verifier is allowed to consume 5 to 10 minutes of wall clock on `composer install`. No network tests beyond composer. No DNS. No external HTTP.
- The `composer test` baseline of 37 tests / 142 assertions comes from the frozen state after `test-harness`, `scaffold-service-stubs`, and the 5 TDD service tasks landed. Laravel's own default tests are not expected to run because `phpunit.xml.dist` only points at `./tests/Unit`.
