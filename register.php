<?php
session_start();
if (isset($_SESSION['user_id'])) { header('Location: dashboard.php'); exit; }
require_once 'php_action/db.php';

// AJAX email check
if ($_SERVER["REQUEST_METHOD"] == "POST" && ($_POST["action"] ?? '') === "check_email") {
    header('Content-Type: application/json');
    $email = trim($_POST["email"] ?? "");
    $stmt  = $conn->prepare("SELECT id FROM users WHERE user_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    echo json_encode(["exists" => $stmt->num_rows > 0]);
    $stmt->close(); $conn->close(); exit;
}

$flash_error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
$page_title  = 'Register';
$active_page = '';
require_once 'components/header.php';
?>
<link href="style.css" rel="stylesheet"/>
<style>
  .hero { position:relative; z-index:1; padding:5rem 0 4rem; }
  .hero-heading { font-family:'Playfair Display',serif; font-weight:900; font-size:clamp(2rem,5vw,3.25rem); line-height:1.15; }
  .hero-heading .hl { background:linear-gradient(135deg,var(--accent) 0%,var(--accent2) 100%); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
  .hero-sub { font-size:1.05rem; color:var(--muted); margin-top:1rem; line-height:1.7; }
  .highlight-list { list-style:none; padding:0; margin:2rem 0 0; display:flex; flex-direction:column; gap:.85rem; }
  .highlight-list li { display:flex; align-items:flex-start; gap:14px; font-size:.95rem; }
  .hi-icon { width:38px; height:38px; border-radius:10px; background:var(--surface2); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; font-size:1rem; flex-shrink:0; }
  .hi-text strong { color:var(--accent); }
  .hi-text small  { display:block; color:var(--muted); font-size:.8rem; }
  .stats-row { display:flex; gap:2rem; margin-top:2.5rem; flex-wrap:wrap; }
  .stat-num { font-family:'Playfair Display',serif; font-size:1.8rem; font-weight:900; color:var(--accent); }
  .stat-label { font-size:.78rem; color:var(--muted); }

  /* Register card */
  .reg-card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--card-shadow); overflow:hidden; animation:fadeUp .6s .1s var(--ease) both; }
  .reg-card-header { background:linear-gradient(135deg,var(--accent) 0%,var(--accent2) 100%); padding:1.5rem 2rem; position:relative; overflow:hidden; }
  .reg-card-header::before { content:''; position:absolute; width:200px; height:200px; border-radius:50%; background:rgba(255,255,255,.08); top:-80px; right:-50px; }
  .reg-card-header h2 { font-family:'Playfair Display',serif; font-weight:700; font-size:1.25rem; color:#fff; position:relative; }
  .reg-card-header p  { color:rgba(255,255,255,.8); font-size:.85rem; margin-top:.25rem; position:relative; }
  .reg-card-body { padding:1.75rem 2rem; }
  .form-row { display:flex; gap:.75rem; }
  .form-row>* { flex:1; min-width:0; }
  .form-group { margin-bottom:1rem; }
  .form-group label { display:block; font-size:.78rem; font-weight:600; letter-spacing:.04em; text-transform:uppercase; color:var(--muted); margin-bottom:5px; }
  .form-control,.form-select { width:100%; padding:.6rem .9rem; font-size:.9rem; font-family:'Plus Jakarta Sans',sans-serif; border-radius:10px; border:1.5px solid var(--border); background:var(--surface2); color:var(--text); outline:none; transition:border-color .2s,box-shadow .2s,background .3s; appearance:none; }
  .form-control::placeholder { color:var(--muted); opacity:.7; }
  .form-control:focus,.form-select:focus { border-color:var(--accent); box-shadow:0 0 0 3.5px var(--accent-glow); background:var(--surface); }
  .select-wrap { position:relative; }
  .select-wrap::after { content:'\f282'; font-family:'bootstrap-icons'; position:absolute; right:12px; top:50%; transform:translateY(-50%); color:var(--muted); pointer-events:none; font-size:.85rem; }
  .select-wrap .form-select { padding-right:2rem; }
  .pw-wrap { position:relative; }
  .pw-wrap .form-control { padding-right:2.5rem; }
  .pw-eye { position:absolute; right:10px; top:50%; transform:translateY(-50%); background:none; border:none; color:var(--muted); cursor:pointer; font-size:.95rem; transition:color .2s; padding:0; }
  .pw-eye:hover { color:var(--accent); }
  .btn-register { width:100%; padding:.75rem; border-radius:12px; border:none; background:linear-gradient(135deg,var(--accent) 0%,var(--accent2) 100%); color:#fff; font-family:'Plus Jakarta Sans',sans-serif; font-size:1rem; font-weight:700; cursor:pointer; transition:opacity .2s,transform .15s,box-shadow .2s; box-shadow:0 6px 20px var(--accent-glow); margin-top:.5rem; }
  .btn-register:hover { opacity:.9; transform:translateY(-2px); }
  .alert-flash { padding:.7rem 1rem; border-radius:10px; font-size:.875rem; font-weight:600; margin-bottom:1rem; background:rgba(239,68,68,.12); border:1px solid rgba(239,68,68,.3); color:#ef4444; }
  /* Email popup */
  .email-popup-backdrop { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); backdrop-filter:blur(4px); z-index:9999; align-items:center; justify-content:center; }
  .email-popup-backdrop.show { display:flex; }
  .email-popup { background:var(--surface); border:1px solid var(--border); border-radius:20px; padding:2rem; max-width:380px; width:90%; text-align:center; box-shadow:0 24px 60px rgba(0,0,0,.35); animation:popIn .3s var(--ease); }
  @keyframes popIn { from{opacity:0;transform:scale(.88) translateY(20px)} to{opacity:1;transform:scale(1) translateY(0)} }
  .popup-btn-row { display:flex; gap:.6rem; margin-top:1rem; }
  .popup-btn { flex:1; padding:.6rem; border-radius:10px; font-size:.875rem; font-weight:700; cursor:pointer; border:none; transition:opacity .2s; }
  .popup-btn.primary { background:linear-gradient(135deg,var(--accent),var(--accent2)); color:#fff; }
  .popup-btn.secondary { background:var(--surface2); border:1.5px solid var(--border); color:var(--muted); }
  @media(max-width:768px){ .hero{padding:3rem 0 2rem} .form-row{flex-direction:column;gap:0} .reg-card-body{padding:1.25rem} }
</style>

<main class="hero">
  <div class="container">
    <div class="row align-items-center g-5">

      <!-- LEFT -->
      <div class="col-lg-6">
        <p style="font-size:.8rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--accent);margin-bottom:.75rem;">
          <i class="bi bi-stars me-1"></i>Powered by AI
        </p>
        <h1 class="hero-heading">
          Your Personal<br>
          <span class="hl">AI Home Tutor</span><br>
          Ask Anything,<br>Learn Everything
        </h1>
        <p class="hero-sub">From doubt solving to full-length tests — get instant, curriculum-aligned help for every subject, any class. 📚</p>
        <ul class="highlight-list">
          <li><span class="hi-icon">🤖</span><div class="hi-text"><strong>100% Doubt Solving</strong> in any subject<small>Ask in text, get step-by-step answers instantly</small></div></li>
          <li><span class="hi-icon">📘</span><div class="hi-text"><strong>100+ Questions</strong> for practice<small>Curated question banks by chapter &amp; difficulty</small></div></li>
          <li><span class="hi-icon">📝</span><div class="hi-text"><strong>Give Your Test</strong> and track progress<small>Timed mock tests with detailed performance reports</small></div></li>
          <li><span class="hi-icon">📄</span><div class="hi-text"><strong>Solutions for Test Papers</strong> instantly<small>Previous year &amp; sample papers with full solutions</small></div></li>
        </ul>
        <div class="stats-row">
          <div><div class="stat-num">10K+</div><div class="stat-label">Students learning</div></div>
          <div><div class="stat-num">50+</div><div class="stat-label">Subjects covered</div></div>
          <div><div class="stat-num">4.9★</div><div class="stat-label">Average rating</div></div>
        </div>
      </div>

      <!-- RIGHT: Register card -->
      <div class="col-lg-6">
        <div class="reg-card">
          <div class="reg-card-header">
            <h2>🎓 Start Learning Today</h2>
            <p>Register now and begin your learning journey!</p>
          </div>
          <div class="reg-card-body">

            <?php if ($flash_error): ?>
              <div class="alert-flash"><i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($flash_error) ?></div>
            <?php endif; ?>

            <form id="courseForm" method="POST" action="php_action/server.php">
              <input type="hidden" name="action" value="register">

              <!-- Class only (board removed) -->
              <div class="form-group">
                <label for="class"><i class="bi bi-mortarboard me-1"></i>Class</label>
                <div class="select-wrap">
                  <select class="form-select" id="class" name="class" required>
                    <option value="" disabled selected>Select your class…</option>
                    <?php for($i=1;$i<=12;$i++) echo "<option value='class$i'>Class $i</option>"; ?>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label for="name"><i class="bi bi-person me-1"></i>Full Name</label>
                <input class="form-control" type="text" id="name" name="name" placeholder="Aarav Sharma" required/>
              </div>
              <div class="form-group">
                <label for="phone"><i class="bi bi-phone me-1"></i>Phone Number</label>
                <input class="form-control" type="tel" id="phone" name="phone" placeholder="+91 98765 43210"/>
              </div>
              <div class="form-group">
                <label for="email"><i class="bi bi-envelope me-1"></i>Email Address</label>
                <input class="form-control" type="email" id="email" name="email" placeholder="you@example.com" required/>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label for="password"><i class="bi bi-lock me-1"></i>Password</label>
                  <div class="pw-wrap">
                    <input class="form-control" type="password" id="password" name="password" placeholder="Min. 8 chars" required/>
                    <button type="button" class="pw-eye" onclick="togglePw('password','eye1')"><i class="bi bi-eye" id="eye1"></i></button>
                  </div>
                </div>
                <div class="form-group">
                  <label for="confirm_password"><i class="bi bi-lock-fill me-1"></i>Confirm</label>
                  <div class="pw-wrap">
                    <input class="form-control" type="password" id="confirm_password" name="confirm_password" placeholder="Repeat" required/>
                    <button type="button" class="pw-eye" onclick="togglePw('confirm_password','eye2')"><i class="bi bi-eye" id="eye2"></i></button>
                  </div>
                </div>
              </div>

              <button type="submit" class="btn-register">
                <i class="bi bi-rocket-takeoff me-2"></i>Register Now — It's Free
              </button>
            </form>

            <p style="text-align:center;margin-top:1rem;font-size:.85rem;color:var(--muted);">
              Already have an account?
              <a href="index.php" style="color:var(--accent);font-weight:600;text-decoration:none;">Log in here →</a>
            </p>
          </div>
        </div>
      </div>

    </div>
  </div>
</main>

<!-- Email already exists popup -->
<div class="email-popup-backdrop" id="emailPopupBackdrop">
  <div class="email-popup">
    <div style="font-size:2.8rem;margin-bottom:.75rem;">⚠️</div>
    <h3 style="font-family:'Playfair Display',serif;font-size:1.2rem;font-weight:700;color:var(--text);margin-bottom:.4rem;">Email Already Registered</h3>
    <p style="font-size:.88rem;color:var(--muted);">An account with <span id="popupEmailShown" style="font-weight:700;color:var(--accent);"></span> already exists.</p>
    <div class="popup-btn-row">
      <button class="popup-btn primary" onclick="window.location.href='index.php'"><i class="bi bi-box-arrow-in-right me-1"></i>Log In</button>
      <button class="popup-btn secondary" onclick="closePopup()"><i class="bi bi-arrow-left me-1"></i>Go Back</button>
    </div>
  </div>
</div>
<script src="script.js"></script>
<script>
function togglePw(fieldId, iconId) {
  const f = document.getElementById(fieldId), i = document.getElementById(iconId);
  if (f.type === 'password') { f.type = 'text'; i.className = 'bi bi-eye-slash'; }
  else { f.type = 'password'; i.className = 'bi bi-eye'; }
}
function showPopup(email) {
  document.getElementById('popupEmailShown').textContent = email;
  document.getElementById('emailPopupBackdrop').classList.add('show');
}
function closePopup() {
  document.getElementById('emailPopupBackdrop').classList.remove('show');
  const ef = document.getElementById('email'); ef.value = ''; ef.focus();
}
document.getElementById('emailPopupBackdrop').addEventListener('click', function(e) { if(e.target===this) closePopup(); });

document.getElementById('courseForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const email = document.getElementById('email').value.trim();
  const fd = new FormData(); fd.append('action','check_email'); fd.append('email',email);
  try {
    const res = await fetch('register.php', { method:'POST', body:fd });
    const data = await res.json();
    if (data.exists) { showPopup(email); } else { this.submit(); }
  } catch { this.submit(); }
});
</script>

<?php require_once 'components/footer.php'; ?>