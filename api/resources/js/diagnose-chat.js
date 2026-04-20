// resources/js/diagnose-chat.js

// デフォルト5問（バックエンドから取れなかったとき用）
const DEFAULT_QUESTIONS = [
    {
        id: 'q1',
        text: '今日はどんな気分？',
        choices: [
            { label: 'わいわい飲みたい',      value: 'a' },
            { label: 'しっとり飲みたい',      value: 'b' },
            { label: 'ひとりで静かに飲みたい', value: 'c' },
            { label: 'サクッと飲みたい',      value: 'd' },
            { label: 'がっつり飲みたい',      value: 'e' },
        ],
    },
    {
        id: 'q2',
        text: '味の方向性はどっち？',
        choices: [
            { label: '甘め・軽やか',       value: 'a' },
            { label: '香り・コク',         value: 'b' },
            { label: '余韻を楽しみたい',   value: 'c' },
            { label: 'スッキリ辛口',       value: 'd' },
            { label: '料理に合わせたい',   value: 'e' },
        ],
    },
    {
        id: 'A1',
        text: '初対面の場、あなたはどう動く？',
        choices: [
            { label: 'ガンガン喋る', value: 'a' },
            { label: 'そこそこ話す', value: 'b' },
            { label: '様子を見る',   value: 'c' },
            { label: '基本聞き役',   value: 'd' },
            { label: '完全に静観',   value: 'e' },
        ],
    },
    {
        id: 'B1',
        text: 'どんなお酒が好き？',
        choices: [
            { label: '飲みやすさ重視',     value: 'a' },
            { label: '香りを楽しみたい',   value: 'b' },
            { label: 'コク深いのが好き',   value: 'c' },
            { label: 'すっきり感最優先',   value: 'd' },
            { label: '料理に合えばOK',     value: 'e' },
        ],
    },
    {
        id: 'C1',
        text: '今日はどんな雰囲気で飲みたい？',
        choices: [
            { label: 'わいわいしたい！',   value: 'a' },
            { label: 'ほどよく話したい',   value: 'b' },
            { label: '静かに飲みたい',     value: 'c' },
            { label: '1人で落ち着きたい', value: 'd' },
            { label: '無心で飲みたい',     value: 'e' },
        ],
    },
];

// アバター生成
function dmCreateAvatar(src) {
    const wrap = document.createElement('div');
    wrap.className = 'dm-avatar';

    const img = document.createElement('img');
    img.src = src;
    img.alt = '';

    wrap.appendChild(img);
    return wrap;
}

// BOT側の吹き出し行
function dmBotRow(icon, text) {
    const row = document.createElement('div');
    row.className = 'dm-row dm-row-bot';

    if (icon) {
        row.appendChild(dmCreateAvatar(icon));
    }

    const bubble = document.createElement('div');
    bubble.className = 'dm-bubble dm-bubble-bot';
    bubble.textContent = text;

    row.appendChild(bubble);
    return row;
}

// ユーザー側の吹き出し行
function dmUserRow(icon, text) {
    const row = document.createElement('div');
    row.className = 'dm-row dm-row-user';

    const bubble = document.createElement('div');
    bubble.className = 'dm-bubble dm-bubble-user';
    bubble.textContent = text;

    row.appendChild(bubble);

    if (icon) {
        row.appendChild(dmCreateAvatar(icon));
    }

    return row;
}

// 選択肢ボタン群
function dmChoiceButtons(choices, onClick) {
    const bar = document.createElement('div');
    bar.className = 'dm-choice-bar';

    choices.forEach(choice => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'dm-choice-btn';
        btn.textContent = choice.label;
        btn.addEventListener('click', () => onClick(choice));
        bar.appendChild(btn);
    });

    return bar;
}

document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('chat-root');
    const chat = document.getElementById('chat');
    if (!root || !chat) {
        return;
    }

    const startEndpoint = root.dataset.startEndpoint || '/api/diagnose/start';
    const scoreEndpoint = root.dataset.scoreEndpoint || '/api/diagnose/score';
    const botIcon       = root.dataset.botIcon  || '';
    const userIcon      = root.dataset.userIcon || '';

    // 質問セッション関連
    let questions   = [];
    let currentIndex = 0;
    const answers   = {};
    let sessionSeed = null;

    // チャットに行追加＋スクロール
    function appendRow(row) {
        chat.appendChild(row);
        chat.scrollTop = chat.scrollHeight;
    }

    function appendBotMessage(text) {
        appendRow(dmBotRow(botIcon, text));
    }

    function appendUserMessage(text) {
        appendRow(dmUserRow(userIcon, text));
    }

    // ▼▼ 最初に叩く API（5問のセットを取りにいく）▼▼
    async function fetchQuestionSession() {
        try {
            const res = await fetch(startEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({}),
            });

            if (!res.ok) {
                throw new Error(`Start API ${res.status}`);
            }

            const data = await res.json(); // { seed, questions: [...] }

            if (!data || !Array.isArray(data.questions) || data.questions.length === 0) {
                throw new Error('questions empty');
            }

            sessionSeed = data.seed ?? null;

            return data.questions;
        } catch (e) {
            console.error('Start API error', e);
            appendBotMessage('質問の取得に失敗したため、デフォルトの質問で診断します。');
            return null;
        }
    }

    // ▼▼ 質問を1つ表示 ▼▼
    function askNextQuestion() {
        // 全部答えたら結果へ
        if (currentIndex >= questions.length) {
            submitScore();
            return;
        }

        const q = questions[currentIndex];

        // 質問メッセージ
        appendBotMessage(q.text);

        // 既存のボタン行があれば削除
        const oldBar = document.getElementById('dm-choice-bar');
        if (oldBar && oldBar.parentNode) {
            oldBar.parentNode.removeChild(oldBar);
        }

        // 選択肢ボタン表示
        const bar = dmChoiceButtons(q.choices, choice => {
            // ユーザー回答をチャットに表示
            appendUserMessage(choice.label);

            // 回答保存
            answers[q.id] = choice.value;

            currentIndex += 1;

            // 少し間を空けて次の質問へ
            setTimeout(askNextQuestion, 400);
        });
        bar.id = 'dm-choice-bar';

        chat.appendChild(bar);
        chat.scrollTop = chat.scrollHeight;
    }

    // ▼▼ 採点 API 呼び出し ▼▼
    async function submitScore() {
        // ボタンを消す
        const oldBar = document.getElementById('dm-choice-bar');
        if (oldBar && oldBar.parentNode) {
            oldBar.parentNode.removeChild(oldBar);
        }

        appendBotMessage('結果を集計しています…');

        try {
            const payload = { answers };
            if (sessionSeed !== null) {
                payload.seed = sessionSeed;
            }

            const res = await fetch(scoreEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
            });

            if (!res.ok) {
                throw new Error(`Score API ${res.status}`);
            }

            const data = await res.json(); // { result_id, result: {...} }

            if (!data.result_id) {
                throw new Error('result_id missing');
            }

            // 結果画面へ遷移
            window.location.href = `/diagnose/result/${data.result_id}`;
        } catch (e) {
            console.error('Score API error', e);
            appendBotMessage('診断に失敗してしまいました…。お手数ですが、時間をおいて再度お試しください。');
        }
    }

    // ▼▼ 初期化 ▼▼
    (async () => {
        appendBotMessage('あなたにピッタリのお酒を提案するね！');

        const sessionQuestions = await fetchQuestionSession();
        if (sessionQuestions && sessionQuestions.length > 0) {
            questions = sessionQuestions;
        } else {
            questions = DEFAULT_QUESTIONS;
        }

        // ここで初めて1問目を表示
        askNextQuestion();
    })();
});
