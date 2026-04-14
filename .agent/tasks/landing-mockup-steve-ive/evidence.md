# Evidence Bundle: landing-mockup-steve-ive

## Summary
- Overall status: PASS
- Last updated: 2026-04-13
- Output file: design/landing.html (2047 bytes, 20 visible body words)
- CTA: "Post agent"
- Theme: dark
- Primary font: "JetBrains Mono"

## Acceptance criteria evidence

### AC1 -- file at design/landing.html, design/ exists
- Status: PASS
- Proof:
  - artifacts/file-info.txt: `design/landing.html: HTML document text, ASCII text`
  - artifacts/post-build-ls.txt lists `design` at repo root
- Gaps: none

### AC2 -- DOCTYPE first, html lang
- Status: PASS
- Proof:
  - artifacts/file-info.txt: `head -1` -> `<!DOCTYPE html>`
  - design/landing.html line 2: `<html lang="en">`
- Gaps: none

### AC3 -- charset utf-8 and viewport meta
- Status: PASS
- Proof:
  - design/landing.html line 4: `<meta charset="utf-8">`
  - design/landing.html line 5: `<meta name="viewport" content="width=device-width, initial-scale=1">`
- Gaps: none

### AC4 -- exactly one title, <=5 words, statement
- Status: PASS
- Proof:
  - artifacts/grep-forbidden.txt + manual grep: `<title>` count = 1
  - Title text: "One click. Agent hosted." (4 words, declarative statement, no marketing fluff)
- Gaps: none

### AC5 -- exactly one primary CTA, verb phrase <=3 words
- Status: PASS
- Proof:
  - `<button>` count = 0; `<a ` count = 1 (the CTA)
  - CTA element: `<a class="cta" href="#post">Post agent</a>`
  - "Post agent" matches `^(Post|Deploy|Spawn|Launch|Ship|Run|Start) [A-Za-z]+$`
  - artifacts/accent-count.txt: CTA text "Post agent" occurrences = 1
- Gaps: none

### AC6 -- no secondary CTA, no "Learn more"
- Status: PASS
- Proof:
  - `<button>` count = 0
  - `learn more` (case-insensitive) count = 0 in artifacts/grep-forbidden.txt-adjacent checks (verified inline)
  - No nav, no link list, only the single CTA `<a>`
- Gaps: none

### AC7 -- no script, no src=, no external links, no @import, no CDN
- Status: PASS
- Proof:
  - `<script` count = 0
  - `src=` count = 0
  - `<link href="http` / `//` count = 0 (no `<link>` tags at all)
  - `@import` count = 0
  - `fonts.googleapis.com` count = 0
- Gaps: none

### AC8 -- single <style> block, no inline style= attrs
- Status: PASS
- Proof:
  - `<style` count = 1 (inside `<head>`)
  - ` style=` count = 0
- Gaps: none

### AC9 -- buzzword ban
- Status: PASS
- Proof:
  - artifacts/grep-forbidden.txt shows zero matches for every banned word: revolutionary, seamless, unleash, harness, empower, best-in-class, world-class, cutting-edge, leverage, synergy, streamline, next-generation, game-changer, transform, we're excited, introducing, unlock
- Gaps: none

### AC10 -- no em or en dash
- Status: PASS
- Proof:
  - artifacts/grep-forbidden.txt: em-dash U+2014 = 0, en-dash U+2013 = 0
- Gaps: none

### AC11 -- no exclamation marks in body
- Status: PASS
- Proof:
  - artifacts/grep-forbidden.txt: raw `!` count = 1 (the `<!DOCTYPE` marker), post-strip count = 0
  - Verifier strips `<!DOCTYPE` per spec, so effective body bang count = 0
- Gaps: none

### AC12 -- crypto hidden
- Status: PASS
- Proof:
  - artifacts/grep-forbidden.txt: zero matches for X-Layer, EVM, chain, gas, signature, USDC, ETH, 0x, blockchain, crypto, wallet
- Gaps: none

### AC13 -- mentions "agent"
- Status: PASS
- Proof:
  - h1 contains "Agent hosted.", lede contains "Paste your agent.", CTA is "Post agent"
- Gaps: none

### AC14 -- :root with at least 3 tokens including bg/fg/accent
- Status: PASS
- Proof:
  - design/landing.html lines 9 to 18: `:root { --bg, --fg, --muted, --rule, --tint, --accent, --radius, --max }` (8 declarations)
  - bg = `--bg`, fg = `--fg`, accent = `--accent`
- Gaps: none

### AC15 -- no hardcoded colors outside :root
- Status: PASS
- Proof:
  - Python regex sweep (logged inline): all `#hex`, `rgb(`, `rgba(`, `hsl(`, `hsla(` literals appear only inside the `:root { ... }` block; non-root content has zero matches
- Gaps: none

### AC16 -- non-default font family with 3+ fallbacks
- Status: PASS
- Proof:
  - body `font-family: "JetBrains Mono", "IBM Plex Mono", "SF Mono", "Fira Code", ui-monospace, monospace`
  - Primary: "JetBrains Mono" (not in ban list)
  - Fallbacks after primary: 5 (>= 3)
- Gaps: none

### AC17 -- theme commitment documented
- Status: PASS
- Proof:
  - design/landing.html line 9 (top of `<style>`): `/* theme: dark */`
- Gaps: none

### AC18 -- single accent var, only on CTA selectors
- Status: PASS
- Proof:
  - artifacts/accent-count.txt:
    - L15: `--accent: #c6ff3d;` (declaration in :root)
    - L71: `background: var(--accent);` inside `a.cta { ... }`
    - L82: `outline: 2px solid var(--accent);` inside `a.cta:hover, a.cta:focus, a.cta:focus-visible { ... }`
  - Body background uses `--tint` (neutral white), not `--accent`
- Gaps: none

### AC19 -- responsive sizing
- Status: PASS
- Proof:
  - h1 uses `font-size: clamp(2.5rem, 7vw, 5.5rem)`
  - p uses `font-size: clamp(1rem, 1.5vw, 1.15rem)`
  - body padding uses `clamp(1.25rem, 4vw, 3rem)`
  - Also: `@media (max-width: 640px) { ... }` block present
- Gaps: none

### AC20 -- file size < 12288 bytes
- Status: PASS
- Proof:
  - artifacts/size-check.txt: `stat -f%z` = 2047 bytes (16.6% of budget)
- Gaps: none

### AC21 -- visible body word count < 60
- Status: PASS
- Proof:
  - Python tag/style/comment strip: 20 whitespace-separated tokens
  - Tokens: One, click., Agent, hosted., One, click., Agent, hosted., Paste, your, agent., We, run, it., You, keep, writing, code., Post, agent
  - (Note: title text "One click. Agent hosted." is double-counted because the strip also runs over the head; even so 20 < 60.)
- Gaps: none

### AC22 -- no collateral damage
- Status: PASS
- Proof:
  - artifacts/pre-build-ls.txt vs artifacts/post-build-ls.txt diff: only addition at repo root is `design`
  - The only files written outside `.agent/tasks/landing-mockup-steve-ive/` are `design/landing.html` and the `design/` directory itself
- Gaps: none

## Commands run
- `mkdir -p .agent/tasks/landing-mockup-steve-ive/artifacts`
- `ls /Users/krutovoy/Projects/hosting-platform/ > artifacts/pre-build-ls.txt`
- Wrote `design/landing.html`
- `wc -c design/landing.html` -> 2047
- `wc -w design/landing.html` -> 241 (includes CSS tokens; visible body word count was measured via tag-stripping python script and is 20)
- `stat -f%z design/landing.html` -> 2047
- Python AST/regex sweep for color literals, accent usage, dashes, exclamation marks, visible word count
- grep sweep for every banned word in AC9 and AC12
- `file design/landing.html`, `head -1 design/landing.html` -> artifacts/file-info.txt
- `ls /Users/krutovoy/Projects/hosting-platform/ > artifacts/post-build-ls.txt`
- `diff artifacts/pre-build-ls.txt artifacts/post-build-ls.txt` -> only `design` added

## Raw artifacts
- .agent/tasks/landing-mockup-steve-ive/artifacts/pre-build-ls.txt
- .agent/tasks/landing-mockup-steve-ive/artifacts/post-build-ls.txt
- .agent/tasks/landing-mockup-steve-ive/artifacts/size-check.txt
- .agent/tasks/landing-mockup-steve-ive/artifacts/grep-forbidden.txt
- .agent/tasks/landing-mockup-steve-ive/artifacts/accent-count.txt
- .agent/tasks/landing-mockup-steve-ive/artifacts/file-info.txt
- .agent/tasks/landing-mockup-steve-ive/raw/build.txt
- .agent/tasks/landing-mockup-steve-ive/raw/screenshot-1.png (placeholder; no headless browser available)

## Known gaps
- No live-rendered screenshot. The task environment has no headless browser. The page was hand-audited statically via grep and python.
