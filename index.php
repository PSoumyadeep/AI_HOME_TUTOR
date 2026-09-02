<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php'); exit;
}
$flash_error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
$page_title  = 'Welcome';
$active_page = 'home';
require_once 'components/header.php';
?>
<link href="style.css" rel="stylesheet"/>
<style>
  .hero { position:relative; z-index:1; padding:5rem 0 4rem; }
  .hero-heading { font-family:'Playfair Display',serif; font-weight:900; font-size:clamp(2rem,5vw,3.25rem); line-height:1.15; color:var(--text); }
  .hero-heading .hl { background:linear-gradient(135deg,var(--accent) 0%,var(--accent2) 100%); -webkit-background-clip:text; -webkit-text-fill-color:transparent; display:inline; }
  .hero-sub { font-size:1.05rem; color:var(--muted); margin-top:1rem; line-height:1.7; }
  .benefit-list { list-style:none; padding:0; margin:2rem 0 0; display:flex; flex-direction:column; gap:.85rem; }
  .benefit-list li { display:flex; align-items:flex-start; gap:14px; font-size:.95rem; color:var(--text); animation:fadeUp .5s var(--ease) both; }
  .benefit-list li:nth-child(1){animation-delay:.1s} .benefit-list li:nth-child(2){animation-delay:.2s} .benefit-list li:nth-child(3){animation-delay:.3s}
  .hi-icon { width:38px; height:38px; border-radius:10px; background:var(--surface2); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; font-size:1rem; flex-shrink:0; }
  .hi-text strong { color:var(--accent); }
  .hi-text small  { display:block; color:var(--muted); font-size:.8rem; margin-top:1px; }

  /* Login Card */
  .login-card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--card-shadow); overflow:hidden; transition:background .4s var(--ease),border-color .4s var(--ease); animation:fadeUp .6s .1s var(--ease) both; }
  .login-card-header { background:linear-gradient(135deg,var(--accent) 0%,var(--accent2) 100%); padding:1.75rem 2rem; position:relative; overflow:hidden; }
  .login-card-header::before { content:''; position:absolute; width:200px; height:200px; border-radius:50%; background:rgba(255,255,255,.08); top:-80px; right:-50px; }
  .login-card-header h2 { font-family:'Playfair Display',serif; font-weight:700; font-size:1.35rem; color:#fff; position:relative; }
  .login-card-header p  { color:rgba(255,255,255,.8); font-size:.875rem; margin-top:.3rem; position:relative; }
  .login-card-body { padding:2rem; }
  .form-group { margin-bottom:1.1rem; }
  .form-group label { display:block; font-size:.78rem; font-weight:600; letter-spacing:.04em; text-transform:uppercase; color:var(--muted); margin-bottom:5px; }
  .form-control { width:100%; padding:.65rem .9rem; font-size:.9rem; font-family:'Plus Jakarta Sans',sans-serif; border-radius:10px; border:1.5px solid var(--border); background:var(--surface2); color:var(--text); outline:none; transition:border-color .2s,box-shadow .2s,background .3s; }
  .form-control::placeholder { color:var(--muted); opacity:.7; }
  .form-control:focus { border-color:var(--accent); box-shadow:0 0 0 3.5px var(--accent-glow); background:var(--surface); }
  .pw-wrap { position:relative; }
  .pw-wrap .form-control { padding-right:2.5rem; }
  .pw-eye { position:absolute; right:10px; top:50%; transform:translateY(-50%); background:none; border:none; color:var(--muted); cursor:pointer; font-size:.95rem; transition:color .2s; padding:0; }
  .pw-eye:hover { color:var(--accent); }
  .forgot-row { display:flex; justify-content:flex-end; margin-top:.3rem; }
  .forgot-link { font-size:.78rem; font-weight:600; color:var(--muted); text-decoration:none; transition:color .2s; }
  .forgot-link:hover { color:var(--accent); }
  .btn-login-submit { width:100%; padding:.78rem; border-radius:12px; border:none; background:linear-gradient(135deg,var(--accent) 0%,var(--accent2) 100%); color:#fff; font-family:'Plus Jakarta Sans',sans-serif; font-size:1rem; font-weight:700; cursor:pointer; transition:opacity .2s,transform .15s,box-shadow .2s; box-shadow:0 6px 20px var(--accent-glow); margin-top:.5rem; }
  .btn-login-submit:hover { opacity:.9; transform:translateY(-2px); }
  .btn-login-submit:active { transform:translateY(0); }
  .alert-flash { padding:.7rem 1rem; border-radius:10px; font-size:.875rem; font-weight:600; margin-bottom:1rem; background:rgba(239,68,68,.12); border:1px solid rgba(239,68,68,.3); color:#ef4444; }
  .or-divider { display:flex; align-items:center; gap:.75rem; margin:1.25rem 0; color:var(--muted); font-size:.8rem; font-weight:600; text-transform:uppercase; letter-spacing:.06em; }
  .or-divider::before,.or-divider::after { content:''; flex:1; height:1px; background:var(--border); }
  @media(max-width:768px){ .hero{padding:3rem 0 2rem} .login-card-body{padding:1.25rem} }
</style>

<main class="hero">
  <div class="container">
    <div class="row align-items-center g-5">

      <!-- LEFT: Hero content -->
      <div class="col-lg-6">
        <p style="font-size:.8rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--accent);margin-bottom:.75rem;">
          <i class="bi bi-stars me-1"></i>Welcome Back
        </p>
        <h1 class="hero-heading">
          Continue Your<br>
          <span class="hl">Learning Journey</span><br>
          Right Where<br>You Left Off
        </h1>
        
        <ul class="benefit-list">
          <li>
            <span class="hi-icon">📊</span>
            <div class="hi-text">
              <strong>Your Progress Dashboard</strong> awaits
              <small>See exactly how much you've improved since last time</small>
            </div>
          </li>
          <li>
            <span class="hi-icon">💬</span>
            <div class="hi-text">
              <strong>Pick Up Doubt Sessions</strong> instantly
              <small>All your past questions and answers saved &amp; searchable</small>
            </div>
          </li>
          <li>
            <span class="hi-icon">🏆</span>
            <div class="hi-text">
              <strong>Resume Mock Tests</strong> in progress
              <small>Timed tests auto-save so you never lose your work</small>
            </div>
          </li>
        </ul>
      </div>

      <!-- RIGHT: Login card -->
      <div class="col-lg-6">
        <div class="login-card">
          <div class="login-card-header">
            <h2>👋 Welcome Back!</h2>
            <p>Sign in to your AI Home Tutor account</p>
          </div>
          <div class="login-card-body">

            <?php if ($flash_error): ?>
              <div class="alert-flash">
                <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($flash_error) ?>
              </div>
            <?php endif; ?>

            <form id="loginForm" method="POST" action="php_action/server.php">
              <input type="hidden" name="action" value="login">

              <div class="form-group">
                <label for="email"><i class="bi bi-envelope me-1"></i>Email Address</label>
                <input class="form-control" type="email" id="email" name="email"
                       placeholder="you@example.com" required
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"/>
              </div>

              <div class="form-group">
                <label for="password"><i class="bi bi-lock me-1"></i>Password</label>
                <div class="pw-wrap">
                  <input class="form-control" type="password" id="password" name="password"
                         placeholder="Enter your password" required/>
                  <button type="button" class="pw-eye" onclick="togglePw('password','eyeLogin')">
                    <i class="bi bi-eye" id="eyeLogin"></i>
                  </button>
                </div>
                <div class="forgot-row">
                  <a href="#" class="forgot-link">Forgot password?</a>
                </div>
              </div>

              <button type="submit" class="btn-login-submit">
                <i class="bi bi-box-arrow-in-right me-2"></i>Log In to My Account
              </button>
            </form>

            <div class="or-divider">or</div>

            <p style="text-align:center;font-size:.875rem;color:var(--muted);margin:0;">
              New to AI Home Tutor?
              <a href="register.php" style="color:var(--accent);font-weight:700;text-decoration:none;">Create a free account →</a>
            </p>

          </div>
        </div>
      </div>

    </div>
  </div>
</main>
<script src="script.js"></script>
<script>
function togglePw(fieldId, iconId) {
  const f = document.getElementById(fieldId), i = document.getElementById(iconId);
  if (f.type === 'password') { f.type = 'text'; i.className = 'bi bi-eye-slash'; }
  else { f.type = 'password'; i.className = 'bi bi-eye'; }
}
<?php if ($flash_error): ?>
const card = document.querySelector('.login-card');
card.style.animation = 'shake .4s ease';
<?php endif; ?>
</script>
<style>
@keyframes shake {
  0%,100%{transform:translateX(0)} 20%{transform:translateX(-8px)}
  40%{transform:translateX(8px)}  60%{transform:translateX(-5px)} 80%{transform:translateX(5px)}
}
</style>

<?php require_once 'components/footer.php'; ?>