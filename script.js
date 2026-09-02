/* js/theme.js — shared theme toggle + user display logic */

(function () {
  const html = document.documentElement;

  function getTheme() { return localStorage.getItem('ai_tutor_theme') || 'light'; }

  function applyTheme(t) {
    html.setAttribute('data-bs-theme', t);
    localStorage.setItem('ai_tutor_theme', t);
    document.querySelectorAll('.pill-knob').forEach(k => k.textContent = t === 'dark' ? '🌙' : '☀️');
    document.querySelectorAll('.pill-label').forEach(l => l.textContent = t === 'dark' ? 'Light' : 'Dark');
  }

  function toggleTheme() {
    applyTheme(html.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark');
  }

  // Apply on load
  applyTheme(getTheme());

  // Wire buttons after DOM ready
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.theme-pill').forEach(btn => {
      btn.addEventListener('click', toggleTheme);
    });

    // Populate user chip if session name is embedded
    const nameEl = document.getElementById('headerUserName');
    const initEl = document.getElementById('headerUserInitial');
    if (nameEl && nameEl.dataset.name) {
      nameEl.textContent = nameEl.dataset.name;
    }
    if (initEl && initEl.dataset.initial) {
      initEl.textContent = initEl.dataset.initial;
    }
  });
})();