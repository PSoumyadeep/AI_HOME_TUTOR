<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
require_once 'php_action/db.php';

$user_class = $_SESSION['user_class'] ?? '';
$page_title  = 'Video Library';
$active_page = 'videos';
require_once 'components/header.php';

// Default: class-relevant videos from cache
$stmt = $conn->prepare("SELECT * FROM class_videos WHERE user_class = ? ORDER BY fetched_at DESC LIMIT 12");
$stmt->bind_param('s', $user_class);
$stmt->execute();
$default_videos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
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

  .mesh { position:fixed; inset:0; z-index:0; pointer-events:none; overflow:hidden; }
  .mesh-blob { position:absolute; border-radius:50%; filter:blur(120px); opacity:.15; animation:blobFloat 18s ease-in-out infinite alternate; }
  .mesh-blob:nth-child(1){width:600px;height:600px;background:#f97316;top:-200px;right:-100px;}
  .mesh-blob:nth-child(2){width:500px;height:500px;background:#ef4444;bottom:-150px;left:-100px;animation-delay:-6s;}
  [data-bs-theme="light"] .mesh-blob { opacity:.05; }
  @keyframes blobFloat { from{transform:translate(0,0) scale(1)} to{transform:translate(30px,40px) scale(1.08)} }

  .videos-hero { position:relative; z-index:1; padding:3rem 0 2rem; }
  .eyebrow { font-size:.72rem; font-weight:700; letter-spacing:.12em; text-transform:uppercase; color:var(--muted); margin-bottom:.6rem; }
  .hero-title { font-family:'Playfair Display',serif; font-weight:900; font-size:clamp(1.8rem,4vw,2.6rem); line-height:1.15; margin-bottom:.5rem; }
  .hero-title .hl { background:linear-gradient(135deg,#f97316,#ef4444); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
  .hero-sub { color:var(--muted); font-size:.95rem; margin-bottom:1.75rem; }

  .search-bar-wrap { display:flex; gap:.6rem; background:var(--surface); border:1.5px solid var(--border); border-radius:14px; padding:.5rem .5rem .5rem 1.1rem; max-width:640px; transition:border-color .2s,box-shadow .2s; }
  .search-bar-wrap:focus-within { border-color:#f97316; box-shadow:0 0 0 4px rgba(249,115,22,.15); }
  .search-bar-wrap input { flex:1; background:none; border:none; outline:none; font-family:'Plus Jakarta Sans',sans-serif; font-size:.95rem; color:var(--text); }
  .search-bar-wrap input::placeholder { color:var(--muted); }
  .btn-search { background:linear-gradient(135deg,#f97316,#ef4444); color:#fff; border:none; border-radius:10px; padding:.6rem 1.4rem; font-weight:700; font-size:.88rem; cursor:pointer; display:flex; align-items:center; gap:6px; transition:opacity .2s,transform .15s; white-space:nowrap; }
  .btn-search:hover { opacity:.9; transform:translateY(-1px); }

  .sec-head { display:flex; align-items:center; gap:12px; margin:2rem 0 1.25rem; position:relative; z-index:1; }
  .sec-head h2 { font-family:'Playfair Display',serif; font-weight:700; font-size:1.3rem; margin:0; }
  .sec-head::after { content:''; flex:1; height:1px; background:linear-gradient(to right,var(--border),transparent); }

  .videos-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:1.25rem; position:relative; z-index:1; }
  .video-card { background:var(--surface); border:1px solid var(--border); border-radius:16px; overflow:hidden; text-decoration:none; color:var(--text); display:flex; flex-direction:column; transition:transform .25s,box-shadow .25s,border-color .25s; animation:cardIn .4s ease both; }
  .video-card:hover { transform:translateY(-4px); box-shadow:0 16px 40px rgba(249,115,22,.15); border-color:rgba(249,115,22,.35); color:var(--text); }
  @keyframes cardIn { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
  .video-thumb-wrap { position:relative; aspect-ratio:16/9; overflow:hidden; background:var(--surface2); }
  .video-thumb-wrap img { width:100%; height:100%; object-fit:cover; }
  .play-overlay { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; background:rgba(0,0,0,0); transition:background .2s; }
  .video-card:hover .play-overlay { background:rgba(0,0,0,.25); }
  .play-overlay i { font-size:2.2rem; color:#fff; opacity:0; transition:opacity .2s; filter:drop-shadow(0 2px 8px rgba(0,0,0,.4)); }
  .video-card:hover .play-overlay i { opacity:1; }
  .video-info { padding:.9rem 1.1rem 1.1rem; }
  .video-title { font-size:.9rem; font-weight:700; line-height:1.4; margin-bottom:.35rem; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
  .video-channel { font-size:.78rem; color:var(--muted); }

  .empty-box, .loading-box { position:relative; z-index:1; text-align:center; padding:3rem 1rem; background:var(--surface); border:1px solid var(--border); border-radius:20px; color:var(--muted); }
  .loading-box .spinner { width:36px; height:36px; margin:0 auto 1rem; border:4px solid var(--border); border-top-color:#f97316; border-radius:50%; animation:spin .8s linear infinite; }
  @keyframes spin { to{transform:rotate(360deg)} }

  @media(max-width:576px){ .videos-grid{grid-template-columns:1fr} .search-bar-wrap{flex-wrap:wrap} }
</style>

<div class="mesh"><div class="mesh-blob"></div><div class="mesh-blob"></div></div>

<section class="videos-hero">
  <div class="container">
    <div class="eyebrow">▶️ Video library</div>
    <h1 class="hero-title">Watch &amp; <span class="hl">Learn</span></h1>
    <p class="hero-sub">Search any topic, or browse videos picked for <?= htmlspecialchars($user_class) ?>.</p>

    <div class="search-bar-wrap">
      <i class="bi bi-search" style="color:var(--muted);align-self:center;"></i>
      <input type="text" id="searchInput" placeholder="Search e.g. Photosynthesis, Trigonometry, Newton's Laws…" autocomplete="off"/>
      <button class="btn-search" id="searchBtn"><i class="bi bi-play-circle"></i> Search</button>
    </div>
  </div>
</section>

<section style="position:relative;z-index:1;margin-bottom:3rem;">
  <div class="container">

    <div class="sec-head" id="sectionHeadTitle"><h2>📌 Recommended for <?= htmlspecialchars($user_class) ?></h2></div>

    <div id="loadingBox" class="loading-box" style="display:none;">
      <div class="spinner"></div>
      <p>Searching YouTube…</p>
    </div>

    <div id="videosGrid" class="videos-grid">
      <?php if (empty($default_videos)): ?>
        <div class="empty-box" style="grid-column:1/-1;">
          <div style="font-size:2.5rem;opacity:.35;margin-bottom:.75rem;">📭</div>
          <p>No recommended videos yet for your class. Try searching above!</p>
        </div>
      <?php else: ?>
        <?php foreach ($default_videos as $v): ?>
          <a href="https://www.youtube.com/watch?v=<?= urlencode($v['youtube_video_id']) ?>" target="_blank" class="video-card">
            <div class="video-thumb-wrap">
              <img src="<?= htmlspecialchars($v['thumbnail_url']) ?>" alt="<?= htmlspecialchars($v['title']) ?>" loading="lazy">
              <div class="play-overlay"><i class="bi bi-play-circle-fill"></i></div>
            </div>
            <div class="video-info">
              <div class="video-title"><?= htmlspecialchars($v['title']) ?></div>
              <div class="video-channel"><?= htmlspecialchars($v['channel_title']) ?></div>
            </div>
          </a>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </div>
</section>

<script src="script.js"></script>
<script>
const searchInput = document.getElementById('searchInput');
const searchBtn   = document.getElementById('searchBtn');
const videosGrid  = document.getElementById('videosGrid');
const loadingBox  = document.getElementById('loadingBox');
const sectionHead = document.getElementById('sectionHeadTitle');

function cardHtml(v) {
  return `<a href="https://www.youtube.com/watch?v=${encodeURIComponent(v.video_id)}" target="_blank" class="video-card">
    <div class="video-thumb-wrap">
      <img src="${v.thumbnail}" alt="${escapeHtml(v.title)}" loading="lazy">
      <div class="play-overlay"><i class="bi bi-play-circle-fill"></i></div>
    </div>
    <div class="video-info">
      <div class="video-title">${escapeHtml(v.title)}</div>
      <div class="video-channel">${escapeHtml(v.channel_title)}</div>
    </div>
  </a>`;
}
function escapeHtml(str) {
  const d = document.createElement('div');
  d.textContent = str;
  return d.innerHTML;
}

async function runSearch() {
  const q = searchInput.value.trim();
  if (!q) return;

  sectionHead.innerHTML = `<h2>🔍 Results for "${escapeHtml(q)}"</h2>`;
  videosGrid.style.display = 'none';
  loadingBox.style.display = 'block';

  try {
    const res  = await fetch('php_action/search_youtube.php?q=' + encodeURIComponent(q));
    const data = await res.json();

    loadingBox.style.display = 'none';
    videosGrid.style.display = 'grid';

    if (data.error || !data.videos || data.videos.length === 0) {
      videosGrid.innerHTML = `<div class="empty-box" style="grid-column:1/-1;">
        <div style="font-size:2.5rem;opacity:.35;margin-bottom:.75rem;">🔍</div>
        <p>No videos found. Try a different search term.</p>
      </div>`;
      return;
    }
    videosGrid.innerHTML = data.videos.map(cardHtml).join('');
  } catch (e) {
    loadingBox.style.display = 'none';
    videosGrid.style.display = 'grid';
    videosGrid.innerHTML = `<div class="empty-box" style="grid-column:1/-1;"><p>Something went wrong: ${escapeHtml(e.message)}</p></div>`;
  }
}

searchBtn.addEventListener('click', runSearch);
searchInput.addEventListener('keydown', e => { if (e.key === 'Enter') runSearch(); });
</script>

<?php require_once 'components/footer.php'; ?>