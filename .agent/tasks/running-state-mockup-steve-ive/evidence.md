# Evidence Bundle: running-state-mockup-steve-ive

## Summary
- Overall status: PASS
- Last updated: 2026-04-13
- Target file: design/running.html (1397 bytes, 9 visible body words)

## Acceptance criteria evidence

### AC1: File exists at design/running.html, design/ dir exists
- Status: PASS
- Proof:
  - `ls design/` returns `agents.html landing.html running.html wizard.html` (4 entries)
  - `test -f design/running.html` exit 0
- Gaps: none

### AC2: doctype, lang, charset utf-8, viewport meta
- Status: PASS
- Proof:
  - Line 1: `<!DOCTYPE html>`
  - Line 2: `<html lang="en">`
  - Line 4: `<meta charset="utf-8">`
  - Line 5: `<meta name="viewport" content="width=device-width, initial-scale=1">`
- Gaps: none

### AC3: exactly one <title>, <= 5 words
- Status: PASS
- Proof:
  - `grep -c '<title>' design/running.html = 1`
  - Title text `Spawning` (1 word)
- Gaps: none

### AC4: zero interactive elements
- Status: PASS
- Proof: all of the following grep counts are 0:
  - `<button` 0, `<form` 0, `<input` 0, `<select` 0, `<textarea` 0, `<nav` 0, `<footer` 0, `<a href=` 0, `<script` 0
- Gaps: none

### AC5: exactly one <h1>, text byte-equals `Spawning your agent.`
- Status: PASS
- Proof:
  - `grep -c '<h1' design/running.html = 1`
  - h1 inner text: `Spawning your agent.` (3 words, ends with period, no `?`, no `!`)
- Gaps: none

### AC6: heading mentions agent/spawn/spawning/provisioning
- Status: PASS
- Proof: h1 contains both `Spawning` and `agent`
- Gaps: none

### AC7: single <p>, inner text byte-equals `This usually takes under sixty seconds.`
- Status: PASS
- Proof:
  - `grep -c '<p' design/running.html = 1`
  - p inner text: `This usually takes under sixty seconds.` (6 whitespace-separated words, within AC7 range 6..12, no `!`, `?`, `-`, `-`)
- Gaps: none

### AC8: marketing buzzword ban
- Status: PASS
- Proof: grep -ci returns 0 for each of: revolutionary, seamless, unleash, harness, empower, best-in-class, world-class, cutting-edge, leverage, synergy, streamline, next-generation, game-changer, transform, we're excited, introducing, unlock (see artifacts/grep-forbidden.txt)
- Gaps: none

### AC9: em-dash (U+2014) and en-dash (U+2013) ban
- Status: PASS
- Proof: python3 byte scan returns count 0 for chr(0x2014) and chr(0x2013)
- Gaps: none

### AC10: no `!` in visible body text (style and comments stripped)
- Status: PASS
- Proof: stripped visible body text contains 0 `!` characters
- Gaps: none

### AC11: crypto-hidden ban
- Status: PASS
- Proof: grep -ciw returns 0 for each of: X-Layer, EVM, chain, gas, signature, USDC, ETH, blockchain, crypto, 0x, wallet (see artifacts/grep-forbidden.txt)
- Gaps: none

### AC12: no scripts, no external resources, no @import, no :// outside comments, no CDN hosts
- Status: PASS
- Proof:
  - `<script` count 0, ` src=` count 0, `@import` count 0
  - `://` count 0 (after stripping HTML and CSS comments)
  - CDN host grep (fonts.googleapis.com, cdn., cloudflare.com, jsdelivr.net, unpkg.com) count 0
- Gaps: none

### AC13: exactly one <style>, no inline style= attributes
- Status: PASS
- Proof:
  - `grep -c '<style' design/running.html = 1`
  - `grep -c ' style=' design/running.html = 0`
- Gaps: none

### AC14: :root declarations byte-identical to landing.html for all 8 keys, preceded by `/* theme: dark */`
- Status: PASS
- Proof:
  - artifacts/landing-root.txt captured from landing.html lines 9-18
  - running.html :root block declares identical values: `--bg: #0a0a0b`, `--fg: #f5f5f4`, `--muted: rgba(245, 245, 244, 0.46)`, `--rule: rgba(245, 245, 244, 0.08)`, `--tint: rgba(245, 245, 244, 0.04)`, `--accent: #c6ff3d`, `--radius: 2px`, `--max: 44rem`
  - `/* theme: dark */` comment precedes the `:root {` line
- Gaps: none

### AC15: body font-family byte-exact landing stack
- Status: PASS
- Proof: body CSS declares `font-family: "JetBrains Mono", "IBM Plex Mono", "SF Mono", "Fira Code", ui-monospace, monospace`. None of Inter/Roboto/Arial/Helvetica/system-ui/-apple-system appear.
- Gaps: none

### AC16: option A accent usage (zero references)
- Status: PASS
- Proof:
  - `grep -c 'var(--accent)' design/running.html = 0`
  - `grep -c -- '--accent:' design/running.html = 1`
- Gaps: none

### AC17: responsive sizing (media query OR fluid unit on heading)
- Status: PASS
- Proof:
  - `@media (max-width: 640px) { h1 { ... } }` present
  - h1 uses `font-size: clamp(2rem, 6vw, 4.5rem)`
- Gaps: none

### AC18: file size strictly less than 10240 bytes
- Status: PASS
- Proof: `wc -c design/running.html` = 1397 bytes (< 10240)
- Gaps: none

### AC19: visible body word count strictly less than 40
- Status: PASS
- Proof: stripped body text `Spawning your agent. This usually takes under sixty seconds.` = 9 whitespace-separated tokens (< 40)
- Gaps: none

### AC20: exactly one <h1>, zero <h2>..<h6>
- Status: PASS
- Proof:
  - `grep -c '<h1' = 1`
  - `grep -cE '<h[2-6]' = 0`
- Gaps: none

### AC21: no `text-transform:` or `transform:` anywhere
- Status: PASS
- Proof:
  - `grep -c 'text-transform' = 0`
  - `grep -cE '(^|[^-a-z])transform[[:space:]]*:' = 0`
- Gaps: none

### AC22: collateral check (landing/wizard/agents sha256 match baseline, ls design returns 4 entries)
- Status: PASS
- Proof:
  - artifacts/prior-designs-pre.sha256 captured BEFORE edits
  - artifacts/prior-designs-post.sha256 captured AFTER edits
  - `diff pre post` exit 0 (SHA MATCH)
  - landing.html: 87e79f41082c177e5c79859afe16b53b95a19e27bd1258e502a1f9011c4d4d64 (matches baseline)
  - wizard.html:  8c3dc84dd422ccd9e1e04502205d7992c29e7d6ebd91632e05f6197d3de14f18 (matches baseline)
  - agents.html:  11cc4d2284b60777a01a298082648a9dbda105383dc601b58f6df2e39ec12795 (matches baseline)
  - `ls design/ | sort` returns: agents.html, landing.html, running.html, wizard.html (4 entries)
- Gaps: none

### AC23: no regressions on 10 prior tasks (excluding tdd-agent-deployer)
- Status: PASS
- Proof: ran `python3 .claude/skills/repo-task-proof-loop/scripts/task_loop.py status --task-id $t` for each of the 10 listed tasks; every one reports `verdict_overall_status: PASS`. Raw output in artifacts/no-regression.txt.
  - bootstrap-proof-loop PASS
  - scaffold-service-stubs PASS
  - test-harness PASS
  - landing-mockup-steve-ive PASS
  - tdd-telegram-validate-token PASS
  - wizard-mockup-steve-ive PASS
  - tdd-onchainos-create-charge PASS
  - agents-list-mockup-steve-ive PASS
  - tdd-kiloclaw-install PASS
  - tdd-telegram-set-webhook PASS
- Gaps: none

## Commands run
- `shasum -a 256 design/landing.html design/wizard.html design/agents.html` (pre and post)
- `ls design/` (pre and post)
- `sed -n '/:root {/,/^}/p' design/landing.html`
- Write `design/running.html`
- `wc -c design/running.html`
- python3 body word count, `!` count, dash scan
- grep sweep for forbidden tags, buzzwords, crypto terms, CDNs
- `python3 .claude/skills/repo-task-proof-loop/scripts/task_loop.py status --task-id <t>` for 10 prior tasks

## Raw artifacts
- design/running.html (target)
- .agent/tasks/running-state-mockup-steve-ive/raw/build.txt
- .agent/tasks/running-state-mockup-steve-ive/artifacts/prior-designs-pre.sha256
- .agent/tasks/running-state-mockup-steve-ive/artifacts/prior-designs-post.sha256
- .agent/tasks/running-state-mockup-steve-ive/artifacts/pre-build-ls.txt
- .agent/tasks/running-state-mockup-steve-ive/artifacts/post-build-ls.txt
- .agent/tasks/running-state-mockup-steve-ive/artifacts/landing-root.txt
- .agent/tasks/running-state-mockup-steve-ive/artifacts/size-check.txt
- .agent/tasks/running-state-mockup-steve-ive/artifacts/word-count.txt
- .agent/tasks/running-state-mockup-steve-ive/artifacts/grep-forbidden.txt
- .agent/tasks/running-state-mockup-steve-ive/artifacts/no-regression.txt

## Known gaps
- none
