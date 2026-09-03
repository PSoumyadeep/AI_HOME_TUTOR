<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
$page_title  = 'MCQ Exam';
$active_page = 'exam';
require_once 'components/header.php';
?>

<link href="style.css" rel="stylesheet"/>
<style>
/* Override to mcq dark/light palette */
[data-bs-theme="dark"] {
  --bg:#0d0f1a; --surface:#13162b; --card:#181c35; --border:#252a4a;
  --accent:#4f8ef7; --accent2:#a78bfa; --text:#e2e8f0; --muted:#7c85b0;
  --correct:#22c55e; --wrong:#ef4444; --shadow:0 8px 40px rgba(0,0,0,.45);
}
[data-bs-theme="light"] {
  --bg:#f0f4ff; --surface:#ffffff; --card:#f8faff; --border:#dde3f5;
  --text:#1a1f3c; --muted:#5a6285; --shadow:0 8px 40px rgba(79,142,247,.12);
}
body { background:var(--bg); font-family:'Sora',sans-serif; }
@import url('https://fonts.googleapis.com/css2?family=Sora:wght@300;400;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap');

main { position:relative; z-index:1; max-width:860px; margin:0 auto; padding:40px 20px 80px; }

.selector-card { background:var(--surface); border:1px solid var(--border); border-radius:24px; padding:36px 32px; box-shadow:var(--shadow); margin-bottom:32px; }
.selector-card h1 { font-size:1.65rem; font-weight:800; letter-spacing:-.5px; margin-bottom:6px; }
.selector-card h1 span { background:linear-gradient(135deg,var(--accent),var(--accent2)); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
.selector-card p { color:var(--muted); font-size:.9rem; margin-bottom:28px; }
.selectors { display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; margin-bottom:24px; }
@media(max-width:600px){ .selectors{grid-template-columns:1fr} }
.field label { display:block; font-size:.72rem; font-weight:600; letter-spacing:.08em; text-transform:uppercase; color:var(--muted); margin-bottom:8px; }
.field select { width:100%; background:var(--card); border:1.5px solid var(--border); border-radius:12px; padding:12px 14px; color:var(--text); font-size:.9rem; appearance:none; cursor:pointer; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%237c85b0' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 14px center; transition:border-color .2s,box-shadow .2s; }
.field select:focus { outline:none; border-color:var(--accent); box-shadow:0 0 0 3px rgba(79,142,247,.15); }
.field select:disabled { opacity:.4; cursor:not-allowed; }

.chapter-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:4px; max-height:280px; overflow-y:auto; padding-right:4px; }
.ch-item { display:flex; align-items:flex-start; gap:10px; background:var(--card); border:1.5px solid var(--border); border-radius:10px; padding:10px 12px; cursor:pointer; transition:border-color .2s,background .2s; user-select:none; }
.ch-item:hover { border-color:var(--accent); }
.ch-item.selected { border-color:var(--accent); background:rgba(79,142,247,.08); }
.ch-item input[type=checkbox] { accent-color:var(--accent); margin-top:2px; flex-shrink:0; }
.ch-item span { font-size:.82rem; line-height:1.4; }
.chapter-section { margin-top:20px; display:none; }
.chapter-section.visible { display:block; }
.chapter-section>label { display:block; font-size:.72rem; font-weight:600; letter-spacing:.08em; text-transform:uppercase; color:var(--muted); margin-bottom:10px; }
.ch-actions { display:flex; gap:8px; margin-bottom:10px; }
.ch-actions button { background:none; border:1px solid var(--border); border-radius:8px; padding:5px 12px; font-size:.75rem; color:var(--muted); cursor:pointer; transition:all .2s; }
.ch-actions button:hover { border-color:var(--accent); color:var(--accent); }
.q-count-pills { display:flex; gap:8px; flex-wrap:wrap; margin-top:8px; }
.q-pill { padding:7px 18px; border-radius:50px; font-size:.82rem; font-weight:600; border:1.5px solid var(--border); background:var(--card); color:var(--muted); cursor:pointer; transition:all .2s; }
.q-pill.active { border-color:var(--accent); background:var(--accent); color:#fff; }
.start-btn { width:100%; margin-top:28px; background:linear-gradient(135deg,var(--accent),var(--accent2)); border:none; border-radius:14px; padding:16px; font-size:1rem; font-weight:700; color:#fff; cursor:pointer; transition:opacity .2s,transform .15s,box-shadow .2s; box-shadow:0 4px 24px rgba(79,142,247,.35); }
.start-btn:hover { opacity:.92; transform:translateY(-2px); }
.start-btn:disabled { opacity:.4; cursor:not-allowed; transform:none; }

#testArea { display:none; }
#testArea.visible { display:block; }
.test-topbar { background:var(--surface); border:1px solid var(--border); border-radius:16px; padding:16px 24px; display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px; box-shadow:var(--shadow); }
.test-meta strong { font-size:1rem; font-weight:700; }
.test-meta span { font-size:.8rem; color:var(--muted); }
.test-stats { display:flex; gap:20px; }
.stat .num { font-size:1.4rem; font-weight:600; color:var(--accent); line-height:1; }
.stat .lbl { font-size:.7rem; color:var(--muted); text-transform:uppercase; letter-spacing:.06em; }
.prog-wrap { height:6px; background:var(--border); border-radius:3px; margin-bottom:28px; overflow:hidden; }
.prog-bar { height:100%; background:linear-gradient(90deg,var(--accent),var(--accent2)); border-radius:3px; transition:width .5s ease; }

.q-card { background:var(--surface); border:1px solid var(--border); border-radius:20px; padding:32px; box-shadow:var(--shadow); margin-bottom:20px; animation:slideUp .35s ease; }
@keyframes slideUp { from{opacity:0;transform:translateY(18px)} to{opacity:1;transform:translateY(0)} }
.q-badge { display:inline-flex; align-items:center; gap:6px; font-size:.75rem; font-weight:600; color:var(--accent); background:rgba(79,142,247,.12); border-radius:50px; padding:4px 12px; margin-bottom:16px; }
.q-text { font-size:1.05rem; font-weight:600; line-height:1.6; margin-bottom:24px; }
.options { display:flex; flex-direction:column; gap:12px; }
.opt { display:flex; align-items:center; gap:14px; background:var(--card); border:1.5px solid var(--border); border-radius:12px; padding:14px 18px; cursor:pointer; transition:all .2s; position:relative; overflow:hidden; }
.opt:hover:not(.answered) { border-color:var(--accent); }
.opt-letter { width:32px; height:32px; flex-shrink:0; border-radius:8px; background:var(--border); display:flex; align-items:center; justify-content:center; font-size:.8rem; font-weight:600; color:var(--muted); transition:all .2s; }
.opt-text { font-size:.9rem; }
.opt.correct { border-color:var(--correct); background:rgba(34,197,94,.08); }
.opt.correct .opt-letter { background:var(--correct); color:#fff; }
.opt.wrong { border-color:var(--wrong); background:rgba(239,68,68,.08); }
.opt.wrong .opt-letter { background:var(--wrong); color:#fff; }
.opt.answered { cursor:not-allowed; }
.explain-box { margin-top:18px; padding:14px 18px; background:rgba(79,142,247,.08); border-left:3px solid var(--accent); border-radius:0 10px 10px 0; font-size:.88rem; line-height:1.6; display:none; animation:fadeIn .3s ease; }
.explain-box.show { display:block; }
@keyframes fadeIn { from{opacity:0} to{opacity:1} }
.next-btn { margin-top:20px; float:right; background:var(--accent); border:none; border-radius:12px; padding:12px 28px; font-size:.9rem; font-weight:700; color:#fff; cursor:pointer; transition:all .2s; display:none; }
.next-btn.show { display:block; }
.next-btn:hover { background:var(--accent2); transform:translateY(-1px); }

#resultCard { display:none; }
#resultCard.visible { display:block; }
.result-wrap { background:var(--surface); border:1px solid var(--border); border-radius:24px; padding:48px 36px; text-align:center; box-shadow:var(--shadow); animation:slideUp .4s ease; }
.result-emoji { font-size:4rem; margin-bottom:16px; }
.result-title { font-size:1.8rem; font-weight:800; margin-bottom:8px; }
.result-sub { color:var(--muted); font-size:.95rem; margin-bottom:36px; }
.score-ring { width:140px; height:140px; margin:0 auto 36px; position:relative; }
.score-ring svg { transform:rotate(-90deg); }
.score-ring circle { fill:none; stroke-width:10; stroke-linecap:round; }
.ring-bg { stroke:var(--border); }
.ring-fg { stroke:url(#scoreGrad); transition:stroke-dashoffset 1s ease; }
.score-num { position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; }
.score-num strong { font-size:2rem; font-weight:700; color:var(--accent); }
.score-num span { font-size:.75rem; color:var(--muted); }
.result-stats { display:flex; justify-content:center; gap:32px; margin-bottom:36px; flex-wrap:wrap; }
.rs .val { font-size:1.4rem; font-weight:700; }
.rs .key { font-size:.75rem; color:var(--muted); text-transform:uppercase; letter-spacing:.06em; }
.rs.c .val { color:var(--correct); }
.rs.w .val { color:var(--wrong); }
.result-actions { display:flex; gap:12px; justify-content:center; flex-wrap:wrap; }
.btn-outline { background:none; border:1.5px solid var(--border); border-radius:12px; padding:13px 28px; font-size:.9rem; font-weight:600; color:var(--text); cursor:pointer; transition:all .2s; }
.btn-outline:hover { border-color:var(--accent); color:var(--accent); }
.btn-primary { background:linear-gradient(135deg,var(--accent),var(--accent2)); border:none; border-radius:12px; padding:13px 28px; font-size:.9rem; font-weight:700; color:#fff; cursor:pointer; box-shadow:0 4px 20px rgba(79,142,247,.3); transition:all .2s; }
.btn-primary:hover { transform:translateY(-2px); }

#loadingCard { display:none; }
#loadingCard.visible { display:block; }
.loading-inner { background:var(--surface); border:1px solid var(--border); border-radius:24px; padding:64px 36px; text-align:center; box-shadow:var(--shadow); }
.spinner { width:52px; height:52px; margin:0 auto 24px; border:4px solid var(--border); border-top-color:var(--accent); border-radius:50%; animation:spin .8s linear infinite; }
@keyframes spin { to{transform:rotate(360deg)} }
.loading-inner p { color:var(--muted); font-size:.9rem; }
.loading-inner strong { display:block; font-size:1.1rem; margin-bottom:8px; }
.toast-q { position:fixed; bottom:28px; left:50%; transform:translateX(-50%) translateY(80px); background:var(--card); border:1px solid var(--border); border-radius:50px; padding:12px 24px; font-size:.85rem; color:var(--text); box-shadow:var(--shadow); transition:transform .3s ease; z-index:999; }
.toast-q.show { transform:translateX(-50%) translateY(0); }
@media(max-width:520px){ .selector-card{padding:24px 18px} .q-card{padding:22px 18px} .result-wrap{padding:36px 20px} .test-topbar{flex-direction:column;align-items:flex-start} }
</style>

<main>

<!-- Selector Card -->
<div class="selector-card" id="selectorCard">
  <h1>Generate Your <span>MCQ Test</span></h1>
  <p>Pick a class, subject and chapters — your personalised test will be ready in seconds.</p>
  <div class="selectors">
    <div class="field">
      <label>Class</label>
      <select id="classSelect" onchange="onClassChange()">
        <option value="">— Select Class —</option>
        <option value="6">Class 6</option><option value="7">Class 7</option><option value="8">Class 8</option>
        <option value="9">Class 9</option><option value="10">Class 10</option><option value="11">Class 11</option>
      </select>
    </div>
    <div class="field">
      <label>Subject</label>
      <select id="subjectSelect" onchange="onSubjectChange()" disabled>
        <option value="">— Select Subject —</option>
      </select>
    </div>
    <div class="field">
      <label>Questions</label>
      <div class="q-count-pills">
        <div class="q-pill active" onclick="setCount(this,5)">5</div>
        <div class="q-pill" onclick="setCount(this,10)">10</div>
        <div class="q-pill" onclick="setCount(this,15)">15</div>
        <div class="q-pill" onclick="setCount(this,20)">20</div>
      </div>
    </div>
  </div>
  <div class="chapter-section" id="chapterSection">
    <label>Select Chapters (pick at least one)</label>
    <div class="ch-actions">
      <button onclick="selectAll()">Select All</button>
      <button onclick="clearAll()">Clear All</button>
    </div>
    <div class="chapter-grid" id="chapterGrid"></div>
  </div>
  <button class="start-btn" id="startBtn" disabled onclick="startTest()">🚀 &nbsp;Generate Test</button>
</div>

<div id="loadingCard">
  <div class="loading-inner">
    <div class="spinner"></div>
    <strong id="loadingTitle">Generating your test…</strong>
    <p>AI is crafting questions based on your selected chapters.</p>
  </div>
</div>

<div id="testArea">
  <div class="test-topbar">
    <div class="test-meta"><strong id="testTitle">—</strong><br><span id="testSub">—</span></div>
    <div class="test-stats">
      <div class="stat"><div class="num" id="statQ">0</div><div class="lbl">Question</div></div>
      <div class="stat"><div class="num" id="statCorrect" style="color:var(--correct)">0</div><div class="lbl">Correct</div></div>
      <div class="stat"><div class="num" id="statWrong" style="color:var(--wrong)">0</div><div class="lbl">Wrong</div></div>
    </div>
  </div>
  <div class="prog-wrap"><div class="prog-bar" id="progBar" style="width:0%"></div></div>
  <div id="questionContainer"></div>
</div>

<div id="resultCard">
  <div class="result-wrap">
    <div class="result-emoji" id="resultEmoji">🎉</div>
    <div class="result-title" id="resultTitle">Test Complete!</div>
    <div class="result-sub" id="resultSub">Here's how you did</div>
    <div class="score-ring">
      <svg width="140" height="140" viewBox="0 0 140 140">
        <defs><linearGradient id="scoreGrad" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" style="stop-color:#4f8ef7"/><stop offset="100%" style="stop-color:#a78bfa"/></linearGradient></defs>
        <circle class="ring-bg" cx="70" cy="70" r="60"/>
        <circle class="ring-fg" id="ringFg" cx="70" cy="70" r="60" stroke-dasharray="377" stroke-dashoffset="377"/>
      </svg>
      <div class="score-num"><strong id="scorePct">0%</strong><span>Score</span></div>
    </div>
    <div class="result-stats">
      <div class="rs c"><div class="val" id="rCorrect">0</div><div class="key">Correct</div></div>
      <div class="rs w"><div class="val" id="rWrong">0</div><div class="key">Wrong</div></div>
      <div class="rs"><div class="val" id="rTotal">0</div><div class="key">Total</div></div>
    </div>
    <div class="result-actions">
      <button class="btn-outline" onclick="resetToSelector()">↩ New Test</button>
      <button class="btn-primary" onclick="retryTest()">🔄 Retry Same Topics</button>
    </div>
  </div>
</div>

</main>
<div class="toast-q" id="toast"></div>


<script src="script.js"></script>
<script>
const API_KEY  = "";
const GEMINI_URL = ``;

const SUBJECTS = {
  6:["Mathematics","Science","English","Social Science","Computer Science"],
  7:["Mathematics","Science","English","Social Science","Computer Science"],
  8:["Mathematics","Science","English","Social Science","Computer Science"],
  9:["Mathematics","Science","English","Social Science","Computer Science"],
  10:["Mathematics","Science","English","Social Science","Computer Science"],
  11:["Mathematics","Physics","Chemistry","Biology","English","Computer Science","Accountancy","Business Studies","Economics","History","Political Science","Geography"],
};
const CHAPTERS = {
  Mathematics:{6:["Knowing Our Numbers","Whole Numbers","Playing with Numbers","Basic Geometrical Ideas","Understanding Elementary Shapes","Integers","Fractions","Decimals","Data Handling","Mensuration","Algebra","Ratio and Proportion","Symmetry","Practical Geometry"],7:["Integers","Fractions and Decimals","Data Handling","Simple Equations","Lines and Angles","The Triangle and Its Properties","Congruence of Triangles","Comparing Quantities","Rational Numbers","Practical Geometry","Perimeter and Area","Algebraic Expressions","Exponents and Powers","Symmetry","Visualising Solid Shapes"],8:["Rational Numbers","Linear Equations in One Variable","Understanding Quadrilaterals","Practical Geometry","Data Handling","Squares and Square Roots","Cubes and Cube Roots","Comparing Quantities","Algebraic Expressions and Identities","Visualising Solid Shapes","Mensuration","Exponents and Powers","Direct and Inverse Proportions","Factorisation","Introduction to Graphs","Playing with Numbers"],9:["Number Systems","Polynomials","Coordinate Geometry","Linear Equations in Two Variables","Lines and Angles","Triangles","Quadrilaterals","Circles","Heron's Formula","Surface Areas and Volumes","Statistics","Probability"],10:["Real Numbers","Polynomials","Pair of Linear Equations","Quadratic Equations","Arithmetic Progressions","Triangles","Coordinate Geometry","Trigonometry","Circles","Surface Areas and Volumes","Statistics","Probability"],11:["Sets","Relations and Functions","Trigonometric Functions","Complex Numbers","Linear Inequalities","Permutations and Combinations","Binomial Theorem","Sequences and Series","Straight Lines","Conic Sections","Limits and Derivatives","Statistics","Probability"]},
  Science:{6:["Food: Where Does It Come From?","Components of Food","Fibre to Fabric","Sorting Materials","Separation of Substances","Changes Around Us","Getting to Know Plants","Body Movements","The Living Organisms","Motion and Measurement","Light, Shadows and Reflections","Electricity and Circuits","Fun with Magnets","Water","Air Around Us","Garbage In, Garbage Out"],7:["Nutrition in Plants","Nutrition in Animals","Heat","Acids, Bases and Salts","Physical and Chemical Changes","Weather and Climate","Soil","Respiration in Organisms","Transportation in Animals and Plants","Reproduction in Plants","Motion and Time","Electric Current","Light","Water","Forests","Wastewater Story"],8:["Crop Production","Microorganisms","Synthetic Fibres","Metals and Non-Metals","Coal and Petroleum","Combustion and Flame","Conservation of Plants","Cell Structure","Reproduction in Animals","Force and Pressure","Friction","Sound","Chemical Effects of Electricity","Natural Phenomena","Stars and the Solar System","Pollution"],9:["Matter in Our Surroundings","Is Matter Around Us Pure","Atoms and Molecules","Structure of the Atom","The Fundamental Unit of Life","Tissues","Motion","Force and Laws of Motion","Gravitation","Work and Energy","Sound","Why Do We Fall Ill","Natural Resources"],10:["Chemical Reactions and Equations","Acids, Bases and Salts","Metals and Non-metals","Carbon and Its Compounds","Periodic Classification","Life Processes","Control and Coordination","How Do Organisms Reproduce?","Heredity and Evolution","Light","Human Eye","Electricity","Magnetic Effects of Electric Current","Sources of Energy","Our Environment"]},
  Physics:{11:["Physical World","Units and Measurements","Motion in a Straight Line","Motion in a Plane","Laws of Motion","Work, Energy and Power","Rotational Motion","Gravitation","Mechanical Properties of Solids","Mechanical Properties of Fluids","Thermal Properties","Thermodynamics","Kinetic Theory","Oscillations","Waves"]},
  Chemistry:{11:["Basic Concepts of Chemistry","Structure of Atom","Classification of Elements","Chemical Bonding","States of Matter","Thermodynamics","Equilibrium","Redox Reactions","Hydrogen","s-Block Elements","p-Block Elements","Organic Chemistry Basics","Hydrocarbons","Environmental Chemistry"]},
  Biology:{11:["The Living World","Biological Classification","Plant Kingdom","Animal Kingdom","Morphology of Flowering Plants","Anatomy of Flowering Plants","Structural Organisation in Animals","Cell: The Unit of Life","Biomolecules","Cell Cycle and Cell Division","Transport in Plants","Mineral Nutrition","Photosynthesis","Respiration in Plants","Plant Growth and Development","Digestion and Absorption","Breathing and Exchange of Gases","Body Fluids and Circulation","Excretory Products","Locomotion and Movement","Neural Control","Chemical Coordination"]},
  English:{6:["Who Did Patrick's Homework?","How the Dog Found a New Master","Taro's Reward","An Indian-American Woman in Space","A Different Kind of School","Who I Am","Fair Play","A Game of Chance","Desert Animals","The Banyan Tree"],7:["Three Questions","A Gift of Chappals","Gopal and the Hilsa Fish","The Ashes That Made Trees Bloom","Quality","Expert Detectives","The Invention of Vita-Wonk","Fire: Friend and Foe","A Bicycle in Good Repair","The Story of Cricket"],8:["The Best Christmas Present","The Tsunami","Glimpses of the Past","Bepin Choudhury's Lapse","The Summit Within","This is Jody's Fawn","A Visit to Cambridge","A Short Monsoon Diary","The Great Stone Face"],9:["The Fun They Had","The Sound of Music","The Little Girl","A Truly Beautiful Mind","The Snake and the Mirror","My Childhood","Packing","Reach for the Top","The Bond of Love","If I Were You"],10:["A Letter to God","Nelson Mandela","Two Stories about Flying","From the Diary of Anne Frank","The Hundred Dresses–I","The Hundred Dresses–II","Glimpses of India","Mijbil the Otter","Madam Rides the Bus","The Sermon at Benares","The Proposal"],11:["The Portrait of a Lady","We're Not Afraid to Die","Discovering Tut","The Ailing Planet","The Browning Version","The Adventure","Silk Road","The Summer of the Beautiful White Horse","The Address","Albert Einstein at School","Mother's Day","Birth"]},
  "Social Science":{6:["What, Where, How and When?","Hunting-Gathering to Growing Food","Early Cities","Books and Burials","Kingdoms and Kings","New Questions and Ideas","Ashoka","Vital Villages","Traders, Kings and Pilgrims","New Empires","Buildings, Paintings and Books","The Earth in the Solar System","Globe: Latitudes and Longitudes","Motions of the Earth","Maps","Major Domains","Major Landforms","Our Country India","India: Climate, Vegetation and Wildlife","Understanding Diversity","Panchayati Raj","Rural Administration","Urban Administration"],7:["Tracing Changes","New Kings and Kingdoms","The Delhi Sultans","The Mughal Empire","Rulers and Buildings","Towns, Traders and Craftspersons","Tribes and Nomads","Devotional Paths","Regional Cultures","Eighteenth-Century Political Formations","Environment","Inside Our Earth","Our Changing Earth","Air","Water","Natural Vegetation","Human Environment","Life in Temperate Grasslands","Life in the Deserts","On Equality","Role of the Government in Health","How the State Government Works"],8:["How, When and Where","From Trade to Territory","Ruling the Countryside","Tribals and Changing World","When People Rebel","Weavers and Iron Smelters","Women, Caste and Reform","The Making of the National Movement","India After Independence","Resources","Land, Soil, Water","Mineral and Power Resources","Agriculture","Industries","Human Resources","The Indian Constitution","Understanding Secularism","Why Do We Need a Parliament?"],9:["The French Revolution","Socialism and Russian Revolution","Nazism","Forest Society and Colonialism","Pastoralists in the Modern World","India – Size and Location","Physical Features of India","Drainage","Climate","Natural Vegetation and Wildlife","Population","What is Democracy?","Constitutional Design","Electoral Politics","Working of Institutions","Democratic Rights","The Story of Village Palampur","People as Resource","Poverty as a Challenge","Food Security in India"],10:["Rise of Nationalism in Europe","Nationalism in India","Making of a Global World","Age of Industrialisation","Print Culture","Resources and Development","Forest and Wildlife","Water Resources","Agriculture","Minerals and Energy Resources","Manufacturing Industries","Lifelines of National Economy","Power Sharing","Federalism","Democracy and Diversity","Gender, Religion and Caste","Popular Struggles","Political Parties","Outcomes of Democracy","Development","Sectors of the Indian Economy","Money and Credit","Globalisation","Consumer Rights"]},
  "Computer Science":{6:["Introduction to Computers","Parts of a Computer","Input and Output Devices","Storage Devices","Software and Hardware","Introduction to MS Windows","MS Paint","MS Word Basics","Introduction to the Internet"],7:["Evolution of Computers","Memory and Storage","Operating System Concepts","MS Word Advanced","MS Excel Basics","MS PowerPoint Introduction","Internet and Email","Cyber Safety"],8:["Advanced Operating System","MS Excel Advanced","MS PowerPoint Advanced","HTML Basics","Introduction to Programming","Algorithms and Flowcharts","Networking Concepts","Cyber Security"],9:["Basics of Information Technology","Computer Hardware","Software Concepts","Python Basics","Web Development Basics","Cyber Ethics","Networking and Internet"],10:["Computer Fundamentals","Python – Control Flow","Python – Functions","Python – Lists, Tuples, Dictionaries","SQL and Databases","HTML and CSS","Networking Concepts","Cyber Security","Emerging Technologies"],11:["Computer System Overview","Encoding Schemes","Emerging Trends","Problem Solving","Getting Started with Python","Flow of Control","Functions","Strings","Lists","Tuples and Dictionaries","Societal Impacts of IT","Introduction to Databases","SQL","Cybersafety"]},
  Accountancy:{11:["Introduction to Accounting","Theory Base of Accounting","Recording of Transactions – I","Recording of Transactions – II","Bank Reconciliation Statement","Trial Balance","Depreciation, Provisions and Reserves","Financial Statements – I","Financial Statements – II","Accounts from Incomplete Records"]},
  "Business Studies":{11:["Business, Trade and Commerce","Forms of Business Organisation","Private, Public and Global Enterprises","Business Services","Emerging Modes of Business","Social Responsibility of Business","Formation of a Company","Sources of Business Finance","Small Business","Internal Trade","International Business"]},
  Economics:{11:["Introduction to Economics","Collection of Data","Organisation of Data","Presentation of Data","Measures of Central Tendency","Measures of Dispersion","Correlation","Index Numbers","Indian Economy on the Eve of Independence","Indian Economy 1950-1990","Liberalisation, Privatisation and Globalisation","Poverty","Human Capital Formation","Rural Development","Employment","Infrastructure","Environment and Sustainable Development"]},
  History:{11:["From the Beginning of Time","Early Cities","An Empire Across Three Continents","The Central Islamic Lands","Nomadic Empires","The Three Orders","Changing Cultural Traditions","Confrontation of Cultures","The Industrial Revolution","Displacing Indigenous Peoples","Paths to Modernisation"]},
  "Political Science":{11:["Political Theory: An Introduction","Freedom","Equality","Social Justice","Rights","Citizenship","Nationalism","Secularism","Peace","Development","Constitution: Why and How?","Rights in the Indian Constitution","Election and Representation","Executive","Legislature","Judiciary","Federalism","Local Governments","The Philosophy of the Constitution"]},
  Geography:{11:["Geography as a Discipline","Origin and Evolution of the Earth","Interior of the Earth","Distribution of Oceans and Continents","Minerals and Rocks","Geomorphic Processes","Landforms and their Evolution","Composition and Structure of Atmosphere","Solar Radiation, Heat Balance and Temperature","Atmospheric Circulation","Water in the Atmosphere","World Climate and Climate Change","Water (Oceans)","Movements of Ocean Water","Life on the Earth","India – Location","Structure and Physiography","Drainage System","Climate","Natural Vegetation","Soils","Natural Hazards and Disasters"]},
};

let selectedClass='', selectedSubject='', selectedChaps=[], qCount=5, questions=[], currentQ=0, correct=0, wrong=0, lastChaps=[], lastClass='', lastSubject='', lastChaptersLabel='';

function onClassChange() {
  selectedClass = parseInt(document.getElementById('classSelect').value) || '';
  const subjSel = document.getElementById('subjectSelect');
  subjSel.disabled = !selectedClass; subjSel.value=''; selectedSubject='';
  document.getElementById('chapterSection').classList.remove('visible');
  document.getElementById('chapterGrid').innerHTML=''; selectedChaps=[]; updateStartBtn();
  subjSel.innerHTML='<option value="">— Select Subject —</option>';
  if (selectedClass && SUBJECTS[selectedClass]) {
    SUBJECTS[selectedClass].forEach(s => { const o=document.createElement('option'); o.value=s; o.textContent=s; subjSel.appendChild(o); });
  }
}
function onSubjectChange() { selectedSubject=document.getElementById('subjectSelect').value; if (selectedSubject) renderChapters(); }
function renderChapters() {
  const grid=document.getElementById('chapterGrid');
  const list=CHAPTERS[selectedSubject]?.[selectedClass]||[];
  if (list.length===0) { grid.innerHTML=`<p style="color:var(--muted);font-size:.85rem;grid-column:1/-1">No chapters listed — AI will generate questions for <strong>${selectedSubject}</strong> Class ${selectedClass}.</p>`; selectedChaps=[-1]; document.getElementById('chapterSection').classList.add('visible'); updateStartBtn(); return; }
  grid.innerHTML=list.map((ch,i)=>`<div class="ch-item selected" data-idx="${i}" onclick="toggleChapter(this,${i})"><input type="checkbox" checked/><span>${ch}</span></div>`).join('');
  selectedChaps=list.map((_,i)=>i); document.getElementById('chapterSection').classList.add('visible'); updateStartBtn();
}
function toggleChapter(el,idx) { el.classList.toggle('selected'); el.querySelector('input').checked=el.classList.contains('selected'); if (el.classList.contains('selected')) { if (!selectedChaps.includes(idx)) selectedChaps.push(idx); } else { selectedChaps=selectedChaps.filter(i=>i!==idx); } updateStartBtn(); }
function selectAll() { document.querySelectorAll('.ch-item').forEach((el,i)=>{el.classList.add('selected');el.querySelector('input').checked=true;}); const list=CHAPTERS[selectedSubject]?.[selectedClass]||[]; selectedChaps=list.map((_,i)=>i); updateStartBtn(); }
function clearAll() { document.querySelectorAll('.ch-item').forEach(el=>{el.classList.remove('selected');el.querySelector('input').checked=false;}); selectedChaps=[]; updateStartBtn(); }
function setCount(el,n) { document.querySelectorAll('.q-pill').forEach(p=>p.classList.remove('active')); el.classList.add('active'); qCount=n; }
function updateStartBtn() { document.getElementById('startBtn').disabled=!(selectedClass&&selectedSubject&&selectedChaps.length>0); }

async function startTest() {
  const list=CHAPTERS[selectedSubject]?.[selectedClass]||[];
  const isSentinel=selectedChaps.length===1&&selectedChaps[0]===-1;
  const names=isSentinel?[selectedSubject]:selectedChaps.map(i=>list[i]);
  lastChaps=[...selectedChaps]; lastClass=selectedClass; lastSubject=selectedSubject;
  document.getElementById('selectorCard').style.display='none'; show('loadingCard');
  document.getElementById('loadingTitle').textContent=`Generating ${qCount} questions on ${selectedSubject}…`;
  const chapterLine=isSentinel?`Subject: ${selectedSubject} (all topics for Class ${selectedClass})`:`Chapters covered: ${names.join(', ')}`;
  const prompt=`You are an expert Indian school teacher. Generate exactly ${qCount} multiple-choice questions for Class ${selectedClass} ${selectedSubject}.\n${chapterLine}.\nRules:\n- Each question must have exactly 4 options (A, B, C, D).\n- Indicate the correct option.\n- Add a short explanation (1-2 sentences) for the correct answer.\n- Questions must be based on NCERT curriculum.\n- Return ONLY valid JSON — no markdown, no backticks, no extra text.\nJSON format:\n{"questions":[{"q":"Question text","options":["A. ...","B. ...","C. ...","D. ..."],"correct":0,"explanation":"..."}]}\ncorrect is the 0-based index of the correct option.`;
  try {
    const res=await fetch(GEMINI_URL,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({contents:[{role:'user',parts:[{text:prompt}]}]})});
    const data=await res.json();
    if (!res.ok) throw new Error(data.error?.message||'API error');
    let raw=data.candidates[0].content.parts[0].text.trim().replace(/```json|```/g,'').trim();
    questions=JSON.parse(raw).questions;
    hide('loadingCard'); runTest();
  } catch(err) { hide('loadingCard'); document.getElementById('selectorCard').style.display=''; toast('Error: '+err.message); }
}

function runTest() {
  currentQ=0; correct=0; wrong=0;
  document.getElementById('testTitle').textContent=`Class ${lastClass} — ${lastSubject}`;
  const chList=CHAPTERS[lastSubject]?.[lastClass]||[];
  const isSentinel=lastChaps.length===1&&lastChaps[0]===-1;
  lastChaptersLabel=isSentinel?'All Topics':lastChaps.map(i=>chList[i]).join(', ');
  document.getElementById('testSub').textContent=lastChaptersLabel;
  show('testArea'); updateStats(); renderQuestion();
}
function renderQuestion() {
  const q=questions[currentQ],num=currentQ+1,total=questions.length;
  document.getElementById('progBar').style.width=((num-1)/total*100)+'%';
  document.getElementById('statQ').textContent=`${num}/${total}`;
  document.getElementById('questionContainer').innerHTML=`<div class="q-card"><div class="q-badge">Q${num} of ${total}</div><div class="q-text">${q.q}</div><div class="options">${q.options.map((opt,i)=>`<div class="opt" id="opt${i}" onclick="choose(${i})"><div class="opt-letter">${['A','B','C','D'][i]}</div><div class="opt-text">${opt.replace(/^[A-D]\.\s*/,'')}</div></div>`).join('')}</div><div class="explain-box" id="explainBox">${q.explanation}</div><button class="next-btn" id="nextBtn" onclick="nextQuestion()">${currentQ<questions.length-1?'Next Question →':'See Results →'}</button></div>`;
}
function choose(idx) {
  const q=questions[currentQ],opts=document.querySelectorAll('.opt');
  opts.forEach(o=>o.classList.add('answered')); opts[q.correct].classList.add('correct');
  if (idx===q.correct) correct++; else { opts[idx].classList.add('wrong'); wrong++; }
  document.getElementById('explainBox').classList.add('show');
  document.getElementById('nextBtn').classList.add('show');
  updateStats();
  document.getElementById('progBar').style.width=((currentQ+1)/questions.length*100)+'%';
}
function nextQuestion() { currentQ++; if (currentQ>=questions.length) showResult(); else renderQuestion(); }
function updateStats() { document.getElementById('statCorrect').textContent=correct; document.getElementById('statWrong').textContent=wrong; }
function showResult() {
  hide('testArea');
  const total=questions.length,pct=Math.round(correct/total*100);
  document.getElementById('rCorrect').textContent=correct;
  document.getElementById('rWrong').textContent=wrong;
  document.getElementById('rTotal').textContent=total;
  document.getElementById('scorePct').textContent=pct+'%';
  const circ=2*Math.PI*60;
  setTimeout(()=>{ document.getElementById('ringFg').style.strokeDashoffset=circ-(pct/100)*circ; },100);
  let emoji='😔',msg='Keep practising!';
  if (pct>=90){emoji='🏆';msg='Outstanding performance!';}
  else if (pct>=75){emoji='🎉';msg='Great job! Almost there!';}
  else if (pct>=50){emoji='👍';msg='Good effort! Keep it up!';}
  document.getElementById('resultEmoji').textContent=emoji;
  document.getElementById('resultTitle').textContent=pct>=50?'Well Done!':'Keep Practising!';
  document.getElementById('resultSub').textContent=msg+` You scored ${correct} out of ${total}.`;
  show('resultCard');
  saveExamAttempt(total, pct);
}
async function saveExamAttempt(total, pct) {
  try {
    await fetch('php_action/save_exam.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        user_class: lastClass,
        subject: lastSubject,
        chapters: lastChaptersLabel,
        total_questions: total,
        correct: correct,
        wrong: wrong,
        score_pct: pct
      })
    });
  } catch (e) { /* non-fatal — don't block the results screen */ }
}
function resetToSelector() { hide('resultCard'); hide('testArea'); hide('loadingCard'); document.getElementById('selectorCard').style.display=''; questions=[]; currentQ=0; correct=0; wrong=0; }
async function retryTest() { hide('resultCard'); hide('testArea'); selectedClass=lastClass; selectedSubject=lastSubject; selectedChaps=[...lastChaps]; await startTest(); }
function show(id){document.getElementById(id).classList.add('visible');}
function hide(id){document.getElementById(id).classList.remove('visible');}
function toast(msg){const t=document.getElementById('toast');t.textContent=msg;t.classList.add('show');setTimeout(()=>t.classList.remove('show'),3000);}
</script>

<?php require_once 'components/footer.php'; ?>
