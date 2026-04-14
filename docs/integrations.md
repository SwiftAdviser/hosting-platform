# Integrations — external dependencies

One section per external system v0.1 talks to. Each section has: what we know, what we call, what we don't know yet (resolve day 0 before coding).

---

## KiloClaw / OpenClaw

**Role:** the agent runtime. When a user clicks "Post agent" we package their config and install it as a KiloClaw plugin on the host, which starts the agent.

**What we know:**
- KiloClaw (rebranded OpenClaw) runs agents as npm-packaged plugins with a `openclaw.plugin.json` manifest. Reference: `~/Projects/mandate/packages/openclaw-plugin/openclaw.plugin.json`
- Plugin manifest declares: `id`, `name`, `version`, `description`, `skills`, `configSchema` (user-provided params), `postInstall` (first command + example), `activation` (status check + required config), `security` (env/network/file access)
- Plugins get installed to `~/.openclaw/<plugin-id>/` on the host
- Free tier (per Alanas call 2026-04-13): 1 week hosting + $2.50 credits, no card, no signup. Claude Sonnet 4.6 under the hood
- Visual/module reference: `~/Obsidian/Krutovoy/Wiki/Companies/Pinata Agents.md` (likely same product)
- Dimas's account has the API key we need (blocker #2)

**What v0.1 calls:**
1. **Register agent** → returns an install URL or token ID the user can paste into their bot
2. **Check status** → `ready`, `booting`, `failed`

**Open questions (resolve day 0):**
1. Does KiloClaw expose an HTTP API for programmatic install, or do we need to shell out to `openclaw` CLI from Laravel?
2. What's the auth model? Account-level API key? Per-agent runtime key (like mandate)?
3. Can we host multiple user agents under one KiloClaw account, or do we need one account per user?
4. Where do we store the user's bot token + personality prompt — in the plugin config schema, or server-side in our DB and inject at runtime?

**Reference files:**
- `~/Projects/mandate/packages/openclaw-plugin/openclaw.plugin.json` — manifest shape
- `~/Projects/mandate/packages/openclaw-plugin/src/plugin.ts` — plugin entry point
- `~/Projects/mandate/packages/openclaw-plugin/skills/mandate/` — skill shape

---

## OnChainOS

**Role:** payment rail. The user pays from their connected wallet and the tx lands on X-Layer. OnChainOS is our abstraction over that.

**What we know:**
- Locked as the payment rail per Apr 13 Alanas call
- Keeps "crypto hidden" — user doesn't see gas, chain, or signature prompts if we can help it
- Roman to Alanas verbatim: *"хостинг агентов напрямую, условно интегрированным OnChainOS"*

**What v0.1 calls:**
1. **Create charge** → price in USD, returns a payment intent / session ID
2. **Wait for confirmation** (webhook or poll) → confirmed, failed, expired
3. **Webhook receiver** → marks the deploy as paid in our DB, triggers KiloClaw install

**Open questions (resolve day 0 — this is blocker #1):**
1. Does OnChainOS sign transactions on behalf of the user (sponsored), or does the user sign from their wallet?
2. What's the minimum fee unit on X-Layer we should charge? $10 is placeholder from the walkthrough — real floor is dictated by gas + OnChainOS fee.
3. Webhook signature scheme — HMAC header like Stripe?
4. Do we need a merchant onboarding step, or can we ship with Roman's personal OnChainOS account?
5. Do we actually need OnChainOS for v0.1, or can we stub it with a hardcoded "mark paid after 3 seconds" for the hackathon demo, then wire real payments in v0.2?

**Reference files:**
- None yet. First service where we start from zero.

---

## Telegram Bot API

**Role:** the agent surface. Every deployed agent runs its own Telegram bot. User provides their own bot token via @BotFather.

**What we know:**
- Mandate already has `TelegramLinkController` + `TelegramWebhookController` — the patterns for pairing, webhook routing, and session state are already solved. Reuse.
- Pinata Agents uses a "DM Policy" model: `Pairing (require device approval)` / `Allowlist (comma-separated user IDs)` / `Open`. Worth stealing for v0.1 antispam default.
- @BotFather flow: user creates bot, gets token, pastes into our wizard
- @userinfobot gives Telegram user ID — we tell the user to use it for the allowlist
- Each bot is its own webhook; we register the webhook when the user saves the bot token: `POST https://api.telegram.org/bot<TOKEN>/setWebhook` with `url=https://platform.thespawn.io/api/telegram/webhook/<agent_id>`

**What v0.1 calls:**
1. **Validate token** → `GET https://api.telegram.org/bot<TOKEN>/getMe` — 200 means token works
2. **Set webhook** → `POST .../setWebhook` pointing at our per-agent endpoint
3. **Inbound webhook** → route messages to the corresponding hosted agent on KiloClaw
4. **Outbound send** → `POST .../sendMessage` when the agent replies
5. **First-message response** (the walkthrough step 7): on first `/start` or first message, reply *"Hi, I'm your new agent. Here's your wallet address: <addr>"*

**Open questions:**
1. Who sends the first "Hi, I'm your new agent" — us (from Laravel after deploy completes) or the agent itself (from KiloClaw after boot)? Answer affects whether the wallet address propagation is synchronous.
2. How do we give KiloClaw's agent runtime access to send via the user's bot? Pass bot token as agent env var, or proxy all sends through our Laravel API?

**Reference files:**
- `~/Projects/mandate/app/Http/Controllers/Api/TelegramLinkController.php`
- `~/Projects/mandate/app/Http/Controllers/Api/TelegramWebhookController.php`

---

## X-Layer

**Role:** hackathon chain target. We need to deploy, pay, or transact on X-Layer to qualify.

**What we know:**
- X-Layer is OKX's EVM L2. Standard EVM RPC.
- Hackathon requirement — we're on it regardless of whether we'd pick it otherwise
- Agent wallets get provisioned by us (server-side) and exposed to the user in first bot message

**What v0.1 calls:**
1. **Create wallet** — generate an EVM keypair, store encrypted in DB, derive public address
2. **Fund on demand** — either sponsored via OnChainOS or user-topped-up after the fact
3. **Tx broadcast** (if not via OnChainOS) — standard `eth_sendRawTransaction` via RPC

**Open questions:**
1. Does X-Layer require any registration/allowlist for contract deployment, or is it open?
2. Public RPC URL and chain ID — look up on docs.okx.com/xlayer before coding
3. Is there a free faucet for the hackathon, or do we need to prefund?

**Reference files:**
- None in repo yet. Public docs: `https://docs.xlayer.tech` (not verified, use at coding time)

---

## Anthropic (via KiloClaw, not direct)

**Role:** the model behind each hosted agent. Claude Sonnet 4.6.

**What we know:**
- KiloClaw's free tier runs Claude Sonnet 4.6 under the hood — we don't need an Anthropic API key for v0.1
- v0.2+: surface "Use your claude.ai Pro subscription" as the primary connect method (Pinata pattern). For v0.1 this is hidden.

**What v0.1 calls:** nothing directly. All Anthropic calls go through KiloClaw.

**Open questions:** none for v0.1.

---

## Coolify (deploy) + Cloudflare (DNS)

**Role:** ops layer. Not a runtime integration, but load-bearing for shipping.

**What we know:**
- Coolify instance: `coolz.krutovoy.me`, server UUID `q4cgokcc4gsgcw00oo0wwsw4`, destination UUID `a4soc8ccgcowkg8o0ow4kkss`
- VPS: `krutovoy-vps` (198.244.202.203), Debian, Docker
- Cloudflare token in `~/.profile` as `CLOUDFLARE_API_TOKEN`
- Parent zone `thespawn.io` — check it's already added to Cloudflare before adding the subdomain

**What v0.1 does:**
1. Via `/domain-dns-ops`: add `A` record `platform.thespawn.io` → `198.244.202.203`, proxied
2. Via `/coolify-ops`: create Nixpacks app, attach env vars from Coolify UI, first deploy

**Open questions:**
1. Is `thespawn.io` already in Cloudflare, or does the apex zone need to be added first?
2. Nixpacks auto-detect PHP+Laravel from mandate's `nixpacks.toml` — confirm it still works on the stripped repo before committing to Coolify.

**Reference files:**
- `~/Projects/mandate/nixpacks.toml` — copy as-is
- `~/.profile` — has `CLOUDFLARE_API_TOKEN`
