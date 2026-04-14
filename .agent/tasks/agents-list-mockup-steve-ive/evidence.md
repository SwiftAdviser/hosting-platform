# Evidence Bundle: agents-list-mockup-steve-ive

## Summary
- Overall status: PASS
- Last updated: 2026-04-13
- Output: `/Users/krutovoy/Projects/hosting-platform/design/agents.html` (3501 bytes, 18 visible body words)

## Acceptance criteria evidence

### AC1: file exists at design/agents.html
- Status: PASS
- Proof:
  - `ls design/` returns `agents.html landing.html wizard.html` (artifacts/post-build-ls.txt).
  - `test -f design/agents.html` returns 0.
- Gaps: none.

### AC2: doctype, lang, charset, viewport
- Status: PASS
- Proof:
  - First non-empty line is `<!DOCTYPE html>` (line 1).
  - `<html lang="en">` (line 2).
  - `<meta charset="utf-8">` (line 4).
  - `<meta name="viewport" content="width=device-width, initial-scale=1">` (line 5).
- Gaps: none.

### AC3: exactly one <title>, <= 5 words
- Status: PASS
- Proof:
  - `grep -c '<title>' design/agents.html` = 1 (artifacts/structure-counts.txt).
  - Title text: `Your agents.` (2 words).
- Gaps: none.

### AC4: exactly one primary CTA with text "Post agent"
- Status: PASS
- Proof:
  - `grep -cE 'class="cta"' design/agents.html` = 1.
  - The single `class="cta"` element is `<button class="cta" type="button">Post agent</button>`.
  - Stop buttons carry `class="stop"`, never `cta`.
- Gaps: none.

### AC5: no banned secondary CTA labels
- Status: PASS
- Proof:
  - All eight banned phrases (`Learn more`, `Read more`, `See more`, `Get started`, `Sign up`, `Sign in`, `Log in`, `More info`) return 0 case-insensitive matches.
- Gaps: none.

### AC6: 3 sample rows, names atlas/nova/sage present
- Status: PASS
- Proof:
  - `grep -c 'class="row"' design/agents.html` = 3.
  - `grep -c 'atlas'` >= 1, `grep -c 'nova'` >= 1, `grep -c 'sage'` >= 1.
- Gaps: none.

### AC7: button count in {3,4}, three stop buttons
- Status: PASS
- Proof:
  - `grep -c '<button' design/agents.html` = 4 (3 stop + 1 cta).
  - `grep -cE 'class="(stop|ghost)"' design/agents.html` = 3.
- Gaps: none.

### AC8: telegram short label, no protocol, no `://`
- Status: PASS
- Proof:
  - Visible text contains `@atlasbot`, `@novabot`, `@sagebot`.
  - `grep -c 'https://'` = 0; `grep -c 'http://'` = 0; `grep -c '://'` = 0.
- Gaps: none.

### AC9: no script, src, link, import, CDN
- Status: PASS
- Proof:
  - `grep -c '<script'` = 0; `grep -c 'src='` = 0; `grep -c '@import'` = 0.
  - No `<link>` tag, no CDN host references.
- Gaps: none.

### AC10: single <style> in head, no inline style attrs
- Status: PASS
- Proof:
  - `grep -c '<style'` = 1; `grep -c ' style='` = 0.
  - The `<style>` block is in `<head>` (lines 7..133).
- Gaps: none.

### AC11: marketing buzzword ban
- Status: PASS
- Proof:
  - All 17 banned words (incl. `transform`, `unlock`, `introducing`) return 0 case-insensitive matches (artifacts/grep-forbidden.txt).
- Gaps: none.

### AC12: no em-dash or en-dash
- Status: PASS
- Proof:
  - U+2014 count = 0; U+2013 count = 0.
- Gaps: none.

### AC13: no exclamation marks in visible body
- Status: PASS
- Proof:
  - Python strip of `<style>`, comments, doctype, attributes, tags then `count('!')` = 0.
- Gaps: none.

### AC14: crypto-hidden ban, ZERO including wallet
- Status: PASS
- Proof:
  - All 11 banned terms (`X-Layer`, `EVM`, `chain`, `gas`, `signature`, `USDC`, `ETH`, `blockchain`, `crypto`, `0x`, `wallet`) return 0 case-insensitive word-boundary matches (artifacts/grep-forbidden.txt).
- Gaps: none.

### AC15: page mentions "agent"
- Status: PASS
- Proof:
  - `grep -ci 'agent' design/agents.html` >= 1 (heading `Three agents.`, title `Your agents.`, CTA `Post agent`).
- Gaps: none.

### AC16: :root tokens byte-identical to landing.html
- Status: PASS
- Proof:
  - artifacts/landing-root.txt extracted from landing.html.
  - agents.html `:root` block (lines 9..18) declares the same 8 keys with identical values; no extra color tokens introduced.
- Gaps: none.

### AC17: font-family stack
- Status: PASS
- Proof:
  - body font-family declared at line 22: `"JetBrains Mono", "IBM Plex Mono", "SF Mono", "Fira Code", ui-monospace, monospace`.
  - First family is `"JetBrains Mono"`. None of Inter/Roboto/Arial/Helvetica/system-ui/-apple-system appear as primary.
- Gaps: none.

### AC18: var(--accent) only in CTA selectors
- Status: PASS
- Proof:
  - `grep -n 'var(--accent)' design/agents.html` returns 2 hits at lines 112 and 120.
  - Line 112 lives in `button.cta { ... }` rule (lines 105..116).
  - Line 120 lives in `button.cta:hover, button.cta:focus, button.cta:focus-visible { ... }` rule (lines 117..122).
  - Row, pill, stop, handle selectors do not reference `var(--accent)`.
- Gaps: none.

### AC19: responsive sizing
- Status: PASS
- Proof:
  - `clamp(2.25rem, 6vw, 4rem)` on `h1`.
  - `@media (max-width: 640px)` block at line 123.
- Gaps: none.

### AC20: file size < 14336 bytes
- Status: PASS
- Proof:
  - `wc -c design/agents.html` = 3501 bytes (artifacts/size-check.txt).
- Gaps: none.

### AC21: visible body word count < 90
- Status: PASS
- Proof:
  - Python tag/style strip yields 18 tokens (artifacts/word-count.txt).
- Gaps: none.

### AC22: exactly one h1, text "Three agents."
- Status: PASS
- Proof:
  - `grep -c '<h1' design/agents.html` = 1.
  - Inner text equals `Three agents.` (line 137).
- Gaps: none.

### AC23: pill labels each appear exactly once
- Status: PASS
- Proof:
  - `grep -ci 'provisioning'` = 1; `grep -ci 'ready'` = 1; `grep -ci 'failed'` = 1.
- Gaps: none.

### AC24: collateral check, prior designs unchanged
- Status: PASS
- Proof:
  - artifacts/prior-designs-pre.sha256 and artifacts/prior-designs-post.sha256 are byte-identical (`diff` returns 0).
  - landing.html sha256 = 87e79f41082c177e5c79859afe16b53b95a19e27bd1258e502a1f9011c4d4d64.
  - wizard.html sha256 = 8c3dc84dd422ccd9e1e04502205d7992c29e7d6ebd91632e05f6197d3de14f18.
  - `ls design/` returns exactly `agents.html`, `landing.html`, `wizard.html`.
- Gaps: none.

### AC25: no regressions on prior tasks
- Status: PASS
- Proof:
  - `task_loop.py status` for each of `bootstrap-proof-loop`, `scaffold-service-stubs`, `test-harness`, `landing-mockup-steve-ive`, `tdd-telegram-validate-token`, `wizard-mockup-steve-ive` reports `verdict_overall_status: PASS` (artifacts/no-regression.txt).
  - The parallel `tdd-onchainos-create-charge` task is excluded per spec.
- Gaps: none.

## Commands run
- `shasum -a 256 design/landing.html design/wizard.html` (pre and post).
- `diff prior-designs-pre.sha256 prior-designs-post.sha256` -> identical.
- `wc -c design/agents.html` -> 3501.
- Python visible-word count -> 18.
- Buzzword/crypto/dash/transform grep sweep -> all 0.
- `grep -n 'var(--accent)' design/agents.html` -> lines 112, 120 (both inside `button.cta` rules).
- `grep -c '<button' design/agents.html` -> 4.
- `grep -cE 'class="(stop|ghost)"' design/agents.html` -> 3.
- `grep -c 'class="row"' design/agents.html` -> 3.
- `grep -c '<h1'` -> 1; `grep -c '<title>'` -> 1; `grep -c '<style'` -> 1.
- `grep -ci 'ready' / 'provisioning' / 'failed'` -> 1 / 1 / 1.
- `python3 .claude/skills/repo-task-proof-loop/scripts/task_loop.py status --task-id <prior>` for six prior tasks -> all PASS.

## Raw artifacts
- .agent/tasks/agents-list-mockup-steve-ive/raw/build.txt
- .agent/tasks/agents-list-mockup-steve-ive/artifacts/prior-designs-pre.sha256
- .agent/tasks/agents-list-mockup-steve-ive/artifacts/prior-designs-post.sha256
- .agent/tasks/agents-list-mockup-steve-ive/artifacts/landing-root.txt
- .agent/tasks/agents-list-mockup-steve-ive/artifacts/size-check.txt
- .agent/tasks/agents-list-mockup-steve-ive/artifacts/word-count.txt
- .agent/tasks/agents-list-mockup-steve-ive/artifacts/grep-forbidden.txt
- .agent/tasks/agents-list-mockup-steve-ive/artifacts/structure-counts.txt
- .agent/tasks/agents-list-mockup-steve-ive/artifacts/accent-count.txt
- .agent/tasks/agents-list-mockup-steve-ive/artifacts/no-regression.txt
- .agent/tasks/agents-list-mockup-steve-ive/artifacts/pre-build-ls.txt
- .agent/tasks/agents-list-mockup-steve-ive/artifacts/post-build-ls.txt
- .agent/tasks/agents-list-mockup-steve-ive/landing.sha256
- .agent/tasks/agents-list-mockup-steve-ive/wizard.sha256
