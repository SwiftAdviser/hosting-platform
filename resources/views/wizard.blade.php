<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>New agent.</title>
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

html, body { margin: 0; padding: 0; background: var(--bg); color: var(--fg); }

body {
  font-family: var(--mono);
  font-size: 15px;
  line-height: 1.55;
  min-height: 100vh;
  display: grid;
  grid-template-rows: auto 1fr auto;
  background:
    radial-gradient(1000px 520px at 50% -8%, rgba(198, 255, 61, 0.035), transparent 65%),
    var(--bg);
  -webkit-font-smoothing: antialiased;
  position: relative;
}

body::before {
  content: "";
  position: fixed;
  inset: 0;
  pointer-events: none;
  opacity: 0.035;
  z-index: 1;
  background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
}

header, main, footer { position: relative; z-index: 2; }

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

header a.mark {
  font-family: var(--display);
  font-style: italic;
  font-weight: 500;
  font-size: 0.95rem;
  text-transform: none;
  letter-spacing: -0.01em;
  color: var(--muted);
  text-decoration: none;
}

header .who em {
  font-family: var(--display);
  font-style: italic;
  font-weight: 500;
  font-size: 0.85rem;
  text-transform: none;
  letter-spacing: -0.01em;
  color: var(--muted);
}

main {
  width: 100%;
  max-width: var(--max);
  margin: 0 auto;
  padding: clamp(2rem, 6vw, 4rem) clamp(1.25rem, 4vw, 3rem);
}

h1 {
  font-family: var(--display);
  font-weight: 700;
  font-size: clamp(2.5rem, 6.5vw, 4.5rem);
  line-height: 0.94;
  letter-spacing: -0.04em;
  margin: 0 0 2.5rem;
  font-variation-settings: "opsz" 144;
}

h1 .turn { display: block; font-style: italic; font-weight: 400; color: var(--muted); }

form { display: grid; gap: 1.75rem; }

label {
  display: block;
  font-family: var(--mono);
  font-size: 0.72rem;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  /* Keep field labels brighter for stronger readability. */
  color: var(--fg);
  margin: 0 0 0.55rem;
}

input[type="text"], textarea {
  width: 100%;
  font: inherit;
  font-family: var(--mono);
  color: var(--fg);
  background: var(--tint);
  border: 1px solid var(--rule);
  border-radius: var(--radius);
  padding: 0.95rem 1rem;
  outline: none;
  transition: border-color 160ms ease;
}

input[type="text"]::placeholder, textarea::placeholder { color: var(--faint); }

input[type="text"]:focus, textarea:focus { border-color: var(--fg); }

textarea { min-height: 8rem; resize: vertical; }

.hint {
  margin: 0.55rem 0 0;
  font-size: 0.78rem;
  /* Keep helper copy softer than labels to avoid visual clash. */
  color: var(--muted);
}

.hint a {
  color: var(--fg);
  text-decoration: underline;
}

button.cta {
  margin-top: 1.25rem;
  display: inline-flex;
  align-items: center;
  gap: 0.65rem;
  padding: 1.05rem 1.6rem;
  font-family: var(--mono);
  font-weight: 700;
  letter-spacing: 0.005em;
  color: var(--bg);
  background: var(--accent);
  border: 0;
  border-radius: var(--radius);
  cursor: pointer;
}

button.cta svg { width: 1rem; height: 1rem; }

button.cta:hover, button.cta:focus, button.cta:focus-visible {
  outline: 2px solid var(--accent);
  outline-offset: 6px;
}

footer {
  padding: 1.5rem clamp(1.25rem, 4vw, 3rem);
  font-family: var(--mono);
  font-size: 0.7rem;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: var(--faint);
  display: flex;
  justify-content: space-between;
  gap: 1rem;
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

@media (max-width: 640px) {
  header { padding: 1rem 1.25rem; }
  h1 { margin-bottom: 2rem; }
  footer { flex-direction: column; align-items: flex-start; gap: 0.5rem; padding: 1rem 1.25rem; }
}
</style>
</head>
<body>

<header>
  <a href="/" class="mark">spawn</a>
  <div class="who">signed in as <em>{{ session('user.email', 'guest') }}</em></div>
</header>

<main>
  <h1>New agent.<span class="turn">Three fields.</span></h1>

  <form method="post" action="/api/deploys">
    @csrf
    <div>
      <label for="name">Name</label>
      <input id="name" name="agent_name" type="text" required placeholder="name your agent">
    </div>
    {{--
    <div>
      <label for="personality">Personality</label>
      <textarea id="personality" name="personality" required placeholder="A laconic agent that ships code."></textarea>
      <p class="hint">Two or three sentences. How it talks. How it decides.</p>
    </div>
    --}}
    <div>
      <label for="bot_token">Telegram Token</label>
      <input id="bot_token" name="telegram_bot_token" type="text" required placeholder="123456:abcdef">
      <p class="hint">
        Learn how to create a <a href="https://help.superchat.com/en/articles/14901-how-do-i-get-the-telegram-token-or-bot-id" target="_blank" rel="noopener noreferrer">Telegram Bot</a>
      </p>
    </div>
    <div>
      <label for="allowlist">Allowed Telegram IDs</label>
      <input id="allowlist" name="allowlist" type="text" placeholder="858032733, 858032711">
      <p class="hint">
        Get your Telegram id <a href="https://t.me/userinfobot" target="_blank" rel="noopener noreferrer">here</a>.
      </p>
    </div>
    <input type="hidden" name="amount_usd" value="10">
    <button class="cta" type="submit">
      Post agent
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
    </button>
  </form>
</main>

<footer>
  <div class="tag">platform.thespawn.io</div>
  <div>step 2 of 7</div>
</footer>

</body>
</html>
