# syntax=docker/dockerfile:1.6
FROM node:24-slim

ARG OPENCLAW_VERSION=2026.4.14

# System deps: tini for PID 1, dumb-init as a backup, curl for healthcheck,
# ca-certificates for npm registry TLS.
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates \
        tini \
        dumb-init \
        curl \
        git \
    && rm -rf /var/lib/apt/lists/*

# Install OpenClaw globally. Smoke check the binary so a bad pin fails the build.
RUN npm install -g openclaw@${OPENCLAW_VERSION} \
    && openclaw --version

# Non-root user. Fixed uid so volume permissions are predictable across hosts.
RUN groupadd --gid 10001 agent \
    && useradd --uid 10001 --gid 10001 --home-dir /home/agent --create-home --shell /bin/bash agent

ENV OPENCLAW_HOME=/home/agent/.openclaw \
    OPENCLAW_WORKSPACE=/home/agent/workspace \
    LLM_PROVIDER=anthropic \
    NODE_ENV=production

# Pre-create the state and workspace dirs so the volume mounts inherit ownership.
RUN mkdir -p /home/agent/.openclaw /home/agent/workspace \
    && chown -R agent:agent /home/agent

COPY --chown=agent:agent entrypoint.sh /home/agent/entrypoint.sh
RUN chmod +x /home/agent/entrypoint.sh

WORKDIR /home/agent
USER agent

VOLUME ["/home/agent/.openclaw", "/home/agent/workspace"]

EXPOSE 8080

HEALTHCHECK --interval=30s --timeout=5s --start-period=60s --retries=3 \
    CMD curl -fsS http://127.0.0.1:8080/health || exit 1

ENTRYPOINT ["/usr/bin/tini", "--", "/home/agent/entrypoint.sh"]
