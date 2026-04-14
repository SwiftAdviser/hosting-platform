# Task Spec: wizard-mockup-steve-ive

## Metadata
- Task ID: wizard-mockup-steve-ive
- Created: 2026-04-14T08:54:59+00:00
- Frozen: 2026-04-13
- Repo root: /Users/krutovoy/Projects/hosting-platform
- Working directory at init: /Users/krutovoy/Projects/hosting-platform

## Guidance sources
- /Users/krutovoy/.claude/skills/steve-ive/SKILL.md
- /Users/krutovoy/.claude/CLAUDE.md (frontend_aesthetics block, voice rules)
- /Users/krutovoy/Projects/hosting-platform/CLAUDE.md (Principle: Crypto hidden, Walkthrough-first)
- /Users/krutovoy/Projects/hosting-platform/docs/sprint_v0.1.md (Day 1 Task 4: Create Agent wizard, collapse Pinata's 4 steps into 1 Inertia page with sections)
- /Users/krutovoy/Projects/hosting-platform/docs/agent_spawn_prd.md (the 7-step walkthrough; this form is step 2)
- /Users/krutovoy/Projects/hosting-platform/design/landing.html (visual tokens to copy verbatim)
- /Users/krutovoy/Projects/hosting-platform/.agent/tasks/landing-mockup-steve-ive/spec.md (sister spec; ban lists reused verbatim)

## Original task statement
Second steve-ive static UI mockup at design/wizard.html: the Create Agent single-form screen from sprint_v0.1.md Day 1 Task 4. One Inertia-equivalent page with one primary CTA. Fields: agent name (required), personality (textarea, required), Telegram bot token (required), allowlist user IDs (optional, comma-separated). No wizard steps. No progress indicator. No secondary CTAs. Same steve-ive principles as landing-mockup-steve-ive: typography does the heavy lifting, one action per screen, ruthless reduction, no marketing copy, no icons, inline CSS only, no JavaScript, no external requests, crypto hidden, no em dashes, no exclamation marks, no buzzwords. Reuse the design language (same fonts, same accent color, same CSS var names) to keep the two pages feeling like one product. Total page under 14 KB, under 80 visible body words. Do not touch any other file.

## Visual tokens locked from design/landing.html
The wizard MUST declare these exact CSS custom properties with these exact values inside its `:root` block. Captured from `design/landing.html` lines 8 to 18:

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

CTA text locked from landing line 96 (`<a class="cta" href="#post">Post agent</a>`):
`Post agent`

The wizard's primary CTA MUST read `Post agent` to match the landing page CTA exactly.

## Steve-ive principles enforced
Quoted from `/Users/krutovoy/.claude/skills/steve-ive/SKILL.md` and used as the rubric:
- "Every element must earn its place"
- "Typography does the heavy lifting"
- "One action per screen"
- "Reduce, then reduce again"
- "Contrast creates meaning"
- "Noise is the enemy, not simplicity"
- Voice: "Short sentences. Active voice. No em dashes. No exclamation marks. No 'we're excited to.'"

## Acceptance criteria

- AC1: File exists at exactly `/Users/krutovoy/Projects/hosting-platform/design/wizard.html`. The parent `design/` directory exists.
- AC2: HTML5 doctype on the first non-empty line (`<!DOCTYPE html>`, case-insensitive). The `<html>` root tag carries a `lang` attribute. The `<head>` declares `<meta charset="utf-8">` (or `UTF-8`) and `<meta name="viewport" content="width=device-width, initial-scale=1">` (initial-scale value may be `1` or `1.0`).
- AC3: Exactly one `<title>` tag. The title text is 5 words or fewer.
- AC4: Exactly one primary CTA element. The CTA text is `Post agent` (matches landing.html). The CTA element is a `<button type="submit">` (preferred) or an `<input type="submit" value="Post agent">`. The text must match the verb-phrase allowlist `Post agent | Deploy agent | Spawn agent | Launch agent | Ship agent | Run agent | Start agent`, and to keep the product consistent with the landing page the chosen text MUST be `Post agent`.
- AC5: No secondary CTA. Zero occurrences (case-insensitive) of any of: `Learn more`, `Read more`, `See more`, `Get started`, `Sign up`, `Sign in`, `Log in`, `More info`, `Back`, `Cancel`. The wizard has no back button and no cancel button. The page has exactly one `<button>` element (see AC25).
- AC6: Exactly four form fields with the shape below (counted as distinct elements inside the `<form>`):
  - one `<input type="text" name="name" required>` with an associated label whose text is short (e.g. `Name`, `Agent name`)
  - one `<textarea name="personality" required></textarea>` with an associated label
  - one `<input type="text" name="bot_token" required>` with an associated label (a `type="password"` is also acceptable; the verifier accepts `type="text"` OR `type="password"` for this field)
  - one `<input type="text" name="allowlist">` with NO `required` attribute and an associated label
  The fifth interactive element is the primary submit CTA (see AC4). No other inputs, textareas, or selects exist anywhere in the document.
- AC7: Every input/textarea is labeled. For each of the four fields, either a `<label for="ID">` references the field by `id`, or the field is wrapped inside its `<label>`. No unlabeled inputs.
- AC8: Exactly one `<form>` element exists. It has `method="post"` (case-insensitive). The `action` attribute may be `#`, `/api/deploys`, or any non-network path; it MUST NOT contain `://`.
- AC9: Zero `<script>` tags. Zero `src=` attributes. Zero `<link>` tags pointing at network resources. Zero `@import url(` rules. Zero CDN hosts. No `://` outside HTML/CSS comments. The verifier extracts comments first; any remaining `://` fails the check.
- AC10: All CSS lives in a single `<style>` block inside `<head>`. No inline `style="..."` attributes anywhere. Verifier asserts `<style` count == 1 and ` style=` count == 0.
- AC11: Marketing buzzword ban. Case-insensitive grep for each of these returns zero matches: `revolutionary`, `seamless`, `unleash`, `harness`, `empower`, `best-in-class`, `world-class`, `cutting-edge`, `leverage`, `synergy`, `streamline`, `next-generation`, `game-changer`, `transform`, `we're excited`, `introducing`, `unlock`.
- AC12: Em-dash and en-dash ban. Grep for U+2014 (`—`) and U+2013 (`–`) returns zero matches.
- AC13: No exclamation marks in visible body text. Verifier strips `<!DOCTYPE`, comments, attribute values, and the `<style>` block, then asserts `!` count == 0. (`!important` inside CSS is allowed because the `<style>` block is excluded from this check.)
- AC14: Crypto-hidden ban. Case-insensitive grep for each of these returns zero matches: `X-Layer`, `EVM`, `chain`, `gas`, `signature`, `USDC`, `ETH`, `0x`, `blockchain`, `crypto`. The word `wallet` is allowed at most once. Default expected count of `wallet`: zero.
- AC15: The page mentions the product noun. Case-insensitive grep for `agent` returns at least one match in visible text.
- AC16: `:root` block declares at minimum `--bg`, `--fg`, `--accent` with values BYTE-IDENTICAL to `design/landing.html`. The verifier extracts the `:root { ... }` block from both files, parses each `--name: value;` declaration, and asserts that for every key present in BOTH files the value strings are identical. Required shared keys: `--bg`, `--fg`, `--accent`, `--muted`, `--rule`, `--tint`, `--radius`, `--max`. Expected exact values:
  - `--bg: #0a0a0b;`
  - `--fg: #f5f5f4;`
  - `--muted: rgba(245, 245, 244, 0.46);`
  - `--rule: rgba(245, 245, 244, 0.08);`
  - `--tint: rgba(245, 245, 244, 0.04);`
  - `--accent: #c6ff3d;`
  - `--radius: 2px;`
  - `--max: 44rem;`
- AC17: Font family declared on `body` (or global selector) starts with `"JetBrains Mono"` (the same primary family as landing.html) and includes at least three fallbacks before the generic `monospace`. The full stack must equal landing.html's stack: `"JetBrains Mono", "IBM Plex Mono", "SF Mono", "Fira Code", ui-monospace, monospace`. None of `Inter`, `Roboto`, `Arial`, `Helvetica`, `system-ui`, `-apple-system` may appear as the primary family.
- AC18: `var(--accent)` is referenced ONLY in CSS rules whose selector targets the primary CTA or its interaction states. Allowed selectors: `.cta`, `button.cta`, `button[type="submit"]`, `input[type="submit"]`, and any of these combined with `:hover`, `:focus`, `:focus-visible`. Verifier extracts every CSS rule that contains `var(--accent)` and asserts the selector matches one of the allowed forms. Form input selectors (`input`, `textarea`, `label`, `:focus` on inputs) must NOT reference `var(--accent)`. Inputs may use `currentColor`, `var(--fg)`, `var(--muted)`, or `var(--rule)` for borders and focus rings.
- AC19: Responsive sizing. The file contains at least one `@media (max-width: ...)` rule AND/OR uses fluid units (`clamp(`, `vw`, `vmin`) on the headline. Verifier passes if either pattern is present.
- AC20: Total file size strictly under 14336 bytes (14 KB). `stat -f%z` reports < 14336.
- AC21: Visible body word count strictly under 80 words. The verifier strips `<style>`, comments, and tags, then counts whitespace-separated tokens. Field labels and helper hints count toward the budget.
- AC22: Collateral check. Only `design/wizard.html` is created by this task. `design/landing.html` is NOT modified. `ls design/` returns exactly two entries: `landing.html` and `wizard.html`. No file at the repo root (other than the existing tracked tree and the `.agent/tasks/wizard-mockup-steve-ive/` workflow artifacts) is added or modified.
- AC23: `design/landing.html` is byte-identical to its pre-task state. The builder snapshots `sha256sum design/landing.html` before any edits and writes the digest to `.agent/tasks/wizard-mockup-steve-ive/landing.sha256`. The verifier re-hashes `design/landing.html` and asserts the digest matches.
- AC24: Each form field has EITHER a placeholder OR a visible helper hint, never both. The verifier walks each of the four fields and asserts `not (has_placeholder_attr and has_visible_hint_sibling)`. Redundancy is noise.
- AC25: The submit CTA is the ONLY `<button>` in the DOM. `grep -c '<button' design/wizard.html` returns `1`. No ghost nav buttons, no theme toggles, no icon buttons, no disclosure buttons.

## Constraints

- No JavaScript anywhere in the file. Zero `<script>` tags.
- No external network requests. No CDN, no `<link href="https?://...">`, no `@import url(...)`, no `<img src="https://...">`, no font loading from the network.
- All CSS lives inline in a single `<style>` block in `<head>`. No `style="..."` attributes on elements.
- The wizard MUST visually match `design/landing.html`: same `:root` token names AND same token values, same `body` font-family stack, same background gradient treatment, same `/* theme: dark */` comment at the top of the `<style>` block.
- The accent color `var(--accent)` is reserved exclusively for the primary CTA and its hover/focus states.
- The form is NOT wired. The `<form action="...">` may point to `#` or `/api/deploys`. There is no client-side validation, no error states, no success state.
- No em dashes (U+2014) or en dashes (U+2013) anywhere in the spec, the HTML, or any commit message produced by this task.
- Any deletes use `trash`, never `rm` or `rm -rf`.
- The page must hold up under steve-ive line-by-line review.
- Crypto stays hidden. The user must not see chain names, token symbols, gas, signatures, or wallet addresses on this page.
- Only `/Users/krutovoy/Projects/hosting-platform/design/wizard.html` is created. `design/landing.html` is not touched. No other repo file is modified except the `.agent/tasks/wizard-mockup-steve-ive/` workflow artifacts.

## Non-goals

- No form validation JavaScript. No regex, no inline error states, no live token-format check on the bot token field.
- No backend wiring. The form's `action` is a placeholder; no `fetch`, no Inertia, no Laravel route.
- No error states. No success screen. No "Agent posted" confirmation page (that is a separate task).
- No progress indicator. No step counter. No "Step 1 of 1" caption.
- No multi-step wizard. The page is one form on one screen.
- No accessibility audit beyond the AC7 label-association requirement. Full a11y is a separate task.
- No animation libraries. No transitions other than what plain CSS provides for free on `:hover` / `:focus-visible`.
- No icons. No illustrations. No SVG decorations.
- No header nav, no footer, no breadcrumbs, no link list, no theme toggle.
- No retry of the landing page. `design/landing.html` is frozen for this task.

## Verification plan

The verifier runs from `/Users/krutovoy/Projects/hosting-platform`. Each AC maps to one or more shell checks.

- AC1: `test -f design/wizard.html && test -d design`
- AC2: `head -1 design/wizard.html | grep -qi '^<!doctype html>'` and `grep -qE '<html[^>]+lang=' design/wizard.html` and `grep -qiE '<meta[^>]+charset="?utf-?8' design/wizard.html` and `grep -qE '<meta[^>]+name="viewport"[^>]+content="width=device-width, initial-scale=1(\.0)?"' design/wizard.html`
- AC3: `[ "$(grep -c '<title>' design/wizard.html)" = "1" ]` and verifier extracts title text and asserts word count <= 5.
- AC4: verifier locates the submit element (`<button type="submit"` OR `<input type="submit"`), extracts its visible text or `value` attribute, asserts the text equals `Post agent` exactly. Verifier also asserts text matches the regex `^(Post|Deploy|Spawn|Launch|Ship|Run|Start) [A-Za-z]+$`.
- AC5: for each banned phrase in `Learn more|Read more|See more|Get started|Sign up|Sign in|Log in|More info|Back|Cancel`, `! grep -qi 'PHRASE' design/wizard.html` (the `Back`/`Cancel` checks are run against visible text only, after stripping `<style>` and attribute values, so a CSS property like `background` does not trip it).
- AC6: verifier parses the form and asserts: exactly one `<input type="text" name="name"` with `required`; exactly one `<textarea name="personality"` with `required`; exactly one `<input` with `name="bot_token"` whose `type` is `text` or `password` and which has `required`; exactly one `<input type="text" name="allowlist"` WITHOUT `required`. Total non-submit form controls inside the `<form>`: 4. Total `<input>` plus `<textarea>` plus `<select>` elements in the document: 4 (the submit `<button>` is not counted here; if `<input type="submit">` is used, total is 5 and one of them is the submit).
- AC7: verifier asserts each of the four field elements has either a wrapping `<label>` or a sibling `<label for="ID">` matching its `id`. No orphan inputs.
- AC8: `[ "$(grep -c '<form' design/wizard.html)" = "1" ]` and `grep -qiE '<form[^>]+method="post"' design/wizard.html` and verifier asserts the `action` attribute does NOT contain `://`.
- AC9: `! grep -q '<script' design/wizard.html` and `! grep -q 'src=' design/wizard.html` and `! grep -qE '<link[^>]+href="(https?:|//)' design/wizard.html` and `! grep -q '@import' design/wizard.html` and `! grep -qiE 'fonts\.googleapis\.com|cdn\.|cloudflare\.com|jsdelivr\.net|unpkg\.com' design/wizard.html` and verifier strips HTML and CSS comments then asserts `://` count == 0.
- AC10: `[ "$(grep -c '<style' design/wizard.html)" = "1" ]` and `! grep -q ' style=' design/wizard.html`
- AC11: for each banned word in the AC11 list, `! grep -qi 'WORD' design/wizard.html`
- AC12: `! grep -q $'\u2014' design/wizard.html` and `! grep -q $'\u2013' design/wizard.html`
- AC13: verifier strips `<!DOCTYPE`, HTML comments, attribute values, and the entire `<style>` block, then asserts `!` count == 0 in the remaining visible text.
- AC14: for each banned crypto term, `! grep -qi 'TERM' design/wizard.html`. For `wallet`: `[ "$(grep -ci 'wallet' design/wizard.html)" -le "1" ]`. Default expected: zero.
- AC15: `grep -qi 'agent' design/wizard.html`
- AC16: verifier extracts the `:root { ... }` block from both `design/landing.html` and `design/wizard.html`, parses each `--name: value;` declaration into a dict, and asserts that for keys `--bg`, `--fg`, `--accent`, `--muted`, `--rule`, `--tint`, `--radius`, `--max` the values are byte-identical between the two files. Expected values listed under AC16 above.
- AC17: verifier parses the `body { font-family: ... }` declaration, asserts the first family is `"JetBrains Mono"`, asserts the full stack equals landing.html's stack, asserts none of the banned defaults appear as the primary family.
- AC18: verifier walks each CSS rule that contains `var(--accent)` and asserts its selector matches one of: `.cta`, `button.cta`, `button[type="submit"]`, `input[type="submit"]`, optionally with `:hover` / `:focus` / `:focus-visible`. Form-input selectors must not match.
- AC19: `grep -qE '@media[^{]*max-width' design/wizard.html` OR `grep -qE 'clamp\(|[0-9.]+vw|[0-9.]+vmin' design/wizard.html`
- AC20: `[ "$(stat -f%z design/wizard.html)" -lt "14336" ]`
- AC21: verifier strips `<style>`, comments, and tags; counts whitespace-separated tokens; asserts < 80.
- AC22: `ls design/ | sort` returns exactly the two lines `landing.html` and `wizard.html`. `git status --porcelain` (run with default flags) lists only new/changed entries inside `design/wizard.html` and `.agent/tasks/wizard-mockup-steve-ive/`. No other repo file is modified.
- AC23: builder writes `sha256sum design/landing.html` to `.agent/tasks/wizard-mockup-steve-ive/landing.sha256` BEFORE making any edits. Verifier re-runs `sha256sum design/landing.html` and asserts the digest matches the stored snapshot.
- AC24: verifier walks each of the four fields. For each field, computes `has_placeholder = field has placeholder=""` and `has_hint = a sibling element with class containing 'hint' or 'help' exists in the same label/wrapper`. Asserts `not (has_placeholder and has_hint)` for every field.
- AC25: `[ "$(grep -c '<button' design/wizard.html)" = "1" ]`

Build: none (static HTML).
Unit tests: none (UI design carve-out from TDD).
Integration tests: none.
Lint: none required; the spec's grep-based checks act as the lint.
Manual checks: open `design/wizard.html` in a browser at 320px, 768px, and 1920px viewport widths. Confirm exactly one CTA is visible and dominant. Confirm the four fields are present and labeled. Confirm the visual language matches `design/landing.html`: same background gradient, same accent on the CTA only, same monospace font, same dark theme. Confirm no marketing copy. Confirm the page passes the 5-question steve-ive decision framework.
