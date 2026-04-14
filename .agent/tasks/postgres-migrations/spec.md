# Task Spec: postgres-migrations

## Metadata
- Task ID: postgres-migrations
- Created: 2026-04-14T15:21:18+00:00
- Frozen: 2026-04-13
- Repo root: /Users/krutovoy/Projects/hosting-platform
- Working directory at init: /Users/krutovoy/Projects/hosting-platform
- Runs after: land-laravel-framework
- Runs before: http-routes-controllers

## Guidance sources
- /Users/krutovoy/Projects/hosting-platform/CLAUDE.md (Laravel 12, PHP 8.2, Postgres target stack, crypto-hidden principle)
- /Users/krutovoy/Projects/hosting-platform/AGENTS.md (workflow rules, validate-without-executing preference)
- /Users/krutovoy/Projects/hosting-platform/docs/sprint_v0.1.md Day 1 Task 2 (agents, deploys, users table columns)
- /Users/krutovoy/Projects/hosting-platform/docs/integrations.md (wallet_address EVM, bot_token_encrypted, kiloclaw_id semantics)
- /Users/krutovoy/Projects/hosting-platform/docs/agent_spawn_prd.md (7-step walkthrough that the schema supports)

## Original task statement
Create Laravel 12 database migrations for the three v0.1 tables on PostgreSQL: `users`, `agents`, `deploys`. This task runs AFTER `land-laravel-framework`, so Laravel is already installed and the `database/migrations/` directory exists. This task does NOT run `php artisan migrate` because no live Postgres connection is assumed in CI. Validation uses a non-executing approach: `php -l` on each file plus a unit test that parses each migration file as text and asserts the required column literals are present. Lock the three filenames, lock the column lists verbatim, and keep the 37 existing unit tests green while adding exactly three new tests (one per migration shape).

## Acceptance criteria

- AC1: File `database/migrations/2026_04_14_120000_create_users_table.php` exists at repo root.
- AC2: File `database/migrations/2026_04_14_120001_create_agents_table.php` exists at repo root.
- AC3: File `database/migrations/2026_04_14_120002_create_deploys_table.php` exists at repo root.
- AC4: Each of the three migration files is valid PHP (`php -l <file>` returns "No syntax errors detected").
- AC5: Each migration uses the Laravel 12 anonymous-class style: the file body contains `return new class extends Migration` exactly once.
- AC6: Each migration imports `Illuminate\Database\Migrations\Migration`, `Illuminate\Database\Schema\Blueprint`, and `Illuminate\Support\Facades\Schema`.
- AC7: Each migration defines a public `up()` method that calls `Schema::create(` with the correct table name and a closure taking `Blueprint $table`.
- AC8: Each migration defines a public `down()` method that calls `Schema::dropIfExists(` with the correct table name.
- AC9: `users` migration declares exactly these columns via Blueprint calls: `$table->id();`, `$table->string('email')->unique();`, `$table->string('name')->nullable();`, `$table->string('google_id')->unique()->nullable();`, `$table->timestamp('email_verified_at')->nullable();`, `$table->string('remember_token', 100)->nullable();`, `$table->timestamps();`.
- AC10: `agents` migration declares exactly these columns via Blueprint calls: `$table->id();`, `$table->foreignId('user_id')->constrained()->cascadeOnDelete();`, `$table->string('name');`, `$table->text('personality');`, `$table->string('icon')->nullable();`, `$table->string('status')->default('pending');`, `$table->string('kiloclaw_id')->nullable();`, `$table->string('wallet_address')->nullable();`, `$table->text('bot_token_encrypted')->nullable();`, `$table->text('allowlist')->nullable();`, `$table->timestamps();`.
- AC11: `agents` migration declares a composite index via `$table->index(['user_id', 'status']);`.
- AC12: `deploys` migration declares exactly these columns via Blueprint calls: `$table->id();`, `$table->foreignId('agent_id')->constrained()->cascadeOnDelete();`, `$table->integer('amount_usd');`, `$table->string('onchainos_session_id')->nullable()->unique();`, `$table->string('status')->default('pending');`, `$table->timestamp('paid_at')->nullable();`, `$table->timestamps();`.
- AC13: `deploys` migration declares a composite index via `$table->index(['agent_id', 'status']);`.
- AC14: Unit test file `tests/Unit/Migrations/MigrationShapeTest.php` exists and declares a class extending `Tests\TestCase` or `PHPUnit\Framework\TestCase`.
- AC15: `MigrationShapeTest` contains exactly three test methods: `test_users_migration_has_expected_shape`, `test_agents_migration_has_expected_shape`, `test_deploys_migration_has_expected_shape`.
- AC16: Each shape test reads its migration file via `file_get_contents`, asserts the file exists, asserts `Schema::create` is called with the correct table name, and asserts via `str_contains` or `assertStringContainsString` that each locked column literal from AC9 / AC10 / AC12 appears verbatim in the file contents.
- AC17: The composite index assertions from AC11 and AC13 are covered by the agents and deploys shape tests respectively.
- AC18: Running `./vendor/bin/phpunit tests/Unit/Migrations/MigrationShapeTest.php` (or the `composer test` subset) exits 0 with 3 passing tests and 0 failures.
- AC19: Full `composer test` run exits 0, and the reported test count is strictly greater than the count recorded at the start of this task (captured in `.agent/tasks/postgres-migrations/evidence.json` as `tests_before` and `tests_after`), with `tests_after - tests_before == 3`.
- AC20: No file outside `database/migrations/` and `tests/Unit/Migrations/MigrationShapeTest.php` is modified. The 10 pre-existing service stub files under `app/Services/` and the 5 pre-existing test files under `tests/` from prior waves remain byte-identical (verified by diff against HEAD).
- AC21: `composer.json`, `composer.lock`, `phpunit.xml`, `config/database.php`, and all framework bootstrap files remain byte-identical to their state after `land-laravel-framework`.
- AC22: No file touched by this task contains an em dash character (U+2014).
- AC23: `php artisan migrate` is NOT invoked by the verifier, by any test, or by any script this task adds. Grep of the task diff for the literal `artisan migrate` returns zero matches outside comments that explicitly document the non-execution rule.

## Constraints

- Laravel 12, PHP 8.2, PostgreSQL target dialect (`DB_CONNECTION=pgsql`), but no live DB connection.
- All migration classes use the anonymous-class pattern introduced in Laravel 9 and standard in Laravel 12.
- Filenames are locked verbatim (timestamps `2026_04_14_120000`, `2026_04_14_120001`, `2026_04_14_120002`) so the verifier can grep them deterministically.
- Column literals are locked verbatim in AC9, AC10, AC12. Any deviation, including different quoting style or whitespace inside the parentheses, fails the verifier.
- Test file uses plain string search (`str_contains`, `assertStringContainsString`) rather than AST parsing or reflection on the anonymous class, because anonymous-class reflection adds complexity the proof loop does not need.
- No new composer packages. No changes to `composer.json`. No new config files.
- No edits to the service stubs or tests from prior waves (land-laravel-framework, scaffold-service-stubs, test-harness, tdd-google-auth-socialite, and any other wave A outputs).
- No em dashes anywhere in added code or docs.
- No TODO markers in the committed migration files or the shape test.

## Non-goals

- Running `php artisan migrate` against a live Postgres instance.
- Writing Eloquent models for `User`, `Agent`, or `Deploy` (separate task).
- Seeding data, factories, or fixtures.
- Enforcing Postgres-specific features (JSONB, GIN indexes, partial indexes). The v0.1 schema uses portable column types only.
- Migrating the `password_reset_tokens`, `sessions`, `cache`, `jobs`, or any other framework-shipped table beyond the three in scope. If `land-laravel-framework` shipped those already, leave them untouched.
- Integration tests that boot Laravel's test kernel against a real database.
- Rollback testing beyond the existence of the `down()` method.

## Verification plan

- Build:
  - `php -l database/migrations/2026_04_14_120000_create_users_table.php`
  - `php -l database/migrations/2026_04_14_120001_create_agents_table.php`
  - `php -l database/migrations/2026_04_14_120002_create_deploys_table.php`
  - All three must return "No syntax errors detected".
- Unit tests:
  - `./vendor/bin/phpunit tests/Unit/Migrations/MigrationShapeTest.php --testdox`: exits 0, 3 passing.
  - `composer test`: exits 0, total count strictly greater than `tests_before` by exactly 3.
- Lint:
  - `./vendor/bin/pint database/migrations/ tests/Unit/Migrations/`: exits 0 with zero style violations.
- Manual checks:
  - `grep -c 'Schema::create' database/migrations/2026_04_14_120000_create_users_table.php` returns `1`.
  - `grep -c 'Schema::create' database/migrations/2026_04_14_120001_create_agents_table.php` returns `1`.
  - `grep -c 'Schema::create' database/migrations/2026_04_14_120002_create_deploys_table.php` returns `1`.
  - `grep -n 'bot_token_encrypted' database/migrations/2026_04_14_120001_create_agents_table.php` returns one line matching the AC10 literal.
  - `grep -n 'onchainos_session_id' database/migrations/2026_04_14_120002_create_deploys_table.php` returns one line matching the AC12 literal.
  - `grep -rn $'\u2014' database/migrations/ tests/Unit/Migrations/` returns zero matches.
  - `git diff --stat` shows only the four expected files (three migrations plus one test).
- Evidence capture:
  - `tests_before` and `tests_after` recorded in `.agent/tasks/postgres-migrations/evidence.json` with `delta == 3`.
  - `files_touched` array contains exactly the four files listed above.
