# Evidence Bundle: land-laravel-framework

## Summary
- Overall status: PASS
- Last updated: 2026-04-13

## Acceptance criteria evidence

### AC1 `laravel/framework ^12.0` in composer.json require
- Status: PASS
- Proof: `composer.json` require block contains `"laravel/framework": "^12.0"`. Verified via `python3 -c "import json; print(json.load(open('composer.json'))['require']['laravel/framework'])"` => `^12.0`. `php artisan --version` => `Laravel Framework 12.56.0` (see `artifacts/artisan-version.txt`).

### AC2 `laravel/socialite ^5.0` in composer.json require
- Status: PASS
- Proof: `composer.json` require block contains `"laravel/socialite": "^5.0"`. `vendor/laravel/socialite/src/SocialiteManager.php` exists after composer install. Package discovery confirmed `laravel/socialite ... DONE` (see `artifacts/composer-install.txt`).

### AC3 `php ^8.2` preserved in require
- Status: PASS
- Proof: `composer.json` require block contains `"php": "^8.2"`.

### AC4 `phpunit/phpunit ^11.0` preserved in require-dev
- Status: PASS
- Proof: `composer.json` require-dev block contains `"phpunit/phpunit": "^11.0"`. Runtime PHPUnit 11.5.55 confirmed in `artifacts/post-install-test.txt`.

### AC5 `autoload.psr-4` maps `App\\` to `app/`
- Status: PASS
- Proof: `composer.json` autoload.psr-4 is `{"App\\": "app/"}` (unchanged from baseline).

### AC6 `autoload-dev.psr-4` maps `Tests\\` to `tests/`
- Status: PASS
- Proof: `composer.json` autoload-dev.psr-4 is `{"Tests\\": "tests/"}` (unchanged).

### AC7 `scripts.test` invokes phpunit
- Status: PASS
- Proof: `composer.json` scripts.test is `@php vendor/bin/phpunit`. `composer test` output confirms it runs phpunit (see `artifacts/post-install-test.txt`).

### AC8 `post-autoload-dump` hook present
- Status: PASS
- Proof: `composer.json` scripts.post-autoload-dump array contains both `Illuminate\\Foundation\\ComposerScripts::postAutoloadDump` and `@php artisan package:discover --ansi`. The live run output `artifacts/composer-install.txt` shows `> @php artisan package:discover --ansi` executing and discovering `laravel/socialite`, `nesbot/carbon`, `nunomaduro/termwind` successfully.

### AC9 `allow-plugins` is a non-empty object
- Status: PASS
- Proof: `composer.json` config.allow-plugins is `{"pestphp/pest-plugin": false, "php-http/discovery": true}`. Non-empty; composer install ran non-interactively without plugin prompts.

### AC10 Laravel framework Application.php exists
- Status: PASS
- Proof: `test -f vendor/laravel/framework/src/Illuminate/Foundation/Application.php` => OK.

### AC11 Socialite SocialiteManager.php exists
- Status: PASS
- Proof: `test -f vendor/laravel/socialite/src/SocialiteManager.php` => OK.

### AC12 `vendor/bin/phpunit` exists and is executable
- Status: PASS
- Proof: `test -x vendor/bin/phpunit` => OK.

### AC13 `vendor/autoload.php` exists
- Status: PASS
- Proof: `test -f vendor/autoload.php` => OK.

### AC14 `bootstrap/app.php` returns `Application::configure(`
- Status: PASS
- Proof: `grep -q 'return Application::configure(' bootstrap/app.php` => OK.

### AC15 `bootstrap/cache/` directory exists and is writable
- Status: PASS
- Proof: `test -d bootstrap/cache && test -w bootstrap/cache` => OK.

### AC16 `public/index.php` exists and loads autoload + bootstrap
- Status: PASS
- Proof: `public/index.php` contains `require __DIR__.'/../vendor/autoload.php'` and `require_once __DIR__.'/../bootstrap/app.php'`. Standard Laravel 12 entrypoint.

### AC17 `config/app.php` exists
- Status: PASS
- Proof: `test -f config/app.php` => OK.

### AC18 `config/auth.php` exists
- Status: PASS
- Proof: `test -f config/auth.php` => OK.

### AC19 `config/services.php` exists
- Status: PASS
- Proof: `test -f config/services.php` => OK.

### AC20 `config/database.php` exists
- Status: PASS
- Proof: `test -f config/database.php` => OK.

### AC21 `config/session.php` exists
- Status: PASS
- Proof: `test -f config/session.php` => OK.

### AC22 `routes/web.php` exists
- Status: PASS
- Proof: `test -f routes/web.php` => OK. Rewritten to a minimal stub with a single `/` route.

### AC23 `routes/api.php` exists
- Status: PASS
- Proof: `test -f routes/api.php` => OK. Rewritten to an empty stub (to be populated by `http-routes-controllers`).

### AC24 `routes/console.php` exists
- Status: PASS
- Proof: `test -f routes/console.php` => OK. Kept rsynced Laravel default `inspire` command.

### AC25 `artisan` executable exists at repo root
- Status: PASS
- Proof: `test -f artisan` => OK. `php artisan --version` succeeds.

### AC26 storage/framework/{cache,sessions,views} + storage/logs exist and writable
- Status: PASS
- Proof: All four directories exist and `test -w` returns OK for each.

### AC27 `.env.example` exists at repo root
- Status: PASS
- Proof: `test -f .env.example` => OK.

### AC28 `.env.example` contains `DB_CONNECTION=pgsql`
- Status: PASS
- Proof: `grep -q '^DB_CONNECTION=pgsql' .env.example` => OK.

### AC29 `.env.example` contains eleven integration placeholder keys
- Status: PASS
- Proof: grep confirms `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI`, `KILOCLAW_API_URL`, `KILOCLAW_API_KEY`, `ONCHAINOS_API_URL`, `ONCHAINOS_MERCHANT_ID`, `ONCHAINOS_WEBHOOK_SECRET`, `XLAYER_RPC_URL`, `XLAYER_CHAIN_ID`, `TELEGRAM_WEBHOOK_BASE` all present.

### AC30 `.env.example` contains five standard Laravel keys
- Status: PASS
- Proof: grep confirms `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL` all present.

### AC31 `php artisan --version` reports Laravel Framework 12
- Status: PASS
- Proof: `artifacts/artisan-version.txt` contains `Laravel Framework 12.56.0` and `EXIT=0`.

### AC32 `php artisan list` exits 0
- Status: PASS
- Proof: `artifacts/artisan-list.txt` ends with `EXIT=0` after a full command list.

### AC33..AC47 Preserved service and test files sha256 unchanged
- Status: PASS
- Proof: `diff artifacts/pre-build-sha256.txt artifacts/post-build-sha256.txt` exits 0. All 15 of these files (plus the additional 13 governance files, total 28 tracked) have identical sha256 before and after the task:
  - app/Services/AgentDeployerService.php (AC33)
  - app/Services/OnChainOSPaymentService.php (AC34)
  - app/Services/KiloClawClientService.php (AC35)
  - app/Services/TelegramBotRegistrarService.php (AC36)
  - app/Services/Telegram/TelegramHttpClient.php (AC37)
  - app/Services/Telegram/TelegramTransportException.php (AC38)
  - app/Services/OnChainOS/OnChainOSClient.php (AC39)
  - app/Services/OnChainOS/OnChainOSException.php (AC40)
  - app/Services/KiloClaw/KiloClawHttpClient.php (AC41)
  - app/Services/KiloClaw/KiloClawException.php (AC42)
  - tests/Unit/SmokeTest.php (AC43)
  - tests/Unit/Services/AgentDeployerServiceTest.php (AC44)
  - tests/Unit/Services/OnChainOSPaymentServiceTest.php (AC45)
  - tests/Unit/Services/KiloClawClientServiceTest.php (AC46)
  - tests/Unit/Services/TelegramBotRegistrarServiceTest.php (AC47)

### AC48 `phpunit.xml.dist` still contains `<testsuite name="Unit">`
- Status: PASS
- Proof: `grep -q 'testsuite name="Unit"' phpunit.xml.dist` => OK. sha256 unchanged (captured in pre/post build snapshots).

### AC49 `composer test` exits 0 with at least 37 tests / 142 assertions
- Status: PASS
- Proof: `artifacts/post-install-test.txt` shows `OK (37 tests, 142 assertions)` and `EXIT=0`. Baseline (`artifacts/baseline-test.txt`) was also `OK (37 tests, 142 assertions)`. No regression.

### AC50 `CLAUDE.md`, `AGENTS.md` sha256 unchanged
- Status: PASS
- Proof: Present in both pre-build and post-build sha256 files; `diff` exit 0.

### AC51 Five `docs/` files sha256 unchanged
- Status: PASS
- Proof: `docs/agent_spawn_prd.md`, `docs/detailed_spec_draft.md`, `docs/integrations.md`, `docs/scaffold.md`, `docs/sprint_v0.1.md` all captured in pre and post; `diff` exit 0.

### AC52 Five `design/` files sha256 unchanged
- Status: PASS
- Proof: `design/landing.html`, `design/wizard.html`, `design/agents.html`, `design/running.html`, `design/agent-wallet.html` all captured in pre and post; `diff` exit 0.

### AC53 All 13 prior proof-loop tasks still PASS
- Status: PASS
- Proof: Ran `task_loop.py status --task-id <id>` for each of the 13 tasks. Every result reports `verdict_overall_status: PASS`. Per-task captures in `artifacts/status-<id>.txt`:
  - bootstrap-proof-loop: PASS
  - test-harness: PASS
  - scaffold-service-stubs: PASS
  - landing-mockup-steve-ive: PASS
  - wizard-mockup-steve-ive: PASS
  - running-state-mockup-steve-ive: PASS
  - agents-list-mockup-steve-ive: PASS
  - agent-wallet-disclosure-mockup-steve-ive: PASS
  - tdd-agent-deployer: PASS
  - tdd-kiloclaw-install: PASS
  - tdd-onchainos-create-charge: PASS
  - tdd-telegram-set-webhook: PASS
  - tdd-telegram-validate-token: PASS

### AC54 No new top-level dir outside Laravel shell set
- Status: PASS
- Proof: `diff artifacts/top-before.txt artifacts/top-after.txt` shows additions limited to `.env.example` (file), `artisan` (file), and the directories `bootstrap/`, `config/`, `database/`, `public/`, `resources/`, `routes/`, `storage/`. All seven added directories are inside the Laravel shell set {bootstrap, config, public, resources, routes, storage, database, vendor} (vendor/ existed at baseline as a stub directory; it was trashed and recreated by composer install). No stray new top-level directory.

### AC55 `.env` file is NOT created
- Status: PASS
- Proof: `test ! -f .env` => OK. Only `.env.example` exists.

## Commands run
- `shasum -a 256 ... > artifacts/pre-build-sha256.txt` (28 files)
- `composer test > artifacts/baseline-test.txt 2>&1` (baseline)
- `ls -1A | sort > artifacts/top-before.txt`
- `Write composer.json`
- `rsync -av --ignore-existing ... ~/Projects/mandate/ ./ > artifacts/rsync.txt`
- `trash <19 non-Laravel top-level entries>`
- `Write bootstrap/app.php, bootstrap/providers.php, routes/web.php, routes/api.php`
- `trash app/Http/Controllers/{Api,Auth,Web}/ app/Http/Middleware/{HandleInertiaRequests,RuntimeKeyAuth,RuntimeKeyOrX402,X402PaymentGate}.php app/Console/Commands/ app/Providers/HorizonServiceProvider.php app/Jobs/ app/Enums/ app/Rules/ app/Models/<11 mandate models> config/{horizon,sanctum,mandate}.php database/seeders/TokenRegistrySeeder.php database/database.sqlite`
- `Edit app/Models/User.php (strip agents() relation)`
- `mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache`
- `Write .env.example`
- `trash composer.lock vendor/`
- `composer install --no-interaction --prefer-dist > artifacts/composer-install.txt`
- `php -d memory_limit=1G artisan --version > artifacts/artisan-version.txt`
- `php -d memory_limit=1G artisan list > artifacts/artisan-list.txt`
- `composer test > artifacts/post-install-test.txt 2>&1` (post-install)
- `shasum -a 256 ... > artifacts/post-build-sha256.txt`
- `diff artifacts/pre-build-sha256.txt artifacts/post-build-sha256.txt` (zero diff)
- `ls -1A | sort > artifacts/top-after.txt`
- `du -sh vendor/` => 49M
- 13x `task_loop.py status --task-id <id> > artifacts/status-<id>.txt`

## Raw artifacts
- .agent/tasks/land-laravel-framework/raw/build.txt
- .agent/tasks/land-laravel-framework/raw/test-unit.txt
- .agent/tasks/land-laravel-framework/raw/test-integration.txt
- .agent/tasks/land-laravel-framework/raw/lint.txt
- .agent/tasks/land-laravel-framework/raw/screenshot-1.png
- .agent/tasks/land-laravel-framework/artifacts/pre-build-sha256.txt
- .agent/tasks/land-laravel-framework/artifacts/post-build-sha256.txt
- .agent/tasks/land-laravel-framework/artifacts/baseline-test.txt
- .agent/tasks/land-laravel-framework/artifacts/post-install-test.txt
- .agent/tasks/land-laravel-framework/artifacts/composer-install.txt
- .agent/tasks/land-laravel-framework/artifacts/artisan-version.txt
- .agent/tasks/land-laravel-framework/artifacts/artisan-list.txt
- .agent/tasks/land-laravel-framework/artifacts/rsync.txt
- .agent/tasks/land-laravel-framework/artifacts/top-before.txt
- .agent/tasks/land-laravel-framework/artifacts/top-after.txt
- .agent/tasks/land-laravel-framework/artifacts/status-*.txt (13 files)

## Known gaps
- None. All 55 acceptance criteria report PASS.
