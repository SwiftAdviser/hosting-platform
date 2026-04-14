# platform.thespawn.io — PRD v0.1

> **One click. Your agent is online, with a wallet, on Telegram.**

**Domain:** `platform.thespawn.io`
**Deadline:** X-Layer hackathon, evening of 2026-04-13
**Fallback:** demo video of the walkthrough if code doesn't ship

---

## Who it's for

Devs who have an agent running on their laptop and nowhere to put it. Canonical user: Dimas.

Not retail. Not crypto-native users. The person who cares about the agent, not the chain.

---

## The walkthrough (this is the spec)

1. Open `platform.thespawn.io`
2. Connect wallet
3. Click **Post agent**
4. Pay from wallet
5. Agent uploads to KiloClaw under the hood
6. Paste the returned token ID into a Telegram bot
7. Bot replies: *"Hi, I'm your new agent. Here's your wallet address."*

Every other feature is out of scope. If it isn't serving one of those seven steps, it ships after the hackathon.

---

## Stack

Laravel, same shape as `~/Projects/mandate`.

- Backend: Laravel 12 (PHP 8.2), PHPUnit, SQLite in-memory for tests
- Frontend: React 19 + Inertia.js + Tailwind 4
- TS packages: bun workspaces in `packages/`
- Lint: Laravel Pint
- LLM under the hood: Claude Sonnet 4.6 via KiloClaw

Scaffold by copying mandate's structure, stripping its services (PolicyEngine, QuotaManager, IntentStateMachine), and adding ours: `AgentDeployer`, `KiloClawClient`, `TelegramBotRegistrar`, `OnChainOSPaymentService`.

---

## Integrations

| Piece | Provider |
|---|---|
| Agent runtime | KiloClaw / OpenClaw |
| Bot surface | Telegram |
| Payment rail | OnChainOS |
| Chain | X-Layer |

Crypto stays under the hood. The user never sees a chain name, gas price, or signature prompt if we can avoid it.

---

## Ops

- **Deploy:** `/coolify-ops` skill to `coolz.krutovoy.me`. Never hand-roll docker or SSH in.
- **DNS:** `/domain-dns-ops` skill to add `platform.thespawn.io`. Never edit the zone by hand.
- **Secrets:** Coolify env only. `.env.example` in repo.

---

## What blocks shipping

1. **Payment flow design.** The walkthrough says "pay from wallet." The on-chain mechanics are undefined. Decide: OnChainOS-sponsored tx on X-Layer, or signed tx from connected wallet.
2. **Dimas's KiloClaw API key.** Reset the account under Roman's email. Dimas loses laptop access to that instance — accepted cost.
3. **KiloClaw reliability.** No backup host. Accepted for v0.1. Known fragile.

Everything else is work, not risk.

---

## Done =

- One agent goes through all 7 steps end-to-end.
- Video of the run.
- Submitted to X-Layer before the deadline.
