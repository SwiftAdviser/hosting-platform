<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>One click. Agent hosted.</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,300..900&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
<style>
/* theme: dark · editorial-terminal */
:root {
  --bg: #0a0a0b;
  --fg: #f5f5f4;
  --muted: rgba(245, 245, 244, 0.46);
  --faint: rgba(245, 245, 244, 0.24);
  --rule: rgba(245, 245, 244, 0.08);
  --tint: rgba(245, 245, 244, 0.04);
  --accent: #c6ff3d;
  --radius: 2px;
  --max: 48rem;
  --mono: "JetBrains Mono", ui-monospace, monospace;
  --display: "Fraunces", "Times New Roman", Georgia, serif;
}

* { box-sizing: border-box; }

html, body {
  margin: 0;
  padding: 0;
  background: var(--bg);
  color: var(--fg);
}

body {
  font-family: var(--mono);
  font-size: 15px;
  line-height: 1.55;
  min-height: 100vh;
  display: grid;
  grid-template-rows: auto 1fr auto;
  background:
    radial-gradient(1100px 600px at 50% -10%, rgba(198, 255, 61, 0.035), transparent 65%),
    radial-gradient(800px 400px at 80% 90%, rgba(245, 245, 244, 0.018), transparent 70%),
    var(--bg);
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  position: relative;
  overflow-x: hidden;
}

/* subtle grain via data-uri svg */
body::before {
  content: "";
  position: fixed;
  inset: 0;
  pointer-events: none;
  opacity: 0.035;
  z-index: 1;
  background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
}

header, footer, main {
  position: relative;
  z-index: 2;
}

header {
  padding: 1.5rem clamp(1.25rem, 4vw, 3rem);
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.72rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--faint);
}

header .mark {
  font-family: var(--display);
  font-style: italic;
  font-weight: 500;
  font-size: 0.95rem;
  text-transform: none;
  letter-spacing: -0.01em;
  color: var(--muted);
}

header .build {
  opacity: 0.7;
}

main {
  width: 100%;
  max-width: var(--max);
  margin: 0 auto;
  padding: clamp(2rem, 6vw, 5rem) clamp(1.25rem, 4vw, 3rem);
  display: flex;
  flex-direction: column;
  justify-content: center;
}

h1 {
  font-family: var(--display);
  font-weight: 700;
  font-size: clamp(3rem, 8.5vw, 6.5rem);
  line-height: 0.92;
  letter-spacing: -0.045em;
  margin: 0 0 1.75rem;
  font-variation-settings: "opsz" 144;
  opacity: 0;
  transform: translateY(14px);
  animation: rise 900ms cubic-bezier(0.16, 1, 0.3, 1) 120ms forwards;
}

h1 .turn {
  display: block;
  font-style: italic;
  font-weight: 400;
  color: var(--muted);
  letter-spacing: -0.035em;
}

hr {
  border: 0;
  border-top: 1px solid var(--rule);
  max-width: 5rem;
  margin: 2.25rem 0;
  margin-left: 0;
  opacity: 0;
  animation: fade 800ms ease 420ms forwards;
}

p.lede {
  font-family: var(--mono);
  font-size: clamp(0.95rem, 1.3vw, 1.05rem);
  line-height: 1.6;
  color: var(--muted);
  margin: 0 0 3rem;
  max-width: 30rem;
  opacity: 0;
  transform: translateY(6px);
  animation: rise 700ms ease 520ms forwards;
}

.action {
  opacity: 0;
  transform: translateY(8px);
  animation: rise 700ms cubic-bezier(0.16, 1, 0.3, 1) 700ms forwards;
  display: flex;
  align-items: center;
  gap: 1.25rem;
  flex-wrap: wrap;
}

a.cta {
  display: inline-flex;
  align-items: center;
  gap: 0.65rem;
  padding: 1.05rem 1.6rem;
  background: var(--accent);
  color: var(--bg);
  text-decoration: none;
  font-family: var(--mono);
  font-weight: 700;
  letter-spacing: 0.005em;
  font-size: 0.95rem;
  border-radius: var(--radius);
  transition: transform 200ms ease, box-shadow 200ms ease;
  will-change: transform;
}

a.cta svg {
  width: 1rem;
  height: 1rem;
  display: block;
}

a.cta:hover,
a.cta:focus,
a.cta:focus-visible {
  outline: 2px solid var(--accent);
  outline-offset: 6px;
  box-shadow: 0 0 0 1px var(--accent);
}

.user {
  font-size: 0.72rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--faint);
}

.user em {
  font-family: var(--display);
  font-style: italic;
  font-weight: 500;
  font-size: 0.82rem;
  letter-spacing: -0.01em;
  color: var(--muted);
  text-transform: none;
}

footer {
  padding: 1.5rem clamp(1.25rem, 4vw, 3rem);
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  gap: 1rem;
  font-family: var(--mono);
  font-size: 0.7rem;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: var(--faint);
  opacity: 0;
  animation: fade 800ms ease 900ms forwards;
}

footer .tag {
  font-family: var(--display);
  font-style: italic;
  font-weight: 400;
  font-size: 0.82rem;
  text-transform: none;
  letter-spacing: -0.01em;
  color: var(--muted);
}

.dot {
  display: inline-block;
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--accent);
  margin-right: 0.4rem;
  vertical-align: middle;
  box-shadow: 0 0 8px rgba(198, 255, 61, 0.55);
  animation: pulse 2.4s ease-in-out infinite;
}

@keyframes rise {
  to { opacity: 1; transform: none; }
}

@keyframes fade {
  to { opacity: 1; }
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.35; }
}

@media (max-width: 640px) {
  header { padding: 1rem 1.25rem; }
  h1 { letter-spacing: -0.03em; }
  p.lede { margin-bottom: 2.25rem; }
  footer { flex-direction: column; align-items: flex-start; gap: 0.5rem; padding: 1rem 1.25rem; }
}

@media (prefers-reduced-motion: reduce) {
  h1, hr, p.lede, .action, footer { animation: none; opacity: 1; transform: none; }
  .dot { animation: none; }
}
</style>
</head>
<body>

<header>
  <div class="mark">spawn</div>
  <div class="build"><span class="dot"></span>live · main@{{ substr(config('app.build_sha', env('BUILD_SHA', 'a835343')), 0, 7) }}</div>
</header>

<main>
  <h1>One click.<span class="turn">Agent hosted.</span></h1>
  <hr>
  <p class="lede">Paste your agent. We run it on KiloClaw. You keep writing code.</p>
  <div class="action">
    @if(session('user'))
      <a class="cta" href="/wizard">
        Post agent
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
      </a>
      <div class="user">signed in as <em>{{ session('user.email') }}</em></div>
    @else
      <a class="cta" href="/auth/google">
        Sign in with Google
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
      </a>
    @endif
  </div>
</main>

<footer>
  <div class="tag">platform.thespawn.io</div>
  <div>hackathon v0.1</div>
</footer>

</body>
</html>
