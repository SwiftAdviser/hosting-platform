# Task Spec: bootstrap-proof-loop

## Metadata
- Task ID: bootstrap-proof-loop
- Created: 2026-04-13T15:19:43+00:00
- Repo root: /Users/krutovoy/Projects/hosting-platform
- Working directory at init: /Users/krutovoy/Projects/hosting-platform

## Guidance sources
- CLAUDE.md (project): hosting-platform PRD, locked decisions, scaffolding notes
- ~/.claude/CLAUDE.md (user): owner, agent protocol, tool conventions
- docs/agent_spawn_prd.md: product spec the future tasks will implement
- .claude/skills/repo-task-proof-loop/SKILL.md and references/*

## Original task statement
Bootstrap the repo-task-proof-loop workflow in hosting-platform. Install project-scoped Codex and Claude subagents, create or refresh managed workflow guidance in AGENTS.md and CLAUDE.md, and create `.agent/tasks/bootstrap-proof-loop/` skeleton so future tasks can follow the spec -> build -> evidence -> verify -> fix loop. No production code is expected to change; this task only installs the workflow scaffolding.

## Acceptance criteria
- AC1: `.agent/tasks/bootstrap-proof-loop/` exists and contains the required artifact set: `spec.md`, `evidence.md`, `evidence.json`, `verdict.json`, `problems.md`, and a `raw/` directory with at least `build.txt`, `test-unit.txt`, `test-integration.txt`, `lint.txt`, `screenshot-1.png` placeholders.
- AC2: Project-scoped Claude subagents are installed at `.claude/agents/task-spec-freezer.md`, `.claude/agents/task-builder.md`, `.claude/agents/task-verifier.md`, `.claude/agents/task-fixer.md`.
- AC3: Project-scoped Codex subagents are installed at `.codex/agents/task-spec-freezer.toml`, `.codex/agents/task-builder.toml`, `.codex/agents/task-verifier.toml`, `.codex/agents/task-fixer.toml`.
- AC4: Repository root `AGENTS.md` exists and contains a managed `<!-- repo-task-proof-loop:start -->` / `<!-- repo-task-proof-loop:end -->` block describing the workflow.
- AC5: Repository root `CLAUDE.md` contains a managed `<!-- repo-task-proof-loop:start -->` / `<!-- repo-task-proof-loop:end -->` block, and all pre-existing CLAUDE.md guidance (owner, PRD pointers, locked decisions, scaffolding notes, commands, source call) is preserved outside the managed block.
- AC6: `scripts/task_loop.py validate --task-id bootstrap-proof-loop` (from the skill directory) exits 0 and reports the artifact set as valid.
- AC7: `scripts/task_loop.py status --task-id bootstrap-proof-loop` returns a status summary without error.
- AC8: `spec.md` has explicit, non-placeholder acceptance criteria (no `AC1: TODO`).

## Constraints
- Do not modify any file under `docs/` or introduce production code in this task.
- Do not remove or reorder pre-existing content in `CLAUDE.md`. Only the managed block may be appended or refreshed.
- All task artifacts must live inside the repository at `.agent/tasks/bootstrap-proof-loop/`.
- Use the skill's own `scripts/task_loop.py` for validate and status checks; do not hand-roll verification logic.
- Follow user CLAUDE.md rules: no em dashes; concise telegraphic style; `trash` for deletes.

## Non-goals
- Implementing any part of the AgentSpawn PRD (KiloClaw, Telegram, OnChainOS, wallets, UI).
- Creating a Laravel project skeleton or copying from `~/Projects/mandate`.
- Wiring CI, tests, linters, or Coolify deploys.
- Authoring future task specs beyond this bootstrap task.

## Verification plan
- Build: not applicable. Record "n/a: no build system yet" in `raw/build.txt`.
- Unit tests: not applicable. Record "n/a" in `raw/test-unit.txt`.
- Integration tests: not applicable. Record "n/a" in `raw/test-integration.txt`.
- Lint: not applicable. Record "n/a" in `raw/lint.txt`.
- Manual checks:
  - `ls .agent/tasks/bootstrap-proof-loop/` and `ls .agent/tasks/bootstrap-proof-loop/raw/` cover AC1.
  - `ls .claude/agents/` and `ls .codex/agents/` cover AC2 and AC3.
  - `grep -l 'repo-task-proof-loop:start' AGENTS.md CLAUDE.md` covers AC4 and AC5.
  - `diff` against the previous CLAUDE.md content (as known from the pre-init context) confirms preservation for AC5.
  - `.claude/skills/repo-task-proof-loop/scripts/task_loop.py validate --task-id bootstrap-proof-loop` covers AC6.
  - `.claude/skills/repo-task-proof-loop/scripts/task_loop.py status --task-id bootstrap-proof-loop` covers AC7.
  - Reading this `spec.md` confirms AC8.
