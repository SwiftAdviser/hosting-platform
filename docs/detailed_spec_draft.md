# Detailed Spec Draft — inspired by Pinata Agents

**Source of inspiration:** `agents.pinata.cloud` video walkthrough (CleanShot 2026-04-09, 5 min, 30 frames sampled).
**Status:** Draft catalogue, NOT prioritized. Next pass: re-prioritize against the `platform.thespawn.io` Apr 13 call context and the 7-step walkthrough in `agent_spawn_prd.md`.
**Intent:** capture every module, screen, and UX pattern worth stealing. Cut ruthlessly in the next pass.

---

## Design language observed

| Element | Choice |
|---|---|
| Background | Cream / off-white (`#F5F1E8`-ish) |
| Borders | Hard black, 1.5-2px, no radius blur |
| Accent | Pink/magenta for primary actions + active states |
| Warning accent | Amber/gold for plan-gate banners |
| Header font | Monospace uppercase, letterspaced (`PINATA | AGENTS`, `MY AGENTS`) |
| Body font | Humanist sans, regular weight |
| Logo | Pixel-art crown mascot |
| Layout | Fixed left sidebar + main content; right-side drawers for detail views |
| Cards | Black border, cream background, no shadows, pill tags inline |
| Buttons | Rectangular, black border, pink fill for primary / white fill for secondary |
| Wizard | Horizontal numbered stepper with filled/unfilled circles, green checkmarks on completed steps |

This aesthetic is directly reusable for `platform.thespawn.io`: retro-terminal + editorial + brutalist. Zero AI-slop purple gradients.

---

## Navigation structure (left sidebar)

```
Search                 (⌘K)
+ Create Agent         (primary CTA, always visible)
─────
My Agents              (home)
Skills Library         (expandable)
  └─ Installed
  └─ Browse ClawHub
Secrets Vault
My Templates
Marketplace
─────
Account                (expandable)
  └─ Workspaces
  └─ Activity
  └─ Billing
Support                (expandable)
  └─ Documentation      (external)
  └─ OpenAPI Docs       (external)
  └─ Changelog
─────
« Collapse
```

---

## Module 1: My Agents (home)

**Purpose:** see, create, manage your agents.

- Header: `MY AGENTS` + one-line description: *"Create and manage autonomous agents running in isolated containers. Each agent gets its own workspace, skills, and secrets."*
- Primary action: `CREATE AGENT` button top-right (opens wizard)
- Plan-gate banner (amber): *"Your current plan does not include agents. Upgrade your plan to create agents."* + `UPGRADE PLAN` button
- Search bar
- **Empty state:** retro terminal card titled `AGENT-01` with animated boot sequence:
  ```
  ▸ Initializing agents...
  ▸ Loading skills & secrets
  ▸ Ready
  ```
- Below empty state: `LAUNCH YOUR FIRST AGENT` + sub-copy about isolated containers

**For platform.thespawn.io:** replace "isolated containers" framing with "agent running with a wallet on Telegram". Keep terminal boot card as empty state — it's a strong first impression.

---

## Module 2: Create Agent wizard

**Four steps in a horizontal stepper:**

### Step 1 — IDENTITY
- Headline: `CREATE YOUR AI ASSISTANT`
- Sub-copy: *"Start from a template or build your own from scratch"*
- Big banner card: **START FROM A TEMPLATE** → `BROWSE TEMPLATES` button (jumps to Marketplace)
- `OR BUILD FROM SCRATCH` section with 4 preset persona cards: `ATLAS` (methodical strategist), `NOVA` (creative problem-solver), `SAGE` (patient knowledge-seeker), `CUSTOM` (blank)
- After picking: form fields
  - `AGENT NAME` (text input)
  - `PERSONALITY` (textarea, placeholder: *"Describe your agent's personality and purpose..."*)
  - Advanced (collapsible):
    - `ICON` picker — row of ~12 emoji/icon options
    - `VIBE` (one-line description field, auto-filled from preset)
- `CANCEL` / `CONTINUE` buttons at bottom

### Step 2 — WORKSPACE
- Headline: `SELECT WORKSPACE` — *"Choose an environment template for your agent"*
- Env template card (selected state with pink border): **Pinata Optimized Agent** — *"Full-featured workspace with standard tools and environment"*. Bullets: Pre-installed Node.js, Python, common CLI tools · Optimized for general-purpose AI agent tasks · Automatic workspace persistence and sync
- `Skills` section: *"Extend your agent's capabilities"* + skill search bar
- Skill card example: `@pinata/api` — *"Pinata IPFS API for file storage, groups, gateways, signatures, x402 payments, and AI-powered vector search"* + `REQUIRED SECRETS: PINATA_JWT, PINATA_GATEWAY_URL` (red warning)
- `workspace/` file tree section (pre-loaded files that populate the agent's workspace on deploy)

### Step 3 — CONNECT
- Headline: `CONNECT` — *"Select an AI provider, add secrets and optionally configure channels"*
- **AI PROVIDER (required)** — 4-up grid: Anthropic · OpenAI · OpenRouter · Venice
  - Each card has icon + name + dropdown "Select method" or `CONFIGURE` button
  - Clicking opens right drawer: **How do you want to connect?**
    - Tab 1 — **Claude Subscription** (RECOMMENDED): *"Use your existing claude.ai Pro or Teams plan. No extra billing."*
    - Tab 2 — **API Key**: paste key with inline instructions (*"Go to console.anthropic.com → Settings → API Keys → Create Key → copy"* + `Open Anthropic Console` link)
  - Secret limit warning inline: *"Secret limit reached (max 0)"* on free plan
- **VARIABLES AND SECRETS** — search bar + list. Missing secrets shown with red triangle + `MISSING — CREATE IN VAULT` pill + `CREATE` button. Modal: `ADD PINATA_JWT` with `SECRET NAME` + `VALUE` field + `SAVE SECRET`
- **CHANNELS (optional)** — *"Configure messaging channels now or later from the agent detail page"*
  - **Telegram** card → CONFIGURE → drawer with:
    - Instructions (get bot token via @BotFather)
    - `BOT TOKEN` field
    - `DM POLICY` dropdown: `Pairing (require device approval)` / `Open to anyone` / `Allowlist only`
    - `ALLOW FROM (comma-separated user IDs)` text field
    - *"Don't know your ID? Message @userinfobot on Telegram."* hint
    - `SAVE` button
  - **Slack** card → CONFIGURE → drawer with:
    - Step-by-step setup (Create Slack App at api.slack.com/apps, Enable Socket Mode, add scopes `chat:write im:write im:history im:read users:read app_mentions:read`)
    - `BOT TOKEN` + `APP TOKEN` fields
  - **Discord** card → CONFIGURE → drawer with:
    - Instructions (Discord Developer Portal, create app, Reset Token, Enable Message Content Intent, invite via OAuth2 URL Generator)
    - `BOT TOKEN` field

### Step 4 — DEPLOY
- `DEPLOY AGENT` button bottom-right (large, pink)

---

## Module 3: Marketplace

**Purpose:** browse and deploy pre-built agent templates.

- Search bar at top
- Category pills: `ALL` · `ACTIONS & TRANSACTIONS` · `BLOCKCHAIN` · `DEFI` · `GENERAL` · `MY TEMPLATES`
- `FEATURED` section header
- Template cards (2-column grid):
  - Card header: template name + `BY <publisher>` + version pill (e.g. `v5`)
  - Description paragraph
  - Tag pills at bottom (category, chain, capability)
- Example templates observed: `AGENT WITH MPP WALLET` (BY PINATA, *"AI agent pre-configured with a Tempo wallet. Discovers, compares, and pays for services in the Tempo MPP directory"*), `ALCHEMY AGENT` (BY ALCHEMY, blockchain intelligence), `AMPERSEND` (BY AMPERSEND, OpenClaw workspace for x402 HTTP), `AMPERSEND X SUBGRAPH MCP`
- **Detail drawer** (opens from card click):
  - Full description
  - **Example prompts** section with 4 categories of sample queries:
    - *"Enrich this company domain for me: acme.com — find their tech stack, employee count, and key contacts"*
    - *Multi-step pipeline:* *"Scrape this webpage, translate it to Spanish, then summarize it. Tell me the total cost before doing anything."*
    - *Crypto & finance:* *"Pull the last 30 days of ETH price data and chart the trend"*
    - *Travel:* *"Find me flights from NYC to London next Friday and show me the three cheapest options"*
  - Metadata card: `CATEGORY`, `VERSION`, `UPDATED` date, tag pills
  - Large `DEPLOY THIS AGENT` pink CTA

---

## Module 4: Skills Library

### 4a. Installed
- Header: `SKILLS LIBRARY` + *"Upload a folder to Pinata to create a skill package. Skills are pinned to IPFS and can be attached to any agent."*
- Plan-gate banner
- Search bar + filter pills: `ALL | INSTALLED` · `BROWSE CLAWHUB`
- Skill cards showing: name (e.g. `@PINATA/API`), publisher pill (`PINATA`), description, IPFS CID, version badge (`v1.0.0`)
- Installed skills observed: `@pinata/api`, `@pinata/erc-8004`, `@pinata/memory-salience`, `@pinata/parasite`, `@pinata/sqlite-sync`

### 4b. Skill Detail Drawer
- Icon + name + IPFS CID
- Publisher pill
- Description
- `REQUIRED SECRETS` section with each secret as a card (click to edit)
- `FILES` expandable tree (e.g. `skill/`)
- `VERSION HISTORY` timeline with pill badges (`v1.0.0 LATEST`) and date
- `View on IPFS` button
- `CLOSE` button

### 4c. Browse ClawHub (community marketplace)
- Search + filter pills
- Skill cards with **author avatar + name**, rating (stars + count), install count (e.g. `366.2K`), version badge
- Examples: `SELF-IMPROVING-AGENT` (by Pexoett, 366.2K installs, 3.1K stars, v0.13), `ONTOLOGY` (by Oswalpalash, typed knowledge graph), `SELF-IMPROVING + PROACTIVE AGENT` (by Ivan, 153K installs, 918 stars), `POLYMARKET` (by Joelchance, query Polymarket prediction markets), `NANO BANANA PRO`, `ADMAPIX`, `PLAYWRIGHT` (automation + MCP + scraper by Ivan, 25.1K / 80 stars), `BAIDU WENKU AIPPT`, `WEB SEARCH BY EXA` (by Ishan Goswami, 24.9K / 62 stars), `DATA ANALYSIS`, `PEEKABOO` (macOS UI automation by Peter Steinberger)
- `LOAD MORE` pagination

---

## Module 5: Secrets Vault

- Header: `SECRETS VAULT` + *"Manage AI provider keys, custom secrets. Secrets are encrypted and available only to agents."*
- Plan-gate banner
- Search bar
- **AI Providers** grid: Anthropic, OpenAI, OpenRouter cards with `Not connected` state + `CONNECT` button
- Footer note: *"This is your global vault. Secrets created here can be attached to individual agents."*

---

## Module 6: Account

### 6a. Workspaces
(Not visible in frames, but implied from nav.)

### 6b. Activity
- Header: `ACTIVITY` — *"A timeline of everything that happens in your workspace."*
- Search bar
- Entity filter pills: `AGENT` · `SECRET` · `SKILL` · `CHANNEL` · `TEMPLATE` · `ACCOUNT`
- Timeline list (skeleton shown, items not yet visible)

### 6c. Billing
- Header: `BILLING` — *"You're one step away from deploying agents. Upgrade your plan below to get started."*
- `CURRENT PLAN: FREE $0/mo` pill
- Three-tier card grid:
  - **FREE** $0/mo · No Agents · 1 Workspace Member · `CURRENT PLAN`
  - **PICNIC** $20/mo · 1 Agent · 3 Workspace Members · `UPGRADE`
  - **FIESTA** $100/mo · 3 Agents · 5 Workspace Members · `UPGRADE`
- Three-dot menu top-right

---

## Module 7: Support > Changelog

- Vertical timeline with filled circles at each release
- Each release card:
  - Version number (e.g. `v0.7.0`)
  - Release type pill: `FEATURE RELEASE` / `MAINTENANCE`
  - Date
  - One-line title (e.g. *"Skill Versioning, Redesigned Drawers & Performance"*)
  - Expandable to show `FEATURE` / `IMPROVEMENT` / `FIX` badge + one-line description for each item
- `LATEST RELEASE` pill on the top item

**Observed release history (for reference — this is what a fully-built platform ends up looking like):**
- `v0.7.0` (Apr 6 2026) — Skill versioning system, Redesigned skill detail drawers with hero cards stats strip and file browser, Agent info analytics panel, Skills "used by" indicator, Files preview with markdown/source toggle
- `v0.3.0` (Mar 1 2026) — UI Overhaul, Dark Mode, Skills Marketplace
- `v0.2.1` (Feb 25 2026) — Stability & performance
- `v0.2.0` (Feb 15 2026) — Venice AI provider support, Split templates into My Templates and Agent Marketplace, Task runs with retry logic and failure log viewer, Script run with nohup, Chat markdown rendering, template.json/manifest.json views, Marketplace feature flag controls, Custom domains for agents, Git support (SSH push/pull/clone), In-app billing, Audit logs, Community marketplace (ClawHub), Model switching, Framework version updates
- `v0.1.0` (Feb 5 2026) — Initial Release, Core Platform

---

## Patterns worth stealing wholesale

1. **Plan-gate banner as "paywall" cue** — amber banner telling the user their plan doesn't allow this feature, with inline `UPGRADE` CTA. Use for: "this deploy needs a paid slot".
2. **4-step wizard (Identity → Workspace → Connect → Deploy)** — this is a clean map of what a hosted agent actually needs. Useful even if we collapse it to 2-3 steps.
3. **Right-side drawers for detail + config** instead of modal dialogs or new pages — keeps context, user never loses their place.
4. **Empty states with retro terminal animation** — signals craft without adding a marketing page.
5. **Global Secrets Vault decoupled from agents** — create secret once, attach to many agents. Clean model.
6. **Channels (Telegram / Slack / Discord) as first-class agent surfaces** — same config pattern, different integration. Telegram's DM policy model (`Pairing require device approval` / `Allowlist`) is the smartest pattern here.
7. **Skill versioning + IPFS CIDs** — every skill is addressable. Community version history. Possibly overkill for v0.1 but philosophically correct.
8. **Example prompt gallery in marketplace detail drawer** — users see what the agent can actually do, not just generic category tags.
9. **Connect Anthropic "use your existing claude.ai subscription"** — huge UX win. No API key paste. Platform calls Claude via user's Claude.ai Pro credentials.
10. **Pricing tiers with playful names** (Free / Picnic / Fiesta) — signal personality.

---

## What NOT to copy

- **ClawHub community marketplace** — too big for v0.1. No users yet to populate it.
- **Git SSH support for agents** — v0.5+ feature.
- **Audit logs for compliance** — v0.5+ feature.
- **Dark mode** — v0.3+. Ship light mode only.
- **Skill versioning with IPFS CIDs** — unless we absolutely need it. v0.2+.
- **Multi-workspace / workspace members / team billing** — v0.4+.
- **Multiple AI provider grid** — we're Claude-only via KiloClaw. Don't offer OpenAI/OpenRouter/Venice until someone asks twice.
- **Custom domains for agents** — v0.5+.

---

## Crosswalk to Apr 13 call context

| Pinata module | Matches call signal? | Verdict |
|---|---|---|
| Create Agent wizard | YES — this IS the 7-step walkthrough | Core v0.1 |
| My Agents (list) | YES — Dimas needs a "this is where my agent lives" page | Core v0.1 |
| Telegram channel config | YES — locked: bot surface is Telegram | Core v0.1 |
| AI Provider: Anthropic via Claude.ai subscription | PARTIAL — we use KiloClaw-hosted Sonnet 4.6 under the hood | Hidden in v0.1, surfaced in v0.2 |
| Secrets Vault | PARTIAL — user provides bot token, we store encrypted | Stripped to minimum in v0.1 |
| Marketplace (templates) | NO — no templates exist yet | v0.3+ |
| Skills Library | NO — no skills exist yet | v0.3+ |
| ClawHub community | NO | v0.5+ |
| Activity log | NO | v0.3+ |
| Workspaces / team | NO | v0.4+ |
| Billing tiers | PARTIAL — we have one pay-per-deploy at ~$10 | Trivial v0.1 |
| Custom domains | NO | v0.5+ |
| Slack / Discord channels | NO — Telegram only | v0.4+ |

**Punchline:** v0.1 keeps 3 modules out of 11: **Create Agent wizard** (collapsed to the 7 steps), **My Agents list** (one card per deployed agent), and **Telegram channel config** (inline in the wizard, not a separate page). Everything else is future scope.

---

## Next pass: re-prioritization

When we re-prioritize, walk through each module in this doc and mark it:
- **v0.1** — must ship tonight for the hackathon
- **v0.2** — next week, after Dimas is live
- **v0.3+** — parking lot, not committed

The PRD `docs/agent_spawn_prd.md` only carries v0.1 scope. Anything else lives here until it earns promotion.
