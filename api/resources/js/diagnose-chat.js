// ==============================================
// diagnose-chat.js（完全版：rendering/finishing 定義済み）
// ==============================================

document.addEventListener('DOMContentLoaded', () => {
  // ---- 要素取得 ----
  const root  = document.getElementById('chat-root');
  const $chat = document.getElementById('chat');
  if (!root || !$chat) {
    console.error('[diagnose] chat-root/chat not found');
    return;
  }

  // ---- 設定 ----
  const API_ENDPOINT = normalizeEndpoint(root.dataset.apiEndpoint || '');
  const BOT_ICON  = root.dataset.botIcon || '';
  const USER_ICON = root.dataset.userIcon || '';
  const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

  console.log('[diagnose] ✅ Initialized');
  console.log('[diagnose] API_ENDPOINT =', API_ENDPOINT);

  // ---- 画面用：vh補正 ----
  setVHVar();
  window.addEventListener('resize', setVHVar);
  window.addEventListener('orientationchange', setVHVar);

  // ---- 質問セット ----
  const QUESTIONS = [
    { id:"q1", text:"今日はどんな気分？",
      choices:[ {label:"わいわい飲みたい", value:"a"}, {label:"しっとり飲みたい", value:"b"} ] },
    { id:"q2", text:"味の方向性はどっち？",
      choices:[ {label:"甘め・軽やか", value:"a"}, {label:"辛口・キリッと", value:"b"} ] },
    { id:"a1", text:"どんなジャンルに惹かれる？",
      choices:[ {label:"日本酒", value:"a"}, {label:"ワイン", value:"b"}, {label:"ビール", value:"c"} ] },
    { id:"b1", text:"飲むシーンは？",
      choices:[ {label:"仕事終わりに一杯", value:"a"}, {label:"休日にゆっくり", value:"b"}, {label:"家飲みでまったり", value:"c"} ] },
    { id:"c1", text:"おつまみは？",
      choices:[ {label:"刺身", value:"a"}, {label:"揚げ物", value:"b"}, {label:"チーズ", value:"c"} ] },
  ];

  // ---- 状態 ----
  let answers   = {};
  let step      = 0;
  let locked    = false;
  let rendering = false;   // ★ 追加：レンダ中ガード
  let finishing = false;   // ★ 追加：finish多重防止

  // ---- ユーティリティ ----
  function setVHVar(){
    const vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty('--vh', `${vh}px`);
  }
  function timeStr(){
    const d = new Date();
    return `${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}`;
  }
  function scrollToBottom(){
    $chat.scrollTop = $chat.scrollHeight;
    requestAnimationFrame(()=>{ $chat.scrollTop = $chat.scrollHeight; });
  }
  function addTyping(){
    const t = document.createElement('div');
    t.className = 'typing';
    t.dataset.role = 'typing';
    t.innerHTML = '<div class="dot"></div><div class="dot"></div><div class="dot"></div>';
    $chat.appendChild(t);
    scrollToBottom();
    return t;
  }
  function removeTyping(){
    [...$chat.querySelectorAll('[data-role="typing"]')].forEach(e=>e.remove());
  }

  function addBot(text, html=null){
    const row = document.createElement('div');
    row.className = 'row bot';
    row.innerHTML = `
      <div class="avatar bot-avatar">${BOT_ICON ? `<img src="${BOT_ICON}" alt="bot">` : ''}</div>
      <div>
        <div class="bubble">${text}</div>
        <div class="time">${timeStr()}</div>
        ${html ?? ''}
      </div>`;
    $chat.appendChild(row);
    scrollToBottom();
    return row; // ← 必ず返す
  }

  function addMe(text){
    const row = document.createElement('div');
    row.className = 'row me';
    row.innerHTML = `
      <div>
        <div class="bubble">${text}</div>
        <div class="time">${timeStr()} <span class="read" aria-hidden="true">✓✓</span></div>
      </div>
      <div class="avatar me-avatar">${USER_ICON ? `<img src="${USER_ICON}" alt="you">` : ''}</div>`;
    $chat.appendChild(row);
    scrollToBottom();
    return row;
  }

  function addChoices(q){
    const wrap = document.createElement('div');
    wrap.className = 'choices';
    q.choices.forEach(ch=>{
      const btn = document.createElement('button');
      btn.className = 'choice';
      btn.textContent = ch.label;
      btn.type = 'button';
      btn.addEventListener('click', async ()=>{
        wrap.querySelectorAll('button').forEach(b=>b.disabled=true);
        await onAnswer(q, ch);
      }, { once:true });
      wrap.appendChild(btn);
    });
    return wrap;
  }

  function botPause(ms=600){ return new Promise(r=>setTimeout(r,ms)); }

  // ---- フロー ----
  async function start(){
    $chat.innerHTML = '';
    answers = {}; step = 0; locked = false; rendering = false; finishing = false;
    addBot('あなたに合うお酒を一緒に探します。');
    await botPause(600);
    await askCurrent();
  }

  async function askCurrent(){
    if (rendering) return;             // ★ 多重実行ガード
    if (step >= QUESTIONS.length) {
      return finish();
    }
    try{
      rendering = true;
      const q = QUESTIONS[step];
      const t = addTyping(); await botPause(600); removeTyping();
      const row = addBot(q.text);
      const choices = addChoices(q);
      const bubble = row.querySelector('.bubble');
      (bubble ?? row).after(choices);
      scrollToBottom();
    } catch(e){
      console.error('askCurrent error', e);
      addBot(`内部エラー（askCurrent）：${e.message || e}`);
    } finally{
      rendering = false;               // ★ 必ず戻す
    }
  }

  async function onAnswer(q, choice){
    if (locked) return;
    locked = true;
    try{
      addMe(choice.label);
      answers[q.id] = choice.value;
      await botPause(250);
      step += 1;
      if (step < QUESTIONS.length) {
        await askCurrent();
      } else {
        await finish();
      }
    } catch(e){
      console.error('onAnswer error', e);
      addBot(`内部エラー（onAnswer）：${e.message || e}`);
    } finally{
      locked = false;
    }
  }

  async function finish(){
    if (finishing) return;             // ★ 多重呼び出し防止
    finishing = true;
    try{
      const t = addTyping(); await botPause(800); removeTyping();
      addBot('診断中…少々お待ちを。');

      const res = await fetch(API_ENDPOINT, {
        method: 'POST',
        headers: {
          'Content-Type':'application/json',
          'Accept':'application/json',
          'X-CSRF-TOKEN': CSRF
        },
        body: JSON.stringify({ answers })
      });

      const text = await res.text();
      let data = {};
      try { data = JSON.parse(text); } catch { data = { raw:text }; }

      const resultId = data.result_id || data.id || (data.result && data.result.result_id);
      if (resultId) {
        window.location.href = `/diagnose/result/${resultId}`;
        return;
      }

      // フォールバック（デバッグ表示）
      addBot('結果IDが見つからなかったため遷移できません。詳細を表示します。', `
        <details><summary>レスポンス</summary>
          <pre>${escapeHtml(JSON.stringify(data, null, 2))}</pre>
        </details>
      `);

    } catch(e){
      console.error('finish error', e);
      addBot(`内部エラー（finish）：${e.message || e}`);
    } finally{
      finishing = false;
    }
  }

  // ---- 起動 ----
  start();

  // ---- 付帯関数 ----
  function escapeHtml(s){
    return String(s).replaceAll('&','&amp;').replaceAll('<','&lt;')
      .replaceAll('>','&gt;').replaceAll('"','&quot;').replaceAll("'",'&#39;');
  }
  function normalizeEndpoint(ep){
    if (!ep || typeof ep !== 'string') return '/api/diagnose/score';
    const url = ep.trim().replace(/\/+$/, '');
    if (/\/api\/diagnose$/.test(url)) return url + '/score';
    return url;
  }
});
