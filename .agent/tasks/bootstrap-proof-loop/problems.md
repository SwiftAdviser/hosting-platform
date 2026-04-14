# Problems: bootstrap-proof-loop

## Pass 1 findings (2026-04-13, after first fresh verifier)

The first fresh verifier recorded PASS for every acceptance criterion, but wrote `verdict.json` with a schema that does not match what `.claude/skills/repo-task-proof-loop/scripts/task_loop.py` expects. As a result, `task_loop.py validate --task-id bootstrap-proof-loop` now exits with code 1, which means AC6 can no longer be considered proven and must be re-scored.

### AC6: `task_loop.py validate --task-id bootstrap-proof-loop` exits 0 and reports the artifact set as valid.
- Status: FAIL
- Why it is not proven:
  - `verdict.json` uses top-level keys `overall_status` and `acceptance_criteria`, but `validate_verdict` in `task_loop.py` (lines 339-364) requires `overall_verdict`, `criteria`, `commands_run`, and `artifacts_used`.
  - Each criterion object must contain `id`, `status`, and `reason`. Current objects use `evidence` instead of `reason`.
- Minimal reproduction steps:
  1. `cd /Users/krutovoy/Projects/hosting-platform`
  2. `.claude/skills/repo-task-proof-loop/scripts/task_loop.py validate --task-id bootstrap-proof-loop`
- Expected: exit 0, `"valid": true`, empty `errors`.
- Actual: exit 1, `"valid": false`, errors:
  - `verdict.json missing keys: artifacts_used, commands_run, criteria, overall_verdict`
  - `verdict.json overall_verdict must be PASS, FAIL, or UNKNOWN.`
  - `verdict.json criteria must be a list.`
- Affected files:
  - `.agent/tasks/bootstrap-proof-loop/verdict.json`
- Smallest safe fix:
  - Rewrite `verdict.json` so top-level keys are `task_id`, `overall_verdict` (value `PASS`), `criteria` (list), `commands_run` (list of commands the verifier ran), `artifacts_used` (list of artifact paths the verifier read). Each criterion object must have `id`, `status`, and `reason`. Preserve the `verified_at` timestamp and the verifier notes as additional informational fields. Do not change the PASS judgments, since each underlying fact is still confirmed by the repo.
- Corrective hint:
  - The schema is driven by `validate_verdict` in `task_loop.py`. After rewriting `verdict.json`, rerun `task_loop.py validate` and confirm exit 0 before spawning a fresh verifier. Also rerun `task_loop.py status` to confirm `verdict_overall_status` reports `PASS`.
