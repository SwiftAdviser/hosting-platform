# Scaffold — bootstrap platform.thespawn.io

Step-by-step to get the repo from empty → deployed on `platform.thespawn.io` with a running Laravel app. No code written yet, just the shell.

## 0. Prereqs check

```bash
php --version        # ≥ 8.2
composer --version
bun --version
gh auth status       # logged in as SwiftAdviser
ls ~/Projects/mandate   # reference project exists
```

## 1. Copy the mandate shell (not `laravel new`)

```bash
cd ~/Projects/hosting-platform

# Copy directory structure, skip node_modules/vendor/secrets/build artifacts
rsync -av \
  --exclude='node_modules/' \
  --exclude='vendor/' \
  --exclude='.env' \
  --exclude='.env.backup' \
  --exclude='storage/logs/*' \
  --exclude='storage/framework/cache/*' \
  --exclude='storage/framework/sessions/*' \
  --exclude='storage/framework/views/*' \
  --exclude='bootstrap/cache/*.php' \
  --exclude='public/build/' \
  --exclude='bun.lock' \
  --exclude='composer.lock' \
  --exclude='.git/' \
  --exclude='tests/coverage/' \
  ~/Projects/mandate/ ./

# Preserve our own CLAUDE.md and docs/
git init
git checkout --orphan main
```

## 2. Strip mandate-specific domain

Remove everything that's about policy/quota/intent validation. Keep the Laravel shell, auth, Inertia setup, test harness.

**Delete:**
```bash
# Services
rm app/Services/PolicyEngineService.php
rm app/Services/PolicyInsightService.php
rm app/Services/QuotaManagerService.php
rm app/Services/IntentStateMachineService.php
rm app/Services/IntentSummaryService.php
rm app/Services/EnvelopeVerifierService.php
rm app/Services/CircuitBreakerService.php
rm app/Services/CalldataDecoderService.php
rm app/Services/X402FacilitatorService.php
rm app/Services/AegisService.php
rm app/Services/ReasonScannerService.php
rm app/Services/ReputationService.php
rm app/Services/AlchemyTransferSearchService.php
rm app/Services/ApprovalNotificationService.php
rm app/Services/InsightNotificationService.php
rm app/Services/PriceOracleService.php
rm app/Services/AccountLinkerService.php

# Controllers
rm app/Http/Controllers/Api/ValidateController.php
rm app/Http/Controllers/Api/IntentController.php
rm app/Http/Controllers/Api/PolicyController.php
rm app/Http/Controllers/Api/ApprovalController.php
rm app/Http/Controllers/Api/CircuitBreakerController.php
rm app/Http/Controllers/Api/DemoIntentController.php
rm app/Http/Controllers/Api/RiskCheckController.php
rm app/Http/Controllers/Api/InsightController.php
rm app/Http/Controllers/Api/ScanTelemetryController.php

# Packages we don't reuse in v0.1
rm -rf packages/sdk
rm -rf packages/mcp-server
rm -rf packages/eliza-plugin
rm -rf packages/goat-plugin
rm -rf packages/agentkit-provider
rm -rf packages/game-plugin
rm -rf packages/acp-plugin
rm -rf packages/claude-code-hook
```

**Keep (reuse as-is or adapt):**
- `app/Http/Controllers/Api/AgentRegistrationController.php` — naming still fits
- `app/Http/Controllers/Api/TelegramLinkController.php` — Telegram auth flow
- `app/Http/Controllers/Api/TelegramWebhookController.php` — Telegram webhook handler
- `app/Http/Controllers/Api/ActivateController.php` — may adapt
- `app/Http/Middleware/RuntimeKeyAuth.php` — rename/adapt for agent auth
- `packages/openclaw-plugin/` — load-bearing reference for how to package a deployable agent unit

## 3. Add our own services (empty stubs)

```bash
cat > app/Services/AgentDeployerService.php <<'PHP'
<?php
namespace App\Services;

class AgentDeployerService
{
    // Orchestrates: validate config -> charge wallet -> upload to KiloClaw -> return token ID
}
PHP

cat > app/Services/KiloClawClientService.php <<'PHP'
<?php
namespace App\Services;

class KiloClawClientService
{
    // Wraps the KiloClaw/OpenClaw host API. See docs/integrations.md §KiloClaw
}
PHP

cat > app/Services/TelegramBotRegistrarService.php <<'PHP'
<?php
namespace App\Services;

class TelegramBotRegistrarService
{
    // Validates user-provided bot token, registers webhook, stores chat pairing state
}
PHP

cat > app/Services/OnChainOSPaymentService.php <<'PHP'
<?php
namespace App\Services;

class OnChainOSPaymentService
{
    // Charges the connected wallet for the deploy fee. See docs/integrations.md §OnChainOS
}
PHP
```

## 4. Rename and prune configuration

```bash
# composer.json name, description, URL
# package.json name
# README.md — wipe, we'll write fresh
# .env.example — strip mandate-specific keys, add ours (see below)
```

**`.env.example` target contents** (only what v0.1 actually needs):

```env
APP_NAME="platform.thespawn.io"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=https://platform.thespawn.io

DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite

# Auth
SESSION_DRIVER=database

# KiloClaw / OpenClaw host
KILOCLAW_API_URL=
KILOCLAW_API_KEY=

# OnChainOS payment rail
ONCHAINOS_API_URL=
ONCHAINOS_MERCHANT_ID=
ONCHAINOS_WEBHOOK_SECRET=

# X-Layer chain
XLAYER_RPC_URL=
XLAYER_CHAIN_ID=

# Telegram (bot-token-per-user, not global)
TELEGRAM_WEBHOOK_BASE=https://platform.thespawn.io/api/telegram/webhook

# Anthropic (for future v0.2 direct connect; v0.1 runs through KiloClaw)
ANTHROPIC_API_KEY=
```

## 5. Fresh install + migrate

```bash
composer install
bun install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
composer dev   # starts Laravel + queue worker + Vite
```

Expected green: `http://localhost:8000` shows Inertia landing.

## 6. First commit

```bash
git add -A
git status | head -40
git commit -m "chore: scaffold from mandate shell, strip domain, add hosting-platform service stubs"
gh repo create SwiftAdviser/hosting-platform --private --source=. --push
```

## 7. DNS + Coolify

**DNS** (via `/domain-dns-ops`):
- Add `A` record for `platform.thespawn.io` → VPS IP `198.244.202.203`
- TTL `auto`
- Proxied: true (so Cloudflare handles TLS + DDoS)

**Coolify** (via `/coolify-ops`):
- Create new app in Coolify
  - Server: `krutovoy-vps` (UUID `q4cgokcc4gsgcw00oo0wwsw4`)
  - Destination: `a4soc8ccgcowkg8o0ow4kkss`
  - Build pack: Nixpacks (mandate parity — has `nixpacks.toml`)
  - Git source: `github.com/SwiftAdviser/hosting-platform` (private, connect via GitHub App)
  - Branch: `main`
  - Domain: `platform.thespawn.io`
- Paste env vars from `.env.example` (populated values, see Secrets section)
- Click Deploy, watch build logs

**Expected green:** `https://platform.thespawn.io` returns the Inertia landing page with TLS.

## 8. Secrets

All goes into Coolify app env, never committed. For v0.1 you need real values for:
- `KILOCLAW_API_KEY` — from Dimas's account reset (blocker #2)
- `ONCHAINOS_*` — from Day 0 decision on payment flow (blocker #1)
- `XLAYER_RPC_URL` — public endpoint, trivial
- `ANTHROPIC_API_KEY` — from `~/.profile` or 1Password

## Done when

- `https://platform.thespawn.io` loads Inertia landing with TLS
- `composer test` passes (empty tests are fine)
- GitHub Actions build (if mandate parity is kept) is green
- The repo has CLAUDE.md, PRD, this scaffold doc, integrations.md, sprint_v0.1.md

Ready to start work from `sprint_v0.1.md`.
