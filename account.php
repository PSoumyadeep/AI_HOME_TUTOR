<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
require_once 'php_action/db.php';

// Fetch fresh data from DB
$stmt = $conn->prepare("SELECT user_name, user_email, user_class,  user_phone, registration_date FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user   = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$user) { header('Location: php_action/logout.php'); exit; }

// Update session with fresh data
$_SESSION['user_name']  = $user['user_name'];
$_SESSION['user_email'] = $user['user_email'];
$_SESSION['user_class'] = $user['user_class'];

$_SESSION['user_phone'] = $user['user_phone'];

$reg_date = date('d M Y', strtotime($user['registration_date']));
$initial  = strtoupper(mb_substr($user['user_name'], 0, 1));

$page_title  = 'My Account';
$active_page = 'account';
require_once 'components/header.php';
?>
<link href="style.css" rel="stylesheet"/>
<style>
  [data-bs-theme="dark"] { --bg:#07090f; --surface:#111827; --surface2:#1a2335; --border:rgba(255,255,255,.07); --muted:#6b7a99; }
  [data-bs-theme="light"] { --bg:#f4f6fb; --surface:#ffffff; --surface2:#eef1fa; --border:rgba(0,0,0,.07); --muted:#6b7a99; }
  body { background:var(--bg); }

  .account-wrap { position:relative; z-index:1; padding:3rem 0 4rem; }

  /* Profile hero card */
  .profile-hero {
    background:linear-gradient(135deg,#6366f1 0%,#0ea5e9 60%,#14b8a6 100%);
    border-radius:24px; padding:2.5rem 2rem; color:#fff;
    display:flex; align-items:center; gap:2rem; flex-wrap:wrap;
    margin-bottom:2rem; position:relative; overflow:hidden;
    box-shadow:0 20px 60px rgba(99,102,241,.35);
    animation:fadeUp .5s var(--ease) both;
  }
  .profile-hero::before { content:''; position:absolute; width:250px; height:250px; border-radius:50%; background:rgba(255,255,255,.07); top:-80px; right:-60px; }
  .profile-hero::after  { content:''; position:absolute; width:160px; height:160px; border-radius:50%; background:rgba(255,255,255,.05); bottom:-60px; left:120px; }

  .avatar-big {
    width:90px; height:90px; border-radius:50%; flex-shrink:0;
    background:rgba(255,255,255,.2); border:3px solid rgba(255,255,255,.4);
    display:flex; align-items:center; justify-content:center;
    font-size:2.2rem; font-weight:900; color:#fff; position:relative; z-index:1;
  }
  .profile-info { position:relative; z-index:1; }
  .profile-info h1 { font-family:'Playfair Display',serif; font-weight:900; font-size:1.9rem; margin-bottom:.2rem; }
  .profile-info p  { opacity:.85; font-size:.92rem; margin:0; }
  .profile-badges { display:flex; gap:.5rem; flex-wrap:wrap; margin-top:.75rem; }
  .profile-badge  { background:rgba(255,255,255,.2); border:1px solid rgba(255,255,255,.3); border-radius:50px; padding:3px 12px; font-size:.75rem; font-weight:700; }

  /* Info cards */
  .info-card { background:var(--surface); border:1px solid var(--border); border-radius:20px; padding:1.75rem; margin-bottom:1.5rem; animation:fadeUp .5s .1s var(--ease) both; }
  .info-card h3 { font-family:'Playfair Display',serif; font-weight:700; font-size:1.1rem; margin-bottom:1.25rem; display:flex; align-items:center; gap:8px; }
  .info-card h3 i { color:var(--accent2); }

  .info-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:1rem; }
  .info-item { background:var(--surface2); border:1px solid var(--border); border-radius:12px; padding:1rem 1.25rem; }
  .info-label { font-size:.72rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--muted); margin-bottom:.35rem; }
  .info-value { font-size:.97rem; font-weight:600; color:var(--text); word-break:break-all; }
  .info-value.accent { color:var(--accent2); }

  /* Stats row inside account */
  .acct-stats { display:grid; grid-template-columns:repeat(auto-fill,minmax(150px,1fr)); gap:1rem; }
  .acct-stat  { background:var(--surface2); border:1px solid var(--border); border-radius:12px; padding:1.1rem; text-align:center; }
  .acct-stat .num { font-family:'Playfair Display',serif; font-size:1.8rem; font-weight:900; color:var(--accent2); }
  .acct-stat .lbl { font-size:.75rem; color:var(--muted); margin-top:2px; }

  @keyframes fadeUp { from{opacity:0;transform:translateY(18px)} to{opacity:1;transform:translateY(0)} }
  @media(max-width:576px){ .profile-hero{flex-direction:column;gap:1rem;text-align:center} .profile-badges{justify-content:center} }
</style>

<div class="account-wrap">
  <div class="container">

    <!-- Profile hero -->
    <div class="profile-hero">
      <div class="avatar-big"><?= $initial ?></div>
      <div class="profile-info">
        <h1><?= htmlspecialchars($user['user_name']) ?></h1>
        <p><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($user['user_email']) ?></p>
        <div class="profile-badges">
          <?php if ($user['user_class']): ?>
            <span class="profile-badge">📚 <?= htmlspecialchars(ucwords(str_replace('class','Class ',$user['user_class']))) ?></span>
          <?php endif; ?>
          <span class="profile-badge">📅 Joined <?= $reg_date ?></span>
        </div>
      </div>
    </div>

    <div class="row g-4">
      <!-- Personal Info -->
      <div class="col-lg-8">
        <div class="info-card">
          <h3><i class="bi bi-person-lines-fill"></i> Personal Information</h3>
          <div class="info-grid">
            <div class="info-item">
              <div class="info-label"><i class="bi bi-person me-1"></i>Full Name</div>
              <div class="info-value"><?= htmlspecialchars($user['user_name']) ?></div>
            </div>
            <div class="info-item">
              <div class="info-label"><i class="bi bi-envelope me-1"></i>Email Address</div>
              <div class="info-value accent"><?= htmlspecialchars($user['user_email']) ?></div>
            </div>
            <div class="info-item">
              <div class="info-label"><i class="bi bi-phone me-1"></i>Phone Number</div>
              <div class="info-value"><?= $user['user_phone'] ? htmlspecialchars($user['user_phone']) : '— Not provided' ?></div>
            </div>
            <div class="info-item">
              <div class="info-label"><i class="bi bi-calendar me-1"></i>Member Since</div>
              <div class="info-value"><?= $reg_date ?></div>
            </div>
          </div>
        </div>

        <div class="info-card">
          <h3><i class="bi bi-mortarboard-fill"></i> Academic Details</h3>
          <div class="info-grid">
            <div class="info-item">
              <div class="info-label"><i class="bi bi-book me-1"></i>Class</div>
              <div class="info-value accent"><?= $user['user_class'] ? htmlspecialchars(ucwords(str_replace('class','Class ',$user['user_class']))) : '— Not set' ?></div>
            </div>
          
          </div>
        </div>
      </div>

      <!-- Quick stats sidebar -->
      <div class="col-lg-4">
        <div class="info-card">
          <h3><i class="bi bi-bar-chart-fill"></i> My Learning Stats</h3>
          <div class="acct-stats">
            <div class="acct-stat">
              <div class="num" id="acctTopics">0</div>
              <div class="lbl">Topics Learned</div>
            </div>
            <div class="acct-stat">
              <div class="num">7</div>
              <div class="lbl">Day Streak 🔥</div>
            </div>
            <div class="acct-stat">
              <div class="num" id="acctExams">0</div>
              <div class="lbl">Exams Taken</div>
            </div>
            <div class="acct-stat">
              <div class="num">—</div>
              <div class="lbl">Best Score</div>
            </div>
          </div>
        </div>

        <div class="info-card" style="animation-delay:.2s;">
          <h3><i class="bi bi-gear-fill"></i> Account Actions</h3>
          <div class="d-flex flex-column gap-2">
            <a href="dashboard.php" class="btn w-100" style="background:linear-gradient(135deg,#6366f1,#0ea5e9);color:#fff;border-radius:10px;font-weight:600;padding:.65rem;">
              <i class="bi bi-grid me-2"></i>Go to Dashboard
            </a>
            <a href="learn.php" class="btn w-100" style="background:var(--surface2);border:1px solid var(--border);color:var(--text);border-radius:10px;font-weight:600;padding:.65rem;">
              <i class="bi bi-book me-2"></i>Start Learning
            </a>
            <a href="php_action/logout.php" class="btn w-100" style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#ef4444;border-radius:10px;font-weight:600;padding:.65rem;">
              <i class="bi bi-box-arrow-right me-2"></i>Logout
            </a>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- Link to an external JS file -->
<script src="script.js"></script>

<script>
// Pull stats from localStorage
document.getElementById('acctTopics').textContent = JSON.parse(localStorage.getItem('topics_history') || '[]').length;
document.getElementById('acctExams').textContent  = JSON.parse(localStorage.getItem('exams_taken') || '[]').length;
</script>

<?php require_once 'components/footer.php'; ?>