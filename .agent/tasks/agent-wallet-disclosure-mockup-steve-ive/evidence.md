# Evidence Bundle: agent-wallet-disclosure-mockup-steve-ive

## Summary
- Overall status: PASS
- Last updated: 2026-04-13
- Target: `design/agent-wallet.html`
- Byte count: 2696 (< 12288)
- Visible body word count: 24 (< 80)
- wallet word-boundary count: 1 (<= 5)
- 0x substring count: 1 (inside the sample address in the single `<code>`)

## Acceptance criteria evidence

### AC1
- Status: PASS
- Proof:
  - `test -f design/agent-wallet.html` exit 0; `test -d design` exit 0.
  - `ls design/ | sort` returns: agent-wallet.html, agents.html, landing.html, running.html, wizard.html (artifacts/post-build-ls.txt).
- Gaps: []

### AC2
- Status: PASS
- Proof:
  - Line 1: `<!DOCTYPE html>`
  - Line 2: `<html lang="en">`
  - Line 4: `<meta charset="utf-8">`
  - Line 5: `<meta name="viewport" content="width=device-width, initial-scale=1">`
- Gaps: []

### AC3
- Status: PASS
- Proof:
  - `grep -c '<title>' design/agent-wallet.html` = 1
  - Title text: `Agent address` (2 words, <= 5).
- Gaps: []

### AC4
- Status: PASS
- Proof:
  - Exactly one element with class `cta`: `<a class="cta" href="agents.html">Done</a>`.
  - `grep -c '<a class="cta"' design/agent-wallet.html` = 1.
  - CTA inner text byte-equals `Done`.
  - The `<summary>` element does not carry class `cta`.
- Gaps: []

### AC5
- Status: PASS
- Proof:
  - Case-insensitive grep for each of `Learn more`, `Read more`, `See more`, `Get started`, `Sign up`, `Sign in`, `Log in`, `More info` returns 0 matches.
- Gaps: []

### AC6
- Status: PASS
- Proof:
  - `grep -c '<details' design/agent-wallet.html` = 1.
  - `grep -cE '<details[^>]+open' design/agent-wallet.html` = 0 (default closed).
- Gaps: []

### AC7
- Status: PASS
- Proof:
  - `grep -c '<summary' design/agent-wallet.html` = 1.
  - Summary inner text byte-equals `Show address` (2 words, no trailing punctuation).
- Gaps: []

### AC8
- Status: PASS
- Proof:
  - `grep -c '<code' design/agent-wallet.html` = 1.
  - Code inner text byte-equals `0x1234abcd5678ef901234abcd5678ef9012345678` (42 chars including `0x`).
- Gaps: []

### AC9
- Status: PASS
- Proof:
  - `grep -c '<script' design/agent-wallet.html` = 0.
  - `grep -c 'src=' design/agent-wallet.html` = 0.
  - `grep -c '<link ' design/agent-wallet.html` = 0.
  - `grep -c '@import' design/agent-wallet.html` = 0.
  - `grep -cF '://' design/agent-wallet.html` = 0.
  - No CDN host substrings (`fonts.googleapis.com`, `cdn.`, `cloudflare.com`, `jsdelivr.net`, `unpkg.com`).
- Gaps: []

### AC10
- Status: PASS
- Proof:
  - `grep -c '<style' design/agent-wallet.html` = 1.
  - `grep -c ' style=' design/agent-wallet.html` = 0.
- Gaps: []

### AC11
- Status: PASS
- Proof:
  - Case-insensitive count returns 0 for each of the 17 buzzwords (revolutionary, seamless, unleash, harness, empower, best-in-class, world-class, cutting-edge, leverage, synergy, streamline, next-generation, game-changer, transform, we're excited, introducing, unlock). See artifacts/grep-forbidden.txt.
- Gaps: []

### AC12
- Status: PASS
- Proof:
  - U+2014 (em dash) byte count: 0.
  - U+2013 (en dash) byte count: 0.
- Gaps: []

### AC13
- Status: PASS
- Proof:
  - Python strip of `<style>`, HTML comments, and tags leaves visible body text with 0 `!` characters.
- Gaps: []

### AC14
- Status: PASS
- Proof:
  - `grep -oiw 'wallet' design/agent-wallet.html | wc -l` = 1 (<= 5; narrow relaxation honored).
  - `grep -ciw` returns 0 for each of: chain, gas, signature, USDC, ETH, X-Layer, EVM, blockchain, crypto.
- Gaps: []

### AC15
- Status: PASS
- Proof:
  - Case-insensitive grep returns 0 for each of: X-Layer, xlayer, Base, Ethereum, Solana, Polygon, Arbitrum, Optimism.
- Gaps: []

### AC16
- Status: PASS
- Proof:
  - `grep -oF '0x' design/agent-wallet.html | wc -l` = 1.
  - The single `0x` occurrence sits inside the sample address literal inside the one `<code>` element.
- Gaps: []

### AC17
- Status: PASS
- Proof:
  - `grep -cF '0x1234abcd5678ef901234abcd5678ef9012345678' design/agent-wallet.html` = 1.
- Gaps: []

### AC18
- Status: PASS
- Proof:
  - `grep -ci 'agent' design/agent-wallet.html` returns several matches in visible body text (heading "Your agent's address.", hint "Your agent uses this wallet...", title "Agent address", label paragraph class).
- Gaps: []

### AC19
- Status: PASS
- Proof:
  - `:root` block declares all eight canonical tokens byte-identical to landing.html:
    - `--bg: #0a0a0b;`
    - `--fg: #f5f5f4;`
    - `--muted: rgba(245, 245, 244, 0.46);`
    - `--rule: rgba(245, 245, 244, 0.08);`
    - `--tint: rgba(245, 245, 244, 0.04);`
    - `--accent: #c6ff3d;`
    - `--radius: 2px;`
    - `--max: 44rem;`
  - `/* theme: dark */` comment precedes the `:root {` line.
- Gaps: []

### AC20
- Status: PASS
- Proof:
  - body CSS declares `font-family: "JetBrains Mono", "IBM Plex Mono", "SF Mono", "Fira Code", ui-monospace, monospace` byte-exact to landing.html.
  - None of Inter, Roboto, Arial, Helvetica, system-ui, -apple-system appear.
- Gaps: []

### AC21
- Status: PASS
- Proof:
  - `grep -n 'var(--accent)' design/agent-wallet.html` returns exactly two lines (artifacts/accent-count.txt):
    - line 90: `background: var(--accent);` inside `a.cta { ... }`.
    - line 101: `outline: 2px solid var(--accent);` inside `a.cta:hover, a.cta:focus, a.cta:focus-visible { ... }`.
  - No `details`, `summary`, `code`, body, `h1`, `.label`, or `.hint` selector references `var(--accent)`.
- Gaps: []

### AC22
- Status: PASS
- Proof:
  - `@media (max-width: 640px)` rule present.
  - `h1` font-size uses `clamp(2rem, 5.5vw, 3.5rem)` fluid unit.
- Gaps: []

### AC23
- Status: PASS
- Proof:
  - `wc -c design/agent-wallet.html` = 2696 (< 12288). See artifacts/size-check.txt.
- Gaps: []

### AC24
- Status: PASS
- Proof:
  - Python strip of `<style>` + HTML comments + tags, whitespace-split: 24 tokens (< 80). See artifacts/word-count.txt.
- Gaps: []

### AC25
- Status: PASS
- Proof:
  - `grep -c '<h1' design/agent-wallet.html` = 1.
  - h1 inner text byte-equals `Your agent's address.` (3 words, trailing period).
- Gaps: []

### AC26
- Status: PASS
- Proof:
  - `grep -cE 'transform[[:space:]]*:' design/agent-wallet.html` = 0.
  - `grep -cE 'text-transform[[:space:]]*:' design/agent-wallet.html` = 0.
- Gaps: []

### AC27
- Status: PASS
- Proof:
  - Pre-build and post-build sha256 captured to artifacts/prior-designs-pre.sha256 and artifacts/prior-designs-post.sha256.
  - `diff` exits 0 (SHA MATCH).
  - landing.html = 87e79f41082c177e5c79859afe16b53b95a19e27bd1258e502a1f9011c4d4d64
  - wizard.html = 8c3dc84dd422ccd9e1e04502205d7992c29e7d6ebd91632e05f6197d3de14f18
  - agents.html = 11cc4d2284b60777a01a298082648a9dbda105383dc601b58f6df2e39ec12795
  - running.html = 566674ad8f283e9ca697b5915b0db29a44bf3597c836303e7279100106fadafd
  - `ls design/ | sort` returns exactly five lines: agent-wallet.html, agents.html, landing.html, running.html, wizard.html.
- Gaps: []

### AC28
- Status: PASS
- Proof:
  - `task_loop.py status --task-id <t>` reports verdict_overall_status PASS for each of: bootstrap-proof-loop, scaffold-service-stubs, test-harness, landing-mockup-steve-ive, wizard-mockup-steve-ive, agents-list-mockup-steve-ive, running-state-mockup-steve-ive, tdd-kiloclaw-install, tdd-telegram-validate-token, tdd-telegram-set-webhook, tdd-onchainos-create-charge.
  - tdd-agent-deployer was checked additionally and also reports PASS. Raw output in artifacts/no-regression.txt.
- Gaps: []

## Commands run
- `shasum -a 256 design/landing.html design/wizard.html design/agents.html design/running.html`
- `wc -c design/agent-wallet.html`
- `grep -c '<h1' design/agent-wallet.html`
- `grep -c '<details' design/agent-wallet.html`
- `grep -c '<summary' design/agent-wallet.html`
- `grep -c '<code' design/agent-wallet.html`
- `grep -c '<style' design/agent-wallet.html`
- `grep -c ' style=' design/agent-wallet.html`
- `grep -c 'var(--accent)' design/agent-wallet.html`
- `grep -oF '0x' design/agent-wallet.html | wc -l`
- `grep -cF '0x1234abcd5678ef901234abcd5678ef9012345678' design/agent-wallet.html`
- `grep -oiw 'wallet' design/agent-wallet.html | wc -l`
- `grep -cE 'transform[[:space:]]*:' design/agent-wallet.html`
- `grep -cE 'text-transform[[:space:]]*:' design/agent-wallet.html`
- `ls design/ | sort`
- `python3 .claude/skills/repo-task-proof-loop/scripts/task_loop.py status --task-id <each of 12 prior tasks>`

## Raw artifacts
- .agent/tasks/agent-wallet-disclosure-mockup-steve-ive/raw/build.txt
- .agent/tasks/agent-wallet-disclosure-mockup-steve-ive/raw/test-unit.txt
- .agent/tasks/agent-wallet-disclosure-mockup-steve-ive/raw/test-integration.txt
- .agent/tasks/agent-wallet-disclosure-mockup-steve-ive/raw/lint.txt
- .agent/tasks/agent-wallet-disclosure-mockup-steve-ive/raw/screenshot-1.png
- .agent/tasks/agent-wallet-disclosure-mockup-steve-ive/artifacts/prior-designs-pre.sha256
- .agent/tasks/agent-wallet-disclosure-mockup-steve-ive/artifacts/prior-designs-post.sha256
- .agent/tasks/agent-wallet-disclosure-mockup-steve-ive/artifacts/pre-build-ls.txt
- .agent/tasks/agent-wallet-disclosure-mockup-steve-ive/artifacts/post-build-ls.txt
- .agent/tasks/agent-wallet-disclosure-mockup-steve-ive/artifacts/size-check.txt
- .agent/tasks/agent-wallet-disclosure-mockup-steve-ive/artifacts/word-count.txt
- .agent/tasks/agent-wallet-disclosure-mockup-steve-ive/artifacts/grep-forbidden.txt
- .agent/tasks/agent-wallet-disclosure-mockup-steve-ive/artifacts/accent-count.txt
- .agent/tasks/agent-wallet-disclosure-mockup-steve-ive/artifacts/no-regression.txt

## Known gaps
- None.
