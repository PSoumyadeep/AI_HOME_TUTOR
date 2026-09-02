<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }

$user_name    = htmlspecialchars($_SESSION['user_name']  ?? 'Student');
$user_initial = strtoupper(mb_substr($_SESSION['user_name'] ?? 'S', 0, 1));

$page_title  = 'My Progress';
$active_page = 'progress';
require_once 'components/header.php';
require_once 'php_action/db.php';

$user_id = (int) $_SESSION['user_id'];

// Fetch all topics for this user, newest first
$stmt = $conn->prepare(
    "SELECT id, topic_name, learned_at
     FROM topics_learned
     WHERE user_id = ?
     ORDER BY learned_at DESC"
);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$topics = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Stats
$total_topics  = count($topics);
$today_count   = 0;
$subjects_seen = [];
$today_str     = date('Y-m-d');
foreach ($topics as $t) {
    if (substr($t['learned_at'], 0, 10) === $today_str) $today_count++;
}

// Streak calculation: count consecutive days with at least 1 topic
$days_with_study = [];
foreach ($topics as $t) {
    $d = substr($t['learned_at'], 0, 10);
    $days_with_study[$d] = true;
}
$streak = 0;
$check_day = new DateTime();
while (isset($days_with_study[$check_day->format('Y-m-d')])) {
    $streak++;
    $check_day->modify('-1 day');
}

// Fetch all exam attempts for this user, newest first
$examStmt = $conn->prepare(
    "SELECT id, user_class, subject, chapters, total_questions, correct, wrong, score_pct, taken_at
     FROM exam_attempts
     WHERE user_id = ?
     ORDER BY taken_at DESC"
);
$examStmt->bind_param('i', $user_id);
$examStmt->execute();
$examResult = $examStmt->get_result();
$exams = $examResult->fetch_all(MYSQLI_ASSOC);
$examStmt->close();

// Group exams by the calendar day they were taken on (Y-m-d -> list of exams)
$exams_by_date = [];
foreach ($exams as $e) {
    $d = substr($e['taken_at'], 0, 10);
    $exams_by_date[$d][] = [
        'subject'  => $e['subject'],
        'class'    => $e['user_class'],
        'chapters' => $e['chapters'],
        'total'    => (int) $e['total_questions'],
        'correct'  => (int) $e['correct'],
        'score'    => (int) $e['score_pct'],
        'time'     => date('h:i A', strtotime($e['taken_at'])),
    ];
}
$total_exams = count($exams);

$conn->close();
?>

<link href="style.css" rel="stylesheet"/>
<style>
  [data-bs-theme="dark"] {
    --bg:#07090f; --surface:#111827; --surface2:#1a2335;
    --border:rgba(255,255,255,.07); --muted:#6b7a99; --text:#e2e8f0;
  }
  [data-bs-theme="light"] {
    --bg:#f4f6fb; --surface:#ffffff; --surface2:#eef1fa;
    --border:rgba(0,0,0,.07); --muted:#6b7a99; --text:#1a202c;
  }
  body { background:var(--bg); min-height:100vh; }

  /* Mesh bg */
  .mesh { position:fixed; inset:0; z-index:0; pointer-events:none; overflow:hidden; }
  .mesh-blob { position:absolute; border-radius:50%; filter:blur(120px); opacity:.15; animation:blobFloat 18s ease-in-out infinite alternate; }
  .mesh-blob:nth-child(1){width:600px;height:600px;background:#6366f1;top:-200px;right:-100px;}
  .mesh-blob:nth-child(2){width:500px;height:500px;background:#0ea5e9;bottom:-150px;left:-100px;animation-delay:-6s;}
  [data-bs-theme="light"] .mesh-blob { opacity:.05; }
  @keyframes blobFloat { from{transform:translate(0,0) scale(1)} to{transform:translate(30px,40px) scale(1.08)} }

  /* Hero strip */
  .progress-hero { position:relative; z-index:1; padding:3rem 0 2rem; }
  .eyebrow { font-size:.72rem; font-weight:700; letter-spacing:.12em; text-transform:uppercase; color:var(--muted); margin-bottom:.6rem; }
  .hero-title { font-family:'Playfair Display',serif; font-weight:900; font-size:clamp(1.8rem,4vw,2.6rem); line-height:1.15; margin-bottom:.5rem; }
  .hero-title .hl { background:linear-gradient(135deg,#6366f1,#0ea5e9,#14b8a6); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }

  /* Stat pills */
  .stats-row { display:flex; gap:1.25rem; flex-wrap:wrap; margin-top:1.75rem; }
  .stat-pill { display:flex; align-items:center; gap:10px; background:var(--surface); border:1px solid var(--border); border-radius:14px; padding:.7rem 1.2rem; }
  .stat-icon { width:38px; height:38px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.1rem; }
  .stat-num { font-size:1.15rem; font-weight:800; line-height:1.2; }
  .stat-lbl { font-size:.7rem; color:var(--muted); }

  /* Section heading */
  .sec-head { display:flex; align-items:center; gap:12px; margin-bottom:1.5rem; position:relative; z-index:1; }
  .sec-head h2 { font-family:'Playfair Display',serif; font-weight:700; font-size:1.35rem; margin:0; }
  .sec-head::after { content:''; flex:1; height:1px; background:linear-gradient(to right,var(--border),transparent); }

  /* Search bar */
  .search-wrap { position:relative; z-index:1; margin-bottom:1.5rem; }
  .search-input { width:100%; background:var(--surface); border:1.5px solid var(--border); border-radius:12px; padding:.65rem 1rem .65rem 2.6rem; font-size:.9rem; color:var(--text); outline:none; font-family:'Plus Jakarta Sans',sans-serif; transition:border-color .2s,box-shadow .2s; }
  .search-input:focus { border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.15); }
  .search-icon { position:absolute; left:.85rem; top:50%; transform:translateY(-50%); color:var(--muted); font-size:.9rem; }

  /* Topics grid */
  .topics-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(290px,1fr)); gap:1.1rem; position:relative; z-index:1; }

  .topic-card { background:var(--surface); border:1px solid var(--border); border-radius:18px; padding:1.4rem 1.5rem; display:flex; flex-direction:column; gap:.75rem; position:relative; overflow:hidden; transition:transform .25s,box-shadow .25s,border-color .25s; animation:cardIn .4s ease both; }
  .topic-card:hover { transform:translateY(-4px); box-shadow:0 16px 40px rgba(99,102,241,.12); border-color:rgba(99,102,241,.3); }
  .topic-card::after { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,#6366f1,#0ea5e9); border-radius:18px 18px 0 0; }
  @keyframes cardIn { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }

  .card-top { display:flex; align-items:flex-start; gap:.9rem; }
  .topic-icon { width:44px; height:44px; border-radius:12px; background:rgba(99,102,241,.1); border:1px solid rgba(99,102,241,.2); display:flex; align-items:center; justify-content:center; font-size:1.3rem; flex-shrink:0; }
  .topic-name { font-family:'Playfair Display',serif; font-weight:700; font-size:1.05rem; color:var(--text); line-height:1.3; flex:1; }
  .topic-date { font-size:.73rem; color:var(--muted); margin-top:2px; display:flex; align-items:center; gap:4px; }

  .card-actions { display:flex; gap:.5rem; }
  .btn-pdf { display:inline-flex; align-items:center; gap:6px; padding:.45rem 1rem; border-radius:9px; font-size:.8rem; font-weight:700; border:none; cursor:pointer; transition:opacity .2s,transform .15s; text-decoration:none; }
  .btn-pdf-primary { background:linear-gradient(135deg,#6366f1,#818cf8); color:#fff; }
  .btn-pdf-primary:hover { opacity:.88; transform:translateY(-1px); color:#fff; }
  .btn-view { background:rgba(99,102,241,.1); color:#818cf8; border:1px solid rgba(99,102,241,.25); }
  .btn-view:hover { background:rgba(99,102,241,.18); color:#818cf8; }

  /* Empty state */
  .empty-box { position:relative; z-index:1; text-align:center; padding:4rem 1rem; background:var(--surface); border:1px solid var(--border); border-radius:20px; margin-top:1rem; }
  .empty-box .empty-icon { font-size:4rem; opacity:.35; margin-bottom:1rem; }
  .empty-box p { color:var(--muted); margin-bottom:1.5rem; }
  .btn-start { display:inline-flex; align-items:center; gap:8px; background:linear-gradient(135deg,#6366f1,#0ea5e9); color:#fff; border:none; border-radius:12px; padding:.7rem 1.5rem; font-weight:700; font-size:.9rem; text-decoration:none; transition:opacity .2s; }
  .btn-start:hover { opacity:.88; color:#fff; }

  /* Modal overlay for inline viewer */
  .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:9999; backdrop-filter:blur(4px); align-items:center; justify-content:center; }
  .modal-overlay.open { display:flex; }
  .modal-box { background:var(--surface); border:1px solid var(--border); border-radius:20px; width:min(740px,95vw); max-height:85vh; overflow:hidden; display:flex; flex-direction:column; animation:slideUp .3s ease; }
  @keyframes slideUp { from{opacity:0;transform:translateY(30px)} to{opacity:1;transform:translateY(0)} }
  .modal-header { display:flex; align-items:center; justify-content:space-between; padding:1.25rem 1.5rem; border-bottom:1px solid var(--border); }
  .modal-header h3 { font-family:'Playfair Display',serif; font-size:1.15rem; font-weight:700; margin:0; }
  .modal-close { width:32px; height:32px; border-radius:8px; background:var(--surface2); border:1px solid var(--border); color:var(--muted); cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:1rem; transition:color .2s; }
  .modal-close:hover { color:var(--text); }
  .modal-body { padding:1.5rem; overflow-y:auto; flex:1; }
  .modal-body h2 { font-family:'Playfair Display',serif; font-size:1.1rem; color:#818cf8; border-bottom:1px solid var(--border); padding-bottom:.4rem; margin:1rem 0 .5rem; }
  .modal-body strong { color:#0ea5e9; }
  .modal-body code { background:rgba(99,102,241,.1); border:1px solid var(--border); border-radius:4px; padding:.1em .35em; font-size:.85em; color:#818cf8; }
  .modal-body blockquote { border-left:3px solid #6366f1; margin:.75rem 0; padding:.5rem 1rem; background:rgba(99,102,241,.06); border-radius:0 8px 8px 0; color:var(--muted); font-style:italic; }
  .modal-footer { padding:1rem 1.5rem; border-top:1px solid var(--border); display:flex; gap:.6rem; justify-content:flex-end; }

  @media(max-width:576px){ .topics-grid{grid-template-columns:1fr} .stats-row{gap:.75rem} }

  /* Exam calendar */
  .cal-card { position:relative; z-index:1; background:var(--surface); border:1px solid var(--border); border-radius:20px; padding:1.5rem 1.6rem 1.7rem; margin-bottom:1rem; }
  .cal-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.1rem; }
  .cal-title { display:flex; align-items:center; gap:.6rem; }
  .cal-title strong { font-family:'Playfair Display',serif; font-size:1.05rem; font-weight:700; }
  .cal-nav { display:flex; align-items:center; gap:.5rem; }
  .cal-nav-btn { width:32px; height:32px; border-radius:9px; border:1px solid var(--border); background:var(--surface2); color:var(--text); display:flex; align-items:center; justify-content:center; cursor:pointer; transition:border-color .2s,color .2s; }
  .cal-nav-btn:hover { border-color:#6366f1; color:#818cf8; }
  .cal-month-label { font-size:.85rem; font-weight:700; min-width:118px; text-align:center; }
  .cal-today-btn { font-size:.72rem; font-weight:700; color:#818cf8; background:rgba(99,102,241,.1); border:1px solid rgba(99,102,241,.25); border-radius:8px; padding:.3rem .7rem; cursor:pointer; }

  .cal-weekdays { display:grid; grid-template-columns:repeat(7,1fr); gap:6px; margin-bottom:6px; }
  .cal-weekdays span { text-align:center; font-size:.68rem; font-weight:700; letter-spacing:.05em; text-transform:uppercase; color:var(--muted); }

  .cal-grid { display:grid; grid-template-columns:repeat(7,1fr); gap:6px; }
  .cal-day { aspect-ratio:1/1; display:flex; flex-direction:column; align-items:center; justify-content:center; border-radius:11px; font-size:.82rem; font-weight:600; color:var(--text); background:var(--surface2); border:1px solid transparent; position:relative; cursor:default; transition:transform .15s,border-color .2s; }
  .cal-day.empty { background:transparent; }
  .cal-day.is-today { border-color:#0ea5e9; box-shadow:0 0 0 2px rgba(14,165,233,.15) inset; }
  .cal-day.has-exam { background:linear-gradient(135deg,rgba(99,102,241,.16),rgba(236,72,153,.14)); border-color:rgba(99,102,241,.35); cursor:pointer; }
  .cal-day.has-exam:hover { transform:translateY(-2px); border-color:#818cf8; }
  .cal-day .exam-dot { position:absolute; bottom:6px; width:5px; height:5px; border-radius:50%; background:#ec4899; }
  .cal-day .exam-count { position:absolute; top:3px; right:5px; font-size:.55rem; font-weight:800; color:#ec4899; }

  .cal-legend { display:flex; align-items:center; gap:.6rem; margin-top:1rem; font-size:.72rem; color:var(--muted); }
  .cal-legend .dot-swatch { width:12px; height:12px; border-radius:4px; background:linear-gradient(135deg,rgba(99,102,241,.5),rgba(236,72,153,.5)); border:1px solid rgba(99,102,241,.35); }

  .cal-popover { position:absolute; z-index:20; min-width:230px; max-width:280px; background:var(--surface); border:1px solid var(--border); border-radius:14px; padding:.9rem 1rem; box-shadow:0 18px 45px rgba(0,0,0,.25); display:none; }
  .cal-popover.open { display:block; }
  .cal-popover h4 { font-family:'Playfair Display',serif; font-size:.88rem; font-weight:700; margin:0 0 .5rem; }
  .cal-popover .exam-row { display:flex; align-items:center; justify-content:space-between; gap:.5rem; padding:.4rem 0; border-top:1px solid var(--border); }
  .cal-popover .exam-row:first-of-type { border-top:none; }
  .cal-popover .exam-row .er-name { font-size:.78rem; font-weight:600; }
  .cal-popover .exam-row .er-sub { font-size:.68rem; color:var(--muted); }
  .cal-popover .exam-row .er-score { font-size:.75rem; font-weight:800; padding:.15rem .5rem; border-radius:7px; }
  .cal-popover .er-score.good { background:rgba(34,197,94,.14); color:#22c55e; }
  .cal-popover .er-score.mid { background:rgba(245,158,11,.14); color:#fbbf24; }
  .cal-popover .er-score.low { background:rgba(239,68,68,.14); color:#ef4444; }
  .cal-popover-close { position:absolute; top:.5rem; right:.6rem; background:none; border:none; color:var(--muted); cursor:pointer; font-size:.85rem; }

  @media(max-width:576px){ .cal-day{font-size:.72rem;} }
  @media (min-width: 992px) {
  .cal-card {
    max-width: 560px;
    margin-left: auto;
    margin-right: auto;
  }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<!-- Mesh -->
<div class="mesh"><div class="mesh-blob"></div><div class="mesh-blob"></div></div>

<!-- Hero -->
<section class="progress-hero">
  <div class="container">
    <div class="eyebrow">📊 Your learning journey</div>
    <h1 class="hero-title">My <span class="hl">Progress</span></h1>
    <p style="color:var(--muted);font-size:.95rem;">Every topic you've studied is saved here — review and download your notes anytime.</p>
    <div class="stats-row">
      <div class="stat-pill">
        <div class="stat-icon" style="background:rgba(99,102,241,.12);color:#818cf8;">📚</div>
        <div><div class="stat-num"><?= $total_topics ?></div><div class="stat-lbl">Topics Studied</div></div>
      </div>
      <div class="stat-pill">
        <div class="stat-icon" style="background:rgba(34,197,94,.12);color:#22c55e;">🔥</div>
        <div><div class="stat-num"><?= $streak ?></div><div class="stat-lbl">Day Streak</div></div>
      </div>
      <div class="stat-pill">
        <div class="stat-icon" style="background:rgba(245,158,11,.12);color:#fbbf24;">⚡</div>
        <div><div class="stat-num"><?= $today_count ?></div><div class="stat-lbl">Studied Today</div></div>
      </div>
      <div class="stat-pill">
        <div class="stat-icon" style="background:rgba(236,72,153,.12);color:#ec4899;">📝</div>
        <div><div class="stat-num"><?= $total_exams ?></div><div class="stat-lbl">Exams Taken</div></div>
      </div>
    </div>
  </div>
</section>

<!-- Exam Calendar -->
<section style="position:relative;z-index:1;margin-bottom:2.5rem;">
  <div class="container">
    <div class="sec-head"><h2>🗓️ Exam Calendar</h2></div>
    <div class="cal-card">
      <div class="cal-head">
        <div class="cal-title"><i class="bi bi-mortarboard-fill" style="color:#818cf8;"></i><strong>Days you appeared for an exam</strong></div>
        <div class="cal-nav">
          <button class="cal-nav-btn" id="calPrev" onclick="calChangeMonth(-1)"><i class="bi bi-chevron-left"></i></button>
          <span class="cal-month-label" id="calMonthLabel">—</span>
          <button class="cal-nav-btn" id="calNext" onclick="calChangeMonth(1)"><i class="bi bi-chevron-right"></i></button>
          <button class="cal-today-btn" onclick="calGoToday()">Today</button>
        </div>
      </div>
      <div class="cal-weekdays">
        <span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span>
      </div>
      <div class="cal-grid" id="calGrid" style="position:relative;"></div>
      <div class="cal-legend"><span class="dot-swatch"></span> Day you took an exam — click it to see details</div>
    </div>
  </div>
</section>

<!-- Topics list -->
<section style="position:relative;z-index:1;margin-bottom:3rem;">
  <div class="container">

    <?php if ($total_topics > 0): ?>
    <div class="sec-head"><h2>📖 Topics You've Learned</h2></div>

    <!-- Search -->
    <div class="search-wrap">
      <i class="bi bi-search search-icon"></i>
      <input type="text" class="search-input" id="searchInput" placeholder="Search your topics…" oninput="filterTopics()">
    </div>

    <div class="topics-grid" id="topicsGrid">
      <?php foreach ($topics as $i => $t): ?>
      <?php
        $icons = ['📖','🧪','🔬','📐','🌍','⚗️','🧬','📜','🎯','🧲','💡','🌱'];
        $icon = $icons[$i % count($icons)];
        $date_fmt = date('d M Y, h:i A', strtotime($t['learned_at']));
      ?>
      <div class="topic-card" data-name="<?= htmlspecialchars(strtolower($t['topic_name'])) ?>">
        <div class="card-top">
          <div class="topic-icon"><?= $icon ?></div>
          <div>
            <div class="topic-name"><?= htmlspecialchars($t['topic_name']) ?></div>
            <div class="topic-date"><i class="bi bi-clock"></i><?= $date_fmt ?></div>
          </div>
        </div>
        <div class="card-actions">
          <a href="generate_pdf.php?id=<?= $t['id'] ?>" class="btn-pdf btn-pdf-primary" target="_blank">
            <i class="bi bi-file-earmark-pdf"></i> Download PDF
          </a>
          <button class="btn-pdf btn-view" onclick='viewTopic(<?= $t["id"] ?>, <?= json_encode($t["topic_name"]) ?>)'>
            <i class="bi bi-eye"></i> View
          </button>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <p class="text-center mt-3" id="noResults" style="display:none;color:var(--muted);">No topics match your search.</p>

    <?php else: ?>
    <div class="empty-box">
      <div class="empty-icon">📭</div>
      <h3 style="font-family:'Playfair Display',serif;font-weight:700;margin-bottom:.5rem;">No topics yet!</h3>
      <p>Start learning a topic and it will automatically appear here.</p>
      <a href="learn.php" class="btn-start"><i class="bi bi-stars"></i> Learn Your First Topic</a>
    </div>
    <?php endif; ?>

  </div>
</section>

<!-- View Modal -->
<div class="modal-overlay" id="viewModal" onclick="if(event.target===this)closeModal()">
  <div class="modal-box">
    <div class="modal-header">
      <h3 id="modalTitle">Topic</h3>
      <button class="modal-close" onclick="closeModal()"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="modal-body" id="modalBody">Loading…</div>
    <div class="modal-footer">
      <a id="modalPdfLink" href="#" class="btn-pdf btn-pdf-primary" target="_blank"><i class="bi bi-file-earmark-pdf"></i> Download PDF</a>
      <button class="btn-pdf btn-view" onclick="closeModal()"><i class="bi bi-x"></i> Close</button>
    </div>
  </div>
</div>

<script src="script.js"></script>
<script>
function filterTopics() {
  const q = document.getElementById('searchInput').value.toLowerCase();
  const cards = document.querySelectorAll('#topicsGrid .topic-card');
  let visible = 0;
  cards.forEach(c => {
    const match = c.dataset.name.includes(q);
    c.style.display = match ? '' : 'none';
    if (match) visible++;
  });
  document.getElementById('noResults').style.display = visible === 0 ? 'block' : 'none';
}

async function viewTopic(id, name) {
  document.getElementById('modalTitle').textContent = name;
  document.getElementById('modalPdfLink').href = 'generate_pdf.php?id=' + id;
  document.getElementById('modalBody').innerHTML = '<p style="color:var(--muted);text-align:center;padding:2rem 0">Loading…</p>';
  document.getElementById('viewModal').classList.add('open');
  document.body.style.overflow = 'hidden';

  try {
    const res  = await fetch('php_action/get_topic_content.php?id=' + id);
    const data = await res.json();
    if (data.success) {
      document.getElementById('modalBody').innerHTML = marked.parse(data.ai_response);
    } else {
      document.getElementById('modalBody').innerHTML = '<p style="color:#ef4444">Failed to load content.</p>';
    }
  } catch(e) {
    document.getElementById('modalBody').innerHTML = '<p style="color:#ef4444">Error: ' + e.message + '</p>';
  }
}

function closeModal() {
  document.getElementById('viewModal').classList.remove('open');
  document.body.style.overflow = '';
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

/* ---------- Exam Calendar ---------- */
const EXAMS_BY_DATE = <?= json_encode($exams_by_date, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
const CAL_MONTH_NAMES = ['January','February','March','April','May','June','July','August','September','October','November','December'];
let calViewYear, calViewMonth; // 0-indexed month
const CAL_TODAY = new Date();

function calInit() {
  calViewYear = CAL_TODAY.getFullYear();
  calViewMonth = CAL_TODAY.getMonth();
  calRender();
}

function calChangeMonth(delta) {
  calCloseAllPopovers();
  calViewMonth += delta;
  if (calViewMonth < 0) { calViewMonth = 11; calViewYear--; }
  else if (calViewMonth > 11) { calViewMonth = 0; calViewYear++; }
  calRender();
}

function calGoToday() {
  calCloseAllPopovers();
  calViewYear = CAL_TODAY.getFullYear();
  calViewMonth = CAL_TODAY.getMonth();
  calRender();
}

function calPad(n) { return n < 10 ? '0' + n : '' + n; }

function calRender() {
  document.getElementById('calMonthLabel').textContent = CAL_MONTH_NAMES[calViewMonth] + ' ' + calViewYear;

  const grid = document.getElementById('calGrid');
  grid.innerHTML = '';

  const firstDay = new Date(calViewYear, calViewMonth, 1).getDay();
  const daysInMonth = new Date(calViewYear, calViewMonth + 1, 0).getDate();
  const todayStr = CAL_TODAY.getFullYear() + '-' + calPad(CAL_TODAY.getMonth() + 1) + '-' + calPad(CAL_TODAY.getDate());

  for (let i = 0; i < firstDay; i++) {
    const blank = document.createElement('div');
    blank.className = 'cal-day empty';
    grid.appendChild(blank);
  }

  for (let day = 1; day <= daysInMonth; day++) {
    const dateStr = calViewYear + '-' + calPad(calViewMonth + 1) + '-' + calPad(day);
    const exams = EXAMS_BY_DATE[dateStr] || [];
    const cell = document.createElement('div');
    cell.className = 'cal-day' + (exams.length ? ' has-exam' : '') + (dateStr === todayStr ? ' is-today' : '');
    cell.textContent = day;

    if (exams.length) {
      if (exams.length > 1) {
        const countEl = document.createElement('span');
        countEl.className = 'exam-count';
        countEl.textContent = exams.length;
        cell.appendChild(countEl);
      }
      const dot = document.createElement('span');
      dot.className = 'exam-dot';
      cell.appendChild(dot);
      cell.onclick = (ev) => calShowPopover(ev, dateStr, exams);
    }
    grid.appendChild(cell);
  }
}

function calEsc(str) {
  const d = document.createElement('div');
  d.textContent = str ?? '';
  return d.innerHTML;
}

function calScoreClass(pct) {
  if (pct >= 75) return 'good';
  if (pct >= 50) return 'mid';
  return 'low';
}

function calCloseAllPopovers() {
  document.querySelectorAll('.cal-popover').forEach(p => p.remove());
}

function calShowPopover(ev, dateStr, exams) {
  ev.stopPropagation();
  calCloseAllPopovers();

  const pop = document.createElement('div');
  pop.className = 'cal-popover open';

  const dateObj = new Date(dateStr + 'T00:00:00');
  const niceDate = dateObj.toLocaleDateString('en-IN', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });

  let rows = exams.map(e => `
    <div class="exam-row">
      <div>
        <div class="er-name">${calEsc(e.subject)}${e.class ? ' · Class ' + calEsc(e.class) : ''}</div>
        <div class="er-sub">${calEsc(e.time)} · ${e.correct}/${e.total} correct</div>
      </div>
      <div class="er-score ${calScoreClass(e.score)}">${e.score}%</div>
    </div>
  `).join('');

  pop.innerHTML = `<button class="cal-popover-close" onclick="calCloseAllPopovers()"><i class="bi bi-x-lg"></i></button>
    <h4>${niceDate}</h4>${rows}`;

  document.querySelector('.cal-card').appendChild(pop);

  const cellRect = ev.currentTarget.getBoundingClientRect();
  const cardRect = document.querySelector('.cal-card').getBoundingClientRect();
  let left = cellRect.left - cardRect.left;
  const maxLeft = cardRect.width - pop.offsetWidth - 10;
  if (left > maxLeft) left = Math.max(10, maxLeft);
  pop.style.left = left + 'px';
  pop.style.top = (cellRect.bottom - cardRect.top + 8) + 'px';
}

document.addEventListener('click', (e) => {
  if (!e.target.closest('.cal-popover') && !e.target.closest('.cal-day')) calCloseAllPopovers();
});

calInit();
</script>

<?php require_once 'components/footer.php'; ?>