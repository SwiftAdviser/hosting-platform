# CLAUDE.md

## What this is

`platform.thespawn.io` — one-click agent hosting. User connects a wallet, pays, gets an agent running on KiloClaw with a Telegram bot and a server-provisioned wallet through OnChainOS. Crypto hidden. Closer to Vercel for agents than to a crypto wallet.

**Spec:** `docs/agent_spawn_prd.md`. The 7-step walkthrough in that file is the entire v0.1.

## Status

Pre-code. No source files. Hackathon deadline: evening of 2026-04-13 (X-Layer).

## Locked decisions

| | |
|---|---|
| Domain | `platform.thespawn.io` (sibling to Spawn visualizer at `thespawn.io`) |
| Backend | Laravel 12 + PHP 8.2 — mandatory, same shape as `~/Projects/mandate` |
| Frontend | React 19 + Inertia.js + Tailwind 4 |
| Agent runtime | KiloClaw / OpenClaw |
| Bot surface | Telegram |
| Payment rail | OnChainOS |
| Chain | X-Layer |
| Deploy | `/coolify-ops` skill → `coolz.krutovoy.me` |
| DNS | `/domain-dns-ops` skill |
| Separate product from Mandate | Yes, v0.1 does not integrate Mandate |
| Target user | Devs with a laptop-bound agent. Not retail |

## Principle

**Crypto hidden.** Do not surface chains, gas, or signatures unless unavoidable. If the user sees a chain name, we've failed the UX test.

**Walkthrough-first.** If a feature isn't in the 7 steps of the PRD, it doesn't ship in v0.1.

**TDD mandatory.** Every feature and bugfix starts with a failing test. Red, green, refactor. No implementation code without a failing PHPUnit test first. Integration tests hit a real test DB, not mocks. Invoke `superpowers:test-driven-development` before writing code.

## Scaffolding

Copy the structural shape from `~/Projects/mandate` rather than `laravel new` from scratch. Strip: `PolicyEngineService`, `QuotaManagerService`, `IntentStateMachineService`, `EnvelopeVerifierService`, `CircuitBreakerService`. Add: `AgentDeployer`, `KiloClawClient`, `TelegramBotRegistrar`, `OnChainOSPaymentService`.

## Commands (target, not yet runnable)

```bash
composer dev           # Laravel + queue + log watcher + Vite
composer test          # PHPUnit
php artisan migrate
bun run dev
./vendor/bin/pint      # PHP lint
```

## Source call

Krisp `019d867bc64674ea970ef50b08dcf05e` — Alanas ↔ Roman, 2026-04-13, Russian. Related same-day calls: HackenProof `019d864bd18474dc8f16427baff17212`, Vlad `019d862f2faf727ea1e47e299cfca4b3`, Alex Dulub `019d85fb77ea7569bfe6329a920d36ae`.

<!-- repo-task-proof-loop:start -->
## Repo task proof loop

For substantial features, refactors, and bug fixes, use the repo-task-proof-loop workflow.

Required artifact path:
- Keep all task artifacts in `.agent/tasks/<TASK_ID>/` inside this repository.

Required sequence:
1. Freeze `.agent/tasks/<TASK_ID>/spec.md` before implementation.
2. Implement against explicit acceptance criteria (`AC1`, `AC2`, ...).
3. Create `evidence.md`, `evidence.json`, and raw artifacts.
4. Run a fresh verification pass against the current codebase and rerun checks.
5. If verification is not `PASS`, write `problems.md`, apply the smallest safe fix, and reverify.

Hard rules:
- Do not claim completion unless every acceptance criterion is `PASS`.
- Verifiers judge current code and current command results, not prior chat claims.
- Fixers should make the smallest defensible diff.

Installed workflow agents:
- `.claude/agents/task-spec-freezer.md`
- `.claude/agents/task-builder.md`
- `.claude/agents/task-verifier.md`
- `.claude/agents/task-fixer.md`
<!-- repo-task-proof-loop:end -->
