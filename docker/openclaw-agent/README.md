# openclaw-agent

Immutable Docker image for the per-tenant OpenClaw gateway used by
`platform.thespawn.io`. The Laravel app spawns one container per paid user from
this image. The image owns no tenant secrets at build time. All secrets arrive
at `docker run` via env vars.

## Build

```bash
docker build \
  -t ghcr.io/swiftadviser/openclaw-agent:2026.4.14 \
  -t ghcr.io/swiftadviser/openclaw-agent:latest \
  --build-arg OPENCLAW_VERSION=2026.4.14 \
  /Users/krutovoy/Projects/hosting-platform/docker/openclaw-agent/
```

`OPENCLAW_VERSION` defaults to the pin baked into `Dockerfile`. Override at
build time to roll forward.

## Run (one tenant)

```bash
docker run -d \
  --name agent-<tenant-uuid> \
  --restart unless-stopped \
  -p 8080:8080 \
  -v agent-<tenant-uuid>-state:/home/agent/.openclaw \
  -v agent-<tenant-uuid>-workspace:/home/agent/workspace \
  -e AGENT_NAME="acme-bot" \
  -e AGENT_PERSONALITY="You are a helpful Solidity reviewer." \
  -e TELEGRAM_BOT_TOKEN="..." \
  -e LLM_API_KEY="sk-ant-..." \
  -e LLM_PROVIDER="anthropic" \
  -e WEBHOOK_PUBLIC_URL="https://agents.thespawn.io/t/<tenant-uuid>" \
  ghcr.io/swiftadviser/openclaw-agent:latest
```

Container is non-root (`uid 10001`, user `agent`). State volumes must be
writable by that uid. On a fresh named volume Docker handles this; on a bind
mount the host path needs `chown 10001:10001`.

## Env vars

| name | required | notes |
|---|---|---|
| `AGENT_NAME` | yes | tenant-visible name passed to `openclaw init` |
| `AGENT_PERSONALITY` | yes | written verbatim to `.openclaw/SYSTEM_PROMPT.md` on first boot |
| `TELEGRAM_BOT_TOKEN` | yes | token from BotFather, used to register the telegram channel |
| `LLM_API_KEY` | yes | provider key, e.g. Anthropic |
| `LLM_PROVIDER` | no  | defaults to `anthropic` |
| `WEBHOOK_PUBLIC_URL` | yes | public base URL the platform routes to this container; entrypoint appends `/telegram` |
| `OPENCLAW_HOME` | no | defaults to `/home/agent/.openclaw` |
| `OPENCLAW_WORKSPACE` | no | defaults to `/home/agent/workspace` |

## Volumes

- `/home/agent/.openclaw`: gateway state, plugin DB, channel sessions
- `/home/agent/workspace`: agent working dir, holds `.openclaw/SYSTEM_PROMPT.md` and the `.provisioned` marker

The marker file `$OPENCLAW_WORKSPACE/.provisioned` makes the entrypoint
idempotent. Delete it to force a re-init on next boot.

## Health

`HEALTHCHECK` curls `http://127.0.0.1:8080/health` every 30s after a 60s
start period. The Laravel orchestrator should treat the Docker health state as
authoritative for "is this tenant up".

## CLI shape, verified against 2026.4.14

The original task spec assumed `openclaw init`, `openclaw channels add telegram`,
and `openclaw gateway start --host 0.0.0.0`. None of those exist in the real
2026.4.14 CLI. The entrypoint uses the actual primitives, verified by running
`--help` inside the built image:

| spec asked for | actual CLI used |
|---|---|
| `openclaw init --non-interactive --name --llm-provider --llm-api-key` | `openclaw agents add <name> --non-interactive --workspace <dir>` plus `openclaw config set models.providers.<provider>.apiKey <key>` |
| `openclaw channels add telegram --token --webhook` | `openclaw config set channels.telegram.botToken`, `channels.telegram.webhookUrl`, `channels.telegram.enabled true` |
| `openclaw gateway start --host 0.0.0.0 --port 8080` | `openclaw gateway run --bind lan --port 8080 --auth none --allow-unconfigured`. `gateway start` in this CLI installs a launchd/systemd service and is wrong for a container; `gateway run` is the foreground runner. `--bind lan` makes the listener reach beyond loopback so Docker port publishing works. |

Webhook URL stored is `${WEBHOOK_PUBLIC_URL}/telegram-webhook` to match the
schema default for `channels.telegram.webhookPath`.

If the upstream CLI changes again, adjust `entrypoint.sh` and bump the image
tag. Never bake tenant secrets into the image.

## Mandate plugin

The first-boot provisioner attempts
`openclaw plugins install @mandate.md/mandate-openclaw-plugin` best-effort. If
the package does not resolve in a fresh image (it may live in a private
registry), the entrypoint logs and continues. v0.1 does not require the plugin
to be present.
