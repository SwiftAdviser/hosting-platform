# Evidence Bundle: scaffold-service-stubs

## Summary
- Overall status: PASS
- Last updated: 2026-04-14
- Build by: task-builder subagent
- Verification: `php -l` + grep/head/ls shape checks per spec.md §Verification plan

## Acceptance criteria evidence

### AC1: Directory `app/Services/` exists under repo root and is a directory
- Status: PASS
- Proof:
  - `mkdir -p /Users/krutovoy/Projects/hosting-platform/app/Services` (build step 2)
  - `/bin/ls -la app/Services/` in `raw/services-ls.txt` shows directory with `.` and `..` entries
  - `test -d app/Services` -> exit 0 (implicit; ls succeeded)
- Gaps: none

### AC2: File `app/Services/AgentDeployerService.php` exists
- Status: PASS
- Proof:
  - `raw/services-ls.txt` lists `AgentDeployerService.php` (157 bytes)
  - `raw/services-cat.txt` contains full file body
- Gaps: none

### AC3: File `app/Services/KiloClawClientService.php` exists
- Status: PASS
- Proof:
  - `raw/services-ls.txt` lists `KiloClawClientService.php` (144 bytes)
  - `raw/services-cat.txt` contains full file body
- Gaps: none

### AC4: File `app/Services/TelegramBotRegistrarService.php` exists
- Status: PASS
- Proof:
  - `raw/services-ls.txt` lists `TelegramBotRegistrarService.php` (156 bytes)
  - `raw/services-cat.txt` contains full file body
- Gaps: none

### AC5: File `app/Services/OnChainOSPaymentService.php` exists
- Status: PASS
- Proof:
  - `raw/services-ls.txt` lists `OnChainOSPaymentService.php` (158 bytes)
  - `raw/services-cat.txt` contains full file body
- Gaps: none

### AC6: Every one of the four files starts with the literal line `<?php` as its first line
- Status: PASS
- Proof:
  - `head -n 1` on each file returns `<?php` (see builder verification; also visible in `raw/services-cat.txt` where each `=== <file> ===` block's next line is `<?php`)
- Gaps: none

### AC7: Every one of the four files contains the line `namespace App\Services;` (exact text)
- Status: PASS
- Proof:
  - `grep -c '^namespace App\\Services;$' <file>` = 1 for each of the four files
  - Visible on line 2 of every block in `raw/services-cat.txt`
- Gaps: none

### AC8: `AgentDeployerService.php` declares `class AgentDeployerService` on its own line
- Status: PASS
- Proof:
  - `grep -c '^class AgentDeployerService$' app/Services/AgentDeployerService.php` = 1
  - Line 4 of `raw/services-cat.txt` AgentDeployerService block
- Gaps: none

### AC9: `KiloClawClientService.php` declares `class KiloClawClientService`
- Status: PASS
- Proof:
  - `grep -c '^class KiloClawClientService$' app/Services/KiloClawClientService.php` = 1
  - Line 4 of `raw/services-cat.txt` KiloClawClientService block
- Gaps: none

### AC10: `TelegramBotRegistrarService.php` declares `class TelegramBotRegistrarService`
- Status: PASS
- Proof:
  - `grep -c '^class TelegramBotRegistrarService$' app/Services/TelegramBotRegistrarService.php` = 1
  - Line 4 of `raw/services-cat.txt` TelegramBotRegistrarService block
- Gaps: none

### AC11: `OnChainOSPaymentService.php` declares `class OnChainOSPaymentService`
- Status: PASS
- Proof:
  - `grep -c '^class OnChainOSPaymentService$' app/Services/OnChainOSPaymentService.php` = 1
  - Line 4 of `raw/services-cat.txt` OnChainOSPaymentService block
- Gaps: none

### AC12: `AgentDeployerService.php` contains `    // Orchestrates: validate config -> charge wallet -> upload to KiloClaw -> return token ID`
- Status: PASS
- Proof:
  - Visible on line 6 of AgentDeployerService block in `raw/services-cat.txt`
  - Byte-exact match against spec (ASCII only, uses `->`, no em dashes)
- Gaps: none

### AC13: `KiloClawClientService.php` contains `    // Wraps the KiloClaw/OpenClaw host API. See docs/integrations.md §KiloClaw`
- Status: PASS
- Proof:
  - Visible on line 6 of KiloClawClientService block in `raw/services-cat.txt`
  - Byte-exact match against spec
- Gaps: none

### AC14: `TelegramBotRegistrarService.php` contains `    // Validates user-provided bot token, registers webhook, stores chat pairing state`
- Status: PASS
- Proof:
  - Visible on line 6 of TelegramBotRegistrarService block in `raw/services-cat.txt`
  - Byte-exact match against spec
- Gaps: none

### AC15: `OnChainOSPaymentService.php` contains `    // Charges the connected wallet for the deploy fee. See docs/integrations.md §OnChainOS`
- Status: PASS
- Proof:
  - Visible on line 6 of OnChainOSPaymentService block in `raw/services-cat.txt`
  - Byte-exact match against spec
- Gaps: none

### AC16: `php -l app/Services/AgentDeployerService.php` exits 0
- Status: PASS
- Proof:
  - `raw/php-lint.txt` block 1: `No syntax errors detected in app/Services/AgentDeployerService.php` then `exit=0`
- Gaps: none

### AC17: `php -l app/Services/KiloClawClientService.php` exits 0
- Status: PASS
- Proof:
  - `raw/php-lint.txt` block 2: `No syntax errors detected in app/Services/KiloClawClientService.php` then `exit=0`
- Gaps: none

### AC18: `php -l app/Services/TelegramBotRegistrarService.php` exits 0
- Status: PASS
- Proof:
  - `raw/php-lint.txt` block 3: `No syntax errors detected in app/Services/TelegramBotRegistrarService.php` then `exit=0`
- Gaps: none

### AC19: `php -l app/Services/OnChainOSPaymentService.php` exits 0
- Status: PASS
- Proof:
  - `raw/php-lint.txt` block 4: `No syntax errors detected in app/Services/OnChainOSPaymentService.php` then `exit=0`
- Gaps: none

### AC20: `app/Services/` contains exactly these four files and no others
- Status: PASS
- Proof:
  - `raw/services-ls.txt` section "/bin/ls app/Services/ (names only)" shows exactly:
    - `AgentDeployerService.php`
    - `KiloClawClientService.php`
    - `OnChainOSPaymentService.php`
    - `TelegramBotRegistrarService.php`
  - Four entries, matching the fixed expected list.
- Gaps: none

### AC21: No files outside `app/Services/` and outside `.agent/tasks/scaffold-service-stubs/` are created or modified by the build step
- Status: PASS
- Proof:
  - `raw/pre-build-ls.txt` vs `raw/post-build-ls.txt` diff output:
    ```
    8a9
    > drwxr-xr-x@   3 krutovoy  staff    96 Apr 14 16:20 app
    ```
  - The only difference at the repo root is a newly added `app/` directory.
  - No other repo-root entries added, removed, or modified.
  - Build tool writes were confined to `app/Services/*.php` (4 files) and `.agent/tasks/scaffold-service-stubs/raw/*` plus `evidence.md`/`evidence.json`.
- Gaps: none

### AC22: Each file follows the exact 7-line layout
- Status: PASS
- Proof:
  - `raw/services-ls.txt` "wc -l" section shows `7` lines for each of the four files.
  - `raw/services-cat.txt` shows byte-exact template match per file:
    ```
    <?php
    namespace App\Services;

    class <Name>
    {
        // <comment>
    }
    ```
  - `<Name>` matches the filename stem in each case (AgentDeployerService, KiloClawClientService, TelegramBotRegistrarService, OnChainOSPaymentService).
  - `<comment>` matches AC12..AC15 exactly.
- Gaps: none

## Commands run
- `mkdir -p /Users/krutovoy/Projects/hosting-platform/app/Services`
- `php -l app/Services/AgentDeployerService.php`
- `php -l app/Services/KiloClawClientService.php`
- `php -l app/Services/TelegramBotRegistrarService.php`
- `php -l app/Services/OnChainOSPaymentService.php`
- `/bin/ls -la /Users/krutovoy/Projects/hosting-platform/`
- `/bin/ls -la app/Services/`
- `/bin/ls app/Services/`
- `wc -l app/Services/*.php`
- `cat app/Services/<file>.php` (four times)
- `head -n 1 app/Services/<file>.php` (four times)
- `grep -c '^namespace App\\Services;$' app/Services/<file>.php` (four times)
- `grep -c '^class <Name>$' app/Services/<file>.php` (four times)
- `diff raw/pre-build-ls.txt raw/post-build-ls.txt`

## Raw artifacts
- `.agent/tasks/scaffold-service-stubs/raw/pre-build-ls.txt`
- `.agent/tasks/scaffold-service-stubs/raw/post-build-ls.txt`
- `.agent/tasks/scaffold-service-stubs/raw/php-lint.txt`
- `.agent/tasks/scaffold-service-stubs/raw/services-ls.txt`
- `.agent/tasks/scaffold-service-stubs/raw/services-cat.txt`
- `.agent/tasks/scaffold-service-stubs/raw/build.txt`

## Known gaps
- None.
