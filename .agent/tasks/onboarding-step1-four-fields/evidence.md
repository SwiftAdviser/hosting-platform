# Evidence Bundle: onboarding-step1-four-fields

## Summary
- Overall status: PASS
- Last updated: 2026-04-14
- Scope completed: wizard step-1 UI updates + backend personality defaulting + test updates.

## Acceptance criteria evidence

### AC1
- Status: PASS
- Proof:
  - `resources/views/wizard.blade.php:211` uses placeholder `name your agent`.
  - Verified by `artifacts/ui-checks.txt`.
- Gaps: none.

### AC2
- Status: PASS
- Proof:
  - Personality block is wrapped with Blade comments at `resources/views/wizard.blade.php:213` and `resources/views/wizard.blade.php:219`.
  - No visible personality input remains in rendered output.
  - Verified by `artifacts/ui-checks.txt`.
- Gaps: none.

### AC3
- Status: PASS
- Proof:
  - Controller path: `app/Http/Controllers/Api/DeployController.php:16-21` merges default personality when missing/blank.
  - Service path: `app/Services/AgentDeployerService.php:12` defines `DEFAULT_PERSONALITY` and `app/Services/AgentDeployerService.php:109-116` normalizes missing/blank personality.
  - Feature tests validate HTTP behavior for missing and blank personality: `tests/Feature/Http/DeployControllerTest.php:84-162`.
  - Unit tests validate direct service behavior for missing and blank personality: `tests/Unit/Services/AgentDeployerServiceTest.php:46-86`.
  - Test outputs: `artifacts/phpunit-feature.txt` and `artifacts/phpunit-unit.txt`.
- Gaps: none.

### AC4
- Status: PASS
- Proof:
  - Telegram token placeholder remains `123456:abcdef` at `resources/views/wizard.blade.php:222`.
  - Helper link text `Learn how to create a Telegram Bot` points to `https://help.superchat.com/en/articles/14901-how-do-i-get-the-telegram-token-or-bot-id` at `resources/views/wizard.blade.php:224`.
  - Verified by `artifacts/ui-checks.txt`.
- Gaps: none.

### AC5
- Status: PASS
- Proof:
  - Label changed to `Allowed Telegram IDs` at `resources/views/wizard.blade.php:228`.
  - Placeholder changed to `858032733, 858032711` at `resources/views/wizard.blade.php:229`.
  - Helper link text `get your telegram id here` points to `https://t.me/userinfobot` at `resources/views/wizard.blade.php:231`.
  - Verified by `artifacts/ui-checks.txt`.
- Gaps: none.

### AC6
- Status: PASS
- Proof:
  - Deploy status map is unchanged in `app/Http/Controllers/Api/DeployController.php:33-39`:
    - `deployed => 201`
    - `payment_failed => 402`
    - `invalid_request => 422`
    - `telegram_invalid => 422`
    - `install_failed => 502`
  - Existing feature tests for 422/402/502 still pass in `tests/Feature/Http/DeployControllerTest.php`.
  - Verified by `artifacts/backend-checks.txt` and `artifacts/phpunit-feature.txt`.
- Gaps: none.

## Commands run
- `php -l app/Http/Controllers/Api/DeployController.php`
- `php -l app/Services/AgentDeployerService.php`
- `php -l resources/views/wizard.blade.php`
- `./vendor/bin/phpunit tests/Feature/Http/DeployControllerTest.php`
- `./vendor/bin/phpunit tests/Unit/Services/AgentDeployerServiceTest.php`
- `rg -n "name your agent|Learn how to create a Telegram Bot|Allowed Telegram IDs|858032733, 858032711|get your telegram id here|personality" resources/views/wizard.blade.php`
- `rg -n "'deployed' => 201|'payment_failed' => 402|'invalid_request' => 422|'telegram_invalid' => 422|'install_failed' => 502" app/Http/Controllers/Api/DeployController.php`
- `rg -n "DEFAULT_PERSONALITY" app/Services/AgentDeployerService.php app/Http/Controllers/Api/DeployController.php`
- `python3 .claude/skills/repo-task-proof-loop/scripts/task_loop.py validate --task-id onboarding-step1-four-fields`
- `python3 .claude/skills/repo-task-proof-loop/scripts/task_loop.py status --task-id onboarding-step1-four-fields`

## Raw artifacts
- `.agent/tasks/onboarding-step1-four-fields/raw/build.txt`
- `.agent/tasks/onboarding-step1-four-fields/raw/test-unit.txt`
- `.agent/tasks/onboarding-step1-four-fields/raw/test-integration.txt`
- `.agent/tasks/onboarding-step1-four-fields/raw/lint.txt`
- `.agent/tasks/onboarding-step1-four-fields/raw/screenshot-1.png`
- `.agent/tasks/onboarding-step1-four-fields/artifacts/ui-checks.txt`
- `.agent/tasks/onboarding-step1-four-fields/artifacts/backend-checks.txt`
- `.agent/tasks/onboarding-step1-four-fields/artifacts/php-lint.txt`
- `.agent/tasks/onboarding-step1-four-fields/artifacts/phpunit-feature.txt`
- `.agent/tasks/onboarding-step1-four-fields/artifacts/phpunit-unit.txt`

## Known gaps
- None.
