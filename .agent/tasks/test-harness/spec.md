# Task Spec: test-harness

## Metadata
- Task ID: test-harness
- Created: 2026-04-14T08:35:34+00:00
- Repo root: /Users/krutovoy/Projects/hosting-platform
- Working directory at init: /Users/krutovoy/Projects/hosting-platform

## Guidance sources
- /Users/krutovoy/Projects/hosting-platform/CLAUDE.md (TDD mandatory; PHP 8.2 mandate; mandate parity)
- /Users/krutovoy/Projects/hosting-platform/AGENTS.md
- /Users/krutovoy/Projects/hosting-platform/docs/scaffold.md (§0 prereqs, §3 service stubs, §5 install discipline)
- /Users/krutovoy/Projects/hosting-platform/docs/sprint_v0.1.md (Day 1 task 1 references composer test passing)
- /Users/krutovoy/Projects/hosting-platform/app/Services/AgentDeployerService.php (existing stub that must autoload)
- /Users/krutovoy/Projects/hosting-platform/app/Services/KiloClawClientService.php
- /Users/krutovoy/Projects/hosting-platform/app/Services/TelegramBotRegistrarService.php
- /Users/krutovoy/Projects/hosting-platform/app/Services/OnChainOSPaymentService.php
- /Users/krutovoy/Projects/hosting-platform/.agent/tasks/test-harness/spec.md (placeholder being rewritten)

## Original task statement
Land a minimal PHPUnit test harness in hosting-platform so all future backend tasks can do TDD per CLAUDE.md. Create composer.json (require-dev phpunit/phpunit ^11), phpunit.xml.dist with one Unit testsuite pointing at tests/Unit, tests/Unit/SmokeTest.php with a single failing-then-passing assertion, and run composer install + vendor/bin/phpunit. Done when vendor/bin/phpunit exits 0 with the smoke test passing. PSR-4 autoload App\\ -> app/. Do not rsync mandate. Do not git init. Do not touch external services. Do not create routes, controllers, models, or migrations.

## Acceptance criteria

- AC1: `composer.json` exists at `/Users/krutovoy/Projects/hosting-platform/composer.json` and is valid JSON.
- AC2: `composer.json` declares `"type": "project"` and a `"name"` field (lowercase, vendor/package form).
- AC3: `composer.json` `require.php` is exactly `"^8.2"` (matches CLAUDE.md mandate).
- AC4: `composer.json` `require-dev` contains exactly one entry: `"phpunit/phpunit": "^11.0"`. No Laravel, no Pest, no Mockery, no Faker.
- AC5: `composer.json` declares PSR-4 autoload mapping `App\\` -> `app/`.
- AC6: `composer.json` declares PSR-4 autoload-dev mapping `Tests\\` -> `tests/`.
- AC7: `composer.json` declares `scripts.test` equal to `"vendor/bin/phpunit"` (exact string).
- AC8: `phpunit.xml.dist` exists at repo root (with the `.dist` suffix; not `phpunit.xml`).
- AC9: `phpunit.xml.dist` references PHPUnit 11 schema `https://schema.phpunit.de/11.0/phpunit.xsd`, sets `bootstrap="vendor/autoload.php"`, and sets `colors="true"`.
- AC10: `phpunit.xml.dist` defines exactly one testsuite named `Unit` whose directory is `./tests/Unit`.
- AC11: Directory `tests/` exists at repo root.
- AC12: Directory `tests/Unit/` exists.
- AC13: `tests/Unit/SmokeTest.php` exists, declares `namespace Tests\Unit;`, extends `PHPUnit\Framework\TestCase`, and contains exactly one test method named `test_smoke_assertion_is_true` whose final body asserts `true`.
- AC14: `composer install` was run successfully; `vendor/` directory exists at repo root.
- AC15: `composer.lock` exists at repo root.
- AC16: `vendor/bin/phpunit` exists and is executable.
- AC17: `composer test` exits 0 against the current code.
- AC18: `vendor/bin/phpunit --testdox` exits 0 and reports `OK (1 test, 1 assertion)`.
- AC19: TDD discipline followed: a raw artifact in `.agent/tasks/test-harness/artifacts/` captures the pre-flip phpunit FAILURE output (smoke test asserting `false`) AND a separate artifact captures the post-flip phpunit SUCCESS output (smoke test asserting `true`). Both artifacts must exist; verifier replays them, not the live state.
- AC20: All four service stubs autoload via the new PSR-4 mapping. After `composer dump-autoload`, a verifier script that does `require __DIR__.'/vendor/autoload.php'; class_exists(\App\Services\AgentDeployerService::class, true);` returns true for all of: `AgentDeployerService`, `KiloClawClientService`, `TelegramBotRegistrarService`, `OnChainOSPaymentService`.
- AC21: `php -l` on `tests/Unit/SmokeTest.php` and on each of the four `app/Services/*.php` files reports `No syntax errors detected`.
- AC22: No collateral scaffolding. Repo root contains no `.git/`, `node_modules/`, `public/`, `bootstrap/`, `config/`, `resources/`, `routes/`, `database/`, `storage/`, `.env`, `.env.example`, `package.json`, `vite.config.*`, or any Laravel framework file. The only top-level additions beyond pre-existing files (`.agent/`, `.claude/`, `.codex/`, `app/`, `docs/`, `AGENTS.md`, `CLAUDE.md`, `.DS_Store`) are: `composer.json`, `composer.lock`, `phpunit.xml.dist`, `tests/`, `vendor/`.
- AC23: No file authored by this task contains an em dash (the U+2014 character). Verifier greps `composer.json`, `phpunit.xml.dist`, `tests/Unit/SmokeTest.php`, and `spec.md` for U+2014.

## Constraints

- Composer is available at `/Users/krutovoy/.config/herd-lite/bin/composer` (Composer 2.8.3). PHP 8.4.1 is available.
- PHP version constraint in `composer.json` must be `^8.2` to match CLAUDE.md mandate (PHP 8.2 minimum).
- Use `phpunit/phpunit ^11.0` as the only require-dev dependency for now. No Laravel framework, no Pest, no Mockery, no Faker. Minimal harness only.
- Autoload PSR-4 mapping `App\\` -> `app/` (so the four service stubs from scaffold-service-stubs autoload).
- Autoload-dev PSR-4 mapping `Tests\\` -> `tests/`.
- Use `composer test` script defined as `vendor/bin/phpunit`.
- `phpunit.xml.dist` (NOT phpunit.xml; use the .dist suffix so it can be checked into git later) with one testsuite named `Unit` pointing at `./tests/Unit`. `colors="true"`. `bootstrap="vendor/autoload.php"`. PHPUnit 11 schema (xsi schemaLocation `https://schema.phpunit.de/11.0/phpunit.xsd`).
- One smoke test at `tests/Unit/SmokeTest.php` in namespace `Tests\Unit`, extending `PHPUnit\Framework\TestCase`, with one test method `test_smoke_assertion_is_true` that asserts `true`. The point is to prove the harness wires together; not to test product code.
- Per the TDD discipline: the smoke test must FIRST be written to fail (e.g., `assertTrue(false)`), the builder must run phpunit, observe the red, then flip to `assertTrue(true)` and observe green. The verifier will replay this from the raw artifacts (not the live state, since the final state is green).
- Do not run `git init` or any `git` command. Do not run `gh`. Do not modify `docs/`, `CLAUDE.md`, `AGENTS.md`, the existing `app/Services/` files, or any other task's artifacts.
- Do not create routes, controllers, models, migrations, .env files, public/, bootstrap/, config/, resources/, or any Laravel scaffold beyond what is strictly needed for `vendor/bin/phpunit` to find and run `tests/Unit/SmokeTest.php`.
- `composer install` is required and authorized for this task. It will create `vendor/` and `composer.lock`. Both should be in scope as expected outputs.
- No em dashes anywhere in spec.md or any file you author. Use `->`, colons, or commas.

## Non-goals

- No Laravel framework install (no `laravel/framework`, no `laravel/pint`, no `laravel/sail`).
- No controllers, no routes, no HTTP layer, no `routes/` directory.
- No DB layer: no models, no migrations, no `database/` directory, no SQLite file.
- No `.env` or `.env.example` file.
- No Inertia, no React, no `resources/` directory, no `package.json`, no `bun install`, no Vite.
- No `git init`, no `.git/` directory, no commits, no `gh repo create`, no remote setup.
- No Coolify deploy, no DNS, no Cloudflare, no `platform.thespawn.io` provisioning.
- No mandate `rsync` copy. The four service stubs already exist from `scaffold-service-stubs`; do not regenerate or edit them.
- No integration tests, no feature tests, no `tests/Feature/` directory. Unit suite only for v0.1 of the harness.
- No `phpunit.xml` (without `.dist`); only `phpunit.xml.dist`.
- No CI workflow files (`.github/workflows/*`).
- No README rewrite, no docs edits, no CLAUDE.md edits.

## Verification plan

Verifier runs the following, in order, from repo root `/Users/krutovoy/Projects/hosting-platform`. Each maps to the listed AC.

1. `test -f composer.json && python3 -c "import json; json.load(open('composer.json'))"` -> AC1
2. `python3 -c "import json; d=json.load(open('composer.json')); assert d['type']=='project'; assert 'name' in d and '/' in d['name']"` -> AC2
3. `python3 -c "import json; d=json.load(open('composer.json')); assert d['require']['php']=='^8.2'"` -> AC3
4. `python3 -c "import json; d=json.load(open('composer.json')); rd=d['require-dev']; assert list(rd.keys())==['phpunit/phpunit']; assert rd['phpunit/phpunit']=='^11.0'"` -> AC4
5. `python3 -c "import json; d=json.load(open('composer.json')); assert d['autoload']['psr-4']['App\\\\']=='app/'"` -> AC5
6. `python3 -c "import json; d=json.load(open('composer.json')); assert d['autoload-dev']['psr-4']['Tests\\\\']=='tests/'"` -> AC6
7. `python3 -c "import json; d=json.load(open('composer.json')); assert d['scripts']['test']=='vendor/bin/phpunit'"` -> AC7
8. `test -f phpunit.xml.dist && test ! -f phpunit.xml` -> AC8
9. `grep -q 'https://schema.phpunit.de/11.0/phpunit.xsd' phpunit.xml.dist && grep -q 'bootstrap="vendor/autoload.php"' phpunit.xml.dist && grep -q 'colors="true"' phpunit.xml.dist` -> AC9
10. `python3 -c "import xml.etree.ElementTree as ET; r=ET.parse('phpunit.xml.dist').getroot(); ts=r.findall('testsuites/testsuite'); assert len(ts)==1; assert ts[0].get('name')=='Unit'; assert ts[0].find('directory').text=='./tests/Unit'"` -> AC10
11. `test -d tests` -> AC11
12. `test -d tests/Unit` -> AC12
13. `grep -q 'namespace Tests\\\\Unit;' tests/Unit/SmokeTest.php && grep -q 'extends TestCase' tests/Unit/SmokeTest.php && grep -q 'function test_smoke_assertion_is_true' tests/Unit/SmokeTest.php && grep -q 'assertTrue(true)' tests/Unit/SmokeTest.php` -> AC13
14. `test -d vendor` -> AC14
15. `test -f composer.lock` -> AC15
16. `test -x vendor/bin/phpunit` -> AC16
17. `/Users/krutovoy/.config/herd-lite/bin/composer test` exits 0 -> AC17
18. `vendor/bin/phpunit --testdox` exits 0 and stdout contains `OK (1 test, 1 assertion)` -> AC18
19. `test -f .agent/tasks/test-harness/artifacts/phpunit-red.txt && test -f .agent/tasks/test-harness/artifacts/phpunit-green.txt && grep -q 'FAILURES' .agent/tasks/test-harness/artifacts/phpunit-red.txt && grep -q 'OK (1 test, 1 assertion)' .agent/tasks/test-harness/artifacts/phpunit-green.txt` -> AC19
20. `/Users/krutovoy/.config/herd-lite/bin/composer dump-autoload -q && php -r 'require "vendor/autoload.php"; foreach(["App\\\\Services\\\\AgentDeployerService","App\\\\Services\\\\KiloClawClientService","App\\\\Services\\\\TelegramBotRegistrarService","App\\\\Services\\\\OnChainOSPaymentService"] as $c){ if(!class_exists($c)){ fwrite(STDERR,"missing $c\n"); exit(1);} } echo "ok\n";'` exits 0 -> AC20
21. `php -l tests/Unit/SmokeTest.php && php -l app/Services/AgentDeployerService.php && php -l app/Services/KiloClawClientService.php && php -l app/Services/TelegramBotRegistrarService.php && php -l app/Services/OnChainOSPaymentService.php` all report `No syntax errors detected` -> AC21
22. `test ! -e .git && test ! -e node_modules && test ! -e public && test ! -e bootstrap && test ! -e config && test ! -e resources && test ! -e routes && test ! -e database && test ! -e storage && test ! -e .env && test ! -e .env.example && test ! -e package.json` -> AC22
23. `python3 -c "import sys; [sys.exit(1) for p in ['composer.json','phpunit.xml.dist','tests/Unit/SmokeTest.php','.agent/tasks/test-harness/spec.md'] if '\u2014' in open(p, encoding='utf-8').read()]"` exits 0 -> AC23
