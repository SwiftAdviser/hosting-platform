# Task Spec: agents-list-mockup-steve-ive

## Metadata
- Task ID: agents-list-mockup-steve-ive
- Created: 2026-04-14T09:15:15+00:00
- Frozen: 2026-04-13
- Repo root: /Users/krutovoy/Projects/hosting-platform
- Working directory at init: /Users/krutovoy/Projects/hosting-platform

## Guidance sources
- /Users/krutovoy/.claude/skills/steve-ive/SKILL.md
- /Users/krutovoy/.claude/CLAUDE.md (frontend_aesthetics block, voice rules)
- /Users/krutovoy/Projects/hosting-platform/CLAUDE.md (Principle: Crypto hidden, Walkthrough-first)
- /Users/krutovoy/Projects/hosting-platform/docs/sprint_v0.1.md (Day 2 Task 6: My Agents list page, one card per deployed agent)
- /Users/krutovoy/Projects/hosting-platform/docs/agent_spawn_prd.md (the 7-step walkthrough; this list is step 6)
- /Users/krutovoy/Projects/hosting-platform/design/landing.html (visual tokens copied verbatim)
- /Users/krutovoy/Projects/hosting-platform/design/wizard.html (sister mockup; lock-in pattern reused)
- /Users/krutovoy/Projects/hosting-platform/.agent/tasks/landing-mockup-steve-ive/spec.md (ban lists reused)
- /Users/krutovoy/Projects/hosting-platform/.agent/tasks/wizard-mockup-steve-ive/spec.md (visual lock-in pattern reused)

## Original task statement
Third steve-ive static UI mockup at design/agents.html: the My Agents list screen from sprint_v0.1.md Day 2 Task 6. One row per deployed agent. Each row shows: agent name, status pill (provisioning, ready, failed), Telegram link (t.me/<botname> shown as a short label not a long URL), Stop button. Optional empty state if zero agents (steve-ive style: one statement, one CTA back to Post agent). Crypto-hidden tension resolution: do NOT show wallet address on this screen, do not mention chain or gas. The wallet display lives on a separate future task. Use the same :root tokens, font stack, theme, and accent color as design/landing.html and design/wizard.html. Same accent reservation: lime is for the primary CTA only (the Post agent button to add a new agent). Status pills use neutral var(--rule) and var(--muted), not the accent. The Stop button is a quiet ghost-style affordance, not an accent. Same constraints as previous mockups: inline CSS only, no JS, no external requests, no em dashes, no exclamation marks, no buzzwords, no transform property name. Render 3 sample agents inline as static HTML. Total file under 14 KB, visible body words under 90.

## Design rationale: wallet deferred
Sprint Day 2 Task 6 originally listed a copyable wallet address per row. That conflicts with the Crypto hidden principle in CLAUDE.md. Wallet display is deferred to a future task. The agents list in v0.1 shows name, status, Telegram, stop. Nothing else.

## Visual tokens locked from design/landing.html
The agents list MUST declare these exact CSS custom properties with these exact values inside its `:root` block. Captured from `design/landing.html` lines 8 to 18:

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

Primary CTA text locked from landing line 96: `Post agent`. The agents list's primary CTA MUST read `Post agent` to match the landing and wizard CTAs exactly.

## Sample data locked
Three rows, in this order:
1. `atlas` — status `ready` — telegram `@atlasbot`
2. `nova` — status `provisioning` — telegram `@novabot`
3. `sage` — status `failed` — telegram `@sagebot`

All three pill states (`provisioning`, `ready`, `failed`) appear exactly once. Names are lowercase, short, mythological, no marketing.

## Heading locked
`<h1>Three agents.</h1>` — numbers beat adjectives (steve-ive voice rule). Two words. One period.

## Steve-ive principles enforced
Quoted from `/Users/krutovoy/.claude/skills/steve-ive/SKILL.md`:
- "Every element must earn its place"
- "Typography does the heavy lifting"
- "One action per screen" (the Post agent CTA; stop buttons are row affordances, not the page's reason for being)
- "Reduce, then reduce again" (no wallet column, no settings, no detail view)
- "Contrast creates meaning" (accent lime reserved for the single primary CTA)
- "Noise is the enemy, not simplicity"
- Voice: "Short sentences. Active voice. No em dashes. No exclamation marks. Numbers are more convincing than adjectives."

## Acceptance criteria

- AC1: File exists at exactly `/Users/krutovoy/Projects/hosting-platform/design/agents.html`. The parent `design/` directory exists.
- AC2: First non-empty line is `<!DOCTYPE html>` (case-insensitive). The `<html>` root tag carries a `lang` attribute. The `<head>` declares `<meta charset="utf-8">` (or `UTF-8`) and `<meta name="viewport" content="width=device-width, initial-scale=1">` (initial-scale value may be `1` or `1.0`).
- AC3: Exactly one `<title>` tag. The title text is 5 words or fewer.
- AC4: Exactly one PRIMARY CTA element. The primary CTA is a `<button class="cta">` or `<a class="cta">` whose visible text equals `Post agent` exactly. The verifier counts primary CTAs by counting elements with `class="cta"` (whole-word match) and asserts the count equals 1. Stop buttons on rows are NOT primary CTAs and MUST carry a different class (`stop` or `ghost`), never `cta`.
- AC5: No secondary CTA labels. Case-insensitive grep for each of these returns zero matches: `Learn more`, `Read more`, `See more`, `Get started`, `Sign up`, `Sign in`, `Log in`, `More info`. NOTE: `Stop` IS allowed as a button label on each row's stop affordance; it is not banned.
- AC6: Exactly 3 sample agent rows are rendered inline. Each row carries a class `row` (or data attribute `data-row`) that the verifier greps for. The verifier asserts the count of row elements equals 3. The three sample agent names (`atlas`, `nova`, `sage`) each appear at least once as visible text.
- AC7: Each row contains exactly ONE stop button. Total `<button>` elements in the document equal 4 (three stop buttons plus one primary CTA if the primary CTA is rendered as a `<button>`) OR equal 3 (three stop buttons if the primary CTA is rendered as an `<a>` tag). Verifier asserts `grep -c '<button' design/agents.html` is in `{3, 4}`. Each stop button carries class `stop` or class `ghost`; verifier asserts `grep -cE 'class="(stop|ghost)"' design/agents.html` equals 3.
- AC8: Each row's Telegram affordance is rendered as a SHORT label. The visible text for each telegram link is either `@atlasbot` / `@novabot` / `@sagebot` (the three allowed) or `t.me/atlasbot` / `t.me/novabot` / `t.me/sagebot`. The visible text MUST NOT contain `https://` or `http://`. The file MUST NOT contain `://` anywhere outside HTML/CSS comments. Verifier strips HTML and CSS comments then asserts `://` count equals 0.
- AC9: Zero `<script>` tags. Zero `src=` attributes. Zero `<link>` tags pointing at network resources (`href="http`, `href="//`). Zero `@import` rules. Zero CDN host references (`fonts.googleapis.com`, `cdn.`, `cloudflare.com`, `jsdelivr.net`, `unpkg.com`).
- AC10: All CSS lives in a single `<style>` block inside `<head>`. Zero inline `style="..."` attributes anywhere. Verifier asserts `<style` count equals 1 and ` style=` count equals 0.
- AC11: Marketing buzzword ban. Case-insensitive grep for each of these returns zero matches: `revolutionary`, `seamless`, `unleash`, `harness`, `empower`, `best-in-class`, `world-class`, `cutting-edge`, `leverage`, `synergy`, `streamline`, `next-generation`, `game-changer`, `transform`, `we're excited`, `introducing`, `unlock`.
- AC12: Em-dash and en-dash ban. Grep for U+2014 (`—`) and U+2013 (`–`) returns zero matches.
- AC13: No exclamation marks in visible body text. Verifier strips `<!DOCTYPE`, HTML comments, attribute values, and the `<style>` block, then asserts `!` count equals 0. (`!important` inside CSS is allowed because the `<style>` block is excluded.)
- AC14: Crypto-hidden ban, ZERO tolerance on this page. Case-insensitive word-boundary grep for each of these returns zero matches: `X-Layer`, `EVM`, `chain`, `gas`, `signature`, `USDC`, `ETH`, `blockchain`, `crypto`, `0x`, `wallet`. Unlike the wizard spec, `wallet` is NOT allowed even once on this page. Wallet display is deferred.
- AC15: The page mentions the product noun. Case-insensitive grep for `agent` returns at least one match in visible body text.
- AC16: `:root` block declares `--bg`, `--fg`, `--muted`, `--rule`, `--tint`, `--accent`, `--radius`, `--max` with values BYTE-IDENTICAL to `design/landing.html`. Verifier extracts the `:root { ... }` block from both files, parses each `--name: value;` declaration, and asserts that for every key the value strings are identical. Expected values:
  - `--bg: #0a0a0b;`
  - `--fg: #f5f5f4;`
  - `--muted: rgba(245, 245, 244, 0.46);`
  - `--rule: rgba(245, 245, 244, 0.08);`
  - `--tint: rgba(245, 245, 244, 0.04);`
  - `--accent: #c6ff3d;`
  - `--radius: 2px;`
  - `--max: 44rem;`
  The `:root` block MAY declare additional tokens (e.g. `--pill-bg`) as long as the eight canonical tokens above are present with the exact landing values. NO `--danger` or other new color token is introduced; the `failed` pill uses `var(--muted)` / `var(--rule)` like the other two pills.
- AC17: Font family declared on `body` (or global selector) starts with `"JetBrains Mono"` and the full stack equals landing.html's stack: `"JetBrains Mono", "IBM Plex Mono", "SF Mono", "Fira Code", ui-monospace, monospace`. None of `Inter`, `Roboto`, `Arial`, `Helvetica`, `system-ui`, `-apple-system` may appear as the primary family.
- AC18: `var(--accent)` is referenced ONLY in CSS rules whose selector targets the primary CTA or its interaction states. Allowed selectors: `.cta`, `button.cta`, `a.cta`, `.cta:hover`, `.cta:focus`, `.cta:focus-visible` (and the combinations with `button.cta` / `a.cta`). The row selector, the row hover state, the status pill selectors, the stop button selector, and the telegram link selector MUST NOT reference `var(--accent)`. Verifier walks every CSS rule containing `var(--accent)` and asserts its selector matches an allowed form.
- AC19: Responsive sizing. The file contains at least one `@media (max-width: ...)` rule AND/OR uses fluid units (`clamp(`, `vw`, `vmin`) on the heading. Verifier passes if either pattern is present.
- AC20: Total file size strictly under 14336 bytes (14 KB). `stat -f%z` reports < 14336.
- AC21: Visible body word count strictly under 90. Verifier strips `<style>`, HTML comments, and tags, then counts whitespace-separated tokens; result must be < 90. Row labels, pill labels, telegram handles, stop labels, heading, and CTA all count.
- AC22: Exactly one `<h1>`. The h1 text is a statement, 5 words or fewer, ends with a period. Locked to `Three agents.` (two words, ends with period).
- AC23: The three status pill labels appear as visible text exactly once each: `provisioning`, `ready`, `failed`. Verifier case-insensitive counts each literal; asserts each count equals 1. The labels MAY be uppercased via CSS (`text-transform: uppercase`) but the literal source text stays lowercase.
- AC24: Collateral check. Only `design/agents.html` is created by this task. `design/landing.html` and `design/wizard.html` are NOT modified. Builder snapshots `sha256sum design/landing.html` and `sha256sum design/wizard.html` BEFORE any edits and writes digests to `.agent/tasks/agents-list-mockup-steve-ive/landing.sha256` and `.agent/tasks/agents-list-mockup-steve-ive/wizard.sha256`. Verifier re-hashes both files and asserts both digests match. `ls design/` returns exactly three entries: `agents.html`, `landing.html`, `wizard.html`.
- AC25: No regressions on prior tasks. Verifier reruns `task_loop.py status` on `landing-mockup-steve-ive`, `wizard-mockup-steve-ive`, `tdd-agent-deployer-scaffold`, `tdd-kiloclaw-client-scaffold`, `tdd-telegram-registrar-scaffold`, `tdd-onchainos-payment-service-scaffold` and asserts each remains in its pre-task state. The parallel task `tdd-onchainos-create-charge` is explicitly excluded from this regression check because it may be in any state during this task's run.

## Constraints

- No JavaScript anywhere in the file. Zero `<script>` tags.
- No external network requests. No CDN, no `<link href="https?://...">`, no `@import url(...)`, no `<img src="...">`, no font loading from the network.
- All CSS lives inline in a single `<style>` block in `<head>`. No `style="..."` attributes on elements.
- The page MUST visually match `design/landing.html` and `design/wizard.html`: same `:root` token names AND values, same `body` font-family stack, same background gradient treatment, same `/* theme: dark */` comment at the top of the `<style>` block.
- The accent color `var(--accent)` is reserved exclusively for the primary `Post agent` CTA and its hover/focus states. Rows, pills, stop buttons, links, and labels must not reference it.
- Status pills use `var(--rule)` for border, `var(--tint)` for background, `var(--muted)` for label color. All three pill states share the same neutral treatment; the `failed` pill does NOT introduce a new color token.
- Stop buttons are ghost-style: transparent background, `var(--rule)` border, `var(--muted)` label; hover state may switch the border to `var(--fg)`. Never uses `var(--accent)`.
- Telegram links use `var(--fg)` or `var(--muted)` for color; not the accent.
- The CSS must not use the `transform` property name anywhere (reuses the previous mockups' constraint).
- No em dashes (U+2014) or en dashes (U+2013) anywhere in the spec, the HTML, or any commit message.
- Any deletes use `trash`, never `rm` or `rm -rf`.
- Crypto stays hidden. Zero chain names, zero token symbols, zero gas, zero signatures, zero wallet references.
- Only `/Users/krutovoy/Projects/hosting-platform/design/agents.html` is created. `design/landing.html` and `design/wizard.html` are not touched. No other repo file is modified except the `.agent/tasks/agents-list-mockup-steve-ive/` workflow artifacts.

## Non-goals

- No JavaScript sorting or filtering.
- No real API call, no fetch, no Inertia wiring, no Laravel route.
- No error states beyond the static `failed` pill on the sage row.
- No empty state rendering (the mockup shows the populated state with 3 rows).
- No pagination. No "load more".
- No agent detail view. No row-click expansion. No modal.
- No wallet display. No copyable address. No chain, no gas, no token.
- No settings page. No account menu. No theme toggle.
- No icons. No SVG decorations. No illustrations.
- No animation libraries. No transitions beyond plain CSS `:hover` / `:focus-visible` defaults.
- No header nav, no footer, no breadcrumbs.
- No retry of the landing or wizard pages. Both are frozen for this task.

## Verification plan

The verifier runs from `/Users/krutovoy/Projects/hosting-platform`. Each AC maps to one or more shell checks.

- AC1: `test -f design/agents.html && test -d design`
- AC2: `head -1 design/agents.html | grep -qi '^<!doctype html>'` and `grep -qE '<html[^>]+lang=' design/agents.html` and `grep -qiE '<meta[^>]+charset="?utf-?8' design/agents.html` and `grep -qE '<meta[^>]+name="viewport"[^>]+content="width=device-width, initial-scale=1(\.0)?"' design/agents.html`
- AC3: `[ "$(grep -c '<title>' design/agents.html)" = "1" ]` and verifier extracts title text and asserts word count <= 5.
- AC4: verifier counts elements whose class attribute exactly matches `cta` (i.e. `class="cta"` or `class="cta ..."`) and asserts the count equals 1. Verifier extracts the CTA element's inner text and asserts it equals `Post agent` exactly. Stop buttons must not carry `cta` in their class list.
- AC5: for each banned phrase in `Learn more|Read more|See more|Get started|Sign up|Sign in|Log in|More info`, `! grep -qi 'PHRASE' design/agents.html`.
- AC6: verifier counts row elements via `grep -c 'class="row"' design/agents.html` (or an equivalent data attribute); asserts the count equals 3. Verifier asserts `grep -c 'atlas' design/agents.html` >= 1, `grep -c 'nova' design/agents.html` >= 1, `grep -c 'sage' design/agents.html` >= 1.
- AC7: `[ "$(grep -c '<button' design/agents.html)" -ge "3" ]` and `[ "$(grep -c '<button' design/agents.html)" -le "4" ]`. Verifier asserts `grep -cE 'class="(stop|ghost)"' design/agents.html` equals 3.
- AC8: verifier extracts visible body text and asserts it contains one of `@atlasbot` / `t.me/atlasbot`, one of `@novabot` / `t.me/novabot`, one of `@sagebot` / `t.me/sagebot`. Verifier strips HTML and CSS comments then asserts `://` count equals 0. `! grep -q 'https://' design/agents.html` and `! grep -q 'http://' design/agents.html`.
- AC9: `! grep -q '<script' design/agents.html` and `! grep -q 'src=' design/agents.html` and `! grep -qE '<link[^>]+href="(https?:|//)' design/agents.html` and `! grep -q '@import' design/agents.html` and `! grep -qiE 'fonts\.googleapis\.com|cdn\.|cloudflare\.com|jsdelivr\.net|unpkg\.com' design/agents.html`.
- AC10: `[ "$(grep -c '<style' design/agents.html)" = "1" ]` and `! grep -q ' style=' design/agents.html`.
- AC11: for each banned word in the AC11 list, `! grep -qi 'WORD' design/agents.html`.
- AC12: `! grep -q $'\u2014' design/agents.html` and `! grep -q $'\u2013' design/agents.html`.
- AC13: verifier strips `<!DOCTYPE`, HTML comments, attribute values, and the entire `<style>` block, then asserts `!` count equals 0 in the remaining visible text.
- AC14: for each banned crypto term in `X-Layer|EVM|chain|gas|signature|USDC|ETH|blockchain|crypto|0x|wallet`, `! grep -qiw 'TERM' design/agents.html` (word-boundary, case-insensitive). Zero matches expected for every term including `wallet`.
- AC15: `grep -qi 'agent' design/agents.html`.
- AC16: verifier extracts `:root { ... }` from both `design/landing.html` and `design/agents.html`, parses each `--name: value;` declaration, and asserts that for keys `--bg`, `--fg`, `--muted`, `--rule`, `--tint`, `--accent`, `--radius`, `--max` the values are byte-identical between the two files.
- AC17: verifier parses the `body { font-family: ... }` declaration, asserts the first family is `"JetBrains Mono"`, asserts the full stack equals landing.html's stack, asserts none of the banned defaults appear as the primary family.
- AC18: verifier walks each CSS rule containing `var(--accent)` and asserts its selector matches one of: `.cta`, `button.cta`, `a.cta`, `.cta:hover`, `.cta:focus`, `.cta:focus-visible`, `button.cta:hover`, `button.cta:focus`, `button.cta:focus-visible`, `a.cta:hover`, `a.cta:focus`, `a.cta:focus-visible`. Row, pill, stop, and link selectors must not match.
- AC19: `grep -qE '@media[^{]*max-width' design/agents.html` OR `grep -qE 'clamp\(|[0-9.]+vw|[0-9.]+vmin' design/agents.html`.
- AC20: `[ "$(stat -f%z design/agents.html)" -lt "14336" ]`.
- AC21: verifier strips `<style>`, HTML comments, and tags; counts whitespace-separated tokens; asserts < 90.
- AC22: `[ "$(grep -c '<h1' design/agents.html)" = "1" ]` and verifier extracts the h1 inner text and asserts it equals `Three agents.` exactly.
- AC23: `[ "$(grep -ci 'provisioning' design/agents.html)" = "1" ]` and `[ "$(grep -ci 'ready' design/agents.html)" = "1" ]` and `[ "$(grep -ci 'failed' design/agents.html)" = "1" ]`.
- AC24: builder writes `sha256sum design/landing.html` to `.agent/tasks/agents-list-mockup-steve-ive/landing.sha256` and `sha256sum design/wizard.html` to `.agent/tasks/agents-list-mockup-steve-ive/wizard.sha256` BEFORE any edits. Verifier re-hashes both files and asserts both digests match the stored snapshots. `ls design/ | sort` returns exactly the three lines `agents.html`, `landing.html`, `wizard.html`.
- AC25: verifier reruns `python3 scripts/task_loop.py status --task-id <TASK_ID>` (or equivalent) on each of the six prior tasks listed in AC25 and asserts each remains in its pre-task terminal state. The parallel `tdd-onchainos-create-charge` task is excluded.

Build: none (static HTML).
Unit tests: none (UI design carve-out from TDD).
Integration tests: none.
Lint: none required; the spec's grep-based checks act as the lint.
Manual checks: open `design/agents.html` in a browser at 320px, 768px, and 1920px viewport widths. Confirm the three rows render with name, pill, telegram handle, and stop button. Confirm only the top-right `Post agent` CTA carries the lime accent. Confirm the three pill states (`provisioning`, `ready`, `failed`) use neutral tokens. Confirm zero wallet references, zero chain references, zero gas references. Confirm the page passes the 5-question steve-ive decision framework.
