# Task Spec: landing-critical-improvement-pass

## Metadata
- Task ID: landing-critical-improvement-pass
- Created: 2026-04-14
- Frozen: 2026-04-14
- Repo root: /Users/Tomas/Desktop/Coding/hosting-platform

## Original task statement
Kill local server, then critically improve the landing page quality with stronger UX/copy/visual hierarchy and better interaction robustness while preserving existing routes/backend behavior.

## Acceptance criteria
- AC1: Landing keeps high-end visual direction while improving information architecture (hero -> proof/trust -> workflow reveal -> expandable technical pillars -> launch CTA).
- AC2: Logged-out primary CTA remains `Sign in with Google` to `/auth/google`; logged-in primary CTA remains `Post agent` to `/wizard`.
- AC3: Copy is Spawn-first and concrete about real flow/integrations (KiloClaw, OnChainOS, Telegram, Google auth).
- AC4: Imported example assets (`hero.png`, `os.png`, `clanker.png`, `traditional.png`) remain actively used in the page.
- AC5: Interaction quality is improved: stable scroll progress, reveal animation, accessible accordion with keyboard controls, pointer effects disabled on touch/reduced-motion, responsive nav behavior.
- AC6: Reduced-motion behavior remains supported and does not trap or require animation.
- AC7: No API/route/schema/controller changes.
- AC8: Deploy/webhook feature tests remain green.

## Constraints
- Blade + inline CSS/JS only (no frontend build migration).
- Keep unrelated local changes untouched.

## Verification plan
- `php -l resources/views/landing.blade.php`
- `vendor/bin/phpunit tests/Feature/Http/DeployControllerTest.php tests/Feature/Http/OnChainOSWebhookControllerTest.php tests/Feature/Http/TelegramWebhookControllerTest.php`
- Static checks for CTA links, section structure, and interaction hooks.
