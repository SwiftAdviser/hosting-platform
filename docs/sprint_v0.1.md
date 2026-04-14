# Sprint v0.1 — hackathon push

Ordered plan for the 2-day push to ship the 7-step walkthrough before the X-Layer deadline (evening 2026-04-13).

## Day 0 — unblock before coding

Three unknowns that block real code. Resolve them in parallel, ideally in the first 2 hours.

| # | Question | How to resolve | Owner |
|---|---|---|---|
| 1 | Payment flow: OnChainOS-sponsored tx vs user-signed? Can we stub for hackathon? | 30-min call with OnChainOS docs or skip — stub a "mark paid after 3s" for v0.1 demo, wire real in v0.2. Decide. | Roman |
| 2 | KiloClaw API surface: HTTP API or CLI shell-out? | Check Dimas's instance + @pinata docs. If no HTTP API, we shell out from Laravel queue worker. | Roman (needs Dimas's account) |
| 3 | First-message send: from Laravel after deploy, or from the agent at boot? | Decision, not research. Pick: Laravel sends first message synchronously after deploy webhook confirms. Simpler. | Decide |

**Hard stop rule:** if any of these three is still open after 3 hours, **switch to the video-demo fallback** (Roman's verbatim: *"я запишу в видео, что вот в один клик мы захостили агента и у него сразу доступ к кошельку через OnChainOS"*). Don't burn the whole day on unblocking.

**Dimas's KiloClaw account reset** runs in parallel — may block step 2 above. Start it before anything else.

## Day 1 — skeleton to payment-confirmed

Goal: a user can open the site, connect wallet, click Post agent, pay (stubbed or real), and see a "paid — provisioning" state.

Ordered tasks (do them serially, not in parallel — scope is tight):

1. **Scaffold** per `scaffold.md` §1-§6. Empty Laravel shell on `platform.thespawn.io`, TLS up via Coolify. **Done when:** domain loads.
2. **DB schema** — one migration, three tables:
   - `agents` (id, user_id, name, personality, icon, status, kiloclaw_id, wallet_address, bot_token_encrypted, created_at)
   - `deploys` (id, agent_id, amount_usd, onchainos_session_id, status, paid_at, created_at)
   - `users` (already exists from mandate scaffold)
3. **Landing page** — single Inertia page, one button: `Post agent`. No marketing copy. Retro terminal empty state if no agents yet (steal from Pinata frame 01).
4. **Create Agent wizard** — **collapse Pinata's 4 steps into 1 Inertia page with sections**. For v0.1 we don't need a multi-step wizard; the user fills a single form:
   - Agent name
   - Personality (textarea)
   - Telegram bot token
   - Allowlist user IDs (optional, comma-separated)
   - `Post agent` button (primary pink)
5. **Wallet connect** — use any existing mandate wallet-connect code if present, else thirdweb or rainbowkit. Minimum: one "Connect" button that returns an EVM address.
6. **Payment create** — `POST /api/deploys` that calls `OnChainOSPaymentService::createCharge()` (or its stub returning a fake session after 3s), returns payment session to the frontend.
7. **Payment webhook** — `POST /api/webhooks/onchainos` (or the stub) that flips `deploys.status` to `paid` and fires the provisioning job.

**Day 1 done when:** user can click Post agent, fill the form, pay (stubbed OK), and see "provisioning".

## Day 2 — provisioning to running agent

Goal: the 7-step walkthrough end-to-end.

1. **KiloClaw install job** — Laravel queue job that:
   - Packages the agent config into an OpenClaw plugin manifest (reference `~/Projects/mandate/packages/openclaw-plugin/openclaw.plugin.json`)
   - Calls `KiloClawClientService::install()` (HTTP or CLI)
   - Polls status until `ready` or timeout
   - Updates `agents.status = ready`, stores `kiloclaw_id`
2. **Agent wallet provisioning** — on install success, generate an EVM keypair server-side, store encrypted, attach the public address to the agent record.
3. **Telegram webhook registration** — validate the user's bot token via `getMe`, call `setWebhook` pointing at `https://platform.thespawn.io/api/telegram/webhook/{agent_id}`.
4. **First-message handler** — Laravel inbound webhook route. On first inbound message (`/start` or any text), reply:
   > Hi, I'm <AgentName>. Here's your wallet address: <addr>
   and enqueue the user's message to be forwarded to KiloClaw.
5. **Message forwarding** — subsequent messages get forwarded to the running agent on KiloClaw; its replies get posted back via `sendMessage`.
6. **My Agents list page** — one card per deployed agent with: name, status pill, wallet address (copyable), Telegram link (t.me/<botname>), `Stop` button (v0.1 can be cosmetic).
7. **Real user test** — run Dimas's agent through the flow end-to-end. If Dimas's account reset failed in Day 0, use a throwaway bot.
8. **Record the demo video** — even if everything works, record it. It's the submission artifact.

**Day 2 done when:** Dimas (or throwaway) can send "hi" to the Telegram bot and get a response from the hosted agent with wallet address in it. Video recorded.

## Scope discipline

**Cut from v0.1 at any sign of trouble:**
- Multi-step wizard (collapse to single form, always)
- Icon picker / Vibe / persona presets (Atlas/Nova/Sage — all cosmetic)
- Workspace/skills selection (assume "Pinata Optimized Agent" default, skip the step)
- Multiple AI provider choice (Anthropic-only via KiloClaw, hidden)
- Secrets vault (inline in form, no separate page)
- Marketplace, Skills Library, ClawHub, Activity log, Billing tiers, Custom domains, Dark mode, Workspaces/team — all v0.2+
- Slack / Discord channels — Telegram only in v0.1
- Mandate security integration — separate product, don't mix

**Never cut from v0.1:**
- The `Post agent` button
- Wallet connect
- Payment (stubbed OK)
- KiloClaw install
- Telegram first-message reply with wallet address
- My Agents list (even if it's one card)

## Failure modes and fallbacks

| If... | Then... |
|---|---|
| Day 0 unknowns unresolved by hour 3 | Switch to video-demo fallback, record the walkthrough manually with stubs |
| KiloClaw API surface turns out to be private/undocumented | Stub `KiloClawClientService` to return a fake `kiloclaw_id` after 2s; the demo still shows the end-to-end flow |
| OnChainOS auth/setup isn't feasible in 2 days | Stub payments; charge = "paid" immediately. Wire real in v0.2 |
| Coolify deploy fails on first push | Run locally, record demo from localhost. Ship the code to GitHub. |
| Telegram webhook doesn't route in time | Pre-record the bot conversation as part of the demo video |

## Artifacts to ship with submission

1. Public GitHub repo: `github.com/SwiftAdviser/hosting-platform` (private → flip to public at submission time)
2. Live URL (or localhost if Coolify fails): `https://platform.thespawn.io`
3. Demo video: the 7-step walkthrough, 60-120 seconds
4. README.md with one-paragraph pitch + link to the demo video
5. The PRD (`docs/agent_spawn_prd.md`) — optional, if the submission form has a "technical description" field
