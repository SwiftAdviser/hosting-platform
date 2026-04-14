# Task Spec: running-state-mockup-steve-ive

## Metadata
- Task ID: running-state-mockup-steve-ive
- Created: 2026-04-14T09:43:30+00:00
- Frozen: 2026-04-13
- Repo root: /Users/krutovoy/Projects/hosting-platform
- Working directory at init: /Users/krutovoy/Projects/hosting-platform

## Guidance sources
- /Users/krutovoy/.claude/skills/steve-ive/SKILL.md (philosophy; voice rules; reduction discipline)
- /Users/krutovoy/.claude/CLAUDE.md (writing rule: no em dashes; voice)
- /Users/krutovoy/Projects/hosting-platform/CLAUDE.md (Principle: Crypto hidden; Walkthrough-first)
- /Users/krutovoy/Projects/hosting-platform/docs/sprint_v0.1.md (Day 1 Task 7: payment webhook flips deploys.status to paid and fires provisioning job; this screen is what the user SEES while that happens)
- /Users/krutovoy/Projects/hosting-platform/docs/agent_spawn_prd.md (steps 5 and 6 of the walkthrough: agent uploads to KiloClaw under the hood)
- /Users/krutovoy/Projects/hosting-platform/design/landing.html (visual tokens captured verbatim)
- /Users/krutovoy/Projects/hosting-platform/design/wizard.html (sibling mockup; structural lock-in pattern reused)
- /Users/krutovoy/Projects/hosting-platform/design/agents.html (sibling mockup; shared vocabulary `provisioning`)
- /Users/krutovoy/Projects/hosting-platform/.agent/tasks/agents-list-mockup-steve-ive/spec.md (AC structure reference and locked `:root` values)

## Original task statement
Fourth steve-ive UI mockup at design/running.html: the post-click provisioning state from docs/agent_spawn_prd.md step 3 and sprint_v0.1.md Day 1 Task 7. Shown immediately after the user clicks Post agent and payment flips to 'paid'. Single screen, zero actions (the user waits), one subtle indicator of progress. No spinner component (too generic). Use a quiet monospace line that updates conceptually: 'provisioning your agent' or similar, with a minimal visual anchor. No buttons. No navigation. No exclamation marks. Reuse the same :root tokens, font stack, theme, and accent color as prior mockups. Accent MAY appear on a single hairline progress bar or pulse dot, but only if it strictly embodies the 'contrast creates meaning' principle; otherwise keep the page fully neutral. The file size stays under 10 KB, visible body words under 40 (this is a waiting state; less is more). No JS. No external requests. Landing, wizard, and agents design files must be byte-identical before and after.

## Design rationale: why this screen exists
Sprint Day 1 Task 7 fires a background job the moment payment flips. That job runs KiloClaw install, agent wallet creation, and Telegram webhook registration. All of it is invisible. The user stares at a tab. This screen's ONE job: reassure them the system is working without asking for anything. Typography does the whole thing.

## Accent decision: option A (zero accent usage)
Chosen: option A. The running state is a quiet moment. Lime accent is reserved across the product for the `Post agent` primary CTA; that action has just been taken, so there is nothing here for the accent to mark. Any accent on a progress bar would reintroduce a CTA-shaped attention point where none is wanted. Reduction is the hardest form of design. The page is fully neutral. No progress bar. No pulse dot. No keyframes. `var(--accent)` appears exactly once, inside the `:root` declaration line, and is never referenced anywhere else.

## Heading locked
`<h1>Spawning your agent.</h1>` — three words, statement, ends with a period, echoes the product name (Spawn) and the product noun (agent). Satisfies the 2-4 word rule, the "mentions agent / spawn / provisioning" rule, and the steve-ive voice rule.

## Subline locked
`<p>This usually takes under sixty seconds.</p>` — seven words, numbers beat adjectives, no exclamation mark, no em dash. Answers the only question the user has at this moment: how long.

## Visual tokens locked from design/landing.html
The running state MUST declare these exact CSS custom properties with these exact values inside its `:root` block. Captured from `design/landing.html` lines 8 to 18:

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

## Prior-design sha256 baseline (locked 2026-04-13)
The builder MUST NOT modify any of these files. The verifier re-hashes them and asserts match.

- `design/landing.html`: `87e79f41082c177e5c79859afe16b53b95a19e27bd1258e502a1f9011c4d4d64`
- `design/wizard.html`:  `8c3dc84dd422ccd9e1e04502205d7992c29e7d6ebd91632e05f6197d3de14f18`
- `design/agents.html`:  `11cc4d2284b60777a01a298082648a9dbda105383dc601b58f6df2e39ec12795`

Builder snapshots these digests BEFORE any edits into:
- `.agent/tasks/running-state-mockup-steve-ive/landing.sha256`
- `.agent/tasks/running-state-mockup-steve-ive/wizard.sha256`
- `.agent/tasks/running-state-mockup-steve-ive/agents.sha256`

## Steve-ive principles enforced
Quoted from `/Users/krutovoy/.claude/skills/steve-ive/SKILL.md`:
- "Every element must earn its place" (zero decoration, zero nav, zero footer)
- "Typography does the heavy lifting" (one h1, one p, nothing else)
- "One action per screen" — here the action is zero. The user waits. The whole screen is an answer to the question "did it work"
- "Reduce, then reduce again" (a tighter reduction than any prior mockup: no form, no rows, no CTA, no links)
- "The result is the experience" (no "we check 12 things" text, no progress copy beyond a time estimate)
- "Noise is the enemy, not simplicity"
- Voice: short sentences, active voice, no em dashes, no exclamation marks, numbers beat adjectives

## Acceptance criteria

- AC1: File exists at exactly `/Users/krutovoy/Projects/hosting-platform/design/running.html`. The parent `design/` directory exists.
- AC2: First non-empty line is `<!DOCTYPE html>` (case-insensitive). The `<html>` root tag carries a `lang` attribute. The `<head>` declares `<meta charset="utf-8">` (or `UTF-8`) and `<meta name="viewport" content="width=device-width, initial-scale=1">` (initial-scale value may be `1` or `1.0`).
- AC3: Exactly one `<title>` tag. The title text is 5 words or fewer.
- AC4: Zero interactive elements. Each of the following per-tag counts is exactly 0: `<button`, `<form`, `<input`, `<select`, `<textarea`, `<nav`, `<footer`, `<a href=`. (A commented-out `<!-- <a href="..."> -->` block is allowed; the grep pattern `<a href=` excludes commented lines by requiring the match to be outside `<!-- ... -->` pairs. Verifier strips HTML comments before counting.) The page is a pure waiting state.
- AC5: Exactly one `<h1>`. The h1 inner text is 2 to 4 whitespace-separated words, ends with a literal `.` period, contains no `?` or `!`. Locked value: `Spawning your agent.` Verifier asserts byte-exact match to `Spawning your agent.`.
- AC6: The heading mentions at least one of `agent`, `spawn`, `spawning`, `provisioning` (case-insensitive). With the locked h1 this is automatically satisfied by `agent` and `spawning`.
- AC7: Optional subline. If present, it is a single `<p>` element whose inner text is 6 to 12 whitespace-separated words, contains no `!`, no `?`, no `—` (U+2014), no `–` (U+2013). Locked value: `This usually takes under sixty seconds.` (seven words). Verifier asserts exactly one `<p>` tag and that its inner text byte-equals `This usually takes under sixty seconds.`.
- AC8: Marketing buzzword ban. Case-insensitive grep for each of these returns zero matches: `revolutionary`, `seamless`, `unleash`, `harness`, `empower`, `best-in-class`, `world-class`, `cutting-edge`, `leverage`, `synergy`, `streamline`, `next-generation`, `game-changer`, `transform`, `we're excited`, `introducing`, `unlock`.
- AC9: Em-dash and en-dash ban. Grep for U+2014 (`—`) and U+2013 (`–`) returns zero matches across the entire file.
- AC10: No `!` in visible body text. Verifier strips `<!DOCTYPE`, HTML comments, all tag attributes, and the entire `<style>` block, then asserts `!` count equals 0 in the remaining visible text. (`!important` inside CSS is allowed because the `<style>` block is excluded.)
- AC11: Crypto-hidden ban. Case-insensitive word-boundary grep for each of these 11 terms returns zero matches: `X-Layer`, `EVM`, `chain`, `gas`, `signature`, `USDC`, `ETH`, `blockchain`, `crypto`, `0x`, `wallet`.
- AC12: Zero `<script>` tags. Zero `src=` attributes. Zero `<link>` tags pointing at network resources (`href="http`, `href="//`). Zero `@import` rules. Zero `://` sequences outside HTML and CSS comments (verifier strips `<!-- ... -->` and `/* ... */` blocks before grepping `://`). Zero CDN host references (`fonts.googleapis.com`, `cdn.`, `cloudflare.com`, `jsdelivr.net`, `unpkg.com`).
- AC13: Exactly one `<style>` block inside `<head>`. Zero inline `style="..."` attributes. Verifier asserts `<style` count equals 1 and ` style=` count equals 0.
- AC14: `:root` block declares `--bg`, `--fg`, `--muted`, `--rule`, `--tint`, `--accent`, `--radius`, `--max` with values BYTE-IDENTICAL to `design/landing.html`. Verifier extracts `:root { ... }` from both files, parses each `--name: value;` declaration, and asserts that for each of the eight canonical keys the value strings are identical. Expected values:
  - `--bg: #0a0a0b;`
  - `--fg: #f5f5f4;`
  - `--muted: rgba(245, 245, 244, 0.46);`
  - `--rule: rgba(245, 245, 244, 0.08);`
  - `--tint: rgba(245, 245, 244, 0.04);`
  - `--accent: #c6ff3d;`
  - `--radius: 2px;`
  - `--max: 44rem;`
  The `:root` block MUST be preceded by the comment `/* theme: dark */` as in landing.html.
- AC15: Font family declared on `body` (or an equivalent global selector) equals byte-exact the landing.html stack: `"JetBrains Mono", "IBM Plex Mono", "SF Mono", "Fira Code", ui-monospace, monospace`. None of `Inter`, `Roboto`, `Arial`, `Helvetica`, `system-ui`, `-apple-system` may appear as the primary family.
- AC16: Accent usage is ZERO (option A). `var(--accent)` appears exactly once in the entire file, and that single occurrence is inside the `:root` block on the `--accent: #c6ff3d;` declaration line. Verifier asserts `grep -c 'var(--accent)' design/running.html` equals 0 (no references outside the declaration), AND asserts `grep -c -- '--accent:' design/running.html` equals 1 (the declaration itself). No progress bar, no pulse dot, no keyframes animating an accent color.
- AC17: Responsive sizing. The file contains at least one `@media (max-width: ...)` rule OR uses fluid units (`clamp(`, `vw`, `vmin`) on the heading. Verifier passes if either pattern is present.
- AC18: Total file size strictly less than 10240 bytes (10 KB). `stat -f%z design/running.html` reports a value < 10240.
- AC19: Visible body word count strictly less than 40. Verifier strips `<style>`, HTML comments, and tags; counts whitespace-separated tokens in the remainder; asserts result < 40. With the locked heading (3 words) and locked subline (7 words) the expected count is 10.
- AC20: Exactly one `<h1>`. Zero other heading tags. Verifier asserts `grep -c '<h1' design/running.html` equals 1 and `grep -cE '<h[2-6]' design/running.html` equals 0.
- AC21: No `text-transform:` or `transform:` anywhere in the file. Verifier asserts `grep -c 'text-transform' design/running.html` equals 0 and `grep -cE '(^|[^-a-z])transform[[:space:]]*:' design/running.html` equals 0.
- AC22: Collateral check. Only `design/running.html` is created by this task. `design/landing.html`, `design/wizard.html`, `design/agents.html` are NOT modified. Builder snapshots `sha256sum` for all three BEFORE any edits and writes digests to `.agent/tasks/running-state-mockup-steve-ive/{landing,wizard,agents}.sha256`. Verifier re-hashes all three files and asserts each digest matches the stored snapshot and matches the baseline recorded in the "Prior-design sha256 baseline" section of this spec:
  - landing: `87e79f41082c177e5c79859afe16b53b95a19e27bd1258e502a1f9011c4d4d64`
  - wizard:  `8c3dc84dd422ccd9e1e04502205d7992c29e7d6ebd91632e05f6197d3de14f18`
  - agents:  `11cc4d2284b60777a01a298082648a9dbda105383dc601b58f6df2e39ec12795`
  `ls design/ | sort` returns exactly four entries: `agents.html`, `landing.html`, `running.html`, `wizard.html`.
- AC23: No regressions on the 10 prior tasks. Verifier reruns `python3 scripts/task_loop.py status --task-id <TASK_ID>` (or equivalent) on each of the following and asserts each remains in its pre-task terminal state:
  1. `bootstrap-proof-loop`
  2. `scaffold-service-stubs`
  3. `test-harness`
  4. `landing-mockup-steve-ive`
  5. `tdd-telegram-validate-token`
  6. `wizard-mockup-steve-ive`
  7. `tdd-onchainos-create-charge`
  8. `agents-list-mockup-steve-ive`
  9. `tdd-kiloclaw-install`
  10. `tdd-telegram-set-webhook`
  The parallel `tdd-agent-deployer` task is explicitly EXCLUDED from this regression check because it may be in any state during this task's run.

## Constraints

- No JavaScript anywhere. Zero `<script>` tags.
- No external network requests. No CDN, no `<link href="https?://...">`, no `@import url(...)`, no `<img src="...">`, no font loading from the network.
- All CSS lives inline in a single `<style>` block in `<head>`. No `style="..."` attributes.
- Zero interactive elements. No `<button>`, `<form>`, `<input>`, `<select>`, `<textarea>`, `<nav>`, `<footer>`, no visible `<a href="...">`. The page is a pure waiting state.
- The page MUST visually match `design/landing.html`, `design/wizard.html`, and `design/agents.html`: same `:root` token names AND values, same `body` font-family stack, same background gradient treatment, same `/* theme: dark */` comment at the top of the `<style>` block.
- Accent option A (zero usage): `var(--accent)` is declared once in `:root` and never referenced. No progress bar. No pulse dot. No CSS keyframes.
- The CSS must not use the `transform` or `text-transform` property names anywhere.
- No em dashes (U+2014) or en dashes (U+2013) anywhere in the spec, the HTML, or any commit message.
- Any deletes use `trash`, never `rm` or `rm -rf`.
- Crypto stays hidden. Zero chain names, zero token symbols, zero gas, zero signatures, zero wallet references.
- Only `/Users/krutovoy/Projects/hosting-platform/design/running.html` is created. `design/landing.html`, `design/wizard.html`, `design/agents.html` are not touched. No other repo file is modified except the `.agent/tasks/running-state-mockup-steve-ive/` workflow artifacts.
- Heading locked to `Spawning your agent.` Subline locked to `This usually takes under sixty seconds.` Builder does not invent alternatives.
- Visible body word count under 40. With locked copy the count is 10.
- File size under 10240 bytes.

## Non-goals

- No spinner component. No animated dots. No CSS keyframes. No marquee.
- No progress bar, no pulse dot, no hairline accent (option A chosen).
- No real API call, no fetch, no polling, no server-sent events, no Inertia wiring.
- No header nav, no footer, no breadcrumbs, no logo.
- No "back to agents" link. The user does not need an escape hatch on a waiting screen.
- No step indicators, no "1 of 7" counter, no timeline.
- No error or timeout state. No retry button. (Those are future tasks.)
- No agent name echo, no chosen personality preview, no payment amount. Crypto hidden principle forbids the amount; the name would require a server round-trip the mockup cannot do.
- No wallet address. No chain label. No gas estimate. No transaction hash.
- No icons, no SVG, no illustrations, no images.
- No theme toggle, no settings, no account menu.
- No retry of the landing, wizard, or agents pages. All three are frozen for this task.

## Verification plan

The verifier runs from `/Users/krutovoy/Projects/hosting-platform`. Each AC maps to one or more shell checks.

- AC1: `test -f design/running.html && test -d design`
- AC2: `head -1 design/running.html | grep -qi '^<!doctype html>'` and `grep -qE '<html[^>]+lang=' design/running.html` and `grep -qiE '<meta[^>]+charset="?utf-?8' design/running.html` and `grep -qE '<meta[^>]+name="viewport"[^>]+content="width=device-width, initial-scale=1(\.0)?"' design/running.html`
- AC3: `[ "$(grep -c '<title>' design/running.html)" = "1" ]` and verifier extracts title text and asserts whitespace-separated word count <= 5.
- AC4: verifier strips HTML comments, then asserts each of the following `grep -c` counts equals 0: `<button`, `<form`, `<input`, `<select`, `<textarea`, `<nav`, `<footer`, `<a href=`.
- AC5: `[ "$(grep -c '<h1' design/running.html)" = "1" ]` and verifier extracts the h1 inner text and asserts it byte-equals `Spawning your agent.`.
- AC6: verifier extracts h1 inner text and asserts case-insensitive match against the regex `(agent|spawn|spawning|provisioning)`.
- AC7: `[ "$(grep -c '<p' design/running.html)" = "1" ]` and verifier extracts the p inner text, asserts byte-equal to `This usually takes under sixty seconds.`, asserts word count between 6 and 12 inclusive, asserts it contains no `!`, `?`, `—`, `–`.
- AC8: for each banned word in the AC8 list, `! grep -qi 'WORD' design/running.html`.
- AC9: `! grep -q $'\u2014' design/running.html` and `! grep -q $'\u2013' design/running.html`.
- AC10: verifier strips `<!DOCTYPE`, HTML comments, attribute values, and the entire `<style>` block, then asserts `!` count equals 0 in the remaining visible text.
- AC11: for each banned crypto term in `X-Layer|EVM|chain|gas|signature|USDC|ETH|blockchain|crypto|0x|wallet`, `! grep -qiw 'TERM' design/running.html`. Zero matches expected for every term.
- AC12: `! grep -q '<script' design/running.html` and `! grep -q 'src=' design/running.html` and `! grep -qE '<link[^>]+href="(https?:|//)' design/running.html` and `! grep -q '@import' design/running.html`. Verifier strips HTML comments and CSS `/* ... */` blocks then asserts `grep -c '://' remaining` equals 0. `! grep -qiE 'fonts\.googleapis\.com|cdn\.|cloudflare\.com|jsdelivr\.net|unpkg\.com' design/running.html`.
- AC13: `[ "$(grep -c '<style' design/running.html)" = "1" ]` and `! grep -q ' style=' design/running.html`.
- AC14: verifier extracts `:root { ... }` from both `design/landing.html` and `design/running.html`, parses each `--name: value;` declaration, asserts that for keys `--bg`, `--fg`, `--muted`, `--rule`, `--tint`, `--accent`, `--radius`, `--max` the values are byte-identical between the two files. Verifier also asserts `grep -q '/\* theme: dark \*/' design/running.html`.
- AC15: verifier parses the `body { font-family: ... }` declaration, asserts the full stack equals `"JetBrains Mono", "IBM Plex Mono", "SF Mono", "Fira Code", ui-monospace, monospace`, asserts none of the banned defaults appear as the primary family.
- AC16: `[ "$(grep -c 'var(--accent)' design/running.html)" = "0" ]` and `[ "$(grep -c -- '--accent:' design/running.html)" = "1" ]`.
- AC17: `grep -qE '@media[^{]*max-width' design/running.html` OR `grep -qE 'clamp\(|[0-9.]+vw|[0-9.]+vmin' design/running.html`.
- AC18: `[ "$(stat -f%z design/running.html)" -lt "10240" ]`.
- AC19: verifier strips `<style>`, HTML comments, and tags; counts whitespace-separated tokens; asserts count < 40.
- AC20: `[ "$(grep -c '<h1' design/running.html)" = "1" ]` and `[ "$(grep -cE '<h[2-6]' design/running.html)" = "0" ]`.
- AC21: `[ "$(grep -c 'text-transform' design/running.html)" = "0" ]` and `[ "$(grep -cE '(^|[^-a-z])transform[[:space:]]*:' design/running.html)" = "0" ]`.
- AC22: builder writes `sha256sum design/landing.html`, `sha256sum design/wizard.html`, `sha256sum design/agents.html` to `.agent/tasks/running-state-mockup-steve-ive/{landing,wizard,agents}.sha256` BEFORE any edits. Verifier re-hashes all three and asserts each digest matches both the stored snapshot AND the baseline in this spec (landing=`87e79f41082c177e5c79859afe16b53b95a19e27bd1258e502a1f9011c4d4d64`, wizard=`8c3dc84dd422ccd9e1e04502205d7992c29e7d6ebd91632e05f6197d3de14f18`, agents=`11cc4d2284b60777a01a298082648a9dbda105383dc601b58f6df2e39ec12795`). `ls design/ | sort` returns exactly the four lines `agents.html`, `landing.html`, `running.html`, `wizard.html`.
- AC23: verifier reruns `python3 scripts/task_loop.py status --task-id <TASK_ID>` (or equivalent) on each of the 10 prior tasks listed in AC23 and asserts each remains in its pre-task terminal state. The parallel `tdd-agent-deployer` task is excluded.

Build: none (static HTML).
Unit tests: none (UI design carve-out from TDD).
Integration tests: none.
Lint: none required; the spec's grep-based checks act as the lint.
Manual checks: open `design/running.html` in a browser at 320px, 768px, and 1920px viewport widths. Confirm one heading and one subline, nothing else. Confirm zero buttons, zero links, zero form controls. Confirm no accent color appears visually. Confirm the page passes the 5-question steve-ive decision framework: the page is for "telling you it's working" (5 words); the user's next action is none; nothing can be removed without breaking purpose; typography carries hierarchy; there is breathing room.
