# Task Spec: scaffold-service-stubs

## Metadata
- Task ID: scaffold-service-stubs
- Created: 2026-04-14T08:15:17+00:00
- Repo root: /Users/krutovoy/Projects/hosting-platform
- Working directory at init: /Users/krutovoy/Projects/hosting-platform

## Guidance sources
- /Users/krutovoy/Projects/hosting-platform/CLAUDE.md (project rules, locked decisions)
- /Users/krutovoy/.claude/CLAUDE.md (global user rules: no em dashes, use `trash`)
- /Users/krutovoy/Projects/hosting-platform/docs/scaffold.md (§3 "Add our own services (empty stubs)", lines 98-140: authoritative stub shapes)
- /Users/krutovoy/Projects/hosting-platform/docs/agent_spawn_prd.md (context)
- /Users/krutovoy/Projects/hosting-platform/docs/sprint_v0.1.md (context)
- /Users/krutovoy/Projects/hosting-platform/.agent/tasks/scaffold-service-stubs/spec.md (placeholder being replaced)

## Original task statement
Create the four service stub files specified in docs/scaffold.md §3: app/Services/AgentDeployerService.php, app/Services/KiloClawClientService.php, app/Services/TelegramBotRegistrarService.php, app/Services/OnChainOSPaymentService.php. Each is an empty Laravel service class in the App\Services namespace with the single-line doc comment shown in scaffold.md. Create the app/Services/ directory if it does not exist. Do not rsync the mandate shell, do not run composer install, do not create a git repo, do not touch any external system.

## Assumptions
- No Laravel scaffold, no composer.json, no PHPUnit harness exists yet in this repo. TDD via `superpowers:test-driven-development` is not applicable: there is no test runner to drive red/green. Verifier relies on `php -l` and file-shape checks (grep, ls) only.
- `php` CLI is available on PATH for `php -l` syntax checks.
- Directory `app/Services/` may not yet exist and must be created fresh.

## Acceptance criteria
- AC1: Directory `app/Services/` exists under repo root and is a directory.
- AC2: File `app/Services/AgentDeployerService.php` exists.
- AC3: File `app/Services/KiloClawClientService.php` exists.
- AC4: File `app/Services/TelegramBotRegistrarService.php` exists.
- AC5: File `app/Services/OnChainOSPaymentService.php` exists.
- AC6: Every one of the four files starts with the literal line `<?php` as its first line.
- AC7: Every one of the four files contains the line `namespace App\Services;` (exact text).
- AC8: `AgentDeployerService.php` declares `class AgentDeployerService` (exact token sequence on its own line).
- AC9: `KiloClawClientService.php` declares `class KiloClawClientService`.
- AC10: `TelegramBotRegistrarService.php` declares `class TelegramBotRegistrarService`.
- AC11: `OnChainOSPaymentService.php` declares `class OnChainOSPaymentService`.
- AC12: `AgentDeployerService.php` contains the exact comment line `    // Orchestrates: validate config -> charge wallet -> upload to KiloClaw -> return token ID` inside the class body.
- AC13: `KiloClawClientService.php` contains the exact comment line `    // Wraps the KiloClaw/OpenClaw host API. See docs/integrations.md §KiloClaw` inside the class body.
- AC14: `TelegramBotRegistrarService.php` contains the exact comment line `    // Validates user-provided bot token, registers webhook, stores chat pairing state` inside the class body.
- AC15: `OnChainOSPaymentService.php` contains the exact comment line `    // Charges the connected wallet for the deploy fee. See docs/integrations.md §OnChainOS` inside the class body.
- AC16: `php -l app/Services/AgentDeployerService.php` exits 0.
- AC17: `php -l app/Services/KiloClawClientService.php` exits 0.
- AC18: `php -l app/Services/TelegramBotRegistrarService.php` exits 0.
- AC19: `php -l app/Services/OnChainOSPaymentService.php` exits 0.
- AC20: `app/Services/` contains exactly these four files and no others (no additional stubs, no sidecar files).
- AC21: No files outside `app/Services/` and outside `.agent/tasks/scaffold-service-stubs/` are created or modified by the build step. Verified via `git status`-style listing against a pre-build snapshot, or equivalent directory diff when no git repo is present.
- AC22: Each file follows the exact layout (whitespace significant):
  ```
  <?php
  namespace App\Services;

  class <Name>
  {
      // <comment>
  }
  ```
  where `<Name>` matches the filename stem and `<comment>` matches the per-file text in AC12..AC15.

## Constraints
- Do not rsync `~/Projects/mandate` or any other repo.
- Do not run `composer install`, `composer dump-autoload`, `bun install`, `npm install`, or any `php artisan` command. Only `php -l <file>` is allowed for verification.
- Do not run `git init`, `gh repo create`, `git add`, `git commit`, or any git-state-mutating command. Repo may not be a git repo: that is fine.
- Do not reach any external service (no HTTP, no DNS writes, no Coolify, no Telegram, no Cloudflare).
- Do not create a composer.json, package.json, .env, routes file, controller, view, migration, or any Laravel bootstrap file.
- Do not modify `docs/`, `CLAUDE.md`, `AGENTS.md`, or any existing file. Writes are limited to `app/Services/*.php` (new files) plus `.agent/tasks/scaffold-service-stubs/` artifacts.
- Use the exact file layout from AC22. No extra blank lines, no docblock, no constructor, no imports, no traits.
- No em dashes anywhere in spec.md, generated files, or build output. Use `->` or colons.
- Any delete operations must use `trash`, never `rm` or `rm -rf`.
- TDD is not applicable for this task: no Laravel scaffold, no PHPUnit, no test runner exists. Verifier uses `php -l` plus file-shape checks in place of unit tests. This is an explicit deviation from the default `superpowers:test-driven-development` rule and is documented here as a frozen assumption.

## Non-goals
- No unit tests, no integration tests, no PHPUnit config, no test fixtures.
- No database, no migration, no model, no factory, no seeder.
- No .env, no .env.example, no config/*.php file.
- No routes/web.php, routes/api.php, controllers, middleware, views, or Blade templates.
- No composer.json, composer.lock, package.json, bun.lock, vite.config.*.
- No full Laravel scaffold, no `laravel new`, no `composer create-project`.
- No git init, no initial commit, no remote, no PR.
- No external I/O: no HTTP calls, no API keys, no Coolify deploy, no Telegram registration, no on-chain call.
- No docs update, no README, no CHANGELOG.
- No service interface (e.g. `AgentDeployerServiceInterface`), no DI binding, no service provider.
- No additional methods, properties, or constructor logic inside the four classes beyond the single comment line.

## Verification plan
Each AC maps 1:1 to a concrete shell command run from repo root.

- AC1: `test -d app/Services` (exit 0).
- AC2..AC5: `test -f app/Services/<File>.php` for each of the four files.
- AC6: `head -n 1 app/Services/<File>.php` equals `<?php` for each file.
- AC7: Grep `^namespace App\\\\Services;$` matches exactly one line in each file.
- AC8..AC11: Grep `^class <Name>$` matches exactly one line in the matching file.
- AC12..AC15: Grep for the exact comment line (literal match, including four-space indent) in the matching file. Exactly one match per file.
- AC16..AC19: `php -l app/Services/<File>.php`; assert exit code 0 and stdout contains `No syntax errors detected`.
- AC20: `ls app/Services/` returns exactly the four expected filenames (sorted, compared against the fixed list).
- AC21: Before build, record a snapshot of all tracked paths under the repo root excluding `.agent/`. After build, diff the snapshot. Only additions permitted: `app/`, `app/Services/`, and the four `.php` files. No modifications or deletions anywhere else. Artifacts written under `.agent/tasks/scaffold-service-stubs/` are exempt.
- AC22: For each file, compare the full file contents against the expected 7-line template (with filename-derived class name and spec-defined comment). Byte-exact comparison after trailing-newline normalization.

## Build commands (reference, to be run by the builder subagent, not this freezer)
```
mkdir -p app/Services
# then write each of the four files with the exact 7-line template from AC22
php -l app/Services/AgentDeployerService.php
php -l app/Services/KiloClawClientService.php
php -l app/Services/TelegramBotRegistrarService.php
php -l app/Services/OnChainOSPaymentService.php
```
