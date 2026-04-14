# Subagent integration

This skill installs project-scoped subagent templates for both Codex and Claude Code.

## Installed files

### Codex

```text
.codex/agents/task-spec-freezer.toml
.codex/agents/task-builder.toml
.codex/agents/task-verifier.toml
.codex/agents/task-fixer.toml
```

### Claude Code

```text
.claude/agents/task-spec-freezer.md
.claude/agents/task-builder.md
.claude/agents/task-verifier.md
.claude/agents/task-fixer.md
```

The agent files are intentionally narrow and role-specific.

## Role definitions

### `task-spec-freezer`

Purpose:
- Freeze the task into `.agent/tasks/<TASK_ID>/spec.md`

Hard boundaries:
- May read repo guidance and relevant code
- Must not change production code
- Must not write verdict or problems files

### `task-builder`

Purpose:
- Implement the task and later pack evidence

Modes:
- `BUILD`
- `EVIDENCE`

Hard boundaries:
- In `BUILD`, implement against the spec
- In `EVIDENCE`, do not change production code

### `task-verifier`

Purpose:
- Fresh-session verification against the current codebase

Hard boundaries:
- Must not edit production code
- Must not patch the evidence bundle to make it look complete
- Must write `verdict.json`
- Must write `problems.md` only when the verdict is not `PASS`

### `task-fixer`

Purpose:
- Repair only what the verifier identified

Hard boundaries:
- Must reread the spec and verifier output
- Must reconfirm the problem before editing
- Must regenerate evidence after the fix
- Must not write final sign-off

## Codex invocation pattern

Use explicit delegation language. The parent should ask Codex to spawn one named child, wait for it, and then continue.
Do not spawn any child until `init <TASK_ID>` has finished and `.agent/tasks/<TASK_ID>/spec.md` exists.
Do not batch `init` with other commands or tool calls.

Suggested shape:

```text
Spawn one `task-spec-freezer` agent for TASK_ID <TASK_ID>. Wait for it. Tell it to freeze the spec in .agent/tasks/<TASK_ID>/spec.md using the repo guidance and the task source.
```

Repeat the same pattern for `task-builder`, `task-verifier`, and `task-fixer`.

Keep delegation depth flat. Use one child per role at a time.

## Claude Code invocation pattern

Use the installed project subagents from `.claude/agents/`. The parent can either explicitly select the named agent or instruct Claude to use that agent for the next step.

Suggested shape:

```text
Use the `task-verifier` agent for TASK_ID <TASK_ID>. It must be a fresh verifier pass against the current codebase and must write verdict.json and, if needed, problems.md.
```

For large tasks, prefer one child per role rather than a single general-purpose child.

## Same-session evidence packing

The preferred pattern is:

1. Spawn `task-builder`
2. Let it implement
3. Continue with the same child in `EVIDENCE` mode

If the platform cannot continue the same child session reliably, run a second `task-builder` child with an explicit `EVIDENCE-ONLY` prompt.

## Why the roles stay separate

The workflow is designed to keep:

- implementation
- judgment
- correction

as separate roles. This reduces self-justification and makes failures easier to localize.
