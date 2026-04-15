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
  color: var(--faint);
}

header a.mark {
  font-family: var(--display);
  font-style: italic;
  font-weight: 500;
  font-size: 0.95rem;
  letter-spacing: -0.01em;
  color: var(--muted);
  text-decoration: none;
}

header .who em {
  font-family: var(--display);
  font-style: italic;
  font-weight: 500;
  font-size: 0.85rem;
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

/* waitlist gate */
form, .waitlist {
  transition: opacity 260ms cubic-bezier(0.2, 0.6, 0.1, 1),
              visibility 260ms cubic-bezier(0.2, 0.6, 0.1, 1),
              max-height 320ms cubic-bezier(0.2, 0.6, 0.1, 1);
}

form.is-gone {
  opacity: 0;
  visibility: hidden;
  max-height: 0;
  overflow: hidden;
  pointer-events: none;
}

.waitlist {
  opacity: 0;
  visibility: hidden;
  max-height: 0;
  overflow: hidden;
  pointer-events: none;
  outline: none;
  position: relative;
  padding-left: 1.25rem;
  border-left: 1px solid var(--rule);
}

.waitlist.is-here {
  opacity: 1;
  visibility: visible;
  max-height: 80vh;
  pointer-events: auto;
}

.waitlist::before {
  content: "";
  position: absolute;
  left: -4px;
  top: 0.55rem;
  width: 7px;
  height: 7px;
  background: var(--accent);
  border-radius: 50%;
}

.waitlist .meta {
  margin: 0 0 1.5rem;
  font-family: var(--mono);
  font-size: 0.72rem;
  letter-spacing: 0.18em;
  color: var(--faint);
}

.waitlist .display {
  font-family: var(--display);
  font-weight: 700;
  font-size: clamp(2.25rem, 5.8vw, 3.75rem);
  line-height: 0.96;
  letter-spacing: -0.035em;
  margin: 0 0 1.75rem;
  font-variation-settings: "opsz" 144;
  color: var(--fg);
}

.waitlist .display-sub {
  display: block;
  font-style: italic;
  font-weight: 400;
  color: var(--muted);
}

.waitlist .body {
  margin: 0 0 2rem;
  max-width: 36rem;
  color: var(--muted);
  font-size: 0.92rem;
  line-height: 1.65;
}

.waitlist .actions {
  display: flex;
  align-items: center;
  gap: 1.75rem;
  flex-wrap: wrap;
}

.waitlist .ghost {
  font-family: var(--mono);
  font-size: 0.78rem;
  letter-spacing: 0.02em;
  color: var(--muted);
  text-decoration: none;
  border-bottom: 1px solid var(--rule);
  padding-bottom: 2px;
}

.waitlist .ghost:hover,
.waitlist .ghost:focus,
.waitlist .ghost:focus-visible {
  color: var(--fg);
  border-bottom-color: var(--fg);
  outline: none;
}

.waitlist .email-step {
  opacity: 0;
  visibility: hidden;
  max-height: 0;
  overflow: hidden;
  margin-top: 0;
  transition: opacity 240ms cubic-bezier(0.2, 0.6, 0.1, 1),
              visibility 240ms cubic-bezier(0.2, 0.6, 0.1, 1),
              max-height 300ms cubic-bezier(0.2, 0.6, 0.1, 1),
              margin-top 300ms cubic-bezier(0.2, 0.6, 0.1, 1);
}

.waitlist .email-step.is-here {
  opacity: 1;
  visibility: visible;
  max-height: 12rem;
  margin-top: 1.75rem;
}

.waitlist .email-step label {
  color: var(--faint);
}

.waitlist .email-row {
  display: flex;
  gap: 0.75rem;
  align-items: stretch;
  max-width: 28rem;
}

.waitlist .email-row input[type="email"] {
  flex: 1;
  font: inherit;
  font-family: var(--mono);
  color: var(--fg);
  background: var(--tint);
  border: 1px solid var(--rule);
  border-radius: var(--radius);
  padding: 0.9rem 1rem;
  outline: none;
  transition: border-color 160ms ease;
}

.waitlist .email-row input[type="email"]::placeholder { color: var(--faint); }
.waitlist .email-row input[type="email"]:focus { border-color: var(--fg); }

.waitlist .email-row button {
  font-family: var(--mono);
  font-weight: 700;
  font-size: 0.82rem;
  letter-spacing: 0.02em;
  color: var(--bg);
  background: var(--accent);
  border: 0;
  border-radius: var(--radius);
  padding: 0 1.2rem;
  cursor: pointer;
}

.waitlist .email-row button:hover,
.waitlist .email-row button:focus,
.waitlist .email-row button:focus-visible {
  outline: 2px solid var(--accent);
  outline-offset: 4px;
}

.waitlist .confirm {
  margin: 1.75rem 0 0;
  font-family: var(--mono);
  font-size: 0.9rem;
  color: var(--fg);
  opacity: 0;
  max-height: 0;
  overflow: hidden;
  transition: opacity 240ms cubic-bezier(0.2, 0.6, 0.1, 1),
              max-height 280ms cubic-bezier(0.2, 0.6, 0.1, 1),
              margin-top 280ms cubic-bezier(0.2, 0.6, 0.1, 1);
}

.waitlist .confirm.is-here {
  opacity: 1;
  max-height: 6rem;
}

.waitlist .confirm .mark-dot {
  display: inline-block;
  width: 6px;
  height: 6px;
  background: var(--accent);
  border-radius: 50%;
  margin-right: 0.6rem;
  vertical-align: middle;
}

footer {
  padding: 1.5rem clamp(1.25rem, 4vw, 3rem);
  font-family: var(--mono);
  font-size: 0.7rem;
  letter-spacing: 0.06em;
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
  <div class="who">SIGNED IN AS <em>{{ session('user.email', 'guest') }}</em></div>
</header>

<main>
  <h1>New agent.<span class="turn">Three fields.</span></h1>

  <form id="agent-form" method="post" action="/api/deploys">
    @csrf
    <div>
      <label for="name">NAME</label>
      <input id="name" name="agent_name" type="text" required placeholder="name your agent">
    </div>
    {{--
    <div>
      <label for="personality">PERSONALITY</label>
      <textarea id="personality" name="personality" required placeholder="A laconic agent that ships code."></textarea>
      <p class="hint">Two or three sentences. How it talks. How it decides.</p>
    </div>
    --}}
    <div>
      <label for="bot_token">TELEGRAM TOKEN</label>
      <input id="bot_token" name="telegram_bot_token" type="text" required placeholder="123456:abcdef">
      <p class="hint">
        Learn how to create a <a href="https://help.superchat.com/en/articles/14901-how-do-i-get-the-telegram-token-or-bot-id" target="_blank" rel="noopener noreferrer">Telegram Bot</a>
      </p>
    </div>
    <div>
      <label for="allowlist">ALLOWED TELEGRAM IDS</label>
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

  <section id="waitlist" class="waitlist" aria-live="polite" aria-hidden="true" tabindex="-1">
    <p class="meta">STATUS  //  PRIVATE ACCESS</p>
    <h2 class="display" id="waitlist-heading" tabindex="-1">
      We are not open yet.<span class="display-sub">Waitlist is the way in.</span>
    </h2>
    <p class="body">
      Spawn is running for a small group of early developers while we harden the OpenClaw runtime. We open to the waitlist first, in small batches. No public launch until the runtime holds.
    </p>
    <div class="actions">
      <button class="cta" id="join-waitlist" type="button">
        Join the waitlist
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
      </button>
      <a class="ghost" href="/">BACK TO OVERVIEW</a>
    </div>

    <form id="waitlist-email-step" class="email-step" aria-hidden="true" novalidate>
      <label for="waitlist-email">YOUR EMAIL</label>
      <div class="email-row">
        <input id="waitlist-email" name="waitlist_email" type="email" required placeholder="you@domain.com" autocomplete="email">
        <button type="submit">SEND</button>
      </div>
    </form>

    <p class="confirm" id="waitlist-confirm" aria-hidden="true">
      <span class="mark-dot" aria-hidden="true"></span>You are on the list. We will reach out before the next batch.
    </p>
  </section>
</main>

<footer>
  <div class="tag">platform.thespawn.io</div>
  <div>STEP 2 OF 7</div>
</footer>

<script>
  (function () {
    var form = document.getElementById('agent-form');
    var panel = document.getElementById('waitlist');
    var heading = document.getElementById('waitlist-heading');
    var joinBtn = document.getElementById('join-waitlist');
    var emailStep = document.getElementById('waitlist-email-step');
    var emailInput = document.getElementById('waitlist-email');
    var confirm = document.getElementById('waitlist-confirm');

    if (!form || !panel) { return; }

    form.addEventListener('submit', function (event) {
      event.preventDefault();
      form.classList.add('is-gone');
      panel.classList.add('is-here');
      panel.setAttribute('aria-hidden', 'false');
      window.setTimeout(function () {
        if (heading && typeof heading.focus === 'function') {
          heading.focus({ preventScroll: false });
        }
      }, 280);
    });

    if (joinBtn && emailStep) {
      joinBtn.addEventListener('click', function () {
        emailStep.classList.add('is-here');
        emailStep.setAttribute('aria-hidden', 'false');
        joinBtn.disabled = true;
        joinBtn.style.opacity = '0.45';
        joinBtn.style.pointerEvents = 'none';
        window.setTimeout(function () {
          if (emailInput && typeof emailInput.focus === 'function') {
            emailInput.focus();
          }
        }, 200);
      });
    }

    if (emailStep) {
      emailStep.addEventListener('submit', function (event) {
        event.preventDefault();
        var value = emailInput && emailInput.value ? emailInput.value.trim() : '';
        if (!value || value.indexOf('@') < 1) {
          if (emailInput) { emailInput.focus(); }
          return;
        }
        emailStep.classList.remove('is-here');
        emailStep.setAttribute('aria-hidden', 'true');
        if (confirm) {
          confirm.classList.add('is-here');
          confirm.setAttribute('aria-hidden', 'false');
        }
      });
    }
  })();
</script>

</body>
</html>
