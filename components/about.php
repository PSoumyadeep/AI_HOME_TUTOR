<?php
session_start();
$page_title  = 'About';
$active_page = 'about';
require_once 'header.php';
?>

<style>
/* ════════════════════════════════════════════
   ABOUT PAGE — "Cosmic Classroom" aesthetic
   Deep space + warm amber accents + editorial
════════════════════════════════════════════ */
@import url('https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,700;0,9..144,900;1,9..144,300;1,9..144,700&family=DM+Sans:wght@300;400;500;600&display=swap');

:root {
  --gold:    #f5b942;
  --gold2:   #e8934a;
  --teal:    #22d3ee;
  --indigo:  #6366f1;
  --ease:    cubic-bezier(.4,0,.2,1);
}

[data-bs-theme="dark"] {
  --bg:      #08090f;
  --surface: #10121e;
  --surface2:#181c30;
  --border:  rgba(245,185,66,.12);
  --text:    #f0ede6;
  --muted:   #7a7890;
}
[data-bs-theme="light"] {
  --bg:      #faf8f2;
  --surface: #ffffff;
  --surface2:#f5f0e8;
  --border:  rgba(230,140,60,.18);
  --text:    #1a1612;
  --muted:   #7a7264;
}

/* Override body font for this page */
.about-page * { font-family: 'DM Sans', sans-serif; }
.about-page h1, .about-page h2, .about-page h3, .about-page .serif {
  font-family: 'Fraunces', serif;
}

body { background: var(--bg); }

/* ── Starfield canvas ── */
#stars-canvas {
  position: fixed; inset: 0; z-index: 0; pointer-events: none; opacity: .55;
  transition: opacity .4s;
}
[data-bs-theme="light"] #stars-canvas { opacity: .12; }

/* ── Shared section wrapper ── */
.about-page {
  position: relative; z-index: 1;
}

/* ════════ HERO ════════ */
.about-hero {
  min-height: 88vh;
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  text-align: center; padding: 5rem 1.5rem 4rem;
  position: relative;
}

.hero-orb {
  position: absolute; border-radius: 50%; filter: blur(110px); pointer-events: none;
}
.hero-orb-1 {
  width: 500px; height: 500px;
  background: radial-gradient(circle, rgba(245,185,66,.22) 0%, transparent 65%);
  top: -100px; left: 50%; transform: translateX(-50%);
  animation: drift1 14s ease-in-out infinite alternate;
}
.hero-orb-2 {
  width: 380px; height: 380px;
  background: radial-gradient(circle, rgba(34,211,238,.15) 0%, transparent 65%);
  bottom: -80px; right: 5%;
  animation: drift2 18s ease-in-out infinite alternate;
}
@keyframes drift1 { from{transform:translateX(-50%) translateY(0)} to{transform:translateX(-50%) translateY(30px) scale(1.06)} }
@keyframes drift2 { from{transform:translate(0,0)} to{transform:translate(-20px,30px) scale(1.08)} }

.hero-pill {
  display: inline-flex; align-items: center; gap: 8px;
  background: rgba(245,185,66,.1); border: 1px solid rgba(245,185,66,.3);
  border-radius: 50px; padding: 5px 16px;
  font-size: .72rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase;
  color: var(--gold); margin-bottom: 1.75rem;
  animation: fadeUp .6s var(--ease) both;
}
.hero-pill .dot-live {
  width: 7px; height: 7px; border-radius: 50%; background: var(--gold);
  animation: pulse 2s ease-in-out infinite;
}
@keyframes pulse { 0%,100%{box-shadow:0 0 0 0 rgba(245,185,66,.4)} 50%{box-shadow:0 0 0 6px rgba(245,185,66,0)} }

.about-hero h1 {
  font-size: clamp(2.8rem, 7vw, 5.5rem);
  font-weight: 900; line-height: 1.05;
  color: var(--text);
  animation: fadeUp .7s .1s var(--ease) both;
}
.about-hero h1 em {
  font-style: italic;
  background: linear-gradient(135deg, var(--gold) 0%, var(--gold2) 100%);
  -webkit-background-clip: text; -webkit-text-fill-color: transparent;
}

.about-hero .sub {
  max-width: 560px; margin: 1.5rem auto 0;
  font-size: 1.08rem; color: var(--muted); line-height: 1.75;
  font-weight: 300;
  animation: fadeUp .7s .2s var(--ease) both;
}

.hero-scroll-hint {
  margin-top: 3rem; display: flex; flex-direction: column; align-items: center; gap: 6px;
  color: var(--muted); font-size: .75rem; letter-spacing: .08em; text-transform: uppercase;
  animation: fadeUp .7s .4s var(--ease) both;
}
.scroll-arrow { animation: scrollBounce 1.6s ease-in-out infinite; font-size: 1.1rem; }
@keyframes scrollBounce { 0%,100%{transform:translateY(0)} 50%{transform:translateY(6px)} }

@keyframes fadeUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }

/* ════════ FEATURES SECTION ════════ */
.features-section { padding: 5rem 0 4rem; }

.section-label {
  font-size: .7rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase;
  color: var(--gold); margin-bottom: .6rem;
  display: flex; align-items: center; gap: 10px;
}
.section-label::after { content:''; flex:1; height:1px; background:linear-gradient(to right,rgba(245,185,66,.3),transparent); }

.section-title {
  font-family: 'Fraunces', serif; font-weight: 900;
  font-size: clamp(1.8rem, 4vw, 2.8rem); line-height: 1.15;
  color: var(--text); margin-bottom: 3rem;
}
.section-title em { font-style: italic; color: var(--gold); }

/* Feature cards */
.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.feat-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 22px;
  padding: 2rem;
  position: relative; overflow: hidden;
  transition: transform .35s var(--ease), box-shadow .35s var(--ease), border-color .35s;
}
.feat-card::before {
  content: '';
  position: absolute; top: 0; left: 0; right: 0; height: 3px;
  background: var(--card-accent, linear-gradient(90deg, var(--gold), var(--gold2)));
  opacity: 0; transition: opacity .3s;
}
.feat-card:hover { transform: translateY(-8px); }
.feat-card:hover::before { opacity: 1; }

.feat-card.c1 { --card-accent: linear-gradient(90deg, #6366f1, #8b5cf6); }
.feat-card.c1:hover { border-color: rgba(99,102,241,.35); box-shadow: 0 24px 60px rgba(99,102,241,.18); }
.feat-card.c2 { --card-accent: linear-gradient(90deg, #f5b942, #e8934a); }
.feat-card.c2:hover { border-color: rgba(245,185,66,.35); box-shadow: 0 24px 60px rgba(245,185,66,.18); }
.feat-card.c3 { --card-accent: linear-gradient(90deg, #ef4444, #f97316); }
.feat-card.c3:hover { border-color: rgba(239,68,68,.35); box-shadow: 0 24px 60px rgba(239,68,68,.15); }
.feat-card.c4 { --card-accent: linear-gradient(90deg, #22d3ee, #0ea5e9); }
.feat-card.c4:hover { border-color: rgba(34,211,238,.35); box-shadow: 0 24px 60px rgba(34,211,238,.15); }
.feat-card.c5 { --card-accent: linear-gradient(90deg, #10b981, #34d399); }
.feat-card.c5:hover { border-color: rgba(16,185,129,.35); box-shadow: 0 24px 60px rgba(16,185,129,.15); }

.feat-icon {
  width: 58px; height: 58px; border-radius: 16px; margin-bottom: 1.5rem;
  display: flex; align-items: center; justify-content: center; font-size: 1.6rem;
  background: var(--surface2); border: 1px solid var(--border);
  transition: transform .3s;
}
.feat-card:hover .feat-icon { transform: scale(1.12) rotate(-5deg); }

.feat-title {
  font-family: 'Fraunces', serif; font-size: 1.18rem; font-weight: 700;
  color: var(--text); margin-bottom: .65rem;
}
.feat-desc { font-size: .9rem; color: var(--muted); line-height: 1.72; }

.feat-tag {
  display: inline-block; margin-top: 1.1rem;
  font-size: .68rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase;
  padding: 3px 10px; border-radius: 50px;
  background: rgba(245,185,66,.1); border: 1px solid rgba(245,185,66,.25); color: var(--gold);
}

/* ════════ STATS STRIP ════════ */
.stats-strip {
  background: linear-gradient(135deg, #f5b942 0%, #e8934a 100%);
  border-radius: 28px; padding: 3rem 2.5rem;
  display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  gap: 2rem; text-align: center; margin: 4rem 0;
  position: relative; overflow: hidden;
  box-shadow: 0 30px 80px rgba(245,185,66,.35);
}
.stats-strip::before {
  content: '';
  position: absolute; top: -60px; right: -60px;
  width: 220px; height: 220px; border-radius: 50%;
  background: rgba(255,255,255,.1);
}
.stats-strip::after {
  content: '';
  position: absolute; bottom: -40px; left: 20px;
  width: 140px; height: 140px; border-radius: 50%;
  background: rgba(255,255,255,.07);
}
.stat-s { position: relative; z-index: 1; }
.stat-s .num {
  font-family: 'Fraunces', serif; font-size: 2.6rem; font-weight: 900;
  color: #fff; line-height: 1;
}
.stat-s .lbl { font-size: .8rem; color: rgba(255,255,255,.75); margin-top: .3rem; font-weight: 500; }

/* ════════ TEAM SECTION ════════ */
.team-section { padding: 4rem 0 5rem; }

.team-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 1.75rem; margin-top: 3rem;
}

.team-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 24px; padding: 2.25rem 1.75rem;
  text-align: center; position: relative; overflow: hidden;
  transition: transform .35s var(--ease), box-shadow .35s;
}
.team-card:hover { transform: translateY(-10px); box-shadow: 0 30px 80px rgba(0,0,0,.2); }

/* Animated gradient ring avatar */
.avatar-ring {
  width: 90px; height: 90px; border-radius: 50%;
  margin: 0 auto 1.25rem;
  padding: 3px;
  background: var(--avatar-gradient, linear-gradient(135deg, var(--gold), var(--gold2)));
  animation: ringRotate 6s linear infinite;
  position: relative;
}
@keyframes ringRotate {
  from { background: var(--avatar-gradient); filter: hue-rotate(0deg); }
  to   { filter: hue-rotate(360deg); }
}
.avatar-inner {
  width: 100%; height: 100%; border-radius: 50%;
  background: var(--surface);
  display: flex; align-items: center; justify-content: center;
  font-size: 2rem; font-weight: 900; font-family: 'Fraunces', serif;
  position: relative; z-index: 1;
}

.team-card:nth-child(1) .avatar-ring { --avatar-gradient: linear-gradient(135deg, #6366f1, #a78bfa); }
.team-card:nth-child(2) .avatar-ring { --avatar-gradient: linear-gradient(135deg, #f5b942, #ef4444); }
.team-card:nth-child(3) .avatar-ring { --avatar-gradient: linear-gradient(135deg, #22d3ee, #10b981); }

.team-name {
  font-family: 'Fraunces', serif; font-size: 1.3rem; font-weight: 700;
  color: var(--text); margin-bottom: .3rem;
}
.team-role {
  font-size: .8rem; font-weight: 600; letter-spacing: .08em; text-transform: uppercase;
  color: var(--gold); margin-bottom: .85rem;
}
.team-bio { font-size: .88rem; color: var(--muted); line-height: 1.68; }

.team-card::after {
  content: '';
  position: absolute; bottom: 0; left: 0; right: 0; height: 4px;
  background: var(--avatar-gradient, linear-gradient(90deg, var(--gold), var(--gold2)));
  opacity: 0; transition: opacity .3s;
}
.team-card:hover::after { opacity: 1; }

/* ════════ CTA SECTION ════════ */
.cta-section {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 28px; padding: 4rem 2rem;
  text-align: center; margin-bottom: 4rem;
  position: relative; overflow: hidden;
}
.cta-section::before {
  content: '';
  position: absolute; top: 0; left: 0; right: 0; bottom: 0;
  background: radial-gradient(ellipse at 50% 0%, rgba(245,185,66,.08) 0%, transparent 65%);
  pointer-events: none;
}
.cta-section h2 {
  font-family: 'Fraunces', serif; font-weight: 900;
  font-size: clamp(1.8rem, 4vw, 2.5rem); color: var(--text); margin-bottom: .75rem;
}
.cta-section h2 em { font-style: italic; color: var(--gold); }
.cta-section p { color: var(--muted); font-size: 1rem; max-width: 480px; margin: 0 auto 2.25rem; line-height: 1.7; }

.btn-cta-main {
  display: inline-flex; align-items: center; gap: 8px;
  background: linear-gradient(135deg, var(--gold), var(--gold2));
  color: #1a1612; font-family: 'DM Sans', sans-serif; font-weight: 700;
  font-size: .97rem; border: none; border-radius: 50px;
  padding: .85rem 2.2rem; text-decoration: none; cursor: pointer;
  box-shadow: 0 8px 30px rgba(245,185,66,.4);
  transition: transform .2s, box-shadow .2s, opacity .2s;
}
.btn-cta-main:hover { transform: translateY(-3px); box-shadow: 0 14px 40px rgba(245,185,66,.5); color: #1a1612; }

.btn-cta-ghost {
  display: inline-flex; align-items: center; gap: 8px;
  background: transparent; color: var(--text); font-family: 'DM Sans', sans-serif;
  font-weight: 600; font-size: .97rem;
  border: 1.5px solid var(--border); border-radius: 50px;
  padding: .85rem 2.2rem; text-decoration: none; cursor: pointer;
  transition: border-color .2s, color .2s, background .2s;
}
.btn-cta-ghost:hover { border-color: var(--gold); color: var(--gold); background: rgba(245,185,66,.06); }

@media(max-width:576px) {
  .features-grid { grid-template-columns: 1fr; }
  .team-grid { grid-template-columns: 1fr; }
  .stats-strip { grid-template-columns: 1fr 1fr; }
}
</style>

<!-- Starfield canvas -->
<canvas id="stars-canvas"></canvas>

<div class="about-page">

  <!-- ══════ HERO ══════ -->
  <section class="about-hero">
    <div class="hero-orb hero-orb-1"></div>
    <div class="hero-orb hero-orb-2"></div>

    <div class="hero-pill">
      <span class="dot-live"></span> Built for Indian Students
    </div>

    <h1>
      Your <em>AI-Powered</em><br>
      Home Tutor
    </h1>

    <p class="sub">
      Everything you need to learn smarter — from step-by-step explanations
      to full-length mock exams, all in one beautifully crafted platform.
    </p>

    <div class="hero-scroll-hint">
      <span>Scroll to explore</span>
      <span class="scroll-arrow">↓</span>
    </div>
  </section>

  <!-- ══════ FEATURES ══════ -->
  <section class="features-section">
    <div class="container">

      <div class="section-label">What We Offer</div>
      <h2 class="section-title">Everything a student<br><em>actually needs</em></h2>

      <div class="features-grid">

        <!-- 1 -->
        <div class="feat-card c1">
          <div class="feat-icon">📖</div>
          <div class="feat-title">Learn Any Topic with AI</div>
          <div class="feat-desc">
            Type any topic — Photosynthesis, Quadratic Equations, the French Revolution — and your AI tutor breaks it down into clear sections with real-life examples, key concepts, and a quick summary tailored for NCERT curriculum.
          </div>
          <span class="feat-tag">AI Explanations</span>
        </div>

        <!-- 2 -->
        <div class="feat-card c2">
          <div class="feat-icon">📄</div>
          <div class="feat-title">Save Topics & Download PDF</div>
          <div class="feat-desc">
            Every topic you learn is saved to your personal history. Revisit them anytime — or download a clean, formatted PDF of the explanation to study offline, share with classmates, or use as revision notes before exams.
          </div>
          <span class="feat-tag">PDF Export · Offline Ready</span>
        </div>

        <!-- 3 -->
        <div class="feat-card c3">
          <div class="feat-icon">📝</div>
          <div class="feat-title">Give AI-Generated Exams</div>
          <div class="feat-desc">
            Choose your class, subject, and chapters — the AI instantly generates a personalised MCQ exam. Get immediate feedback after each answer with a detailed explanation. Track your score and retry for improvement.
          </div>
          <span class="feat-tag">MCQ · Timed · Scored</span>
        </div>

        <!-- 4 -->
        <div class="feat-card c4">
          <div class="feat-icon">📊</div>
          <div class="feat-title">Track Your Progress</div>
          <div class="feat-desc">
            Your learning streak, topics studied, exams taken, and scores are all tracked in one place. Watch your progress grow day by day, identify weak areas, and stay motivated with your personal dashboard.
          </div>
          <span class="feat-tag">Dashboard · Streaks · Analytics</span>
        </div>

        <!-- 5 -->
        <div class="feat-card c4" style="--card-accent:linear-gradient(90deg,#f97316,#fbbf24)">
          <div class="feat-icon">▶️</div>
          <div class="feat-title">YouTube Video Suggestions</div>
          <div class="feat-desc">
            Search any topic and get curated YouTube video recommendations filtered for your class and board — no more wasting time scrolling. The best educational videos, handpicked and ready to watch instantly.
          </div>
          <span class="feat-tag">Coming Soon · Board-wise Curation</span>
        </div>

        <!-- 6 -->
        <div class="feat-card c5">
          <div class="feat-icon">📚</div>
          <div class="feat-title">Free Book PDFs</div>
          <div class="feat-desc">
            Access free PDF links for NCERT textbooks, RD Sharma, HC Verma, RS Aggarwal, and more — all curated by subject and class. No more searching across ten sites; find what you need in seconds.
          </div>
          <span class="feat-tag">Coming Soon · NCERT · Reference Books</span>
        </div>

      </div>
    </div>
  </section>

  <!-- ══════ STATS STRIP ══════ -->
  <div class="container">
    <div class="stats-strip">
      <div class="stat-s"><div class="num">10K+</div><div class="lbl">Students Learning</div></div>
      <div class="stat-s"><div class="num">50+</div><div class="lbl">Subjects Covered</div></div>
      <div class="stat-s"><div class="num">100+</div><div class="lbl">Topics Explained</div></div>
      <div class="stat-s"><div class="num">4.9 ★</div><div class="lbl">Average Rating</div></div>
      <div class="stat-s"><div class="num">Class 6–12</div><div class="lbl">All Classes</div></div>
    </div>
  </div>

  <!-- ══════ TEAM ══════ -->
  <section class="team-section">
    <div class="container">

      <div class="section-label">The Makers</div>
      <h2 class="section-title">Built with ❤️ by<br><em>three curious minds</em></h2>

      <div class="team-grid">

        <div class="team-card">
          <div class="avatar-ring">
            <div class="avatar-inner">S</div>
          </div>
          <div class="team-name">Sudeshna</div>
          <div class="team-role">Co-Founder &amp; Lead Designer</div>
          <div class="team-bio">
            The creative force behind AI Home Tutor's beautiful interface and learning experience. Passionate about making education accessible and visually delightful for every student.
          </div>
        </div>

        <div class="team-card">
          <div class="avatar-ring">
            <div class="avatar-inner">S</div>
          </div>
          <div class="team-name">Soumyadeep</div>
          <div class="team-role">Co-Founder &amp; Backend Dev</div>
          <div class="team-bio">
            The architect who powers everything under the hood. Soumyadeep built the robust PHP backend, database systems, and AI integrations that make the platform fast, secure, and reliable.
          </div>
        </div>

        <div class="team-card">
          <div class="avatar-ring">
            <div class="avatar-inner">A</div>
          </div>
          <div class="team-name">Ayan</div>
          <div class="team-role">Co-Founder &amp; AI Engineer</div>
          <div class="team-bio">
            The mind behind the AI magic. Ayan engineered the Gemini-powered tutoring engine, MCQ generator, and smart prompting systems that make every explanation feel personal and accurate.
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ══════ CTA ══════ -->
  <div class="container">
    <div class="cta-section">
      <h2>Ready to learn <em>smarter?</em></h2>
      <p>Join thousands of students already using AI Home Tutor to ace their exams, understand tough topics, and track their growth.</p>
      <div class="d-flex gap-3 justify-content-center flex-wrap">
        <?php if (isset($_SESSION['user_id'])): ?>
          <a href="dashboard.php" class="btn-cta-main"><i class="bi bi-grid me-1"></i>Go to Dashboard</a>
          <a href="learn.php"     class="btn-cta-ghost"><i class="bi bi-book me-1"></i>Start Learning</a>
        <?php else: ?>
          <a href="register.php" class="btn-cta-main"><i class="bi bi-rocket-takeoff me-1"></i>Get Started Free</a>
          <a href="index.php"    class="btn-cta-ghost"><i class="bi bi-box-arrow-in-right me-1"></i>Log In</a>
        <?php endif; ?>
      </div>
    </div>
  </div>

</div><!-- /about-page -->

<script>
/* ── Starfield ── */
(function () {
  const canvas = document.getElementById('stars-canvas');
  const ctx = canvas.getContext('2d');
  let stars = [];

  function resize() {
    canvas.width  = window.innerWidth;
    canvas.height = window.innerHeight;
  }

  function initStars(n) {
    stars = [];
    for (let i = 0; i < n; i++) {
      stars.push({
        x: Math.random() * canvas.width,
        y: Math.random() * canvas.height,
        r: Math.random() * 1.4 + .2,
        o: Math.random() * .7 + .1,
        s: Math.random() * .4 + .05,
        d: Math.random() > .5 ? 1 : -1,
        t: Math.random() * Math.PI * 2,
      });
    }
  }

  function draw() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
    stars.forEach(s => {
      s.t += s.s * .02;
      const opacity = s.o * (.6 + .4 * Math.sin(s.t));
      ctx.beginPath();
      ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2);
      ctx.fillStyle = isDark
        ? `rgba(255,240,200,${opacity})`
        : `rgba(100,80,20,${opacity * .6})`;
      ctx.fill();
    });
    requestAnimationFrame(draw);
  }

  resize();
  initStars(220);
  draw();
  window.addEventListener('resize', () => { resize(); initStars(220); });
})();

/* ── Scroll-triggered fade-in for cards ── */
const observer = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      e.target.style.opacity = '1';
      e.target.style.transform = 'translateY(0)';
    }
  });
}, { threshold: .12 });

document.querySelectorAll('.feat-card, .team-card, .stat-s').forEach((el, i) => {
  el.style.opacity = '0';
  el.style.transform = 'translateY(28px)';
  el.style.transition = `opacity .55s ${i * .07}s ease, transform .55s ${i * .07}s ease`;
  observer.observe(el);
});
</script>

<?php require_once 'footer.php'; ?>