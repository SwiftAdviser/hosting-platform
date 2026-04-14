# Task Spec: agent-wallet-disclosure-mockup-steve-ive

## Metadata
- Task ID: agent-wallet-disclosure-mockup-steve-ive
- Created: 2026-04-14T09:43:45+00:00
- Frozen: 2026-04-13
- Repo root: /Users/krutovoy/Projects/hosting-platform
- Working directory at init: /Users/krutovoy/Projects/hosting-platform

## Guidance sources
- /Users/krutovoy/.claude/skills/steve-ive/SKILL.md
- /Users/krutovoy/.claude/CLAUDE.md (frontend_aesthetics block, voice rules: no em dash, no exclamation, numbers beat adjectives)
- /Users/krutovoy/Projects/hosting-platform/CLAUDE.md (Principle: Crypto hidden, narrowly relaxed on this one surface)
- /Users/krutovoy/Projects/hosting-platform/docs/agent_spawn_prd.md (Step 7: first Telegram bot message surfaces the address once: "Hi, I'm <AgentName>. Here's your wallet address: <addr>")
- /Users/krutovoy/Projects/hosting-platform/design/landing.html (visual tokens copied verbatim; sha256 captured below)
- /Users/krutovoy/Projects/hosting-platform/design/wizard.html (sister mockup; lock-in pattern reused)
- /Users/krutovoy/Projects/hosting-platform/design/agents.html (sister mockup; same tokens, wallet deliberately deferred to this task)
- /Users/krutovoy/Projects/hosting-platform/design/running.html (zero-accent sister mockup; same tokens)
- /Users/krutovoy/Projects/hosting-platform/.agent/tasks/agents-list-mockup-steve-ive/spec.md (AC structure reference, wallet deferral rationale)
- /Users/krutovoy/Projects/hosting-platform/.agent/tasks/landing-mockup-steve-ive/spec.md (buzzword ban list reused)
- /Users/krutovoy/Projects/hosting-platform/.agent/tasks/wizard-mockup-steve-ive/spec.md (visual lock-in pattern reused)
- /Users/krutovoy/Projects/hosting-platform/.agent/tasks/running-state-mockup-steve-ive/spec.md (zero-accent pattern reference)

## Original task statement
Fifth steve-ive UI mockup at design/agent-wallet.html: the deferred contextual wallet disclosure surface. This is the one place in the v0.1 product where the crypto-hidden principle is relaxed, because the first Telegram bot message and a future in-dashboard context require showing the agent's wallet address ONCE to the user (per docs/agent_spawn_prd.md step 7). The disclosure must be OPT-IN (the user expands it or taps 'show address') and the default state hides the address behind a placeholder like the summary of a `<details>` element. The word 'wallet' is allowed on this page (this is its canonical home) but chain names, token symbols, and gas mentions remain banned. Expose one sample agent (atlas) behind a pure-HTML `<details>` element that reveals the fake sample address on click with zero JavaScript. Only one primary CTA on the page: a single action to return to the agents list (`Done`). Reuse the same `:root` tokens, font stack, theme, and accent. File under 12 KB, body words under 80. Landing/wizard/agents/running-state sha unchanged.

## Design rationale: narrow crypto-hidden relaxation
Per /Users/krutovoy/Projects/hosting-platform/CLAUDE.md the Principle reads: "Crypto hidden. Do not surface chains, gas, or signatures unless unavoidable. If the user sees a chain name, we've failed the UX test." This page is the ONLY v0.1 surface where the user can see their agent's address on request. The first Telegram bot message already surfaces it once ("Hi, I'm <name>. Here's your wallet address: <addr>") per docs/agent_spawn_prd.md step 7. This page surfaces it a second time, on request, behind a pure-HTML disclosure affordance. Default state hides the address. The user reveals it by expanding a native `<details>` element (no JavaScript). The word `wallet` is allowed here up to five times. Every other crypto-adjacent term stays banned. The `0x` prefix is allowed exactly once, and only inside the single locked sample address.

The prior task `agents-list-mockup-steve-ive` explicitly deferred wallet display to this task to keep the agents list crypto-hidden. This spec is the authoritative statement of how wallet disclosure lands.

## Visual tokens locked from design/landing.html
The page MUST declare these exact CSS custom properties with these exact values inside its `:root` block. Captured from `design/landing.html` lines 8 to 18:

```
/* theme: dark */
:root {
  --bg: #0a0a0b;
  --fg: #f5f5f4;
  --muted: rgba(245, 245, 244, 0.46);
  --rule: rgba(245, 245, 244, 0.08);
  --tint: rgba(245, 245, 244, 0.04);
  --accent: #c6ff3d;
  --radius: 2px;
  --max: 44rem;
}
```

Font family stack to reuse verbatim from landing line 26:
`"JetBrains Mono", "IBM Plex Mono", "SF Mono", "Fira Code", ui-monospace, monospace`

Background treatment from landing lines 30 to 32:
`radial-gradient(900px 520px at 50% -8%, var(--tint), transparent 60%), var(--bg)`

Primary CTA text locked: `Done`. The Done CTA returns the user to the agents list. In a real app it routes to `/agents`; this static mockup uses the relative `href="agents.html"`.

## Sample data locked
- Sample agent name: `atlas` (lowercase, matches the first row of agents.html).
- Sample wallet address literal: `0x1234abcd5678ef901234abcd5678ef9012345678` (42 characters including the `0x` prefix; valid-looking EVM address pattern). The verifier greps for this exact string.
- Only ONE `<details>` element. One agent surfaced. Tighter is better.
- Helper line inside the `<details>` explains the address in plain words, at most 15 words, and does NOT mention chain, token, gas, or signature.

## Heading locked
`<h1>Your agent's address.</h1>` (3 words, ends with a period). Matches steve-ive voice: statements beat questions; short declaratives; no exclamation.

## Collateral sha256 baselines (captured 2026-04-13)
Stored here so AC27 can re-verify these four files are untouched after the build step.
- `design/landing.html` sha256: `87e79f41082c177e5c79859afe16b53b95a19e27bd1258e502a1f9011c4d4d64`
- `design/wizard.html` sha256: `8c3dc84dd422ccd9e1e04502205d7992c29e7d6ebd91632e05f6197d3de14f18`
- `design/agents.html` sha256: `11cc4d2284b60777a01a298082648a9dbda105383dc601b58f6df2e39ec12795`
- `design/running.html` sha256: `566674ad8f283e9ca697b5915b0db29a44bf3597c836303e7279100106fadafd`

## Steve-ive principles enforced
Quoted from `/Users/krutovoy/.claude/skills/steve-ive/SKILL.md`:
- "Every element must earn its place" (one heading, one disclosure, one CTA; nothing else).
- "Typography does the heavy lifting" (no icons, no illustrations).
- "One action per screen" (the `Done` CTA is the only primary action; the `<summary>` affordance is a neutral disclosure, not the CTA).
- "Reduce, then reduce again" (one `<details>`, not two; one helper line, not a paragraph).
- "Contrast creates meaning" (accent lime reserved for the `Done` CTA; the disclosure affordance is muted).
- Voice: "Short sentences. Active voice. No em dashes. No exclamation marks."

## Acceptance criteria

- AC1: File exists at exactly `/Users/krutovoy/Projects/hosting-platform/design/agent-wallet.html`. The parent `design/` directory exists.
- AC2: First non-empty line is `<!DOCTYPE html>` (case-insensitive). The `<html>` root tag carries a `lang` attribute. The `<head>` declares `<meta charset="utf-8">` (or `UTF-8`) and `<meta name="viewport" content="width=device-width, initial-scale=1">` (initial-scale may be `1` or `1.0`).
- AC3: Exactly one `<title>` tag. The title text is 5 words or fewer.
- AC4: Exactly one PRIMARY CTA element. The primary CTA is a `<button class="cta">` or `<a class="cta">` whose visible text equals `Done` exactly. Verifier counts elements whose class attribute contains `cta` (whole-word match); asserts count equals 1. The `<summary>` element MUST NOT carry `class="cta"`.
- AC5: No secondary CTA labels. Case-insensitive grep for each of these returns zero matches: `Learn more`, `Read more`, `See more`, `Get started`, `Sign up`, `Sign in`, `Log in`, `More info`.
- AC6: Exactly one `<details>` element. `grep -c '<details' design/agent-wallet.html` equals 1. The `<details>` element MUST NOT carry the `open` attribute (default state is closed).
- AC7: Exactly one `<summary>` element. `grep -c '<summary' design/agent-wallet.html` equals 1. The visible text of the `<summary>` equals `Show address` exactly (2 words, no trailing punctuation).
- AC8: Exactly one `<code>` element. `grep -c '<code' design/agent-wallet.html` equals 1. The inner text of the `<code>` element equals the LOCKED sample address literal `0x1234abcd5678ef901234abcd5678ef9012345678` (42 characters, byte-for-byte).
- AC9: Zero `<script>` tags. Zero `src=` attributes. Zero `<link>` tags pointing at network resources (`href="http`, `href="//`). Zero `@import` rules. Zero `://` substrings anywhere in the file (HTML or CSS). Zero CDN host references (`fonts.googleapis.com`, `cdn.`, `cloudflare.com`, `jsdelivr.net`, `unpkg.com`).
- AC10: All CSS lives in a single `<style>` block inside `<head>`. Zero inline `style="..."` attributes anywhere. Verifier asserts `<style` count equals 1 and ` style=` count equals 0.
- AC11: Marketing buzzword ban. Case-insensitive grep for each of these returns zero matches: `revolutionary`, `seamless`, `unleash`, `harness`, `empower`, `best-in-class`, `world-class`, `cutting-edge`, `leverage`, `synergy`, `streamline`, `next-generation`, `game-changer`, `transform`, `we're excited`, `introducing`, `unlock`.
- AC12: Em-dash and en-dash ban. Grep for U+2014 (`—`) and U+2013 (`–`) returns zero matches.
- AC13: No exclamation marks in visible body text. Verifier strips `<!DOCTYPE`, HTML comments, attribute values, and the entire `<style>` block, then asserts `!` count equals 0 in the remaining visible text. (`!important` inside CSS is allowed because the `<style>` block is excluded.)
- AC14: Crypto-hidden NARROW RELAXATION. The word `wallet` MAY appear on this page up to 5 times (case-insensitive word-boundary count <= 5 and >= 0). This is the ONLY v0.1 surface that permits `wallet`. All other banned crypto-adjacent terms return zero matches: case-insensitive word-boundary grep for each of `chain`, `gas`, `signature`, `USDC`, `ETH`, `X-Layer`, `EVM`, `blockchain`, `crypto` returns zero matches.
- AC15: Chain-name ban holds. The literal strings `X-Layer`, `xlayer`, `Base`, `Ethereum`, `Solana`, `Polygon`, `Arbitrum`, `Optimism` each return zero matches (case-insensitive).
- AC16: EXACTLY ONE `0x` substring occurs in the entire file. `grep -oF '0x' design/agent-wallet.html | wc -l` equals 1. The single `0x` occurrence is inside the locked sample address inside the one `<code>` element. No other element, comment, CSS value, or attribute may contain `0x`.
- AC17: Sample address literal matches `0x1234abcd5678ef901234abcd5678ef9012345678` verbatim. `grep -cF '0x1234abcd5678ef901234abcd5678ef9012345678' design/agent-wallet.html` equals 1.
- AC18: Page mentions the product noun. Case-insensitive grep for `agent` returns at least one match in visible body text.
- AC19: `:root` block declares `--bg`, `--fg`, `--muted`, `--rule`, `--tint`, `--accent`, `--radius`, `--max` with values BYTE-IDENTICAL to `design/landing.html`. Verifier extracts the `:root { ... }` block from both files and asserts every declaration matches:
  - `--bg: #0a0a0b;`
  - `--fg: #f5f5f4;`
  - `--muted: rgba(245, 245, 244, 0.46);`
  - `--rule: rgba(245, 245, 244, 0.08);`
  - `--tint: rgba(245, 245, 244, 0.04);`
  - `--accent: #c6ff3d;`
  - `--radius: 2px;`
  - `--max: 44rem;`
  The `:root` block MAY declare additional tokens as long as the eight canonical tokens above are present with the exact landing values.
- AC20: Font family declared on `body` (or global selector) starts with `"JetBrains Mono"` and the full stack equals landing.html's stack: `"JetBrains Mono", "IBM Plex Mono", "SF Mono", "Fira Code", ui-monospace, monospace`. None of `Inter`, `Roboto`, `Arial`, `Helvetica`, `system-ui`, `-apple-system` may appear as the primary family.
- AC21: `var(--accent)` is referenced ONLY in CSS rules whose selector targets the primary CTA or its interaction states. Allowed selectors: `.cta`, `button.cta`, `a.cta`, and the `:hover` / `:focus` / `:focus-visible` variants of those. The `<details>` selector, `<summary>` selector, `<code>` selector, body, heading, and helper text selectors MUST NOT reference `var(--accent)`.
- AC22: Responsive sizing. The file contains at least one `@media (max-width: ...)` rule AND/OR uses fluid units (`clamp(`, `vw`, `vmin`) on the heading. Verifier passes if either pattern is present.
- AC23: Total file size strictly under 12288 bytes (12 KB). `stat -f%z design/agent-wallet.html` reports < 12288.
- AC24: Visible body word count strictly under 80. Verifier strips `<style>`, HTML comments, and tags, then counts whitespace-separated tokens; asserts < 80. Heading, agent label, summary text, helper text, the address, and the CTA label all count.
- AC25: Exactly one `<h1>`. The h1 inner text equals `Your agent's address.` exactly (3 words, ends with a period).
- AC26: No `transform:` or `text-transform:` CSS property anywhere. `grep -qE 'transform[[:space:]]*:' design/agent-wallet.html` returns nothing; `grep -qE 'text-transform[[:space:]]*:' design/agent-wallet.html` returns nothing.
- AC27: Collateral check. Only `design/agent-wallet.html` is created by this task. The four prior design files are unchanged. Builder snapshots their sha256 BEFORE any edits and writes digests to `.agent/tasks/agent-wallet-disclosure-mockup-steve-ive/*.sha256`. Verifier re-hashes and asserts each matches the baseline captured above:
  - `design/landing.html` = `87e79f41082c177e5c79859afe16b53b95a19e27bd1258e502a1f9011c4d4d64`
  - `design/wizard.html` = `8c3dc84dd422ccd9e1e04502205d7992c29e7d6ebd91632e05f6197d3de14f18`
  - `design/agents.html` = `11cc4d2284b60777a01a298082648a9dbda105383dc601b58f6df2e39ec12795`
  - `design/running.html` = `566674ad8f283e9ca697b5915b0db29a44bf3597c836303e7279100106fadafd`
  `ls design/ | sort` returns exactly five lines: `agent-wallet.html`, `agents.html`, `landing.html`, `running.html`, `wizard.html`.
- AC28: No regressions on prior tasks. Verifier reruns `python3 .claude/skills/repo-task-proof-loop/scripts/task_loop.py status --task-id <ID>` on each of the twelve prior tasks and asserts each remains in its pre-task terminal state:
  1. `bootstrap-proof-loop`
  2. `scaffold-service-stubs`
  3. `test-harness`
  4. `landing-mockup-steve-ive`
  5. `wizard-mockup-steve-ive`
  6. `agents-list-mockup-steve-ive`
  7. `running-state-mockup-steve-ive`
  8. `tdd-kiloclaw-install`
  9. `tdd-telegram-validate-token`
  10. `tdd-telegram-set-webhook`
  11. `tdd-onchainos-create-charge`
  12. (reserved slot; only the eleven above are asserted)
  The parallel in-flight task `tdd-agent-deployer` is explicitly EXCLUDED from this regression check: if its status is UNKNOWN at verify time, skip it.

## Constraints

- No JavaScript anywhere in the file. Zero `<script>` tags.
- No external network requests. No CDN, no `<link href="https?://...">`, no `@import url(...)`, no `<img src="...">`, no font loading from the network. Zero `://` substrings in the file.
- All CSS lives inline in a single `<style>` block in `<head>`. No `style="..."` attributes on elements.
- The page MUST visually match landing/wizard/agents/running: same `:root` token names AND values, same `body` font-family stack, same background gradient treatment, same `/* theme: dark */` comment at the top of the `<style>` block.
- The accent color `var(--accent)` is reserved exclusively for the primary `Done` CTA and its hover/focus states. The `<details>` element, `<summary>` affordance, `<code>` block, heading, and helper text must not reference it.
- The `<details>` element is default-closed. No `open` attribute.
- Crypto-hidden RELAXATION is narrow and explicit:
  - `wallet` word-boundary count: <= 5.
  - `0x` substring count: EXACTLY 1, and that occurrence lives inside the single `<code>` element containing the locked sample address.
  - Every other crypto-adjacent term (`chain`, `gas`, `signature`, `USDC`, `ETH`, `X-Layer`, `EVM`, `blockchain`, `crypto`) returns zero matches.
  - Chain names (`X-Layer`, `xlayer`, `Base`, `Ethereum`, `Solana`, `Polygon`, `Arbitrum`, `Optimism`) return zero matches.
- The CSS must not use the `transform` or `text-transform` property name anywhere.
- No em dashes (U+2014) or en dashes (U+2013) anywhere in the spec, the HTML, or any commit message.
- No exclamation marks in visible body text.
- No buzzwords from the AC11 list.
- Any deletes use `trash`, never `rm` or `rm -rf`.
- Only `/Users/krutovoy/Projects/hosting-platform/design/agent-wallet.html` is created. The four prior `design/*.html` files are not touched. No other repo file is modified except the `.agent/tasks/agent-wallet-disclosure-mockup-steve-ive/` workflow artifacts.

## Non-goals

- No JavaScript copy-to-clipboard. The user copies the address manually or from the Telegram bot message.
- No real API call, no fetch, no Inertia wiring, no Laravel route.
- No QR code. No avatar. No icons. No SVG.
- No second agent in the disclosure. One `<details>`, not two.
- No "address hidden" vs "address shown" side-by-side rendering. The `<details>` element is the disclosure; that is the whole mechanism.
- No chain name. No network indicator. No balance display. No token symbol. No gas estimate. No signature.
- No header nav, no footer, no breadcrumbs, no back arrow.
- No retry of the landing, wizard, agents, or running pages. All four are frozen for this task.
- No new color tokens. No accent on the disclosure affordance.
- No animation. No transitions beyond plain CSS `:hover` / `:focus-visible` defaults.

## Verification plan

The verifier runs from `/Users/krutovoy/Projects/hosting-platform`. Each AC maps to one or more concrete shell checks.

- AC1: `test -f design/agent-wallet.html && test -d design`
- AC2: `head -1 design/agent-wallet.html | grep -qi '^<!doctype html>'` and `grep -qE '<html[^>]+lang=' design/agent-wallet.html` and `grep -qiE '<meta[^>]+charset="?utf-?8' design/agent-wallet.html` and `grep -qE '<meta[^>]+name="viewport"[^>]+content="width=device-width, initial-scale=1(\.0)?"' design/agent-wallet.html`
- AC3: `[ "$(grep -c '<title>' design/agent-wallet.html)" = "1" ]`; verifier extracts title text and asserts word count <= 5.
- AC4: verifier counts elements whose class attribute contains the whole word `cta` and asserts the count equals 1. Verifier extracts the CTA element's inner text and asserts it equals `Done` exactly. The `<summary>` element must not carry `cta` in its class list.
- AC5: for each banned phrase in `Learn more|Read more|See more|Get started|Sign up|Sign in|Log in|More info`, `! grep -qi 'PHRASE' design/agent-wallet.html`.
- AC6: `[ "$(grep -c '<details' design/agent-wallet.html)" = "1" ]` and `! grep -qE '<details[^>]+open' design/agent-wallet.html`.
- AC7: `[ "$(grep -c '<summary' design/agent-wallet.html)" = "1" ]`; verifier extracts the `<summary>` inner text and asserts it equals `Show address` exactly.
- AC8: `[ "$(grep -c '<code' design/agent-wallet.html)" = "1" ]`; verifier extracts the `<code>` inner text and asserts it equals `0x1234abcd5678ef901234abcd5678ef9012345678` exactly.
- AC9: `! grep -q '<script' design/agent-wallet.html` and `! grep -q 'src=' design/agent-wallet.html` and `! grep -qE '<link[^>]+href="(https?:|//)' design/agent-wallet.html` and `! grep -q '@import' design/agent-wallet.html` and `! grep -qF '://' design/agent-wallet.html` and `! grep -qiE 'fonts\.googleapis\.com|cdn\.|cloudflare\.com|jsdelivr\.net|unpkg\.com' design/agent-wallet.html`.
- AC10: `[ "$(grep -c '<style' design/agent-wallet.html)" = "1" ]` and `! grep -q ' style=' design/agent-wallet.html`.
- AC11: for each banned word in the AC11 list, `! grep -qi 'WORD' design/agent-wallet.html`.
- AC12: `! grep -q $'\u2014' design/agent-wallet.html` and `! grep -q $'\u2013' design/agent-wallet.html`.
- AC13: verifier strips `<!DOCTYPE`, HTML comments, attribute values, and the `<style>` block, then asserts `!` count equals 0 in the remaining visible text.
- AC14: `[ "$(grep -ciow 'wallet' design/agent-wallet.html | wc -l)" -le "5" ]`; for each of `chain|gas|signature|USDC|ETH|X-Layer|EVM|blockchain|crypto`, `! grep -qiw 'TERM' design/agent-wallet.html`.
- AC15: for each of `X-Layer|xlayer|Base|Ethereum|Solana|Polygon|Arbitrum|Optimism`, `! grep -qi 'NAME' design/agent-wallet.html`.
- AC16: `[ "$(grep -oF '0x' design/agent-wallet.html | wc -l | tr -d ' ')" = "1" ]`. Verifier asserts the single `0x` occurrence is inside the `<code>` element.
- AC17: `[ "$(grep -cF '0x1234abcd5678ef901234abcd5678ef9012345678' design/agent-wallet.html)" = "1" ]`.
- AC18: `grep -qi 'agent' design/agent-wallet.html`.
- AC19: verifier extracts `:root { ... }` from both `design/landing.html` and `design/agent-wallet.html`, parses each `--name: value;` declaration, and asserts that for keys `--bg`, `--fg`, `--muted`, `--rule`, `--tint`, `--accent`, `--radius`, `--max` the values are byte-identical between the two files.
- AC20: verifier parses the `body { font-family: ... }` declaration and asserts the first family is `"JetBrains Mono"` and the full stack equals the landing stack. `! grep -qE 'font-family:[^;]*(Inter|Roboto|Arial|Helvetica|system-ui|-apple-system)' design/agent-wallet.html`.
- AC21: verifier walks each CSS rule containing `var(--accent)` and asserts its selector matches one of: `.cta`, `button.cta`, `a.cta`, `.cta:hover`, `.cta:focus`, `.cta:focus-visible`, `button.cta:hover`, `button.cta:focus`, `button.cta:focus-visible`, `a.cta:hover`, `a.cta:focus`, `a.cta:focus-visible`.
- AC22: `grep -qE '@media[^{]*max-width' design/agent-wallet.html` OR `grep -qE 'clamp\(|[0-9.]+vw|[0-9.]+vmin' design/agent-wallet.html`.
- AC23: `[ "$(stat -f%z design/agent-wallet.html)" -lt "12288" ]`.
- AC24: verifier strips `<style>`, HTML comments, and tags; counts whitespace-separated tokens; asserts < 80.
- AC25: `[ "$(grep -c '<h1' design/agent-wallet.html)" = "1" ]`; verifier extracts the h1 inner text and asserts it equals `Your agent's address.` exactly.
- AC26: `! grep -qE 'transform[[:space:]]*:' design/agent-wallet.html` and `! grep -qE 'text-transform[[:space:]]*:' design/agent-wallet.html`.
- AC27: builder writes `sha256sum design/landing.html`, `sha256sum design/wizard.html`, `sha256sum design/agents.html`, `sha256sum design/running.html` to `.agent/tasks/agent-wallet-disclosure-mockup-steve-ive/*.sha256` BEFORE any edits. Verifier re-hashes all four files and asserts each digest matches the baseline captured above. `ls design/ | sort` returns exactly the five lines `agent-wallet.html`, `agents.html`, `landing.html`, `running.html`, `wizard.html`.
- AC28: verifier reruns `python3 .claude/skills/repo-task-proof-loop/scripts/task_loop.py status --task-id <TASK_ID>` on each of the eleven prior tasks listed in AC28 and asserts each remains in its pre-task terminal state. The parallel `tdd-agent-deployer` task is excluded: if its status is UNKNOWN, skip.

Build: none (static HTML).
Unit tests: none (UI design carve-out from TDD).
Integration tests: none.
Lint: none required; the spec's grep-based checks act as the lint.
Manual checks: open `design/agent-wallet.html` in a browser at 320px, 768px, and 1920px. Confirm the heading reads `Your agent's address.`, the `<details>` is collapsed by default, expanding the summary reveals the locked sample address inside a `<code>` block, and the only lime-accented element on the page is the `Done` CTA. Confirm the `<summary>` affordance is neutral (muted or fg). Confirm zero chain names, zero gas references, zero token symbols. Confirm the page passes the 5-question steve-ive decision framework.
