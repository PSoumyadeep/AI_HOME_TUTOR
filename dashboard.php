<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }

$user_name  = htmlspecialchars($_SESSION['user_name']  ?? 'Student');
$user_class = htmlspecialchars($_SESSION['user_class'] ?? '—');
$user_board = htmlspecialchars($_SESSION['user_board'] ?? '—');
$user_email = htmlspecialchars($_SESSION['user_email'] ?? '—');
$user_initial = strtoupper(mb_substr($_SESSION['user_name'] ?? 'S', 0, 1));

$page_title  = 'Dashboard';
$active_page = 'dashboard';
require_once 'components/header.php';
?>


<link href="style.css" rel="stylesheet"/>
<style>
  /* ── Dashboard theme overrides (dark-first palette) ── */
  [data-bs-theme="dark"] {
    --bg:      #07090f;
    --surface: #111827;
    --surface2:#1a2335;
    --border:  rgba(255,255,255,.07);
    --muted:   #6b7a99;
  }
  [data-bs-theme="light"] {
    --bg:      #f4f6fb;
    --surface: #ffffff;
    --surface2:#eef1fa;
    --border:  rgba(0,0,0,.07);
    --muted:   #6b7a99;
  }

  body { background:var(--bg); }

  /* Mesh blobs */
  .mesh { position:fixed; inset:0; z-index:0; pointer-events:none; overflow:hidden; }
  .mesh-blob { position:absolute; border-radius:50%; filter:blur(120px); opacity:.18; animation:blobFloat 18s ease-in-out infinite alternate; }
  .mesh-blob:nth-child(1){width:600px;height:600px;background:#6366f1;top:-200px;right:-100px;}
  .mesh-blob:nth-child(2){width:500px;height:500px;background:#0ea5e9;bottom:-150px;left:-100px;animation-delay:-6s;}
  .mesh-blob:nth-child(3){width:400px;height:400px;background:#14b8a6;top:40%;left:40%;animation-delay:-12s;}
  [data-bs-theme="light"] .mesh-blob { opacity:.07; }
  @keyframes blobFloat { from{transform:translate(0,0) scale(1)} to{transform:translate(30px,40px) scale(1.08)} }

  /* Welcome strip */
  .welcome-strip { position:relative; z-index:1; padding:3rem 0 2rem; }
  .welcome-eyebrow { font-size:.72rem; font-weight:700; letter-spacing:.12em; text-transform:uppercase; color:var(--muted); margin-bottom:.5rem; display:flex; align-items:center; gap:8px; }
  .live-dot { width:8px; height:8px; border-radius:50%; background:#22c55e; animation:pulse 1.8s ease-in-out infinite; }
  @keyframes pulse { 0%,100%{box-shadow:0 0 0 0 rgba(34,197,94,.4)} 50%{box-shadow:0 0 0 6px rgba(34,197,94,0)} }
  .welcome-title { font-family:'Playfair Display',serif; font-weight:900; font-size:clamp(1.8rem,4vw,2.8rem); line-height:1.15; margin-bottom:.6rem; }
  .welcome-title .name-hl { background:linear-gradient(135deg,#6366f1 0%,#0ea5e9 60%,#14b8a6 100%); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
  .welcome-sub { color:var(--muted); font-size:.97rem; }
  .stats-row { display:flex; gap:1.5rem; flex-wrap:wrap; margin-top:2rem; }
  .stat-pill { display:flex; align-items:center; gap:10px; background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:.6rem 1.1rem; }
  .stat-icon { width:36px; height:36px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:1rem; }
  .stat-num { font-size:1.1rem; font-weight:700; color:var(--text); line-height:1.3; }
  .stat-lbl { font-size:.72rem; color:var(--muted); }

  /* Section head */
  .section-head { display:flex; align-items:center; gap:12px; margin-bottom:1.5rem; }
  .section-head h2 { font-family:'Playfair Display',serif; font-weight:700; font-size:1.45rem; margin:0; }
  .section-head::after { content:''; flex:1; height:1px; background:linear-gradient(to right,var(--border),transparent); }

  /* Dashboard grid */
  .dash-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:1.25rem; position:relative; z-index:1; }

  .dash-card { background:var(--surface); border:1px solid var(--border); border-radius:20px; padding:1.75rem; cursor:pointer; text-decoration:none; display:flex; flex-direction:column; position:relative; overflow:hidden; transition:transform .3s,box-shadow .3s,border-color .3s; animation:cardIn .5s var(--ease) both; }
  .dash-card:nth-child(1){animation-delay:.05s} .dash-card:nth-child(2){animation-delay:.1s} .dash-card:nth-child(3){animation-delay:.15s} .dash-card:nth-child(4){animation-delay:.2s}
  @keyframes cardIn { from{opacity:0;transform:translateY(24px)} to{opacity:1;transform:translateY(0)} }
  .dash-card::before { content:''; position:absolute; inset:0; border-radius:20px; opacity:0; transition:opacity .3s; pointer-events:none; }
  .dash-card::after  { content:''; position:absolute; top:0; left:0; right:0; height:3px; border-radius:20px 20px 0 0; opacity:0; transition:opacity .3s; }
  .dash-card:hover { transform:translateY(-6px); }
  .dash-card:hover::before,.dash-card:hover::after { opacity:1; }

  /* Card colour themes */
  .card-learn  { --c1:#6366f1; --c2:#818cf8; --glow:rgba(99,102,241,.18); }
  .card-exam   { --c1:#f43f5e; --c2:#fb7185; --glow:rgba(244,63,94,.18); }
  .card-yt     { --c1:#f97316; --c2:#fb923c; --glow:rgba(249,115,22,.18); }
  .card-books  { --c1:#10b981; --c2:#34d399; --glow:rgba(16,185,129,.18); }
  .card-learn:hover  { border-color:rgba(99,102,241,.4);  box-shadow:0 20px 60px rgba(99,102,241,.18); }
  .card-exam:hover   { border-color:rgba(244,63,94,.4);   box-shadow:0 20px 60px rgba(244,63,94,.15); }
  .card-yt:hover     { border-color:rgba(249,115,22,.4);  box-shadow:0 20px 60px rgba(249,115,22,.15); }
  .card-books:hover  { border-color:rgba(16,185,129,.4);  box-shadow:0 20px 60px rgba(16,185,129,.15); }
  .card-learn::before  { background:radial-gradient(circle at 80% 20%,rgba(99,102,241,.08) 0%,transparent 60%); }
  .card-exam::before   { background:radial-gradient(circle at 80% 20%,rgba(244,63,94,.08) 0%,transparent 60%); }
  .card-yt::before     { background:radial-gradient(circle at 80% 20%,rgba(249,115,22,.08) 0%,transparent 60%); }
  .card-books::before  { background:radial-gradient(circle at 80% 20%,rgba(16,185,129,.08) 0%,transparent 60%); }
  .card-learn::after   { background:linear-gradient(90deg,#6366f1,#818cf8); }
  .card-exam::after    { background:linear-gradient(90deg,#f43f5e,#fb7185); }
  .card-yt::after      { background:linear-gradient(90deg,#f97316,#fb923c); }
  .card-books::after   { background:linear-gradient(90deg,#10b981,#34d399); }

  .card-icon-wrap { width:56px; height:56px; border-radius:16px; margin-bottom:1.25rem; display:flex; align-items:center; justify-content:center; font-size:1.5rem; background:color-mix(in srgb,var(--c1) 12%,transparent); border:1px solid color-mix(in srgb,var(--c1) 25%,transparent); transition:transform .3s,box-shadow .3s; }
  .dash-card:hover .card-icon-wrap { transform:scale(1.1) rotate(-4deg); box-shadow:0 8px 28px var(--glow); }
  .card-badge { position:absolute; top:1.25rem; right:1.25rem; font-size:.65rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; padding:2px 8px; border-radius:50px; background:color-mix(in srgb,var(--c1) 15%,transparent); color:var(--c1); border:1px solid color-mix(in srgb,var(--c1) 30%,transparent); }
  .card-title { font-family:'Playfair Display',serif; font-weight:700; font-size:1.2rem; margin-bottom:.45rem; color:var(--text); }
  .card-desc  { font-size:.875rem; color:var(--muted); line-height:1.65; flex:1; }
  .card-footer-row { display:flex; align-items:center; justify-content:space-between; margin-top:1.5rem; padding-top:1rem; border-top:1px solid var(--border); }
  .card-tags { display:flex; gap:.4rem; flex-wrap:wrap; }
  .card-tag  { font-size:.68rem; font-weight:600; padding:2px 8px; border-radius:50px; background:color-mix(in srgb,var(--c1) 10%,transparent); color:var(--c1); }
  .card-arrow { width:32px; height:32px; border-radius:50%; background:color-mix(in srgb,var(--c1) 12%,transparent); border:1px solid color-mix(in srgb,var(--c1) 25%,transparent); display:flex; align-items:center; justify-content:center; color:var(--c1); font-size:.85rem; transition:background .2s,transform .2s; flex-shrink:0; }
  .dash-card:hover .card-arrow { background:var(--c1); color:#fff; transform:translate(2px,-2px); }

  /* Streak section */
  .progress-section { position:relative; z-index:1; background:var(--surface); border:1px solid var(--border); border-radius:20px; padding:1.75rem; margin-bottom:1rem; }
  .progress-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.25rem; flex-wrap:wrap; gap:.5rem; }
  .progress-head h3 { font-family:'Playfair Display',serif; font-weight:700; font-size:1.1rem; }
  .progress-head span { font-size:.8rem; color:var(--muted); }
  .streak-row { display:flex; gap:.5rem; flex-wrap:wrap; }
  .streak-day { width:40px; height:40px; border-radius:9px; border:1px solid var(--border); display:flex; align-items:center; justify-content:center; font-size:.65rem; font-weight:700; color:var(--muted); flex-direction:column; gap:1px; }
  .streak-day.done  { background:rgba(99,102,241,.15); border-color:rgba(99,102,241,.35); color:#818cf8; }
  .streak-day.today { background:#6366f1; border-color:#6366f1; color:#fff; box-shadow:0 0 0 3px rgba(99,102,241,.25); }
  .streak-day .day-abbr { font-size:.55rem; opacity:.7; }
  @media(max-width:576px){ .dash-grid{grid-template-columns:1fr} .stats-row{gap:.75rem} }
</style>

<!-- Mesh background -->
<div class="mesh">
  <div class="mesh-blob"></div>
  <div class="mesh-blob"></div>
  <div class="mesh-blob"></div>
</div>

<!-- Welcome strip -->
<section class="welcome-strip">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-8">
        <div class="welcome-eyebrow"><span class="live-dot"></span> Your learning dashboard</div>
        <h1 class="welcome-title">
          Welcome back,<br>
          <span class="name-hl"><?= $user_name ?></span> 👋
        </h1>
        <p class="welcome-sub">Pick up where you left off. Your AI tutor is ready to help you today.</p>
        <div class="stats-row">
          <div class="stat-pill">
            <div class="stat-icon" style="background:rgba(99,102,241,.12);color:#818cf8;">🔥</div>
            <div><div class="stat-num">7</div><div class="stat-lbl">Day Streak</div></div>
          </div>
          <div class="stat-pill">
            <div class="stat-icon" style="background:rgba(16,185,129,.12);color:#34d399;">📚</div>
            <div><div class="stat-num" id="topicsCount">0</div><div class="stat-lbl">Topics Learned</div></div>
          </div>
          <div class="stat-pill">
            <div class="stat-icon" style="background:rgba(245,158,11,.12);color:#fbbf24;">🎓</div>
            <div><div class="stat-num"><?= $user_class ?></div><div class="stat-lbl">My Class</div></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Dashboard cards -->
<section style="position:relative;z-index:1;margin-bottom:2.5rem;">
  <div class="container">
    <div class="section-head"><h2>What do you want to do?</h2></div>
    <div class="dash-grid">

      <!-- 1. Learn a Topic -->
      <a href="learn.php" class="dash-card card-learn">
        <div class="card-badge">Start Now</div>
        <div class="card-icon-wrap">📖</div>
        <div class="card-title">Learn a Topic</div>
        <div class="card-desc">Ask your AI tutor to explain any subject — Photosynthesis, Quadratic Equations, World War II — in clear, step-by-step detail.</div>
        <div class="card-footer-row">
          <div class="card-tags"><span class="card-tag">All Subjects</span><span class="card-tag">AI Explained</span></div>
          <div class="card-arrow"><i class="bi bi-arrow-up-right"></i></div>
        </div>
      </a>

      <!-- 2. Give Exam -->
      <a href="mcq_test.php" class="dash-card card-exam">
        <div class="card-badge">Start Now</div>
        <div class="card-icon-wrap">📝</div>
        <div class="card-title">Give an Exam</div>
        <div class="card-desc">Test your knowledge with timed, AI-generated MCQ exams. Get instant results with detailed explanations after each question.</div>
        <div class="card-footer-row">
          <div class="card-tags"><span class="card-tag">MCQ</span><span class="card-tag">Timed</span><span class="card-tag">Scored</span></div>
          <div class="card-arrow"><i class="bi bi-arrow-up-right"></i></div>
        </div>
      </a>

      <!-- 3. YouTube Videos -->
     <a href="videos.php" class="dash-card card-yt">
  <div class="card-badge">Start Now</div>
  <div class="card-icon-wrap">▶️</div>
  <div class="card-title">YouTube Videos</div>
  <div class="card-desc">Get curated YouTube video recommendations for any topic — handpicked for your class and board, so you never waste time searching.</div>
  <div class="card-footer-row">
    <div class="card-tags"><span class="card-tag">Curated</span><span class="card-tag">Board-wise</span></div>
    <div class="card-arrow"><i class="bi bi-arrow-up-right"></i></div>
  </div>
</a>

      </div>
  </div>
</section>

<!-- Streak section -->
<section style="position:relative;z-index:1;margin-bottom:3rem;">
  <div class="container">
    <div class="progress-section">
      <div class="progress-head">
        <h3>🔥 Weekly Learning Streak</h3>
        <span>Keep it going — don't break the chain!</span>
      </div>
      <div class="streak-row" id="streakRow"></div>
    </div>
  </div>
</section>


<script src="script.js"></script>
<script>
// Topics count from localStorage
const tc = document.getElementById('topicsCount');
const hist = JSON.parse(localStorage.getItem('topics_history') || '[]');
if (tc) tc.textContent = hist.length;

// Streak row
const days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
const today = new Date().getDay();
const row = document.getElementById('streakRow');
days.forEach((d, i) => {
  const el = document.createElement('div');
  el.className = 'streak-day' + (i < today ? ' done' : '') + (i === today ? ' today' : '');
  el.innerHTML = `<span>${i <= today ? '✓' : '○'}</span><span class="day-abbr">${d}</span>`;
  row.appendChild(el);
});
</script>

<?php require_once 'components/footer.php'; ?>