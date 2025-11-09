// ---- 設定は Blade の data-* から取得（BladeとJSの疎結合化） ----
const root = document.getElementById('chat-root');
const $chat = document.getElementById('chat');
const API_ENDPOINT = root.dataset.apiEndpoint;
const BOT_ICON  = root.dataset.botIcon;
const USER_ICON = root.dataset.userIcon;
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// 画面共通の質問セット（必要ならAPI化／config化も可）
const QUESTIONS = [
  { id:"q1", text:"今日はどんな気分？",
    choices:[ {label:"わいわい飲みたい", value:"1"}, {label:"しっとり飲みたい", value:"2"} ] },
  { id:"q2", text:"味の方向性はどっち？",
    choices:[ {label:"甘め・軽やか", value:"sweet_light"}, {label:"辛口・キリッと", value:"dry_sharp"} ] },
  { id:"catA", text:"どんなジャンルに惹かれる？",
    choices:[ {label:"日本酒", value:"nihonshu"}, {label:"ワイン", value:"wine"}, {label:"ビール", value:"beer"} ] },
];

// ---- モバイルvh対策（アドレスバーの高さ変動を吸収） ----
function setVHVar(){
  const vh = window.innerHeight * 0.01;
  document.documentElement.style.setProperty('--vh', `${vh}px`);
}
setVHVar();
window.addEventListener('resize', setVHVar);
window.addEventListener('orientationchange', setVHVar);

// ---- ユーティリティ ----
function timeStr(){
  const d=new Date(); return `${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}`;
}
function scrollToBottom(){
  $chat.scrollTop = $chat.scrollHeight;
  requestAnimationFrame(()=>{ $chat.scrollTop = $chat.scrollHeight; });
}
function addTyping(){
  const t=document.createElement('div');
  t.className='typing'; t.dataset.role='typing';
  t.innerHTML='<div class="dot"></div><div class="dot"></div><div class="dot"></div>';
  $chat.appendChild(t); scrollToBottom(); return t;
}
function removeTyping(){
  [...$chat.querySelectorAll('[data-role="typing"]')].forEach(e=>e.remove());
}

// ---- 既読管理 ----
let _lastUserRow = null;
function markLatestUserRead(){
  if(!_lastUserRow) return;
  const rr = _lastUserRow.querySelector('.read');
  if(rr){
    rr.classList.add('show');  // ✓✓ 表示
    rr.setAttribute('aria-label','既読');
  }
  _lastUserRow = null;
}

// ---- メッセージ描画 ----
function addBot(text, extra=null){
  markLatestUserRead(); // Botが喋る＝直前のユーザー発話が既読になる
  const row=document.createElement('div');
  row.className='row bot';
  row.innerHTML = `
    <div class="avatar bot-avatar"><img src="${BOT_ICON}" alt="bot"></div>
    <div>
      <div class="bubble">${text}</div>
      <div class="time">${timeStr()}</div>
      ${extra ?? ''}
    </div>`;
  $chat.appendChild(row); scrollToBottom(); return row;
}

function addMe(text){
  const row=document.createElement('div');
  row.className='row me';
  row.innerHTML = `
    <div>
      <div class="bubble">${text}</div>
      <div class="time">${timeStr()} <span class="read" aria-hidden="true">✓✓</span></div>
    </div>
    <div class="avatar me-avatar"><img src="${USER_ICON}" alt="you"></div>
  `;
  $chat.appendChild(row);

  // 短文は1行、長文は自動改行へ切替
  const bubble=row.querySelector('.bubble');
  bubble.classList.add('singleline');
  requestAnimationFrame(()=>{
    if(bubble.scrollWidth > bubble.clientWidth){
      bubble.classList.remove('singleline');
    }
    scrollToBottom();
  });

  _lastUserRow = row;
  return row;
}

function addChoices(q){
  const wrap=document.createElement('div');
  wrap.className='choices';
  q.choices.forEach(ch=>{
    const btn=document.createElement('button');
    btn.className='choice'; btn.textContent=ch.label;
    btn.addEventListener('click', ()=>{
      wrap.querySelectorAll('button').forEach(b=>b.disabled=true); // 誤連打防止
      onAnswer(q, ch);
    }, { once:true });
    btn.type = 'button';
    wrap.appendChild(btn);
  });
  return wrap;
}

// ---- フロー制御 ----
let answers={}, step=0, locked=false;

async function start(){
  $chat.innerHTML=''; answers={}; step=0; locked=false;
  addBot('あなたに合うお酒を一緒に探します。');
  await botPause(); await askCurrent();
}
function botPause(ms=600){ return new Promise(r=>setTimeout(r,ms)); }

async function askCurrent(){
  const q = QUESTIONS[step];
  if(!q){ return finish(); }
  const typing = addTyping();
  await botPause(650);
  removeTyping();
  const row = addBot(q.text);
  const choices = addChoices(q);
  row.querySelector('.bubble').after(choices);
  scrollToBottom();
}

async function onAnswer(q, choice){
  if(locked) return; locked = true;
  addMe(choice.label);
  answers[q.id] = choice.value;
  await botPause(250);
  step += 1; locked = false;
  await askCurrent();
}

async function finish(){
  const typing = addTyping(); await botPause(800);
  try{
    removeTyping(); addBot('診断中…少々お待ちを。');
    const res = await fetch(API_ENDPOINT, {
      method:'POST',
      headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF },
      body: JSON.stringify({ answers })   // バック側は {answers:{...}} を想定
    });
    const data = await res.json();
    await botPause(400);
    renderResult(data);
  }catch(e){
    removeTyping(); addBot('すみません、サーバでエラーが発生しました。');
    console.error(e);
  }
}

function renderResult(data){
  const moodLabel = (data?.mood==1 || data?.mood==='ww') ? 'わいわい' : 'しっとり';
  const labels    = (data?.chartData||[]).map(d=>d.label);
  const top3      = labels.slice(0,3);

  const html = `
    <div class="result-card">
      <span class="badge">診断結果</span>
      <h2>タイプ：<strong>${data?.primary ?? '-'}</strong></h2>
      <div class="mutetip">気分：${moodLabel}</div>
      <div class="recommend">${top3.map(t=>`<span class="pill">${t}</span>`).join('')}</div>
      <details>
        <summary>詳細スコアを見る</summary>
        <pre>${escapeHtml(JSON.stringify(data, null, 2))}</pre>
      </details>
    </div>
  `;
  addBot('結果が出ました！', html);
  document.getElementById('restart')?.focus();
}

function escapeHtml(s){
  return s.replaceAll('&','&amp;')
          .replaceAll('<','&lt;')
          .replaceAll('>','&gt;')
          .replaceAll('"','&quot;')
          .replaceAll("'","&#39;");
}

// ---- イベント ----
document.getElementById('restart')?.addEventListener('click', start);
start();
