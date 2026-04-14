# Task Spec: onboarding-step1-four-fields

## Metadata
- Task ID: onboarding-step1-four-fields
- Created: 2026-04-14T18:56:15+00:00
- Frozen: 2026-04-14
- Repo root: /Users/Tomas/Desktop/Coding/hosting-platform
- Working directory at init: /Users/Tomas/Desktop/Coding/hosting-platform

## Guidance sources
- AGENTS.md
- CLAUDE.md
- User-approved implementation plan in this chat (Onboarding Step 1: Four-Field Wizard Update)

## Original task statement
Implement onboarding wizard step 1 updates on the server-rendered wizard page. Remove visible personality selection, keep four visible fields, and enforce a backend default personality for all deploy requests when personality is missing or blank.

## Acceptance criteria
- AC1: In `resources/views/wizard.blade.php`, the Name input placeholder is exactly `name your agent`.
- AC2: In `resources/views/wizard.blade.php`, the Personality field block is removed from visible UI and retained as Blade comments (`{{-- ... --}}`) for future restore.
- AC3: Deploy requests default personality to `A general helpful assistant specialized in wallet operations.` when personality is missing or blank, including both HTTP controller path and direct service call path.
- AC4: In `resources/views/wizard.blade.php`, Telegram Token keeps placeholder `123456:abcdef` and has helper text with hyperlink label `Learn how to create a Telegram Bot` pointing to `https://help.superchat.com/en/articles/14901-how-do-i-get-the-telegram-token-or-bot-id`.
- AC5: In `resources/views/wizard.blade.php`, allowlist label becomes `Allowed Telegram IDs`, placeholder becomes `858032733, 858032711`, and helper text contains hyperlink label `get your telegram id here` pointing to `https://t.me/userinfobot`.
- AC6: Existing deploy flow status mapping remains unchanged (`deployed`->201, `payment_failed`->402, `invalid_request`->422, `telegram_invalid`->422, `install_failed`->502), and tests reflect the new default-personality behavior.

## Constraints
- Keep backend response shape unchanged.
- No route changes.
- No schema/migration changes.
- Keep edits minimal and scoped to wizard view, deploy controller/service, and related tests.

## Non-goals
- No parsing or validation changes for allowlist format.
- No redesign of other onboarding or dashboard UI flows.
- No changes to unrelated APIs.

## Verification plan
- Build:
  - `php -l app/Http/Controllers/Api/DeployController.php`
  - `php -l app/Services/AgentDeployerService.php`
- Unit tests:
  - `php artisan test tests/Unit/Services/AgentDeployerServiceTest.php`
- Integration tests:
  - `php artisan test tests/Feature/Http/DeployControllerTest.php`
- Lint:
  - N/A (no dedicated formatter run required for this scoped change)
- Manual checks:
  - `rg -n "name your agent|Learn how to create a Telegram Bot|Allowed Telegram IDs|858032733, 858032711|get your telegram id here" resources/views/wizard.blade.php`
  - `rg -n "DEFAULT_PERSONALITY|personality" app/Http/Controllers/Api/DeployController.php app/Services/AgentDeployerService.php`
  - `git diff -- resources/views/wizard.blade.php app/Http/Controllers/Api/DeployController.php app/Services/AgentDeployerService.php tests/Feature/Http/DeployControllerTest.php tests/Unit/Services/AgentDeployerServiceTest.php`
