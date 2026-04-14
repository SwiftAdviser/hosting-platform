#!/usr/bin/env python3
"""Smoke-test the repo-task-proof-loop skill package."""

from __future__ import annotations

import json
from pathlib import Path
import re
import subprocess
import sys
import tempfile


REQUIRED_FRONTMATTER_KEYS = {"name", "description"}


def parse_frontmatter(path: Path) -> tuple[dict[str, str], str]:
    text = path.read_text(encoding="utf-8")
    match = re.match(r"^---\n(.*?)\n---\n(.*)$", text, re.DOTALL)
    if not match:
        raise ValueError("SKILL.md must start with YAML frontmatter.")
    frontmatter_text, body = match.groups()
    data: dict[str, str] = {}
    current_key: str | None = None
    for raw_line in frontmatter_text.splitlines():
        line = raw_line.rstrip()
        if not line.strip():
            continue
        if re.match(r"^[A-Za-z0-9_-]+:", line):
            key, value = line.split(":", 1)
            data[key.strip()] = value.strip().strip('"')
            current_key = key.strip()
        elif current_key and line.startswith("  "):
            # Ignore nested metadata content for this smoke test.
            continue
    return data, body


def run(cmd: list[str], cwd: Path) -> subprocess.CompletedProcess[str]:
    return subprocess.run(cmd, cwd=cwd, check=True, text=True, capture_output=True)


def main() -> int:
    skill_root = Path(__file__).resolve().parent.parent
    skill_md = skill_root / "SKILL.md"
    task_loop = skill_root / "scripts" / "task_loop.py"

    frontmatter, body = parse_frontmatter(skill_md)
    missing = sorted(REQUIRED_FRONTMATTER_KEYS - set(frontmatter.keys()))
    if missing:
        raise SystemExit(f"SKILL.md frontmatter missing keys: {', '.join(missing)}")

    if frontmatter["name"] != skill_root.name:
        raise SystemExit("SKILL.md name must match the parent directory name.")

    if not re.fullmatch(r"[a-z0-9]+(?:-[a-z0-9]+)*", frontmatter["name"]):
        raise SystemExit("SKILL.md name does not match the allowed skill-name pattern.")

    if not body.strip():
        raise SystemExit("SKILL.md body must not be empty.")

    with tempfile.TemporaryDirectory(prefix="repo-task-proof-loop-") as tmp_dir:
        repo = Path(tmp_dir) / "demo-repo"
        repo.mkdir(parents=True)
        run(["git", "init"], repo)

        init_result = run(
            [
                sys.executable,
                str(task_loop),
                "init",
                "--task-id",
                "demo-task",
                "--task-text",
                "Implement a demo task.",
                "--guides",
                "both",
                "--install-subagents",
                "both",
            ],
            repo,
        )
        validate_result = subprocess.run(
            [
                sys.executable,
                str(task_loop),
                "validate",
                "--task-id",
                "demo-task",
            ],
            cwd=repo,
            text=True,
            capture_output=True,
        )
        status_result = run(
            [
                sys.executable,
                str(task_loop),
                "status",
                "--task-id",
                "demo-task",
            ],
            repo,
        )

        validate_json = json.loads(validate_result.stdout)
        if validate_result.returncode != 0 or not validate_json.get("valid"):
            raise SystemExit(f"Validation failed: {validate_result.stdout}\n{validate_result.stderr}")

        required_paths = [
            repo / ".agent" / "tasks" / "demo-task" / "spec.md",
            repo / ".agent" / "tasks" / "demo-task" / "evidence.json",
            repo / ".agent" / "tasks" / "demo-task" / "verdict.json",
            repo / ".agent" / "tasks" / "demo-task" / "raw" / "screenshot-1.png",
            repo / ".codex" / "agents" / "task-spec-freezer.toml",
            repo / ".claude" / "agents" / "task-spec-freezer.md",
            repo / "AGENTS.md",
            repo / "CLAUDE.md",
        ]
        for path in required_paths:
            if not path.exists():
                raise SystemExit(f"Expected path missing after init: {path}")

        print(json.dumps(
            {
                "skill_root": str(skill_root),
                "frontmatter_name": frontmatter["name"],
                "init_stdout": json.loads(init_result.stdout),
                "validate_stdout": validate_json,
                "status_stdout": json.loads(status_result.stdout),
                "result": "PASS",
            },
            indent=2,
        ))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
