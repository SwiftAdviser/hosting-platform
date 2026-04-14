
# Reference

When the examples below mention `scripts/task_loop.py`, that path is relative to this skill root. Run it while your shell working directory is inside the target repository.

This skill is designed to be portable, but the repository-local artifacts and subagent files it creates must stay in the target repository.

## Recommended install locations

### Codex

Project skill:
- `.agents/skills/repo-task-proof-loop/`

Personal skill:
- `$HOME/.agents/skills/repo-task-proof-loop/`

### Claude Code

Project skill:
- `.claude/skills/repo-task-proof-loop/`

Personal skill:
- `~/.claude/skills/repo-task-proof-loop/`

The same skill directory can be reused in either product. The initialization script writes repo-local workflow files into the current repository, not into the skill directory.

## Repo files created by `init`

```text
.agent/tasks/TASK_ID/
  spec.md
  evidence.md
  evidence.json
  raw/
    build.txt
    test-unit.txt
    test-integration.txt
    lint.txt
    screenshot-1.png
  verdict.json
  problems.md
```

The initializer also creates or refreshes these project-level integration files:

```text
.codex/agents/
  task-spec-freezer.toml
  task-builder.toml
  task-verifier.toml
  task-fixer.toml

.claude/agents/
  task-spec-freezer.md
  task-builder.md
  task-verifier.md
  task-fixer.md
```

And it inserts a managed workflow block into:

- `AGENTS.md`
- `CLAUDE.md`

The managed block is replaced in place on re-run, so user-authored content outside the managed markers is preserved.

## Commands

### Initialize workflow files

```bash
scripts/task_loop.py init --task-id my-task
```

Seed the task from a task file:

```bash
scripts/task_loop.py init --task-id my-task --task-file docs/task.md
```

Seed the task from inline text:

```bash
scripts/task_loop.py init --task-id my-task --task-text "Implement feature X"
```

Control which guide files are created or updated:

```bash
scripts/task_loop.py init --task-id my-task --guides auto
scripts/task_loop.py init --task-id my-task --guides both
scripts/task_loop.py init --task-id my-task --guides agents
scripts/task_loop.py init --task-id my-task --guides claude
scripts/task_loop.py init --task-id my-task --guides none
```

Control which project subagent sets are installed:

```bash
scripts/task_loop.py init --task-id my-task --install-subagents both
scripts/task_loop.py init --task-id my-task --install-subagents codex
scripts/task_loop.py init --task-id my-task --install-subagents claude
scripts/task_loop.py init --task-id my-task --install-subagents none
```

### Validate the artifact set

```bash
scripts/task_loop.py validate --task-id my-task
```

### Summarize current status

```bash
scripts/task_loop.py status --task-id my-task
```

## Expected working pattern

1. Initialize the task folder
2. Freeze the spec
3. Implement
4. Pack evidence
5. Fresh verify
6. Fix if needed
7. Fresh verify again

For exact prompts to use with child agents, see `references/COMMANDS.md`.

## Notes

- The initializer does not write the final `spec.md` content for you. It creates the strict file structure and seeds the task statement when provided. The actual spec freeze is an agent step.
- `evidence.json` and `verdict.json` are created with valid placeholder content so validation can run immediately after `init`.
- `raw/screenshot-1.png` is created as a tiny placeholder PNG so the required path exists from the start.
