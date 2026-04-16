<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Spawn Platform - Agent hosting that ships</title>
<meta name="description" content="Deploy your agent from Spawn with Google sign-in, OnChainOS payment flow, OpenClaw runtime provisioning, and Telegram delivery.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
<link href="https://api.fontshare.com/v2/css?f[]=clash-display@400,500,600,700&display=swap" rel="stylesheet">
<style>
:root {
  --bg: #05070d;
  --bg-soft: #0b1222;
  --panel: rgba(10, 16, 29, 0.9);
  --panel-elevated: rgba(11, 19, 35, 0.95);
  --text-primary: #e8eefb;
  --text-secondary: #a4b3d0;
  --text-muted: #7889a8;
  --line: rgba(170, 186, 216, 0.2);
  --line-strong: rgba(170, 186, 216, 0.34);
  --cyan: #39d8e7;
  --cyan-dim: rgba(57, 216, 231, 0.16);
  --orange: #ff9a5f;
  --success: #4fe0a6;
  --danger: #ff6f7e;
  --radius-lg: 24px;
  --radius-md: 16px;
  --radius-sm: 12px;
  --container: 1200px;
  --nav-height: 78px;
}

* {
  box-sizing: border-box;
}

html,
body {
  margin: 0;
  padding: 0;
  background: var(--bg);
  color: var(--text-primary);
}

body {
  font-family: "Manrope", system-ui, -apple-system, sans-serif;
  min-height: 100vh;
  overflow-x: hidden;
  background:
    radial-gradient(900px 500px at 20% -10%, rgba(57, 216, 231, 0.13), transparent 66%),
    radial-gradient(1000px 600px at 100% 0%, rgba(255, 154, 95, 0.09), transparent 68%),
    linear-gradient(180deg, #05070d 0%, #060c17 58%, #05070d 100%);
}

body::before {
  content: "";
  position: fixed;
  inset: 0;
  pointer-events: none;
  z-index: 1;
  opacity: 0.05;
  background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='grain'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.72' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23grain)'/%3E%3C/svg%3E");
}

a {
  color: inherit;
  text-decoration: none;
}

.page {
  position: relative;
  z-index: 2;
}

.topbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 40;
  backdrop-filter: blur(10px);
  background: rgba(5, 10, 20, 0.72);
  border-bottom: 1px solid rgba(170, 186, 216, 0.16);
}

.topbar-shell {
  width: min(var(--container), calc(100% - 32px));
  margin: 0 auto;
  min-height: var(--nav-height);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 18px;
}

.brand {
  font-family: "Clash Display", "Manrope", sans-serif;
  font-size: clamp(26px, 3vw, 34px);
  letter-spacing: -0.03em;
  font-weight: 700;
  line-height: 1;
  text-transform: lowercase;
}

.brand span {
  color: var(--cyan);
}

.nav-links {
  display: flex;
  align-items: center;
  gap: 22px;
}

.nav-link {
  font-family: "JetBrains Mono", ui-monospace, monospace;
  font-size: 11px;
  letter-spacing: 0.09em;
  text-transform: uppercase;
  color: var(--text-muted);
  transition: color 180ms ease;
}

.nav-link:hover,
.nav-link:focus-visible {
  color: var(--text-primary);
}

.mobile-nav-toggle {
  display: none;
  align-items: center;
  justify-content: center;
  border: 1px solid var(--line);
  border-radius: 999px;
  min-height: 40px;
  padding: 0 14px;
  background: rgba(8, 15, 29, 0.88);
  color: var(--text-primary);
  font-family: "JetBrains Mono", ui-monospace, monospace;
  font-size: 11px;
  letter-spacing: 0.1em;
  text-transform: uppercase;
}

.nav-cta {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 999px;
  min-height: 44px;
  padding: 0 18px;
  border: 1px solid rgba(57, 216, 231, 0.42);
  background: rgba(7, 15, 30, 0.74);
  font-family: "JetBrains Mono", ui-monospace, monospace;
  font-size: 11px;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--text-primary);
  transition: border-color 180ms ease, background 180ms ease;
}

.nav-cta:hover,
.nav-cta:focus-visible {
  border-color: var(--cyan);
  background: rgba(57, 216, 231, 0.16);
}

.progress-track {
  position: absolute;
  left: 0;
  right: 0;
  bottom: -1px;
  height: 1px;
  background: rgba(170, 186, 216, 0.09);
}

.progress-bar {
  height: 100%;
  width: 0%;
  background: linear-gradient(90deg, var(--cyan), #7be9f4);
  box-shadow: 0 0 12px rgba(57, 216, 231, 0.6);
}

main {
  width: 100%;
}

.section {
  padding-inline: 16px;
}

.shell {
  width: min(var(--container), 100%);
  margin: 0 auto;
}

.kicker {
  margin: 0;
  font-family: "JetBrains Mono", ui-monospace, monospace;
  font-size: 11px;
  letter-spacing: 0.13em;
  text-transform: uppercase;
  color: var(--cyan);
}

.hero {
  padding-top: calc(var(--nav-height) + 42px);
  padding-bottom: 46px;
}

.hero-grid {
  border: 1px solid var(--line);
  border-radius: 30px;
  overflow: hidden;
  background:
    linear-gradient(120deg, rgba(57, 216, 231, 0.1), transparent 40%),
    linear-gradient(210deg, rgba(255, 154, 95, 0.08), transparent 46%),
    var(--panel-elevated);
  display: grid;
  grid-template-columns: 1.12fr 0.88fr;
}

.hero-copy {
  padding: clamp(24px, 5vw, 64px);
  position: relative;
}

.hero-title {
  margin: 16px 0 0;
  font-family: "Clash Display", "Manrope", sans-serif;
  font-size: clamp(56px, 11vw, 132px);
  line-height: 0.88;
  letter-spacing: -0.04em;
}

.hero-title .soft {
  display: block;
  color: rgba(232, 238, 251, 0.55);
  font-weight: 500;
}

.hero-title .strong {
  display: block;
  color: var(--text-primary);
  font-weight: 700;
}

.hero-description {
  margin: 22px 0 0;
  max-width: 640px;
  color: var(--text-secondary);
  font-size: clamp(16px, 1.7vw, 21px);
  line-height: 1.6;
}

.hero-actions {
  margin-top: 28px;
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.button-primary,
.button-secondary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 52px;
  border-radius: 999px;
  padding: 0 24px;
  font-family: "JetBrains Mono", ui-monospace, monospace;
  font-size: 12px;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  border: 1px solid transparent;
}

.button-primary {
  color: #061321;
  background: var(--text-primary);
  border-color: rgba(255, 255, 255, 0.88);
}

.button-primary:hover,
.button-primary:focus-visible {
  background: #fbfeff;
}

.button-secondary {
  color: var(--text-primary);
  background: rgba(8, 15, 29, 0.64);
  border-color: var(--line);
}

.button-secondary:hover,
.button-secondary:focus-visible {
  border-color: var(--cyan);
  color: #e9ffff;
}

.quick-checklist {
  margin: 22px 0 0;
  padding: 0;
  list-style: none;
  display: grid;
  gap: 10px;
}

.quick-checklist li {
  display: flex;
  align-items: center;
  gap: 10px;
  color: var(--text-secondary);
  font-size: 14px;
}

.quick-checklist li::before {
  content: "";
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: var(--cyan);
  box-shadow: 0 0 12px rgba(57, 216, 231, 0.5);
}

.hero-side {
  border-left: 1px solid var(--line);
  padding: clamp(20px, 4vw, 34px);
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.runtime-visual {
  margin: 0;
  min-height: 240px;
  border-radius: var(--radius-md);
  overflow: hidden;
  border: 1px solid rgba(57, 216, 231, 0.36);
  background: #081321;
}

.runtime-visual img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  opacity: 0.88;
}

.status-card {
  border-radius: var(--radius-md);
  border: 1px solid var(--line);
  background: rgba(5, 11, 20, 0.92);
  padding: 14px;
}

.status-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  margin-bottom: 10px;
}

.status-label {
  font-family: "JetBrains Mono", ui-monospace, monospace;
  font-size: 10px;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--text-muted);
}

.badge-live {
  border-radius: 999px;
  border: 1px solid rgba(79, 224, 166, 0.4);
  background: rgba(79, 224, 166, 0.1);
  color: var(--success);
  font-family: "JetBrains Mono", ui-monospace, monospace;
  font-size: 10px;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  padding: 4px 8px;
}

.status-list {
  margin: 0;
  padding: 0;
  list-style: none;
  display: grid;
  gap: 7px;
}

.status-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  font-size: 12px;
  color: var(--text-secondary);
}

.status-row strong {
  font-family: "JetBrains Mono", ui-monospace, monospace;
  font-size: 11px;
  letter-spacing: 0.05em;
  text-transform: uppercase;
}

.ok {
  color: var(--success);
}

.wait {
  color: var(--orange);
}

.trust {
  padding-top: 16px;
  padding-bottom: 42px;
}

.trust-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 10px;
}

.trust-item {
  border: 1px solid var(--line);
  border-radius: var(--radius-sm);
  background: rgba(9, 15, 28, 0.8);
  padding: 13px 14px;
}

.trust-item h3 {
  margin: 0;
  font-family: "JetBrains Mono", ui-monospace, monospace;
  font-size: 10px;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--text-muted);
}

.trust-item p {
  margin: 8px 0 0;
  color: var(--text-secondary);
  font-size: 13px;
  line-height: 1.45;
}

.manifesto {
  min-height: 150vh;
  padding-top: 20px;
  padding-bottom: 20px;
}

.manifesto-track {
  height: 150vh;
  position: relative;
}

.manifesto-sticky {
  position: sticky;
  top: 10vh;
  height: 78vh;
  border-radius: var(--radius-lg);
  border: 1px solid var(--line);
  overflow: hidden;
}

.manifesto-base,
.manifesto-overlay {
  position: absolute;
  inset: 0;
  display: grid;
  place-items: center;
  text-align: center;
  padding: clamp(18px, 4vw, 36px);
}

.manifesto-base {
  background: linear-gradient(130deg, #ecf6ff, #d7e8ff);
  color: #0e1629;
}

.manifesto-base h2,
.manifesto-overlay h2 {
  margin: 0;
  max-width: 940px;
  font-family: "Clash Display", "Manrope", sans-serif;
  font-size: clamp(35px, 7vw, 90px);
  line-height: 0.96;
  letter-spacing: -0.03em;
  text-transform: uppercase;
}

.manifesto-overlay {
  color: var(--text-primary);
  background: linear-gradient(140deg, #08162d, #091b39);
  clip-path: circle(calc(var(--manifesto-progress, 0) * 145%) at 50% 50%);
}

.manifesto-overlay::before {
  content: "";
  position: absolute;
  inset: -8%;
  background-image: url('/landing/os.png');
  background-size: cover;
  background-position: center;
  opacity: 0.24;
}

.manifesto-overlay::after {
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(8, 22, 45, 0.26), rgba(8, 22, 45, 0.92));
}

.manifesto-overlay h2 {
  position: relative;
  z-index: 1;
}

.manifesto-overlay h2 span {
  color: var(--cyan);
}

.pillars {
  padding-top: 84px;
  padding-bottom: 84px;
}

.pillars-shell {
  border: 1px solid var(--line);
  border-radius: var(--radius-lg);
  background: linear-gradient(170deg, rgba(8, 14, 27, 0.9), rgba(5, 10, 19, 0.94));
  padding: clamp(22px, 3.5vw, 42px);
}

.pillars-title {
  margin: 10px 0 22px;
  font-family: "Clash Display", "Manrope", sans-serif;
  font-size: clamp(32px, 5.4vw, 64px);
  line-height: 0.94;
  letter-spacing: -0.03em;
  text-transform: uppercase;
}

.pillars-title span {
  color: rgba(232, 238, 251, 0.58);
  font-weight: 500;
}

.pillar {
  border-top: 1px solid var(--line);
}

.pillar:first-of-type {
  border-top: 0;
}

.pillar-trigger {
  width: 100%;
  border: 0;
  background: transparent;
  color: inherit;
  padding: 23px 2px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  text-align: left;
  cursor: pointer;
}

.pillar-trigger:focus-visible {
  outline: 2px solid var(--cyan);
  outline-offset: 4px;
  border-radius: 8px;
}

.pillar-index {
  min-width: 38px;
  font-family: "JetBrains Mono", ui-monospace, monospace;
  font-size: 11px;
  letter-spacing: 0.11em;
  text-transform: uppercase;
  color: var(--cyan);
}

.pillar-title {
  flex: 1;
  margin: 0;
  font-family: "Clash Display", "Manrope", sans-serif;
  font-size: clamp(28px, 4.1vw, 54px);
  line-height: 0.97;
  letter-spacing: -0.03em;
  text-transform: uppercase;
  color: rgba(232, 238, 251, 0.88);
}

.pillar-arrow {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  border: 1px solid var(--line);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 17px;
  transition: transform 220ms ease, border-color 220ms ease, background 220ms ease;
}

.pillar.is-open .pillar-arrow {
  transform: rotate(45deg);
  border-color: rgba(57, 216, 231, 0.48);
  background: var(--cyan-dim);
}

.pillar-panel {
  overflow: hidden;
  max-height: 0;
  opacity: 0;
  transition: max-height 360ms ease, opacity 220ms ease;
}

.pillar.is-open .pillar-panel {
  opacity: 1;
}

.pillar-grid {
  padding: 4px 0 28px;
  display: grid;
  grid-template-columns: 1fr 330px;
  gap: 20px;
}

.pillar-text {
  margin: 0;
  color: var(--text-secondary);
  font-size: clamp(15px, 1.45vw, 19px);
  line-height: 1.68;
}

.pillar-list {
  margin: 16px 0 0;
  padding: 0;
  list-style: none;
  display: grid;
  gap: 10px;
}

.pillar-list li {
  border-left: 2px solid rgba(57, 216, 231, 0.45);
  padding-left: 12px;
  color: var(--text-primary);
  font-family: "JetBrains Mono", ui-monospace, monospace;
  font-size: 12px;
  letter-spacing: 0.03em;
}

.pillar-media {
  margin: 0;
  min-height: 230px;
  border-radius: var(--radius-md);
  border: 1px solid rgba(57, 216, 231, 0.35);
  overflow: hidden;
  background: #091322;
}

.pillar-media img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.launch {
  padding-bottom: 44px;
}

.launch-shell {
  border-top: 1px solid var(--line);
  padding-top: 32px;
}

.status-dot {
  width: 14px;
  height: 14px;
  border-radius: 50%;
  background: var(--cyan);
  box-shadow: 0 0 24px rgba(57, 216, 231, 0.52);
  margin: 0 auto 10px;
  animation: pulse 2.4s ease-in-out infinite;
}

.launch h2 {
  margin: 0;
  text-align: center;
  font-family: "Clash Display", "Manrope", sans-serif;
  font-size: clamp(38px, 7.8vw, 96px);
  line-height: 0.9;
  letter-spacing: -0.04em;
  text-transform: uppercase;
}

.launch h2 span {
  color: rgba(232, 238, 251, 0.55);
  font-weight: 500;
}

.launch p {
  margin: 16px auto 0;
  max-width: 560px;
  text-align: center;
  color: var(--text-secondary);
  font-size: 15px;
  line-height: 1.66;
}

.launch-actions {
  margin-top: 22px;
  display: flex;
  justify-content: center;
}

.footer-meta {
  margin-top: 32px;
  padding-top: 16px;
  border-top: 1px solid var(--line);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  flex-wrap: wrap;
  color: var(--text-muted);
  font-family: "JetBrains Mono", ui-monospace, monospace;
  font-size: 11px;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.reveal {
  opacity: 0;
  transform: translateY(22px);
  transition: opacity 460ms ease, transform 460ms ease;
}

.reveal.visible {
  opacity: 1;
  transform: none;
}

.magnetic,
.magnetic-inner {
  will-change: transform;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.45; }
}

@media (max-width: 1024px) {
  .hero-grid {
    grid-template-columns: 1fr;
  }

  .hero-side {
    border-left: 0;
    border-top: 1px solid var(--line);
  }

  .trust-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .pillar-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 860px) {
  .mobile-nav-toggle {
    display: inline-flex;
  }

  .nav-links {
    position: absolute;
    top: calc(var(--nav-height) - 8px);
    right: 0;
    width: min(280px, calc(100vw - 20px));
    border: 1px solid var(--line);
    border-radius: var(--radius-sm);
    background: rgba(8, 14, 28, 0.96);
    backdrop-filter: blur(10px);
    padding: 12px;
    flex-direction: column;
    align-items: stretch;
    gap: 8px;
    opacity: 0;
    transform: translateY(-8px);
    pointer-events: none;
    transition: opacity 180ms ease, transform 180ms ease;
  }

  .nav-links.is-open {
    opacity: 1;
    transform: none;
    pointer-events: auto;
  }

  .trust-grid {
    grid-template-columns: 1fr;
  }

  .manifesto {
    min-height: 124vh;
  }

  .manifesto-track {
    height: 124vh;
  }

  .manifesto-sticky {
    top: 9vh;
    height: 72vh;
  }
}

@media (max-width: 680px) {
  .section {
    padding-inline: 12px;
  }

  .topbar-shell {
    width: calc(100% - 24px);
  }

  .hero {
    padding-top: calc(var(--nav-height) + 22px);
  }

  .hero-actions,
  .launch-actions {
    flex-direction: column;
    align-items: stretch;
  }

  .button-primary,
  .button-secondary,
  .nav-cta {
    width: 100%;
  }

  .footer-meta {
    flex-direction: column;
    align-items: flex-start;
  }
}

@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
    scroll-behavior: auto !important;
  }

  .reveal {
    opacity: 1;
    transform: none;
  }

  .manifesto-overlay {
    clip-path: none;
  }
}
</style>
</head>
<body>
<div class="page">
  <header class="topbar">
    <div class="topbar-shell">
      <a href="/" class="brand">spawn<span>.</span></a>

      <button type="button" class="mobile-nav-toggle" id="mobileNavToggle" aria-expanded="false" aria-controls="navLinks">Menu</button>

      <nav class="nav-links" id="navLinks" aria-label="Main navigation">
        <a href="#workflow" class="nav-link">Workflow</a>
        <a href="#pillars" class="nav-link">Integrations</a>
        <a href="#launch" class="nav-link">Launch</a>
        @if(session('user'))
          <a class="nav-cta magnetic" href="/wizard"><span class="magnetic-inner">Post agent</span></a>
        @else
          <a class="nav-cta magnetic" href="/auth/google"><span class="magnetic-inner">Sign in with Google</span></a>
        @endif
      </nav>
    </div>
    <div class="progress-track" aria-hidden="true"><div class="progress-bar" id="progressBar"></div></div>
  </header>

  <main>
    <section class="section hero reveal" id="hero">
      <div class="shell hero-grid">
        <div class="hero-copy">
          <p class="kicker">platform.thespawn.io // production flow</p>

          <h1 class="hero-title">
            <span class="soft">Ship faster.</span>
            <span class="strong">Host once.</span>
          </h1>

          <p class="hero-description">
            Spawn gives you a direct path from local code to a live Telegram agent: Google sign-in,
            payment-gated deploy via OnChainOS, runtime provisioning in OpenClaw, and bot delivery in one flow.
          </p>

          <div class="hero-actions">
            @if(session('user'))
              <a class="button-primary magnetic" href="/wizard"><span class="magnetic-inner">Post agent</span></a>
            @else
              <a class="button-primary magnetic" href="/auth/google"><span class="magnetic-inner">Sign in with Google</span></a>
            @endif
            <a class="button-secondary" href="#pillars">See integration details</a>
          </div>

          <ul class="quick-checklist">
            <li>Bring only your bot token and agent prompt.</li>
            <li>Deploy requests are blocked until payment status is valid.</li>
            <li>Telegram token validation runs before runtime install.</li>
          </ul>
        </div>

        <aside class="hero-side" aria-label="Runtime preview">
          <figure class="runtime-visual">
            <img src="/landing/hero.png" alt="Spawn runtime preview">
          </figure>

          <div class="status-card">
            <div class="status-head">
              <span class="status-label">deploy status snapshot</span>
              <span class="badge-live">live checks</span>
            </div>

            <ul class="status-list">
              <li class="status-row"><span>Auth</span><strong class="ok">google ok</strong></li>
              <li class="status-row"><span>Payment</span><strong class="wait">awaiting charge</strong></li>
              <li class="status-row"><span>Runtime</span><strong class="ok">openclaw ready</strong></li>
              <li class="status-row"><span>Channel</span><strong class="ok">telegram active</strong></li>
            </ul>
          </div>
        </aside>
      </div>
    </section>

    <section class="section trust reveal" aria-label="Trust and proof">
      <div class="shell trust-grid">
        <article class="trust-item">
          <h3>Validation first</h3>
          <p>Telegram token checks run before any charge or install starts.</p>
        </article>
        <article class="trust-item">
          <h3>Payment gated</h3>
          <p>OnChainOS charge status gates provisioning so installs do not run unpaid.</p>
        </article>
        <article class="trust-item">
          <h3>Runtime status</h3>
          <p>OpenClaw install state is mapped to clear deployed, failed, or booting results.</p>
        </article>
        <article class="trust-item">
          <h3>Webhook safety</h3>
          <p>OnChainOS webhook signatures are verified before accepting callbacks.</p>
        </article>
      </div>
    </section>

    <section class="section manifesto" id="workflow">
      <div class="shell manifesto-track">
        <div class="manifesto-sticky">
          <div class="manifesto-base">
            <h2>manual deploy loops waste your iteration speed.</h2>
          </div>

          <div class="manifesto-overlay" id="manifestoOverlay">
            <h2>
              Spawn workflow:
              <span>sign in -> post agent -> pay -> live on Telegram.</span>
            </h2>
          </div>
        </div>
      </div>
    </section>

    <section class="section pillars reveal" id="pillars">
      <div class="shell pillars-shell">
        <p class="kicker">Technical path</p>
        <h2 class="pillars-title">the runtime stack, <span>made explicit</span></h2>

        <article class="pillar is-open" data-pillar>
          <h3>
            <button class="pillar-trigger" type="button" id="pillar-btn-1" aria-expanded="true" aria-controls="pillar-panel-1">
              <span class="pillar-index">01</span>
              <span class="pillar-title">Google Auth Entry</span>
              <span class="pillar-arrow" aria-hidden="true">+</span>
            </button>
          </h3>
          <div class="pillar-panel" id="pillar-panel-1" role="region" aria-labelledby="pillar-btn-1">
            <div class="pillar-grid">
              <div>
                <p class="pillar-text">
                  The homepage routes through Google OAuth so deploy actions run under a verified session.
                  Signed-in users jump straight to the wizard, while anonymous visitors are sent to auth.
                </p>
                <ul class="pillar-list">
                  <li>Session-aware CTAs on nav, hero, and footer</li>
                  <li>Direct `/auth/google` entry for unauthenticated traffic</li>
                  <li>Low-friction path back to `/wizard` after callback</li>
                </ul>
              </div>
              <figure class="pillar-media">
                <img src="/landing/hero.png" alt="Google auth flow visual">
              </figure>
            </div>
          </div>
        </article>

        <article class="pillar" data-pillar>
          <h3>
            <button class="pillar-trigger" type="button" id="pillar-btn-2" aria-expanded="false" aria-controls="pillar-panel-2">
              <span class="pillar-index">02</span>
              <span class="pillar-title">OnChainOS Charge Gate</span>
              <span class="pillar-arrow" aria-hidden="true">+</span>
            </button>
          </h3>
          <div class="pillar-panel" id="pillar-panel-2" role="region" aria-labelledby="pillar-btn-2" hidden>
            <div class="pillar-grid">
              <div>
                <p class="pillar-text">
                  Deploy creates a payment intent through OnChainOS and proceeds only when the charge state is valid.
                  Callback signatures are verified so only authentic settlement events are accepted.
                </p>
                <ul class="pillar-list">
                  <li>Idempotent charge creation per deploy request</li>
                  <li>Deterministic failure mapping for API responses</li>
                  <li>Webhook signature verification at ingress</li>
                </ul>
              </div>
              <figure class="pillar-media">
                <img src="/landing/traditional.png" alt="OnChainOS payment gate visual">
              </figure>
            </div>
          </div>
        </article>

        <article class="pillar" data-pillar>
          <h3>
            <button class="pillar-trigger" type="button" id="pillar-btn-3" aria-expanded="false" aria-controls="pillar-panel-3">
              <span class="pillar-index">03</span>
              <span class="pillar-title">OpenClaw Runtime Install</span>
              <span class="pillar-arrow" aria-hidden="true">+</span>
            </button>
          </h3>
          <div class="pillar-panel" id="pillar-panel-3" role="region" aria-labelledby="pillar-btn-3" hidden>
            <div class="pillar-grid">
              <div>
                <p class="pillar-text">
                  Spawn packages a manifest and calls OpenClaw install with idempotency. Runtime status is normalized
                  into ready, booting, or failed so deployment state is transparent for the client.
                </p>
                <ul class="pillar-list">
                  <li>Manifest payload validated before send</li>
                  <li>Install response sanity checks</li>
                  <li>Consistent runtime status translation</li>
                </ul>
              </div>
              <figure class="pillar-media">
                <img src="/landing/clanker.png" alt="OpenClaw install visual">
              </figure>
            </div>
          </div>
        </article>

        <article class="pillar" data-pillar>
          <h3>
            <button class="pillar-trigger" type="button" id="pillar-btn-4" aria-expanded="false" aria-controls="pillar-panel-4">
              <span class="pillar-index">04</span>
              <span class="pillar-title">Telegram Delivery Layer</span>
              <span class="pillar-arrow" aria-hidden="true">+</span>
            </button>
          </h3>
          <div class="pillar-panel" id="pillar-panel-4" role="region" aria-labelledby="pillar-btn-4" hidden>
            <div class="pillar-grid">
              <div>
                <p class="pillar-text">
                  User bot tokens are validated before deploy and webhooks bind each bot to the correct agent endpoint,
                  so message delivery does not depend on local machine uptime.
                </p>
                <ul class="pillar-list">
                  <li>Token validation before payment and install</li>
                  <li>Per-agent webhook routing endpoint</li>
                  <li>Single operator flow from wizard to live bot</li>
                </ul>
              </div>
              <figure class="pillar-media">
                <img src="/landing/os.png" alt="Telegram delivery visual">
              </figure>
            </div>
          </div>
        </article>
      </div>
    </section>

    <section class="section launch reveal" id="launch">
      <div class="shell launch-shell">
        <div class="status-dot" aria-hidden="true"></div>
        <p class="kicker" style="text-align:center; margin-bottom: 14px;">system online</p>

        <h2>launch your next <span>agent run</span></h2>

        <p>
          Keep building your agent locally. Spawn owns hosting, payment-gated deployment,
          and Telegram runtime delivery in one stable path.
        </p>

        <div class="launch-actions">
          @if(session('user'))
            <a class="button-primary magnetic" href="/wizard"><span class="magnetic-inner">Post agent now</span></a>
          @else
            <a class="button-primary magnetic" href="/auth/google"><span class="magnetic-inner">Sign in with Google</span></a>
          @endif
        </div>

        <div class="footer-meta">
          <span>platform.thespawn.io</span>
          <span>main@{{ substr(config('app.build_sha', env('BUILD_SHA', 'a835343')), 0, 7) }}</span>
          <span>&copy; {{ date('Y') }} Spawn</span>
        </div>
      </div>
    </section>
  </main>
</div>

<script>
(() => {
  const reduceMotionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
  const touchQuery = window.matchMedia('(hover: none), (pointer: coarse)');

  const supportsPointerFx = () => !reduceMotionQuery.matches && !touchQuery.matches;

  const clamp = (value, min, max) => Math.min(Math.max(value, min), max);

  const progressBar = document.getElementById('progressBar');
  const updateProgress = () => {
    if (!progressBar) {
      return;
    }

    const max = Math.max(document.documentElement.scrollHeight - window.innerHeight, 1);
    const pct = clamp((window.scrollY / max) * 100, 0, 100);
    progressBar.style.width = pct.toFixed(2) + '%';
  };
  window.addEventListener('scroll', updateProgress, { passive: true });
  window.addEventListener('resize', updateProgress);
  updateProgress();

  const mobileNavToggle = document.getElementById('mobileNavToggle');
  const navLinks = document.getElementById('navLinks');
  if (mobileNavToggle && navLinks) {
    const closeNav = () => {
      mobileNavToggle.setAttribute('aria-expanded', 'false');
      navLinks.classList.remove('is-open');
    };

    mobileNavToggle.addEventListener('click', () => {
      const expanded = mobileNavToggle.getAttribute('aria-expanded') === 'true';
      mobileNavToggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
      navLinks.classList.toggle('is-open', !expanded);
    });

    navLinks.querySelectorAll('a').forEach((link) => link.addEventListener('click', closeNav));

    document.addEventListener('click', (event) => {
      if (!navLinks.classList.contains('is-open')) {
        return;
      }
      if (!navLinks.contains(event.target) && event.target !== mobileNavToggle) {
        closeNav();
      }
    });

    window.addEventListener('resize', () => {
      if (window.innerWidth > 860) {
        closeNav();
      }
    });
  }

  const revealTargets = document.querySelectorAll('.reveal');
  if (reduceMotionQuery.matches || !('IntersectionObserver' in window)) {
    revealTargets.forEach((el) => el.classList.add('visible'));
  } else {
    const revealObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.14 });

    revealTargets.forEach((el) => revealObserver.observe(el));
  }

  const manifestoSection = document.getElementById('workflow');
  const manifestoOverlay = document.getElementById('manifestoOverlay');
  const updateManifesto = () => {
    if (!manifestoSection || !manifestoOverlay) {
      return;
    }

    if (reduceMotionQuery.matches) {
      manifestoOverlay.style.setProperty('--manifesto-progress', '1');
      return;
    }

    const rect = manifestoSection.getBoundingClientRect();
    const viewport = window.innerHeight || 1;
    const start = viewport * 0.84;
    const end = -rect.height * 0.3;
    const progress = clamp((start - rect.top) / (start - end), 0, 1);
    manifestoOverlay.style.setProperty('--manifesto-progress', progress.toFixed(3));
  };
  window.addEventListener('scroll', updateManifesto, { passive: true });
  window.addEventListener('resize', updateManifesto);
  updateManifesto();

  const accordionButtons = Array.from(document.querySelectorAll('.pillar-trigger'));
  const closePanel = (button, panel, pillar) => {
    if (!panel || !pillar) {
      return;
    }

    button.setAttribute('aria-expanded', 'false');
    pillar.classList.remove('is-open');
    panel.style.maxHeight = panel.scrollHeight + 'px';

    requestAnimationFrame(() => {
      panel.style.maxHeight = '0px';
    });

    const onEnd = (event) => {
      if (event.propertyName !== 'max-height') {
        return;
      }
      panel.hidden = true;
      panel.removeEventListener('transitionend', onEnd);
    };
    panel.addEventListener('transitionend', onEnd);
  };

  const openPanel = (button, panel, pillar) => {
    if (!panel || !pillar) {
      return;
    }

    panel.hidden = false;
    button.setAttribute('aria-expanded', 'true');
    pillar.classList.add('is-open');
    panel.style.maxHeight = panel.scrollHeight + 'px';
  };

  const setOpenButton = (targetButton) => {
    accordionButtons.forEach((button) => {
      const panelId = button.getAttribute('aria-controls');
      const panel = panelId ? document.getElementById(panelId) : null;
      const pillar = button.closest('[data-pillar]');
      const isTarget = button === targetButton;

      if (isTarget) {
        openPanel(button, panel, pillar);
      } else {
        closePanel(button, panel, pillar);
      }
    });
  };

  accordionButtons.forEach((button, index) => {
    button.addEventListener('click', () => setOpenButton(button));

    button.addEventListener('keydown', (event) => {
      if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        setOpenButton(button);
        return;
      }

      if (event.key === 'ArrowDown') {
        event.preventDefault();
        const next = accordionButtons[(index + 1) % accordionButtons.length];
        next.focus();
      }

      if (event.key === 'ArrowUp') {
        event.preventDefault();
        const prev = accordionButtons[(index - 1 + accordionButtons.length) % accordionButtons.length];
        prev.focus();
      }
    });
  });

  const defaultOpen = accordionButtons.find((button) => button.getAttribute('aria-expanded') === 'true') || accordionButtons[0];
  if (defaultOpen) {
    setOpenButton(defaultOpen);
  }

  const applyPointerFx = () => {
    if (!supportsPointerFx()) {
      document.querySelectorAll('.magnetic, .magnetic-inner, .hero-copy, .runtime-visual img').forEach((el) => {
        el.style.transform = 'translate(0, 0)';
      });
      return;
    }

    document.querySelectorAll('.magnetic').forEach((element) => {
      const inner = element.querySelector('.magnetic-inner') || element;

      element.addEventListener('mousemove', (event) => {
        const rect = element.getBoundingClientRect();
        const x = event.clientX - rect.left - rect.width / 2;
        const y = event.clientY - rect.top - rect.height / 2;
        element.style.transform = `translate(${x * 0.14}px, ${y * 0.14}px)`;
        inner.style.transform = `translate(${x * 0.08}px, ${y * 0.08}px)`;
      });

      element.addEventListener('mouseleave', () => {
        element.style.transform = 'translate(0, 0)';
        inner.style.transform = 'translate(0, 0)';
      });
    });

    const heroSection = document.getElementById('hero');
    const heroCopy = document.querySelector('.hero-copy');
    const runtimeImage = document.querySelector('.runtime-visual img');

    if (heroSection && heroCopy && runtimeImage) {
      heroSection.addEventListener('mousemove', (event) => {
        const rect = heroSection.getBoundingClientRect();
        const x = (event.clientX - rect.left) / rect.width - 0.5;
        const y = (event.clientY - rect.top) / rect.height - 0.5;
        heroCopy.style.transform = `translate(${x * -9}px, ${y * -9}px)`;
        runtimeImage.style.transform = `translate(${x * 11}px, ${y * 11}px) scale(1.02)`;
      });

      heroSection.addEventListener('mouseleave', () => {
        heroCopy.style.transform = 'translate(0, 0)';
        runtimeImage.style.transform = 'translate(0, 0) scale(1)';
      });
    }
  };

  applyPointerFx();

  const handleMotionChange = () => {
    if (reduceMotionQuery.matches) {
      document.querySelectorAll('.reveal').forEach((el) => el.classList.add('visible'));
      if (manifestoOverlay) {
        manifestoOverlay.style.setProperty('--manifesto-progress', '1');
      }
    }
    applyPointerFx();
  };

  if (typeof reduceMotionQuery.addEventListener === 'function') {
    reduceMotionQuery.addEventListener('change', handleMotionChange);
  } else if (typeof reduceMotionQuery.addListener === 'function') {
    reduceMotionQuery.addListener(handleMotionChange);
  }

  if (typeof touchQuery.addEventListener === 'function') {
    touchQuery.addEventListener('change', handleMotionChange);
  } else if (typeof touchQuery.addListener === 'function') {
    touchQuery.addListener(handleMotionChange);
  }
})();
</script>
</body>
</html>
