<?php
session_start();
$page_title  = 'About';
$active_page = 'about';
require_once 'components/header.php';
?>

<link href="style.css" rel="stylesheet"/>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

  [data-bs-theme="dark"] {
    --bg:#07090f; --surface:#111827; --surface2:#1a2335;
    --border:rgba(255,255,255,.07); --muted:#6b7a99; --text:#e2e8f0;
  }
  [data-bs-theme="light"] {
    --bg:#f4f6fb; --surface:#ffffff; --surface2:#eef1fa;
    --border:rgba(0,0,0,.08); --muted:#6b7a99; --text:#1a202c;
  }

  body { background:var(--bg); min-height:100vh; overflow-x:hidden; }

  /* ── Mesh blobs ── */
  .mesh { position:fixed; inset:0; z-index:0; pointer-events:none; overflow:hidden; }
  .mesh-blob { position:absolute; border-radius:50%; filter:blur(130px); opacity:.18; animation:blobFloat 20s ease-in-out infinite alternate; }
  .mesh-blob:nth-child(1){width:700px;height:700px;background:#6366f1;top:-250px;right:-150px;}
  .mesh-blob:nth-child(2){width:500px;height:500px;background:#0ea5e9;bottom:-200px;left:-100px;animation-delay:-7s;}
  .mesh-blob:nth-child(3){width:350px;height:350px;background:#14b8a6;top:50%;left:35%;animation-delay:-13s;}
  [data-bs-theme="light"] .mesh-blob { opacity:.06; }
  @keyframes blobFloat { from{transform:translate(0,0) scale(1)} to{transform:translate(35px,45px) scale(1.1)} }

  /* ── Hero ── */
  .about-hero { position:relative; z-index:1; padding:5rem 0 3.5rem; text-align:center; }
  .hero-badge { display:inline-flex; align-items:center; gap:8px; background:rgba(99,102,241,.1); border:1px solid rgba(99,102,241,.3); border-radius:50px; padding:5px 18px; font-size:.73rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:#818cf8; margin-bottom:1.5rem; }
  .badge-dot { width:7px; height:7px; border-radius:50%; background:#818cf8; animation:pulse 2s ease-in-out infinite; }
  @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.4;transform:scale(.8)} }

  .hero-title { font-family:'Playfair Display',serif; font-weight:900; font-size:clamp(2.2rem,6vw,4rem); line-height:1.1; margin-bottom:1.2rem; }
  .hero-title .hl { background:linear-gradient(135deg,#6366f1 0%,#0ea5e9 55%,#14b8a6 100%); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
  .hero-sub { color:var(--muted); font-size:1.05rem; max-width:560px; margin:0 auto 2.5rem; line-height:1.8; }

  /* ── Divider ── */
  .gradient-divider { width:80px; height:3px; background:linear-gradient(90deg,#6366f1,#0ea5e9,#14b8a6); border-radius:3px; margin:0 auto 3.5rem; }

  /* ── Section titles ── */
  .sec-label { font-size:.72rem; font-weight:700; letter-spacing:.15em; text-transform:uppercase; color:#818cf8; margin-bottom:.6rem; }
  .sec-title { font-family:'Playfair Display',serif; font-weight:900; font-size:clamp(1.6rem,3.5vw,2.4rem); line-height:1.2; margin-bottom:1rem; }
  .sec-desc  { color:var(--muted); font-size:.97rem; line-height:1.8; max-width:520px; }

  /* ── Features grid ── */
  .features-section { position:relative; z-index:1; padding:1rem 0 4rem; }
  .features-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:1.25rem; margin-top:3rem; }

  .feat-card { background:var(--surface); border:1px solid var(--border); border-radius:22px; padding:2rem; position:relative; overflow:hidden; transition:transform .3s,box-shadow .3s,border-color .3s; animation:cardIn .5s ease both; }
  .feat-card:nth-child(1){animation-delay:.05s} .feat-card:nth-child(2){animation-delay:.1s}
  .feat-card:nth-child(3){animation-delay:.15s} .feat-card:nth-child(4){animation-delay:.2s}
  .feat-card:nth-child(5){animation-delay:.25s} .feat-card:nth-child(6){animation-delay:.3s}
  @keyframes cardIn { from{opacity:0;transform:translateY(28px)} to{opacity:1;transform:translateY(0)} }

  .feat-card::after { content:''; position:absolute; top:0; left:0; right:0; height:3px; border-radius:22px 22px 0 0; opacity:0; transition:opacity .3s; background:var(--card-grad); }
  .feat-card:hover { transform:translateY(-6px); }
  .feat-card:hover::after { opacity:1; }

  /* colour themes */
  .fc-indigo { --c1:#6366f1; --c2:#818cf8; --glow:rgba(99,102,241,.15); --card-grad:linear-gradient(90deg,#6366f1,#818cf8); }
  .fc-rose   { --c1:#f43f5e; --c2:#fb7185; --glow:rgba(244,63,94,.13);  --card-grad:linear-gradient(90deg,#f43f5e,#fb7185); }
  .fc-amber  { --c1:#f59e0b; --c2:#fbbf24; --glow:rgba(245,158,11,.13); --card-grad:linear-gradient(90deg,#f59e0b,#fbbf24); }
  .fc-sky    { --c1:#0ea5e9; --c2:#38bdf8; --glow:rgba(14,165,233,.13); --card-grad:linear-gradient(90deg,#0ea5e9,#38bdf8); }
  .fc-teal   { --c1:#14b8a6; --c2:#2dd4bf; --glow:rgba(20,184,166,.13); --card-grad:linear-gradient(90deg,#14b8a6,#2dd4bf); }
  .fc-violet { --c1:#8b5cf6; --c2:#a78bfa; --glow:rgba(139,92,246,.13); --card-grad:linear-gradient(90deg,#8b5cf6,#a78bfa); }

  .feat-card:hover { box-shadow:0 20px 55px var(--glow); border-color:color-mix(in srgb,var(--c1) 35%,transparent); }

  .feat-icon { width:52px; height:52px; border-radius:15px; display:flex; align-items:center; justify-content:center; font-size:1.5rem; margin-bottom:1.25rem; background:color-mix(in srgb,var(--c1) 12%,transparent); border:1px solid color-mix(in srgb,var(--c1) 22%,transparent); transition:transform .3s,box-shadow .3s; }
  .feat-card:hover .feat-icon { transform:scale(1.1) rotate(-5deg); box-shadow:0 8px 24px var(--glow); }
  .feat-title { font-family:'Playfair Display',serif; font-weight:700; font-size:1.15rem; margin-bottom:.5rem; color:var(--text); }
  .feat-desc  { color:var(--muted); font-size:.875rem; line-height:1.7; }
  .feat-tag   { display:inline-block; margin-top:1rem; font-size:.68rem; font-weight:700; padding:3px 10px; border-radius:50px; background:color-mix(in srgb,var(--c1) 10%,transparent); color:var(--c1); border:1px solid color-mix(in srgb,var(--c1) 25%,transparent); }

  /* ── Team section ── */
  .team-section { position:relative; z-index:1; padding:2rem 0 5rem; }
  .team-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(240px,1fr)); gap:1.5rem; margin-top:3rem; }

  .team-card { background:var(--surface); border:1px solid var(--border); border-radius:24px; padding:2.25rem 1.75rem; text-align:center; position:relative; overflow:hidden; transition:transform .3s,box-shadow .3s; animation:cardIn .5s ease both; }
  .team-card:nth-child(1){animation-delay:.1s} .team-card:nth-child(2){animation-delay:.2s} .team-card:nth-child(3){animation-delay:.3s}
  .team-card::before { content:''; position:absolute; inset:0; border-radius:24px; opacity:0; transition:opacity .3s; }
  .team-card:hover { transform:translateY(-8px); box-shadow:0 24px 60px rgba(99,102,241,.15); }
  .team-card:hover::before { opacity:1; }
  .tc-1::before { background:radial-gradient(circle at 50% 0%,rgba(99,102,241,.07),transparent 65%); }
  .tc-2::before { background:radial-gradient(circle at 50% 0%,rgba(14,165,233,.07),transparent 65%); }
  .tc-3::before { background:radial-gradient(circle at 50% 0%,rgba(20,184,166,.07),transparent 65%); }

  .avatar { width:80px; height:80px; border-radius:50%; margin:0 auto 1.25rem; display:flex; align-items:center; justify-content:center; font-family:'Playfair Display',serif; font-size:1.8rem; font-weight:900; color:#fff; position:relative; }
  .avatar::after { content:''; position:absolute; inset:-3px; border-radius:50%; z-index:-1; }
  .av-1 { background:linear-gradient(135deg,#6366f1,#818cf8); box-shadow:0 8px 28px rgba(99,102,241,.35); }
  .av-2 { background:linear-gradient(135deg,#0ea5e9,#38bdf8); box-shadow:0 8px 28px rgba(14,165,233,.35); }
  .av-3 { background:linear-gradient(135deg,#14b8a6,#2dd4bf); box-shadow:0 8px 28px rgba(20,184,166,.35); }

  .member-name { font-family:'Playfair Display',serif; font-weight:700; font-size:1.2rem; margin-bottom:.3rem; color:var(--text); }
  .member-role { font-size:.78rem; font-weight:600; color:var(--muted); letter-spacing:.06em; text-transform:uppercase; margin-bottom:1rem; }
  .member-desc { font-size:.85rem; color:var(--muted); line-height:1.65; }

  /* ── Stats banner ── */
  .stats-banner { position:relative; z-index:1; margin:0 0 4rem; }
  .stats-inner { background:var(--surface); border:1px solid var(--border); border-radius:24px; padding:2.5rem; display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:1.5rem; text-align:center; position:relative; overflow:hidden; }
  .stats-inner::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,#6366f1,#0ea5e9,#14b8a6); }
  .stat-num { font-family:'Playfair Display',serif; font-weight:900; font-size:2.2rem; background:linear-gradient(135deg,#6366f1,#0ea5e9); -webkit-background-clip:text; -webkit-text-fill-color:transparent; line-height:1.1; }
  .stat-lbl { font-size:.8rem; color:var(--muted); margin-top:.3rem; font-weight:600; letter-spacing:.05em; }

  /* ── CTA ── */
  .cta-section { position:relative; z-index:1; text-align:center; padding:1rem 0 5rem; }
  .cta-box { background:var(--surface); border:1px solid var(--border); border-radius:28px; padding:3.5rem 2rem; position:relative; overflow:hidden; }
  .cta-box::before { content:''; position:absolute; inset:0; background:radial-gradient(ellipse at 50% 0%,rgba(99,102,241,.08),transparent 65%); pointer-events:none; }
  .cta-box::after  { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,#6366f1,#0ea5e9,#14b8a6); }
  .cta-title { font-family:'Playfair Display',serif; font-weight:900; font-size:clamp(1.6rem,4vw,2.5rem); margin-bottom:.75rem; }
  .cta-desc  { color:var(--muted); font-size:.97rem; margin-bottom:2rem; max-width:480px; margin-left:auto; margin-right:auto; }
  .btn-cta { display:inline-flex; align-items:center; gap:8px; background:linear-gradient(135deg,#6366f1,#0ea5e9); color:#fff; border:none; border-radius:14px; padding:.85rem 2rem; font-family:'Plus Jakarta Sans',sans-serif; font-weight:700; font-size:.95rem; text-decoration:none; transition:opacity .2s,transform .2s,box-shadow .2s; }
  .btn-cta:hover { opacity:.9; transform:translateY(-2px); box-shadow:0 12px 32px rgba(99,102,241,.3); color:#fff; }
  .btn-cta-outline { background:transparent; border:1.5px solid var(--border); color:var(--text); margin-left:.75rem; }
  .btn-cta-outline:hover { border-color:#6366f1; color:#818cf8; box-shadow:none; }

  @media(max-width:600px) {
    .features-grid,.team-grid { grid-template-columns:1fr; }
    .btn-cta-outline { margin-left:0; margin-top:.6rem; }
  }
</style>

<!-- Mesh -->
<div class="mesh">
  <div class="mesh-blob"></div>
  <div class="mesh-blob"></div>
  <div class="mesh-blob"></div>
</div>

<!-- ── Hero ── -->
<section class="about-hero">
  <div class="container">
    <div class="hero-badge"><span class="badge-dot"></span> About AI Home Tutor</div>
    <h1 class="hero-title">Your Personal<br><span class="hl">AI-Powered Classroom</span></h1>
    <p class="hero-sub">AI Home Tutor is a smart learning platform built for Indian school students — combining AI explanations, self-assessment exams, progress tracking, and curated resources, all in one place.</p>
    <div class="gradient-divider"></div>
  </div>
</section>

<!-- ── Stats ── -->
<section class="stats-banner">
  <div class="container">
    <div class="stats-inner">
      <div><div class="stat-num">6+</div><div class="stat-lbl">Core Features</div></div>
      <div><div class="stat-num">AI</div><div class="stat-lbl">Powered Explanations</div></div>
      <div><div class="stat-num">∞</div><div class="stat-lbl">Topics to Explore</div></div>
      <div><div class="stat-num">3</div><div class="stat-lbl">Developers</div></div>
    </div>
  </div>
</section>

<!-- ── Features ── -->
<section class="features-section">
  <div class="container">
    <div class="sec-label">What we offer</div>
    <h2 class="sec-title">Everything you need to<br>learn smarter</h2>
    <p class="sec-desc">From AI-generated lessons to downloadable PDFs — every feature is designed to make studying easier, faster, and more effective.</p>

    <div class="features-grid">

      <div class="feat-card fc-indigo">
        <div class="feat-icon">📖</div>
        <div class="feat-title">Learn Any Topic with AI</div>
        <div class="feat-desc">Type any subject — Photosynthesis, Quadratic Equations, World War II — and get a clear, structured explanation with examples, key concepts, and a quick summary.</div>
        <span class="feat-tag">AI Explained</span>
      </div>

      <div class="feat-card fc-sky">
        <div class="feat-icon">📄</div>
        <div class="feat-title">Download Study Notes as PDF</div>
        <div class="feat-desc">Every topic you learn is automatically saved to your account. Revisit it anytime or download a beautifully formatted PDF of your AI-generated study notes.</div>
        <span class="feat-tag">Save & Download</span>
      </div>

      <div class="feat-card fc-rose">
        <div class="feat-icon">📝</div>
        <div class="feat-title">Take AI-Generated Exams</div>
        <div class="feat-desc">Test your knowledge with timed, AI-generated MCQ exams for any topic. Get instant results with detailed explanations for every answer.</div>
        <span class="feat-tag">MCQ · Timed · Scored</span>
      </div>

      <div class="feat-card fc-teal">
        <div class="feat-icon">📊</div>
        <div class="feat-title">Track Your Progress</div>
        <div class="feat-desc">Your personal progress dashboard shows every topic you've studied, your daily learning streak, and how many topics you've covered today — keeping you motivated.</div>
        <span class="feat-tag">Progress Tracking</span>
      </div>

      <div class="feat-card fc-amber">
        <div class="feat-icon">▶️</div>
        <div class="feat-title">YouTube Video Suggestions</div>
        <div class="feat-desc">Search any topic and get curated YouTube video recommendations handpicked for your class and board — so you never waste time searching for the right video.</div>
        <span class="feat-tag">Coming Soon</span>
      </div>

      <div class="feat-card fc-violet">
        <div class="feat-icon">📚</div>
        <div class="feat-title">Books & Free PDF Links</div>
        <div class="feat-desc">Discover the best reference books for every subject, with free download links to NCERT, RD Sharma, HC Verma, and more — all in one place.</div>
        <span class="feat-tag">Coming Soon</span>
      </div>

    </div>
  </div>
</section>

<!-- ── Team ── -->
<section class="team-section">
  <div class="container">
    <div class="sec-label">Meet the team</div>
    <h2 class="sec-title">Built with ❤️ by students,<br>for students</h2>
    <p class="sec-desc">AI Home Tutor was created by three passionate developers from West Bengal who wanted to make quality education accessible to every student.</p>

    <div class="team-grid">

      <div class="team-card tc-1">
        <div class="avatar av-1">S</div>
        <div class="member-name">Sudeshna Paul</div>
        <div class="member-role">Co-Founder & Developer</div>
        <div class="member-desc">Passionate about making AI-powered education accessible to every student across India.</div>
      </div>

      <div class="team-card tc-2">
        <div class="avatar av-2">S</div>
        <div class="member-name">Soumyadeep</div>
        <div class="member-role">Co-Founder & Developer</div>
        <div class="member-desc">Focused on building seamless user experiences and robust backend systems that just work.</div>
      </div>

      <div class="team-card tc-3">
        <div class="avatar av-3">A</div>
        <div class="member-name">Ayan</div>
        <div class="member-role">Co-Founder & Developer</div>
        <div class="member-desc">Bringing creative ideas and technical skills together to shape the future of this platform.</div>
      </div>

    </div>
  </div>
</section>

<!-- ── CTA ── -->
<section class="cta-section">
  <div class="container">
    <div class="cta-box">
      <h2 class="cta-title">Ready to start learning?</h2>
      <p class="cta-desc">Join AI Home Tutor today and experience a smarter way to study — completely free.</p>
      <div>
        <a href="<?= isset($_SESSION['user_id']) ? 'dashboard.php' : 'index.php' ?>" class="btn-cta">
          <i class="bi bi-stars"></i>
          <?= isset($_SESSION['user_id']) ? 'Go to Dashboard' : 'Get Started Free' ?>
        </a>
        <a href="learn.php" class="btn-cta btn-cta-outline">
          <i class="bi bi-book-open"></i> Learn a Topic
        </a>
      </div>
    </div>
  </div>
</section>

<?php require_once 'components/footer.php'; ?>