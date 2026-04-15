# Publishing openclaw-agent to GHCR

Run by Roman manually. The hosting-platform CI does not have the GHCR PAT.

## One-time

```bash
echo "$GHCR_PAT" | docker login ghcr.io -u SwiftAdviser --password-stdin
```

The PAT needs `write:packages` and `read:packages`.

## Pinned version

Current pin: `OPENCLAW_VERSION=2026.4.14`.

Confirmed via:

```bash
npm view openclaw version
# 2026.4.14
```

Roll forward by re-running `npm view openclaw version`, updating the
`OPENCLAW_VERSION` `ARG` default in `Dockerfile`, and bumping the image tag
below.

## Build (multi-arch, recommended)

```bash
cd /Users/krutovoy/Projects/hosting-platform/docker/openclaw-agent

docker buildx create --use --name openclaw-builder 2>/dev/null || \
  docker buildx use openclaw-builder

docker buildx build \
  --platform linux/amd64,linux/arm64 \
  --build-arg OPENCLAW_VERSION=2026.4.14 \
  -t ghcr.io/swiftadviser/openclaw-agent:2026.4.14 \
  -t ghcr.io/swiftadviser/openclaw-agent:latest \
  --push \
  .
```

## Build (single-arch, faster for local iteration)

```bash
cd /Users/krutovoy/Projects/hosting-platform/docker/openclaw-agent

docker build \
  --build-arg OPENCLAW_VERSION=2026.4.14 \
  -t ghcr.io/swiftadviser/openclaw-agent:2026.4.14 \
  -t ghcr.io/swiftadviser/openclaw-agent:latest \
  .

docker push ghcr.io/swiftadviser/openclaw-agent:2026.4.14
docker push ghcr.io/swiftadviser/openclaw-agent:latest
```

## Verify the published image

```bash
docker pull ghcr.io/swiftadviser/openclaw-agent:2026.4.14
docker inspect ghcr.io/swiftadviser/openclaw-agent:2026.4.14 \
  --format '{{.Config.User}} {{.Config.Entrypoint}} {{.Config.Healthcheck.Test}}'
```

Expected: `agent [/usr/bin/tini -- /home/agent/entrypoint.sh] [CMD-SHELL curl -fsS http://127.0.0.1:8080/health || exit 1]`.

## Mark the package public on GHCR

After the first push, open
`https://github.com/users/SwiftAdviser/packages/container/openclaw-agent/settings`
and switch visibility to public so Coolify on `coolz.krutovoy.me` can pull
without auth.
