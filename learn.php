<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
$page_title  = 'Learn a Topic';
$active_page = 'learn';
require_once 'components/header.php';
?>

<link href="style.css" rel="stylesheet"/>
<style>
  /* ── CSS Variables override for learn page ─── */
  :root {
    --accent:  #22d3ee; --accent2: #0ea5e9; --accent3: #6366f1;
    --glow:    rgba(34,211,238,.18);
  }
  [data-bs-theme="dark"] {
    --bg:#080e14; --surface:#0f1e2d; --surface2:#162130;
    --border:rgba(34,211,238,.15); --text:#dff6ff; --muted:#5d8fa8;
    --tag-bg:rgba(34,211,238,.08); --msg-user:rgba(34,211,238,.1); --msg-bot:#111c27;
  }
  [data-bs-theme="light"] {
    --bg:#f0f8ff; --surface:#ffffff; --surface2:#e0f2fe;
    --border:rgba(14,165,233,.22); --text:#0a1929; --muted:#4a7a96;
    --tag-bg:rgba(14,165,233,.08); --msg-user:rgba(14,165,233,.12); --msg-bot:#ffffff;
  }
  body { background:var(--bg); display:flex; flex-direction:column; min-height:100vh; }

  /* Grid bg */
  body::before {
    content:''; position:fixed; inset:0;
    background-image:linear-gradient(var(--border) 1px,transparent 1px),linear-gradient(90deg,var(--border) 1px,transparent 1px);
    background-size:44px 44px; pointer-events:none; z-index:0; opacity:.35;
  }
  .orb { position:fixed; width:600px; height:600px; border-radius:50%; background:radial-gradient(circle,var(--glow) 0%,transparent 65%); top:-180px; right:-180px; pointer-events:none; z-index:0; animation:floatOrb 14s ease-in-out infinite alternate; }
  @keyframes floatOrb { from{transform:translate(0,0) scale(1)} to{transform:translate(-40px,60px) scale(1.1)} }

  /* ── Hero ── */
  .hero-section { position:relative; z-index:1; padding:3.5rem 0 2rem; text-align:center; }
  .hero-badge { display:inline-flex; align-items:center; gap:6px; background:var(--tag-bg); border:1px solid var(--border); border-radius:50px; padding:4px 14px; font-size:.73rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--accent); margin-bottom:1rem; }
  .hero-badge .dot { width:7px; height:7px; border-radius:50%; background:var(--accent); animation:blink 1.5s ease-in-out infinite; }
  @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.25} }
  .hero-title { font-family:'Playfair Display',serif; font-weight:900; font-size:clamp(1.9rem,5vw,3.2rem); line-height:1.12; margin-bottom:.9rem; }
  .hero-title .hl { background:linear-gradient(135deg,var(--accent),var(--accent2),var(--accent3)); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
  .hero-sub { color:var(--muted); font-size:.97rem; max-width:500px; margin:0 auto 2.25rem; line-height:1.7; }

  .topic-card { background:var(--surface); border:1px solid var(--border); border-radius:18px; padding:1.75rem; max-width:660px; margin:0 auto; box-shadow:0 20px 60px rgba(0,0,0,.25); position:relative; overflow:hidden; }
  .topic-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,var(--accent),var(--accent2),var(--accent3)); }
  .topic-label { font-size:.73rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--muted); margin-bottom:.6rem; display:block; }
  .topic-input-wrap { display:flex; gap:.6rem; background:var(--surface2); border:1.5px solid var(--border); border-radius:12px; padding:.45rem .45rem .45rem .9rem; transition:border-color .2s,box-shadow .2s; }
  .topic-input-wrap:focus-within { border-color:var(--accent); box-shadow:0 0 0 4px var(--glow); }
  .topic-input { flex:1; background:none; border:none; outline:none; font-family:'Plus Jakarta Sans',sans-serif; font-size:.97rem; font-weight:500; color:var(--text); }
  .topic-input::placeholder { color:var(--muted); }
  .btn-explain { background:linear-gradient(135deg,var(--accent),var(--accent2)); color:#041020; border:none; border-radius:9px; padding:.55rem 1.25rem; font-family:'Plus Jakarta Sans',sans-serif; font-weight:700; font-size:.88rem; cursor:pointer; display:flex; align-items:center; gap:6px; transition:opacity .2s,transform .15s,box-shadow .2s; white-space:nowrap; }
  .btn-explain:hover { opacity:.9; transform:translateY(-1px); box-shadow:0 8px 22px var(--glow); }
  .quick-topics { display:flex; flex-wrap:wrap; gap:.45rem; margin-top:1.1rem; justify-content:center; }
  .quick-chip { background:var(--tag-bg); border:1px solid var(--border); border-radius:50px; padding:.22rem .8rem; font-size:.77rem; font-weight:600; color:var(--muted); cursor:pointer; transition:background .2s,color .2s,border-color .2s; }
  .quick-chip:hover { background:var(--accent); border-color:var(--accent); color:#041020; }

  /* ── Chat Area ── */
  .chat-area { position:relative; z-index:1; flex:1; max-width:820px; width:100%; margin:2rem auto 0; padding:0 1rem; display:none; flex-direction:column; gap:1.25rem; }
  .chat-area.visible { display:flex; }
  .topic-bar { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.6rem; }
  .topic-tag { display:flex; align-items:center; gap:8px; background:var(--tag-bg); border:1px solid var(--border); border-radius:50px; padding:.35rem 1rem; font-size:.83rem; font-weight:700; color:var(--accent); }
  .action-btns { display:flex; gap:.4rem; }
  .icon-btn { width:32px; height:32px; border-radius:8px; border:1px solid var(--border); background:var(--surface2); color:var(--muted); display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:.85rem; transition:color .2s,background .2s,border-color .2s; }
  .icon-btn:hover { color:var(--accent); border-color:var(--accent); background:var(--tag-bg); }
  .chat-body { display:flex; flex-direction:column; gap:1rem; min-height:120px; padding-bottom:1rem; }

  /* Bubbles */
  .message { display:flex; gap:12px; align-items:flex-start; animation:msgIn .35s var(--ease) both; }
  @keyframes msgIn { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
  .user-message { flex-direction:row-reverse; }
  .bot-avatar { width:38px; height:38px; border-radius:50%; flex-shrink:0; fill:var(--accent); background:linear-gradient(135deg,var(--accent),var(--accent2)); padding:6px; box-shadow:0 0 14px var(--glow); }
  .user-avatar-msg { width:38px; height:38px; border-radius:50%; flex-shrink:0; background:linear-gradient(135deg,var(--accent3),#8b5cf6); display:flex; align-items:center; justify-content:center; font-size:.88rem; font-weight:700; color:#fff; }
  .message-text { background:var(--msg-bot); border:1px solid var(--border); border-radius:4px 16px 16px 16px; padding:1rem 1.25rem; font-size:.92rem; line-height:1.78; color:var(--text); max-width:78%; }
  .user-message .message-text { background:var(--msg-user); border-color:var(--accent); border-radius:16px 4px 16px 16px; font-weight:500; }
  .message-text h2 { font-family:'Playfair Display',serif; font-size:1.15rem; padding-bottom:.4rem; border-bottom:1px solid var(--border); margin:1rem 0 .5rem; }
  .message-text strong { color:var(--accent2); font-weight:700; }
  .message-text code { font-family:monospace; background:var(--tag-bg); border:1px solid var(--border); border-radius:5px; padding:.12em .4em; font-size:.85em; color:var(--accent); }
  .message-text p { margin-bottom:.85rem; }
  .message-text ul,.message-text ol { padding-left:1.2rem; margin-bottom:.85rem; }
  .message-text blockquote { border-left:3px solid var(--accent); margin:.75rem 0; padding:.6rem 1rem; background:var(--tag-bg); border-radius:0 8px 8px 0; color:var(--muted); font-style:italic; }
  .thinking-indicator { display:flex; align-items:center; gap:5px; padding:.3rem 0; }
  .thinking-indicator .dot { width:9px; height:9px; border-radius:50%; background:var(--accent); animation:bounce 1.2s ease-in-out infinite; }
  .thinking-indicator .dot:nth-child(1){animation-delay:0s} .thinking-indicator .dot:nth-child(2){animation-delay:.2s} .thinking-indicator .dot:nth-child(3){animation-delay:.4s}
  @keyframes bounce { 0%,60%,100%{transform:translateY(0);opacity:.35} 30%{transform:translateY(-8px);opacity:1} }

  /* Input bar */
  .input-wrapper { position:sticky; bottom:0; z-index:10; background:var(--bg); border-top:1px solid var(--border); padding:1rem 0 1.4rem; }
  .followup-label { font-size:.72rem; font-weight:700; letter-spacing:.09em; text-transform:uppercase; color:var(--muted); margin-bottom:.5rem; display:flex; align-items:center; gap:6px; }
  .followup-label::after { content:''; flex:1; height:1px; background:linear-gradient(to right,var(--border),transparent); }
  .chat-form { display:flex; gap:.6rem; align-items:flex-end; background:var(--surface); border:1.5px solid var(--border); border-radius:32px; padding:.55rem .55rem .55rem 1.1rem; transition:border-color .2s,box-shadow .2s; }
  .chat-form:focus-within { border-color:var(--accent); box-shadow:0 0 0 4px var(--glow); }
  .message-input { flex:1; background:none; border:none; outline:none; resize:none; font-family:'Plus Jakarta Sans',sans-serif; font-size:.93rem; color:var(--text); line-height:1.5; max-height:150px; overflow-y:auto; }
  .message-input::placeholder { color:var(--muted); }
  #mic-btn { width:36px; height:36px; border-radius:10px; flex-shrink:0; background:var(--surface2); border:1px solid var(--border); color:var(--muted); display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:.95rem; transition:color .2s,border-color .2s; }
  #mic-btn:hover,#mic-btn.listening { color:var(--accent); border-color:var(--accent); }
  #send-message { width:38px; height:38px; border-radius:12px; border:none; flex-shrink:0; background:linear-gradient(135deg,var(--accent),var(--accent2)); color:#041020; font-size:1rem; display:flex; align-items:center; justify-content:center; cursor:pointer; transition:opacity .2s,transform .15s; }
  #send-message:hover { opacity:.87; transform:scale(1.05); }
  .empty-state { text-align:center; padding:2.5rem 1rem; color:var(--muted); position:relative; z-index:1; }
  .toast-msg { position:fixed; bottom:5.5rem; right:1.5rem; background:var(--accent); color:#041020; font-weight:700; font-size:.82rem; padding:.5rem 1.1rem; border-radius:50px; opacity:0; transform:translateY(12px); transition:opacity .3s,transform .3s; z-index:9999; pointer-events:none; }
  .toast-msg.show { opacity:1; transform:translateY(0); }
  @media(max-width:600px){ .message-text{max-width:90%;font-size:.85rem} .topic-card{padding:1.1rem} }
</style>

<!-- Font Awesome for mic icon -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<div class="orb"></div>

<!-- Hero -->
<section class="hero-section" id="heroSection">
  <div class="container">
    <div class="hero-badge"><span class="dot"></span>AI-Powered Explanations</div>
    <h1 class="hero-title">What do you want to<br><span class="hl">learn today?</span></h1>
    <p class="hero-sub">Type any topic and your AI tutor will explain it clearly — with examples, key concepts, and a summary.</p>
    <div class="topic-card">
      <span class="topic-label"><i class="bi bi-lightbulb me-1"></i>Enter a topic to explain</span>
      <div class="topic-input-wrap">
        <input class="topic-input" id="topicInput" type="text" placeholder="e.g. Photosynthesis, Newton's Laws, World War II…" autocomplete="off"/>
        <button class="btn-explain" id="startBtn" onclick="startLesson()"><i class="bi bi-stars"></i> Explain It</button>
      </div>
      <div class="quick-topics">
        <span class="quick-chip" onclick="setTopic(this)">Photosynthesis</span>
        <span class="quick-chip" onclick="setTopic(this)">Newton's Laws</span>
        <span class="quick-chip" onclick="setTopic(this)">Pythagoras Theorem</span>
        <span class="quick-chip" onclick="setTopic(this)">French Revolution</span>
        <span class="quick-chip" onclick="setTopic(this)">DNA &amp; Genetics</span>
        <span class="quick-chip" onclick="setTopic(this)">Quadratic Equations</span>
        <span class="quick-chip" onclick="setTopic(this)">Periodic Table</span>
        <span class="quick-chip" onclick="setTopic(this)">Indian Independence</span>
      </div>
    </div>
  </div>
</section>

<!-- Chat area -->
<div class="chat-area" id="chatArea">
  <div class="topic-bar">
    <div class="topic-tag"><i class="bi bi-book-open"></i><span id="topicLabel">Topic</span></div>
    <div class="action-btns">
      <div class="icon-btn" title="Copy last answer" onclick="copyLast()"><i class="bi bi-clipboard"></i></div>
      <div class="icon-btn" title="Read aloud" id="readBtn" onclick="readAloud()"><i class="bi bi-volume-up"></i></div>
      <div class="icon-btn" title="New topic" onclick="resetAll()"><i class="bi bi-arrow-counterclockwise"></i></div>
    </div>
  </div>
  <div class="chat-body"></div>
</div>

<!-- Sticky input -->
<div class="input-wrapper" id="inputWrapper" style="display:none;">
  <div class="container" style="max-width:820px;">
    <div class="followup-label"><i class="bi bi-chat-dots me-1"></i>Ask a follow-up question</div>
    <form class="chat-form">
      <textarea class="message-input" placeholder="Ask anything about this topic…" rows="1"></textarea>
      <button type="button" id="mic-btn" title="Voice input"><i class="fas fa-microphone"></i></button>
      <button type="submit" id="send-message" title="Send"><i class="bi bi-send-fill"></i></button>
    </form>
  </div>
</div>

<div class="empty-state" id="emptyState">
  <div style="font-size:3.5rem;opacity:.3;margin-bottom:.75rem;">📚</div>
  <p>Enter a topic above and your AI tutor will explain it in detail.</p>
</div>

<div class="toast-msg" id="toastMsg">Copied!</div>
<script src="script.js"></script>
<script>
const API_KEY = "AIzaSyDJmendCvCKztWW-k_h0lh7yBE6Bcokg_o";
const API_URL = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent?key=${API_KEY}`;
const chatBody = document.querySelector(".chat-body");
const messageInput = document.querySelector(".message-input");
const userData = { message: null };
const chatHistory = [];
const initialInputHeight = messageInput.scrollHeight;

const createMessageElement = (content, ...classes) => {
  const div = document.createElement("div");
  div.classList.add("message", ...classes);
  div.innerHTML = content;
  return div;
};

/**
 * LEARN.PHP — UPDATED generateBotResponse FUNCTION
 * Replace the existing generateBotResponse function in learn.php with this one.
 * The only addition is the saveTopicToDB() call at the bottom of the try block.
 */

const generateBotResponse = async (incomingMessageDiv) => {
  const messageElement = incomingMessageDiv.querySelector(".message-text");
  chatHistory.push({ role: "user", parts: [{ text: userData.message }] });

  const requestOptions = {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ contents: chatHistory })
  };

  try {
    const response = await fetch(API_URL, requestOptions);
    const data = await response.json();
    if (!response.ok) throw new Error(data.error.message);

    const apiResponseText = data.candidates[0].content.parts[0].text.trim();
    messageElement.innerHTML = marked.parse(apiResponseText);
    chatHistory.push({ role: "model", parts: [{ text: apiResponseText }] });

    // ── Save topic + AI response to DB ───────────────────────────────────────
    const topicName = document.getElementById('topicLabel').textContent;
    saveTopicToDB(topicName, apiResponseText);

    // Keep localStorage count in sync too
    const history = JSON.parse(localStorage.getItem('topics_history') || '[]');
    if (!history.includes(topicName)) {
      history.push(topicName);
      localStorage.setItem('topics_history', JSON.stringify(history));
    }
    // ─────────────────────────────────────────────────────────────────────────

  } catch (error) {
    messageElement.innerText = error.message;
    messageElement.style.color = "#ef4444";
  } finally {
    incomingMessageDiv.classList.remove("thinking");
    chatBody.scrollTo({ top: chatBody.scrollHeight, behavior: "smooth" });
  }
};

/**
 * Saves topic name + AI response to the database via save_topic.php
 * Only called once per topic lesson (initial explanation).
 */
async function saveTopicToDB(topicName, aiResponse) {
  // Only save the first/full explanation, not follow-up Q&As
  // We detect this by checking if chatHistory length is small (first response)
  // chatHistory has: [user_prompt, model_response] = 2 entries at first save
  if (chatHistory.length > 4) return; // skip follow-up saves

  try {
    await fetch('php_action/save_topic.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        topic_name:  topicName,
        ai_response: aiResponse
      })
    });
    // Silent save — no UI disruption
  } catch (e) {
    console.warn('Topic save failed (non-critical):', e.message);
  }
}

const BOT_SVG = `<svg class="bot-avatar" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 1024 1024"><path d="M738.3 287.6H285.7c-59 0-106.8 47.8-106.8 106.8v303.1c0 59 47.8 106.8 106.8 106.8h81.5v111.1c0 .7.8 1.1 1.4.7l166.9-110.6 41.8-.8h117.4l43.6-.4c59 0 106.8-47.8 106.8-106.8V394.5c0-59-47.8-106.9-106.8-106.9zM351.7 448.2c0-29.5 23.9-53.5 53.5-53.5s53.5 23.9 53.5 53.5-23.9 53.5-53.5 53.5-53.5-23.9-53.5-53.5zm157.9 267.1c-67.8 0-123.8-47.5-132.3-109h264.6c-8.6 61.5-64.5 109-132.3 109zm110-213.7c-29.5 0-53.5-23.9-53.5-53.5s23.9-53.5 53.5-53.5 53.5 23.9 53.5 53.5-23.9 53.5-53.5 53.5zM867.2 644.5V453.1h26.5c19.4 0 35.1 15.7 35.1 35.1v121.1c0 19.4-15.7 35.1-35.1 35.1h-26.5zM95.2 609.4V488.2c0-19.4 15.7-35.1 35.1-35.1h26.5v191.3h-26.5c-19.4 0-35.1-15.7-35.1-35.1zM561.5 149.6c0 23.4-15.6 43.3-36.9 49.7v44.9h-30v-44.9c-21.4-6.5-36.9-26.3-36.9-49.7 0-28.6 23.3-51.9 51.9-51.9s51.9 23.3 51.9 51.9z"></path></svg>`;
const THINKING_BUBBLE = `${BOT_SVG}<div class="message-text"><div class="thinking-indicator"><div class="dot"></div><div class="dot"></div><div class="dot"></div></div></div>`;

const handleOutgoingMessage = (e) => {
  e.preventDefault();
  userData.message = messageInput.value.trim();
  if (!userData.message) return;
  messageInput.value = "";
  messageInput.dispatchEvent(new Event("input"));
  const initial = "<?= $user_initial ?>";
  const outgoing = createMessageElement(`<div class="user-avatar-msg">${initial}</div><div class="message-text"></div>`, "user-message");
  outgoing.querySelector(".message-text").textContent = userData.message;
  chatBody.appendChild(outgoing);
  chatBody.scrollTo({top:chatBody.scrollHeight,behavior:"smooth"});
  setTimeout(() => {
    const incoming = createMessageElement(THINKING_BUBBLE, "bot-message", "thinking");
    chatBody.appendChild(incoming);
    chatBody.scrollTo({top:chatBody.scrollHeight,behavior:"smooth"});
    generateBotResponse(incoming);
  }, 600);
};

messageInput.addEventListener("keydown", (e) => {
  if (e.key === "Enter" && e.target.value.trim() && !e.shiftKey) handleOutgoingMessage(e);
});
messageInput.addEventListener("input", () => {
  messageInput.style.height = `${initialInputHeight}px`;
  messageInput.style.height = `${messageInput.scrollHeight}px`;
  document.querySelector(".chat-form").style.borderRadius = messageInput.scrollHeight > initialInputHeight ? "15px" : "32px";
});
document.querySelector(".chat-form").addEventListener("submit", handleOutgoingMessage);

// Mic
const micBtn = document.getElementById("mic-btn");
const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
if (SR) {
  const recognition = new SR();
  recognition.lang = 'en-IN'; recognition.interimResults = false;
  let listening = false;
  micBtn.addEventListener("click", () => {
    if (!listening) { recognition.start(); listening=true; micBtn.classList.add("listening"); micBtn.innerHTML=`<i class='fas fa-pause'></i>`; }
    else { recognition.stop(); }
  });
  recognition.onresult = (e) => { messageInput.value = e.results[0][0].transcript; messageInput.dispatchEvent(new Event("input")); };
  recognition.onend = () => { listening=false; micBtn.classList.remove("listening"); micBtn.innerHTML=`<i class="fas fa-microphone"></i>`; };
} else { micBtn.disabled=true; }

function startLesson() {
  const topic = document.getElementById('topicInput').value.trim();
  if (!topic) { document.getElementById('topicInput').focus(); return; }
  document.getElementById('heroSection').style.display = 'none';
  document.getElementById('emptyState').style.display  = 'none';
  document.getElementById('chatArea').classList.add('visible');
  document.getElementById('inputWrapper').style.display = 'block';
  document.getElementById('topicLabel').textContent = topic;
  userData.message =
    `You are an expert AI tutor for Indian school students (Classes 6–12). ` +
    `Explain the topic using clear Markdown formatting:\n` +
    `- ## for main sections, ### for sub-sections\n- Bullet points for key points\n` +
    `- **Bold** key terms\n- > blockquotes for definitions\n- Real-life examples\n` +
    `- End with a ## 💡 Quick Summary section\n\n` +
    `Topic: "${topic}"\n\nCover: 1) What it is  2) How it works / key concepts  3) Real-life examples  4) Important points  5) Quick summary`;
  setTimeout(() => {
    const div = createMessageElement(THINKING_BUBBLE, "bot-message", "thinking");
    chatBody.appendChild(div);
    chatBody.scrollTo({top:chatBody.scrollHeight,behavior:"smooth"});
    generateBotResponse(div);
  }, 300);
}

function setTopic(el) { document.getElementById('topicInput').value = el.textContent; startLesson(); }

function copyLast() {
  const msgs = chatBody.querySelectorAll('.bot-message .message-text');
  if (!msgs.length) return;
  navigator.clipboard.writeText(msgs[msgs.length-1].innerText).then(() => showToast('Copied!'));
}
let isSpeaking = false;
function readAloud() {
  const msgs = chatBody.querySelectorAll('.bot-message .message-text');
  if (!msgs.length) return;
  const btn = document.getElementById('readBtn');
  if (isSpeaking) { window.speechSynthesis.cancel(); isSpeaking=false; btn.innerHTML='<i class="bi bi-volume-up"></i>'; return; }
  const utt = new SpeechSynthesisUtterance(msgs[msgs.length-1].innerText);
  utt.lang='en-IN'; utt.rate=0.95;
  utt.onend=()=>{ isSpeaking=false; btn.innerHTML='<i class="bi bi-volume-up"></i>'; };
  window.speechSynthesis.speak(utt); isSpeaking=true; btn.innerHTML='<i class="bi bi-stop-fill"></i>';
}
function resetAll() {
  window.speechSynthesis?.cancel(); chatHistory.length=0; chatBody.innerHTML='';
  document.getElementById('topicInput').value='';
  document.getElementById('heroSection').style.display='block';
  document.getElementById('emptyState').style.display='block';
  document.getElementById('chatArea').classList.remove('visible');
  document.getElementById('inputWrapper').style.display='none';
  window.scrollTo({top:0,behavior:'smooth'});
}
function showToast(msg) {
  const t = document.getElementById('toastMsg'); t.textContent=msg; t.classList.add('show');
  setTimeout(()=>t.classList.remove('show'),2000);
}
document.getElementById('topicInput').addEventListener('keydown', e => { if(e.key==='Enter') startLesson(); });
</script>

<?php require_once 'components/footer.php'; ?>