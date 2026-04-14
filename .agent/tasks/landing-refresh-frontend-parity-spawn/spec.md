# Task Spec: landing-refresh-frontend-parity-spawn

## Metadata
- Task ID: landing-refresh-frontend-parity-spawn
- Created: 2026-04-14
- Frozen: 2026-04-14
- Repo root: /Users/Tomas/Desktop/Coding/hosting-platform

## Original task statement
Rebuild `/` as a near-clone of `frontend-example` section rhythm and interaction style using Blade + vanilla CSS/JS, with Spawn-first product copy and existing project flow.

## Acceptance criteria
- AC1: `resources/views/landing.blade.php` adopts the same 5-part section rhythm as the example: corner nav, kinetic hero, manifesto/reveal section, expandable pillars section, footer CTA.
- AC2: Primary CTA for logged-out users is `Sign in with Google` to `/auth/google`; signed-in users see `Post agent` to `/wizard`.
- AC3: Copy is Spawn-branded and project-specific (no AgentPad/X Layer/Mandate-primary hero copy).
- AC4: Imported example visual assets (`hero.png`, `os.png`, `clanker.png`, `traditional.png`) are integrated as the main art direction.
- AC5: Core interactions work without frontend bundler migration: scroll progress indicator, section reveal animations, accordion expand/collapse, CTA hover/magnetic feel, responsive navigation behavior.
- AC6: Reduced-motion and keyboard accessibility are preserved (accordion operable via keyboard, no motion traps).
- AC7: No API/route/schema/controller changes.
- AC8: Existing deploy/webhook feature tests remain green.

## Constraints
- Keep implementation in server-rendered Blade and inline CSS/JS.
- Do not modify backend routes/controllers/schemas.
- Keep unrelated existing local edits untouched.

## Verification plan
- `php -l resources/views/landing.blade.php`
- `vendor/bin/phpunit tests/Feature/Http/DeployControllerTest.php tests/Feature/Http/OnChainOSWebhookControllerTest.php tests/Feature/Http/TelegramWebhookControllerTest.php`
- Manual/static checks for CTA routes, section IDs, interaction hooks, reduced-motion handling.
