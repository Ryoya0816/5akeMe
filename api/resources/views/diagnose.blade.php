<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>5akeMe 診断（最小版）</title>
  <style>
    :root { --bg:#fff; --fg:#222; --muted:#888; --brand:#0d6efd; }
    body { font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; background: var(--bg); color: var(--fg); margin:0; }
    .wrap { max-width: 720px; margin: 24px auto; padding: 16px; }
    .card { border: 1px solid #ddd; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,.03); }
    .title { font-size: 20px; font-weight: 700; margin: 0 0 12px; }
    .muted { color: var(--muted); font-size: 12px; }
    .btn { display:block; width:100%; text-align:center; border:1px solid #ddd; padding:12px; border-radius:10px; background:#fafafa; cursor:pointer; margin:8px 0; }
    .btn:hover { background:#f2f2f2; }
    .btn.primary { background: var(--brand); color:#fff; border-color:var(--brand); }
    .stack { display:grid; gap:8px; }
    .center { text-align:center; }
    .space { height: 12px; }
    progress { width: 100%; height: 8px; }
    pre { background:#f7f7f7; padding:12px; border-radius:8px; overflow:auto; }
  </style>
</head>
<body>
  <div class="wrap">
    <div id="app" class="card">
      <div class="title">5akeMe 診断</div>
      <div class="muted">最小版：質問→結果 まで</div>
      <div class="space"></div>

      <div id="view-start">
        <button class="btn primary" id="btn-start">診断をはじめる</button>
      </div>

      <div id="view-q" style="display:none;">
        <div class="muted"><span id="step"></span></div>
        <div class="space"></div>
        <div id="q-text" class="title"></div>
        <div id="q-opts" class="stack"></div>
        <div class="space"></div>
        <progress id="prog" value="0" max="5"></progress>
      </div>

      <div id="view-result" style="display:none;">
        <div class="title">結果</div>
        <div id="result-main" class="center"></div>
        <div class="space"></div>
        <details>
          <summary>詳細スコアを見る</summary>
          <pre id="result-json"></pre>
        </details>
        <div class="space"></div>
        <button class="btn" id="btn-again">もう一度</button>
      </div>
    </div>
  </div>

  <script>
  (function () {
    const elStart   = document.getElementById('view-start');
    const elQ       = document.getElementById('view-q');
    const elRes     = document.getElementById('view-result');
    const elText    = document.getElementById('q-text');
    const elOpts    = document.getElementById('q-opts');
    const elStep    = document.getElementById('step');
    const elProg    = document.getElementById('prog');
    const elMain    = document.getElementById('result-main');
    const elJson    = document.getElementById('result-json');

    let allQuestions = [];     // 全設問
    let sessionIds   = [];     // 今回出題するID [q1,q2,...]
    let queue        = [];     // 表示用の質問オブジェクト配列
    let answers      = [];     // {id,label} の配列
    let idx          = 0;

    function show(view) {
      elStart.style.display = (view === 'start')  ? '' : 'none';
      elQ.style.display     = (view === 'q')      ? '' : 'none';
      elRes.style.display   = (view === 'result') ? '' : 'none';
    }

    async function fetchJSON(url, opts = {}) {
      const r = await fetch(url, { headers: { 'Content-Type':'application/json' }, ...opts });
      if (!r.ok) throw new Error(`${r.status} ${r.statusText}`);
      return await r.json();
    }

    async function start() {
      show('q');
      // 1) 全設問
      const qres = await fetchJSON('/api/diagnose/questions');
      allQuestions = qres.questions || [];
      // 2) セッション（固定2 + A/B/C各1）
      const seed = Date.now();
      const sres = await fetchJSON('/api/diagnose/session', {
        method: 'POST',
        body: JSON.stringify({ seed })
      });
      sessionIds = sres.question_ids || [];
      // 3) 今回の出題オブジェクトを並べ替えで作る
      const map = Object.fromEntries(allQuestions.map(q => [q.id, q]));
      queue = sessionIds.map(id => map[id]).filter(Boolean);
      idx = 0; answers = [];
      elProg.value = 0; elProg.max = queue.length;
      renderQ();
    }

    function renderQ() {
      if (idx >= queue.length) return finish();
      const q = queue[idx];
      elStep.textContent = `質問 ${idx + 1} / ${queue.length}`;
      elText.textContent = q.text;
      elOpts.innerHTML = '';
      (q.options || []).forEach(opt => {
        const btn = document.createElement('button');
        btn.className = 'btn';
        btn.textContent = opt.label;
        btn.onclick = () => {
          answers.push({ id: q.id, label: opt.label });
          idx += 1;
          elProg.value = idx;
          renderQ();
        };
        elOpts.appendChild(btn);
      });
    }

    async function finish() {
      // 採点
      const res = await fetchJSON('/api/diagnose/score', {
        method: 'POST',
        body: JSON.stringify({ answers })
      });
      const moodName = res.mood === 2 ? 'しっとり' : 'わいわい';
      const primary  = res.primary || '(未決定)';
      elMain.innerHTML = `
        <div style="font-size:28px; font-weight:800;">タイプ：${primary}</div>
        <div class="muted">mood：${moodName}</div>
      `;
      elJson.textContent = JSON.stringify(res, null, 2);
      show('result');
    }

    document.getElementById('btn-start').addEventListener('click', start);
    document.getElementById('btn-again').addEventListener('click', () => {
      show('start');
    });

    // 初期表示
    show('start');
  })();
  </script>
</body>
</html>
