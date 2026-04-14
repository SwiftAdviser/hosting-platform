# Evidence Bundle: wizard-mockup-steve-ive

## Summary
- Overall status: PASS
- Last updated: 2026-04-13T00:00:00Z
- File built: design/wizard.html
- Bytes: 3049 (limit < 14336)
- Visible body words: 21 (limit < 80)
- CTA text: Post agent
- Theme: dark (matches landing.html)
- Font primary: "JetBrains Mono"
- Accent: #c6ff3d, used only on button.cta and its hover/focus rules
- landing.html sha256 unchanged: 87e79f41082c177e5c79859afe16b53b95a19e27bd1258e502a1f9011c4d4d64

## Acceptance criteria evidence

### AC1 - file exists at design/wizard.html
- Status: PASS
- Proof: `ls design/` returns `landing.html` and `wizard.html`. See artifacts/post-build-ls.txt.
- Gaps: none

### AC2 - HTML5 doctype, lang, charset, viewport
- Status: PASS
- Proof: `head -1 design/wizard.html` returns `<!DOCTYPE html>`. Line 2 is `<html lang="en">`. `<meta charset="utf-8">` and `<meta name="viewport" content="width=device-width, initial-scale=1">` present. See artifacts/file-info.txt.
- Gaps: none

### AC3 - exactly one <title>, <= 5 words
- Status: PASS
- Proof: `grep -c '<title>'` returns 1. Title text "New agent." is 2 words.
- Gaps: none

### AC4 - exactly one CTA, text "Post agent", <button type="submit">
- Status: PASS
- Proof: One `<button class="cta" type="submit">Post agent</button>` at line 135. Matches verb-phrase allowlist (Post + agent).
- Gaps: none

### AC5 - no secondary CTA, no banned phrases, exactly one <button>
- Status: PASS
- Proof: Zero matches for Learn more / Read more / See more / Get started / Sign up / Sign in / Log in / More info / Back / Cancel in visible text. `grep -c '<button'` returns 1. See artifacts/grep-forbidden.txt and artifacts/element-counts.txt.
- Gaps: none

### AC6 - exactly four form fields with required shape
- Status: PASS
- Proof:
  - `<input id="name" name="name" type="text" required placeholder="atlas">`
  - `<textarea id="personality" name="personality" required></textarea>`
  - `<input id="bot_token" name="bot_token" type="text" required placeholder="123456:abcdef">`
  - `<input id="allowlist" name="allowlist" type="text" placeholder="42, 7, 1024">` (no required)
  - Plus one submit `<button class="cta" type="submit">`
- Gaps: none

### AC7 - every field labeled
- Status: PASS
- Proof: each field has a sibling `<label for="ID">` matching its `id` (name, personality, bot_token, allowlist).
- Gaps: none

### AC8 - exactly one <form>, method="post", action without ://
- Status: PASS
- Proof: `grep -c '<form'` returns 1. `<form method="post" action="#post">`. `grep -c '://'` returns 0.
- Gaps: none

### AC9 - no script, src, link, @import, ://, CDN
- Status: PASS
- Proof: `grep -c '<script'`=0, `grep -c 'src='`=0, `grep -c '<link'`=0, `grep -c '@import'`=0, `grep -cE 'https?://'`=0, `grep -c '://'`=0. See artifacts/element-counts.txt.
- Gaps: none

### AC10 - all CSS in one <style>, no inline style=
- Status: PASS
- Proof: `grep -c '<style'`=1, `grep -c ' style='`=0.
- Gaps: none

### AC11 - marketing buzzword ban
- Status: PASS
- Proof: zero matches for revolutionary, seamless, unleash, harness, empower, best-in-class, world-class, cutting-edge, leverage, synergy, streamline, next-generation, game-changer, transform, we're excited, introducing, unlock. Note: `text-transform` was removed in favor of all-caps label text and letter-spacing only. See artifacts/grep-forbidden.txt.
- Gaps: none

### AC12 - no em-dash or en-dash
- Status: PASS
- Proof: U+2014 count = 0, U+2013 count = 0.
- Gaps: none

### AC13 - no exclamation marks in visible body text
- Status: PASS
- Proof: after stripping <style>, comments, doctype, and tags, `!` count = 0.
- Gaps: none

### AC14 - crypto hidden
- Status: PASS
- Proof: zero matches for X-Layer, EVM, chain, gas, signature, USDC, blockchain, crypto, 0x. ETH word-boundary count = 0. wallet count = 0. (Note: substring `eth` appears once inside the HTML attribute name `method="post"`, which is required by AC8 and is not the ETH token.)
- Gaps: none

### AC15 - mentions agent
- Status: PASS
- Proof: `agent` appears 3 times case-insensitive (h1, label none, CTA, plus title).
- Gaps: none

### AC16 - :root tokens byte-identical to landing.html
- Status: PASS
- Proof: :root block at lines 9-18 declares --bg, --fg, --muted, --rule, --tint, --accent, --radius, --max with the exact values from design/landing.html lines 10-17 (`#0a0a0b`, `#f5f5f4`, `rgba(245, 245, 244, 0.46)`, `rgba(245, 245, 244, 0.08)`, `rgba(245, 245, 244, 0.04)`, `#c6ff3d`, `2px`, `44rem`). See artifacts/landing-root.txt.
- Gaps: none

### AC17 - font-family stack matches landing.html
- Status: PASS
- Proof: `body { font-family: "JetBrains Mono", "IBM Plex Mono", "SF Mono", "Fira Code", ui-monospace, monospace; }`. None of Inter, Roboto, Arial, Helvetica, system-ui, -apple-system appear.
- Gaps: none

### AC18 - var(--accent) only on CTA selectors
- Status: PASS
- Proof: `grep -n 'var(--accent)'` returns 2 lines (97 background, 105 outline). Line 97 is inside `button.cta { ... }` (lines 89-101). Line 105 is inside `button.cta:hover, button.cta:focus, button.cta:focus-visible { ... }` (lines 102-107). No form-input rule references --accent. See artifacts/accent-count.txt.
- Gaps: none

### AC19 - responsive sizing
- Status: PASS
- Proof: file contains `@media (max-width: 640px)` and `clamp(2.25rem, 6vw, 4rem)` on h1 font-size and `clamp(2.5rem, 6vw, 5rem) clamp(1.25rem, 4vw, 3rem)` on body padding.
- Gaps: none

### AC20 - file size strictly under 14336 bytes
- Status: PASS
- Proof: `stat -f%z design/wizard.html` returns 3049. See artifacts/size-check.txt.
- Gaps: none

### AC21 - visible body word count strictly under 80
- Status: PASS
- Proof: python3 strip-and-count returns 21 words. See artifacts/word-count.txt.
- Gaps: none

### AC22 - collateral check, only design/wizard.html added
- Status: PASS
- Proof: pre-build vs post-build `ls design/` diff shows only `wizard.html` added. Top-level `ls /Users/krutovoy/Projects/hosting-platform/` unchanged. See artifacts/pre-build-ls.txt and artifacts/post-build-ls.txt.
- Gaps: none

### AC23 - design/landing.html byte-identical
- Status: PASS
- Proof: pre-build sha256 = 87e79f41082c177e5c79859afe16b53b95a19e27bd1258e502a1f9011c4d4d64. post-build sha256 = same. Stored at .agent/tasks/wizard-mockup-steve-ive/landing.sha256 and artifacts/landing-pre.sha256, verified against artifacts/landing-post.sha256. `diff` exit 0.
- Gaps: none

### AC24 - placeholder XOR hint per field
- Status: PASS
- Proof: name has placeholder, no hint. personality has hint paragraph, no placeholder. bot_token has placeholder, no hint. allowlist has placeholder, no hint. No field carries both.
- Gaps: none

### AC25 - submit is the only <button>
- Status: PASS
- Proof: `grep -c '<button' design/wizard.html` returns 1.
- Gaps: none

## Commands run
- `shasum -a 256 design/landing.html` (pre and post)
- `mkdir -p .agent/tasks/wizard-mockup-steve-ive/artifacts`
- `ls /Users/krutovoy/Projects/hosting-platform/`
- `ls /Users/krutovoy/Projects/hosting-platform/design/`
- write `design/wizard.html` via Write tool
- `wc -c design/wizard.html`
- `wc -w design/wizard.html`
- `stat -f%z design/wizard.html`
- python3 visible-word-count strip
- python3 forbidden-term scan
- `grep -n 'var(--accent)' design/wizard.html`
- `grep -c '<button' design/wizard.html`
- `grep -c '<form' design/wizard.html`
- `grep -c '<style' design/wizard.html`
- `grep -c ' style=' design/wizard.html`
- `grep -c '<title>' design/wizard.html`
- `grep -c '<script' design/wizard.html`
- `grep -c 'src=' design/wizard.html`
- `grep -c '@import' design/wizard.html`
- `grep -cE 'https?://' design/wizard.html`
- `grep -c '<link' design/wizard.html`
- `grep -c '://' design/wizard.html`
- `head -1 design/wizard.html`
- `file design/wizard.html`
- `python3 task_loop.py status --task-id bootstrap-proof-loop`
- `python3 task_loop.py status --task-id scaffold-service-stubs`
- `python3 task_loop.py status --task-id test-harness`
- `python3 task_loop.py status --task-id landing-mockup-steve-ive`

## Raw artifacts
- .agent/tasks/wizard-mockup-steve-ive/raw/build.txt
- .agent/tasks/wizard-mockup-steve-ive/raw/test-unit.txt
- .agent/tasks/wizard-mockup-steve-ive/raw/test-integration.txt
- .agent/tasks/wizard-mockup-steve-ive/raw/lint.txt
- .agent/tasks/wizard-mockup-steve-ive/raw/screenshot-1.png
- .agent/tasks/wizard-mockup-steve-ive/landing.sha256
- .agent/tasks/wizard-mockup-steve-ive/artifacts/landing-pre.sha256
- .agent/tasks/wizard-mockup-steve-ive/artifacts/landing-post.sha256
- .agent/tasks/wizard-mockup-steve-ive/artifacts/landing-root.txt
- .agent/tasks/wizard-mockup-steve-ive/artifacts/pre-build-ls.txt
- .agent/tasks/wizard-mockup-steve-ive/artifacts/post-build-ls.txt
- .agent/tasks/wizard-mockup-steve-ive/artifacts/size-check.txt
- .agent/tasks/wizard-mockup-steve-ive/artifacts/word-count.txt
- .agent/tasks/wizard-mockup-steve-ive/artifacts/grep-forbidden.txt
- .agent/tasks/wizard-mockup-steve-ive/artifacts/accent-count.txt
- .agent/tasks/wizard-mockup-steve-ive/artifacts/element-counts.txt
- .agent/tasks/wizard-mockup-steve-ive/artifacts/file-info.txt
- .agent/tasks/wizard-mockup-steve-ive/artifacts/status-bootstrap-proof-loop.txt
- .agent/tasks/wizard-mockup-steve-ive/artifacts/status-scaffold-service-stubs.txt
- .agent/tasks/wizard-mockup-steve-ive/artifacts/status-test-harness.txt
- .agent/tasks/wizard-mockup-steve-ive/artifacts/status-landing-mockup-steve-ive.txt

## Known gaps
- None.
