# Task Spec: landing-mockup-steve-ive

## Metadata
- Task ID: landing-mockup-steve-ive
- Created: 2026-04-14T08:35:45+00:00
- Repo root: /Users/krutovoy/Projects/hosting-platform
- Working directory at init: /Users/krutovoy/Projects/hosting-platform

## Guidance sources
- /Users/krutovoy/.claude/skills/steve-ive/SKILL.md
- /Users/krutovoy/.claude/CLAUDE.md (frontend_aesthetics block)
- /Users/krutovoy/Projects/hosting-platform/CLAUDE.md (What this is, Principle: Crypto hidden, Walkthrough-first)
- /Users/krutovoy/Projects/hosting-platform/docs/agent_spawn_prd.md (the 7-step walkthrough; landing is step 0/1)
- /Users/krutovoy/Projects/hosting-platform/docs/sprint_v0.1.md (Day 1 Task 3: single Inertia page, one button: Post agent, no marketing copy)
- /Users/krutovoy/Projects/hosting-platform/.agent/tasks/landing-mockup-steve-ive/spec.md (placeholder it replaces)

## Original task statement
Build a single static HTML/CSS landing-page mockup at design/landing.html for platform.thespawn.io using the steve-ive design philosophy: one action per screen, typography-led hierarchy, ruthless reduction. The page exists for one purpose: get a developer to click 'Post agent'. No marketing copy. No icons. No multiple CTAs. No section labels. The form lives behind that single primary action; it is NOT shown on this page. No JavaScript. No external assets. Inline CSS only. Mobile and desktop must both work. Reference docs/agent_spawn_prd.md for the product premise (one-click agent hosting on KiloClaw with hidden crypto wallet via OnChainOS). Do not wire into Inertia or Laravel. Do not modify CLAUDE.md, AGENTS.md, docs/, or any existing app/Services file. UI design is the carve-out: TDD does not apply to this task, but every visual decision must be defensible against the steve-ive principles.

## Steve-ive principles enforced
Quoted from `/Users/krutovoy/.claude/skills/steve-ive/SKILL.md` and used as the rubric:
- "Every element must earn its place"
- "Typography does the heavy lifting"
- "One action per screen"
- "Reduce, then reduce again"
- "Contrast creates meaning"
- "Noise is the enemy, not simplicity"
- Voice: "Short sentences. Active voice. No em dashes. No exclamation marks. No 'we're excited to.'"
- Frontend aesthetics (user global CLAUDE.md): no Inter/Roboto/Arial/system defaults; commit to a palette via CSS vars; bold accents over timid gradients; depth in background, not flat default; avoid purple-on-white clichés.

## Acceptance criteria

- AC1: File exists at exactly `/Users/krutovoy/Projects/hosting-platform/design/landing.html`. The parent `design/` directory exists.
- AC2: First non-empty line of the file is `<!DOCTYPE html>` (case-insensitive). The `<html>` root tag carries a `lang` attribute.
- AC3: The `<head>` declares `<meta charset="utf-8">` (or `UTF-8`) and `<meta name="viewport" content="width=device-width, initial-scale=1">` (initial-scale value may be `1` or `1.0`).
- AC4: The file contains exactly one `<title>` tag. The title text is 5 words or fewer and is a statement, not marketing fluff.
- AC5: The file contains exactly one primary CTA element. The CTA text is a verb phrase of 3 words or fewer (for example: `Post agent`, `Deploy agent`, `Spawn agent`). The CTA is a `<button>` or an `<a>` tag.
- AC6: There is no secondary CTA: no second `<button>`, no `Learn more` link, no nav menu, no link list. The file contains zero occurrences of the literal phrase `Learn more` (case-insensitive).
- AC7: The file contains zero `<script>` tags. The file contains zero `src=` attributes (no external JS, no images). The file contains zero `<link>` tags pointing at network resources (`href="http`, `href="//`). The file contains zero `@import` rules. The file contains zero references to `fonts.googleapis.com` or any other CDN host.
- AC8: All CSS lives in a single `<style>` block inside `<head>`. No inline `style="..."` attributes outside that block (the verifier counts `style=` occurrences and expects zero).
- AC9: Marketing buzzword ban. Case-insensitive grep for each of these returns zero matches: `revolutionary`, `seamless`, `unleash`, `harness`, `empower`, `best-in-class`, `world-class`, `cutting-edge`, `leverage`, `synergy`, `streamline`, `next-generation`, `game-changer`, `transform`, `we're excited`, `introducing`, `unlock`.
- AC10: Em-dash ban. Grep for U+2014 (`—`) and U+2013 (`–`) returns zero matches in the file.
- AC11: No exclamation marks in body text. Grep for `!` returns zero matches outside HTML attribute syntax. (The verifier strips attribute values and `<!DOCTYPE` / `<!--` markers before counting.)
- AC12: Crypto-hidden enforcement. Case-insensitive grep for each of these returns zero matches: `X-Layer`, `EVM`, `chain`, `gas`, `signature`, `USDC`, `ETH`, `0x`, `blockchain`, `crypto`. The word `wallet` is allowed at most once and only if essential to user-facing copy. Default expected count of `wallet`: zero.
- AC13: The page mentions the product noun. Case-insensitive grep for `agent` returns at least one match.
- AC14: CSS custom properties exist in a `:root` block for at least three tokens: a primary background color, a primary text color, and an accent color. Variable names must follow the `--name` convention. The verifier checks that `:root` is present and that at least three `--` declarations live inside it.
- AC15: No hardcoded color literals outside `:root`. Grep for `#` followed by 3 or 6 hex digits, and for `rgb(` / `rgba(` / `hsl(` / `hsla(`, returns matches only on lines inside the `:root { ... }` block. Color use elsewhere goes through `var(--token)`.
- AC16: Non-default font family. The first font family declared in `body` (or the global selector) is a real named font, not one of: `Inter`, `Roboto`, `Arial`, `Helvetica`, `sans-serif`, `serif`, `system-ui`, `-apple-system`. The family stack must include at least three fallbacks after the primary.
- AC17: Theme commitment. The file either contains a `@media (prefers-color-scheme:` rule, or the spec records that the page commits to a single theme (dark or light). The chosen approach is documented in a comment at the top of the `<style>` block.
- AC18: Single accent color, applied only to the CTA. There is exactly one `--accent` (or similarly named accent) custom property. Every reference to that variable in the stylesheet attaches to the CTA selector or its `:hover` / `:focus` / `:focus-visible` state.
- AC19: Responsive sizing. The file contains either at least one `@media` rule targeting narrow viewports (`max-width` <= 640px) OR uses fluid units (`clamp(`, `vw`, `vmin`) on the headline font size. The verifier greps for either pattern.
- AC20: Total file size strictly under 12288 bytes (12 KB). `stat -f%z` reports < 12288.
- AC21: Visible body word count under 60 words. The verifier strips tags, comments, and the `<style>` block, then counts whitespace-separated tokens; result must be < 60.
- AC22: No collateral damage. Only `design/landing.html` (and the `design/` directory if it did not exist) is created. No other file in the repo is added or modified by this task. `git status --porcelain` (run from repo root) lists at most `design/` and `design/landing.html` as new entries, plus the `.agent/tasks/landing-mockup-steve-ive/` artifacts that the workflow itself writes.

## Constraints

- No JavaScript anywhere in the file. Zero `<script>` tags.
- No external network requests. No `<script src=...>`, no `<link href=...>` to any URL, no `@import url(...)`, no `fonts.googleapis.com`, no `<img src="https://...">`.
- All CSS lives inline in a single `<style>` block in `<head>`. No `style="..."` attributes on elements.
- The only file created in the repo by this task is `/Users/krutovoy/Projects/hosting-platform/design/landing.html` (plus the `design/` directory).
- Do not edit `app/`, `docs/`, `CLAUDE.md`, `AGENTS.md`, any other task's artifacts, or any existing service stubs.
- No em dashes anywhere in the spec, the HTML file, or any commit message produced by this task.
- Any deletes use `trash`, never `rm` or `rm -rf`.
- The page must hold up under steve-ive line-by-line review: a fresh designer reading SKILL.md and looking at the page must not find a violation.
- Crypto stays hidden. The user must not see chain names, token symbols, gas, signatures, or wallet addresses on this page.

## Non-goals

- Not wired into Inertia, React, or Laravel. No `.tsx`, no `.blade.php`, no Vite config touched.
- No multi-page navigation. No header nav, no footer link list.
- No actual form on this page. The form lives behind the CTA on a future screen and is a separate task.
- No animation libraries. No Lottie, no Framer Motion, no GSAP.
- No mockup of the Create Agent wizard, the My Agents list, the payment screen, or the Telegram screen. Each is a separate task.
- No accessibility audit beyond basic semantic HTML. Full a11y is a separate task.
- No backend wiring of any kind. No fetch calls, no form actions, no `href` to a real route.
- No copy that explains the product before interaction. The result sells itself behind the CTA.

## Verification plan

The verifier runs from `/Users/krutovoy/Projects/hosting-platform`. Each AC maps to one or more shell checks.

- AC1: `test -f design/landing.html && test -d design`
- AC2: `head -1 design/landing.html | grep -qi '^<!doctype html>'` and `grep -qE '<html[^>]+lang=' design/landing.html`
- AC3: `grep -qiE '<meta[^>]+charset="?utf-?8' design/landing.html` and `grep -qE '<meta[^>]+name="viewport"[^>]+content="width=device-width, initial-scale=1(\.0)?"' design/landing.html`
- AC4: `[ "$(grep -c '<title>' design/landing.html)" = "1" ]` and verifier extracts title text, splits on whitespace, asserts word count <= 5.
- AC5: verifier counts `<button` and `<a ` occurrences. Exactly one element carries a class matching the CTA selector defined in `:root` usage (or is the only `<button>`). Verifier extracts that element's inner text, asserts word count <= 3 and matches a verb-phrase regex (`^(Post|Deploy|Spawn|Launch|Ship|Run|Start) [A-Za-z]+$`).
- AC6: `! grep -qi 'learn more' design/landing.html` and `[ "$(grep -c '<button' design/landing.html)" -le "1" ]`
- AC7: `! grep -q '<script' design/landing.html` and `! grep -q 'src=' design/landing.html` and `! grep -qE '<link[^>]+href="(https?:|//)' design/landing.html` and `! grep -q '@import' design/landing.html` and `! grep -qi 'fonts.googleapis.com' design/landing.html`
- AC8: `[ "$(grep -c '<style' design/landing.html)" = "1" ]` and `! grep -q ' style=' design/landing.html`
- AC9: for each banned word in the list, `! grep -qi 'WORD' design/landing.html`
- AC10: `! grep -q $'\u2014' design/landing.html` and `! grep -q $'\u2013' design/landing.html`
- AC11: verifier strips `<!DOCTYPE`, comments, and attribute values, then asserts `!` count == 0.
- AC12: for each banned crypto term, `! grep -qi 'TERM' design/landing.html`. For `wallet`: `[ "$(grep -ci 'wallet' design/landing.html)" -le "1" ]`. Default expected: zero.
- AC13: `grep -qi 'agent' design/landing.html`
- AC14: `grep -q ':root' design/landing.html` and verifier asserts that the `:root { ... }` block contains at least three `--` declarations including a background, a foreground, and an accent.
- AC15: verifier extracts the `:root` block, then greps the rest of the file for `#[0-9A-Fa-f]{3,6}\b` and `rgba?\(` and `hsla?\(`; result must be empty.
- AC16: verifier extracts the body font-family stack, checks the first family is not in the banned list, checks fallbacks count >= 3.
- AC17: `grep -q 'prefers-color-scheme' design/landing.html` OR the file contains a `<style>`-leading comment `/* theme: dark */` or `/* theme: light */`.
- AC18: `[ "$(grep -c -- '--accent' design/landing.html)" -ge "1" ]` and verifier asserts every `var(--accent...)` use sits on a CTA selector or its hover/focus state.
- AC19: `grep -qE '@media[^{]*max-width' design/landing.html` OR `grep -qE 'clamp\(|[0-9.]+vw' design/landing.html`
- AC20: `[ "$(stat -f%z design/landing.html)" -lt "12288" ]`
- AC21: verifier strips `<style>`, comments, and tags; counts whitespace-separated tokens; asserts < 60.
- AC22: `git status --porcelain` (run with `-uall` disabled per CLAUDE.md, so plain `git status --porcelain`) shows only `design/landing.html`, `design/`, and the `.agent/tasks/landing-mockup-steve-ive/` workflow artifacts.

Build: none (static HTML).
Unit tests: none (UI design carve-out from TDD).
Integration tests: none.
Lint: none required; the spec's grep-based checks act as the lint.
Manual checks: open `design/landing.html` in a browser at 320px, 768px, and 1920px viewport widths. Confirm exactly one CTA is visible and dominant. Confirm zero marketing copy. Confirm typography carries the hierarchy. Confirm the page passes the 5-question steve-ive decision framework.
