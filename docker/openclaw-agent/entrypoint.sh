#!/usr/bin/env bash
# OpenClaw per-tenant gateway entrypoint.
# Idempotent: re-running on an existing volume must not re-init.
# Never echo env vars. No `set -x`.

set -euo pipefail

require_env() {
    local name="$1"
    if [ -z "${!name:-}" ]; then
        echo "[entrypoint] missing required env: ${name}" >&2
        exit 1
    fi
}

require_env AGENT_NAME
require_env AGENT_PERSONALITY
require_env TELEGRAM_BOT_TOKEN
require_env LLM_API_KEY
require_env WEBHOOK_PUBLIC_URL

: "${LLM_PROVIDER:=anthropic}"
: "${OPENCLAW_HOME:=/home/agent/.openclaw}"
: "${OPENCLAW_WORKSPACE:=/home/agent/workspace}"

mkdir -p "$OPENCLAW_HOME" "$OPENCLAW_WORKSPACE"

MARKER="$OPENCLAW_WORKSPACE/.provisioned"

if [ ! -f "$MARKER" ]; then
    echo "[entrypoint] first boot: provisioning workspace at $OPENCLAW_WORKSPACE"
    cd "$OPENCLAW_WORKSPACE"

    # OpenClaw 2026.4.x has no `openclaw init` and `configure` is interactive
    # only. Provisioning shape verified against `openclaw <cmd> --help`:
    #   1. agents add --non-interactive --workspace <dir>
    #   2. config set models.providers.<provider>.apiKey <key>
    #   3. config set channels.telegram.botToken <token>
    #   4. config set channels.telegram.webhookUrl <url>
    #   5. config set channels.telegram.enabled true
    #   6. gateway run --bind lan --port 8080 (foreground)

    # Step 1: create the isolated agent. `agents add` fails idempotently if the
    # agent already exists, so we ignore that case on re-runs that lost the
    # marker but kept state.
    if ! openclaw agents add "$AGENT_NAME" \
            --non-interactive \
            --workspace "$OPENCLAW_WORKSPACE"; then
        echo "[entrypoint] agents add failed (may already exist), continuing" >&2
    fi

    # Step 2: write the system prompt file the agent reads on each turn.
    mkdir -p .openclaw
    printf '%s\n' "$AGENT_PERSONALITY" > .openclaw/SYSTEM_PROMPT.md

    # Step 3: bind the LLM provider key. Schema path:
    #   models.providers.<id>.apiKey: string
    if ! openclaw config set "models.providers.${LLM_PROVIDER}.apiKey" "$LLM_API_KEY"; then
        echo "[entrypoint] failed to set LLM provider api key" >&2
        exit 1
    fi

    # Step 4: telegram bot token + webhook. Schema path:
    #   channels.telegram.botToken    (string or SecretRef)
    #   channels.telegram.webhookUrl  (string)
    #   channels.telegram.enabled     (boolean)
    if ! openclaw config set channels.telegram.botToken "$TELEGRAM_BOT_TOKEN"; then
        echo "[entrypoint] failed to set telegram botToken" >&2
        exit 1
    fi
    if ! openclaw config set channels.telegram.webhookUrl "${WEBHOOK_PUBLIC_URL}/telegram-webhook"; then
        echo "[entrypoint] failed to set telegram webhookUrl" >&2
        exit 1
    fi
    if ! openclaw config set channels.telegram.enabled true --strict-json; then
        echo "[entrypoint] failed to enable telegram channel" >&2
        exit 1
    fi

    # Step 5: best-effort plugin install. Mandate plugin may not resolve in a
    # fresh image (private registry, network policy). Non-fatal.
    if openclaw plugins install @mandate.md/mandate-openclaw-plugin; then
        echo "[entrypoint] mandate plugin installed"
    else
        echo "[entrypoint] mandate plugin install skipped (non-fatal)"
    fi

    touch "$MARKER"
    echo "[entrypoint] provisioning complete"
else
    echo "[entrypoint] workspace already provisioned, skipping init"
fi

cd "$OPENCLAW_WORKSPACE"
exec openclaw gateway run \
    --bind lan \
    --port 8080 \
    --auth none \
    --allow-unconfigured
