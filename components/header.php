<?php
/**
 * components/header.php
 * Include this at the top of every page AFTER session_start().
 *
 * Variables used from the including page:
 *   $page_title  — <title> tag text  (default: 'AI Home Tutor')
 *   $active_page — which nav link is active: 'home'|'dashboard'|'learn'|'exam'|'account'
 *
 * Session vars read:
 *   $_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_email']
 */

$page_title  = $page_title  ?? 'AI Home Tutor';
$active_page = $active_page ?? '';
$is_logged_in = isset($_SESSION['user_id']);
$user_name   = $is_logged_in ? htmlspecialchars($_SESSION['user_name']) : '';
$user_initial = $is_logged_in ? strtoupper(mb_substr($_SESSION['user_name'], 0, 1)) : '';
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($page_title) ?> — AI Home Tutor</title>

  <!-- Bootstrap 5.3 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
  <!-- Shared CSS -->
  <link href="css/style.css" rel="stylesheet"/>
  <!-- Shared Theme JS (runs immediately for no flash) -->
  <script>
    (function(){
      var t = localStorage.getItem('ai_tutor_theme') || 'light';
      document.documentElement.setAttribute('data-bs-theme', t);
    })();
  </script>
</head>
<body>

<!-- ═══════════════════════════════════════════════
     SHARED HEADER / NAVBAR
═══════════════════════════════════════════════ -->
<header class="site-header navbar navbar-expand-lg">
  <div class="container">

    <!-- Brand -->
    <a class="navbar-brand" href="<?= $is_logged_in ? 'dashboard.php' : 'index.php' ?>">
      <span class="brand-icon">🤖</span>
      <span class="brand-name">AI <em>Home Tutor</em></span>
    </a>

    <!-- Mobile theme pill + toggler -->
    <div class="d-flex align-items-center gap-2 d-lg-none ms-auto me-2">
      <button class="theme-pill" aria-label="Toggle theme">
        <span class="pill-track"><span class="pill-knob">☀️</span></span>
      </button>
    </div>

    <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse" data-bs-target="#siteNav"
            aria-controls="siteNav" aria-expanded="false">
      <i class="bi bi-list" style="color:var(--text);font-size:1.5rem;"></i>
    </button>

    <div class="collapse navbar-collapse site-nav" id="siteNav">

      <?php if ($is_logged_in): ?>
        <!-- ── Logged-in nav ── -->
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1">
          <li class="nav-item">
            <a class="nav-link <?= $active_page==='dashboard'?'active':'' ?>" href="dashboard.php">
              <i class="bi bi-grid me-1"></i>Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $active_page==='learn'?'active':'' ?>" href="learn.php">
              <i class="bi bi-book me-1"></i>Learn
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $active_page==='exam'?'active':'' ?>" href="mcq_test.php">
              <i class="bi bi-pencil-square me-1"></i>Exam
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $active_page==='progress'?'active':'' ?>" href="progress.php">
              <i class="bi bi-bar-chart me-1"></i>Progress
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $active_page==='account'?'active':'' ?>" href="account.php">
              <i class="bi bi-person-circle me-1"></i>My Account
            </a>
          </li>
        </ul>

        <!-- Desktop right side -->
        <div class="d-none d-lg-flex align-items-center gap-3">
          <button class="theme-pill" aria-label="Toggle theme">
            <span class="pill-track"><span class="pill-knob">☀️</span></span>
            <span class="pill-label">Dark</span>
          </button>
          <a href="account.php" class="user-chip">
            <span class="u-avatar"><?= $user_initial ?></span>
            <span><?= $user_name ?></span>
          </a>
          <a href="php_action/logout.php" class="btn-logout">
            <i class="bi bi-box-arrow-right me-1"></i>Logout
          </a>
        </div>

        <!-- Mobile right side -->
        <div class="d-lg-none mt-2 d-flex flex-wrap align-items-center gap-2">
          <a href="account.php" class="user-chip">
            <span class="u-avatar"><?= $user_initial ?></span>
            <span><?= $user_name ?></span>
          </a>
          <a href="php_action/logout.php" class="btn-logout">
            <i class="bi bi-box-arrow-right me-1"></i>Logout
          </a>
        </div>

      <?php else: ?>
        <!-- ── Guest nav ── -->
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1">
          <li class="nav-item">
            <a class="nav-link <?= $active_page==='home'?'active':'' ?>" href="index.php">
              <i class="bi bi-house me-1"></i>Home
            </a>
          </li>
          <li class="nav-item"><a class="nav-link" href="about.php"><i class="bi bi-people me-1"></i>About Us</a></li>
          <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-book me-1"></i>Courses</a></li>
          <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-envelope me-1"></i>Contact Us</a></li>
        </ul>
        <div class="d-none d-lg-flex align-items-center gap-3">
          <button class="theme-pill" aria-label="Toggle theme">
            <span class="pill-track"><span class="pill-knob">☀️</span></span>
            <span class="pill-label">Dark</span>
          </button>
          <a href="login.php"    class="btn-auth"><i class="bi bi-box-arrow-in-right me-1"></i>Log In</a>
          <a href="register.php" class="btn-auth" style="background:var(--accent);color:#fff;">
            <i class="bi bi-person-plus me-1"></i>Sign Up
          </a>
        </div>
        <div class="d-lg-none mt-2 d-flex gap-2">
          <a href="login.php"    class="btn-auth"><i class="bi bi-box-arrow-in-right me-1"></i>Log In</a>
          <a href="register.php" class="btn-auth"><i class="bi bi-person-plus me-1"></i>Sign Up</a>
        </div>
      <?php endif; ?>

    </div>
  </div>
</header>
<!-- END HEADER -->