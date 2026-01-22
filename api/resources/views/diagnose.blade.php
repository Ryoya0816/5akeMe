@extends('layouts.app')

@section('title', 'お酒診断 - 5akeMe')
@section('description', 'チャット形式で5つの質問に答えるだけ。あなたの好みに合ったお酒のタイプを診断します。')
@section('og_title', 'お酒診断 - 5akeMe')
@section('og_description', 'チャット形式で5つの質問に答えるだけ！あなたにぴったりのお酒を見つけよう。')

@section('content')
{{-- ローディングオーバーレイ（広告表示用） --}}
<div class="dm-loading-overlay" id="dm-loading-overlay">
    <div class="dm-loading-icon">🍶</div>
    <div class="dm-loading-text">あなたにぴったりのお酒を<br>探しています...</div>
    <div class="dm-loading-ad">
        <div class="dm-loading-ad-title">- ADVERTISEMENT -</div>
        <div class="dm-loading-ad-box">
            📢 広告募集中！<br>
            <small>ここにあなたの広告を掲載しませんか？</small>
        </div>
        <div class="dm-loading-ad-contact">お問い合わせ: hello.sagaworld816@gmail.com</div>
    </div>
</div>

<div class="dm-page-wrap">

            {{-- ★ この画面専用の簡易スタイル --}}
            <style>
                .dm-page-wrap { max-width: 42rem; margin: 0 auto; padding: 1rem 1.5rem 2rem; }
                .dm-page-note { display: block; margin-top: 0.5rem; color: #6b7280; font-size: 12px; }
                .dm-chat-card {
                    border-radius: 24px;
                    border: 1px solid #e5e7eb;
                    background: #f5f7fb;
                    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
                    overflow: hidden;
                }

                .dm-chat-header {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 16px 20px 8px;
                    background: #ffffff;
                    border-bottom: 1px solid #e5e7eb;
                }

                .dm-chat-header-title {
                    font-size: 16px;
                    font-weight: 700;
                }

                .dm-chat-header-sub {
                    font-size: 12px;
                    color: #6b7280;
                }

                .dm-chat-body {
                    padding: 16px 16px 8px;
                    height: 420px;
                    overflow-y: auto;
                    background: #f5f7fb;
                }

                .dm-chat-footer {
                    display: flex;
                    justify-content: flex-end;
                    padding: 8px 16px 16px;
                    background: #f5f7fb;
                }

                .dm-msg-row {
                    display: flex;
                    margin-bottom: 10px;
                }

                .dm-msg-row.bot {
                    justify-content: flex-start;
                }

                .dm-msg-row.user {
                    justify-content: flex-end;
                }

                .dm-avatar {
                    width: 32px;
                    height: 32px;
                    border-radius: 999px;
                    background: #e5e7eb;
                    flex-shrink: 0;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    overflow: hidden;
                }

                .dm-avatar img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                }

                .dm-msg-bubble {
                    max-width: 70%;
                    padding: 10px 14px;
                    border-radius: 16px;
                    font-size: 14px;
                    line-height: 1.5;
                    word-break: break-word;
                }

                .dm-msg-row.bot .dm-msg-bubble {
                    margin-left: 8px;
                    border-bottom-left-radius: 4px;
                    background: #ffffff;
                }

                .dm-msg-row.user .dm-msg-bubble {
                    margin-right: 8px;
                    border-bottom-right-radius: 4px;
                    background: #22c55e;
                    color: #ffffff;
                }

                .dm-question-bubble {
                    font-weight: 600;
                    font-size: 15px;
                }

                .dm-choice-bar {
                    padding: 8px 16px 4px;
                    background: #f5f7fb;
                    border-top: 1px solid #e5e7eb;
                    display: flex;
                    flex-wrap: wrap;
                    gap: 8px;
                }

                .dm-choice-pill {
                    border-radius: 999px;
                    border: 1px solid #d1d5db;
                    background: #ffffff;
                    padding: 6px 12px;
                    font-size: 13px;
                    cursor: pointer;
                    transition: background 0.15s, transform 0.05s, box-shadow 0.15s;
                }

                .dm-choice-pill:hover {
                    background: #e5f6ff;
                    box-shadow: 0 2px 6px rgba(59, 130, 246, 0.25);
                    transform: translateY(-1px);
                }

                .dm-choice-pill:disabled {
                    opacity: 0.4;
                    cursor: default;
                    box-shadow: none;
                    transform: none;
                }

                .dm-restart-btn {
                    font-size: 12px;
                }

                @media (max-width: 768px) {
                    .dm-chat-body {
                        height: 360px;
                    }
                    .dm-msg-bubble {
                        max-width: 78%;
                    }
                }

                /* ローディングオーバーレイ（広告表示用） */
                .dm-loading-overlay {
                    display: none;
                    position: fixed;
                    inset: 0;
                    z-index: 9999;
                    background: linear-gradient(135deg, #fbf3e8 0%, #f7e9dc 100%);
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    gap: 24px;
                    padding: 20px;
                }
                .dm-loading-overlay.is-visible {
                    display: flex;
                }
                .dm-loading-text {
                    font-size: 18px;
                    font-weight: 700;
                    color: #9c3f2e;
                    text-align: center;
                }
                .dm-loading-icon {
                    font-size: 48px;
                    animation: dm-bounce 1s ease-in-out infinite;
                }
                @keyframes dm-bounce {
                    0%, 100% { transform: translateY(0); }
                    50% { transform: translateY(-10px); }
                }
                .dm-loading-ad {
                    margin-top: 20px;
                    padding: 20px;
                    background: #fff;
                    border: 2px dashed #d1d5db;
                    border-radius: 12px;
                    text-align: center;
                    max-width: 400px;
                    width: 100%;
                }
                .dm-loading-ad-title {
                    font-size: 14px;
                    color: #9ca3af;
                    margin-bottom: 12px;
                }
                .dm-loading-ad-box {
                    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
                    border-radius: 8px;
                    padding: 40px 20px;
                    color: #6b7280;
                    font-size: 16px;
                    font-weight: 600;
                }
                .dm-loading-ad-contact {
                    margin-top: 12px;
                    font-size: 12px;
                    color: #9ca3af;
                }
            </style>

            <div class="dm-chat-card"
                 id="chat-root"
                 data-start-endpoint="{{ url('/api/diagnose/start') }}"
                 data-score-endpoint="{{ url('/api/diagnose/score') }}"
                 data-bot-icon="{{ asset('images/bot.png') }}"
                 data-user-icon="{{ asset('images/user.png') }}">

                <div class="dm-chat-header">
                    <div>
                        <div class="dm-chat-header-title">5akeMe お酒チャット診断</div>
                        <div class="dm-chat-header-sub">
                            あなたにピッタリのお酒をいっしょに探します。
                        </div>
                    </div>
                </div>

                <div class="dm-chat-body" id="dm-chat-body">
                    {{-- JS でメッセージを追加していく --}}
                </div>

                <div class="dm-choice-bar" id="dm-choice-bar">
                    {{-- JS が選択肢ボタンをここに並べる --}}
                </div>

                <div class="dm-chat-footer">
                    <button type="button"
                            class="dm-restart-btn"
                            onclick="window.location.reload()">
                        はじめからやり直す
                    </button>
                </div>
            </div>

            <small class="dm-page-note">
                ※ 質問に答えると、あなたに合いそうなお酒を 5 問で診断します。
            </small>

</div>

{{-- ★ ここからこの画面専用の JS を直接書く --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const root = document.getElementById('chat-root');
    if (!root) return;

    const chatBody   = root.querySelector('#dm-chat-body');
    const choiceBar  = root.querySelector('#dm-choice-bar');
    const startEndpoint = root.dataset.startEndpoint;
    const scoreEndpoint = root.dataset.scoreEndpoint;
    const botIcon    = root.dataset.botIcon;
    const userIcon   = root.dataset.userIcon;

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content') || '';

    let questions = [];
    let answers   = {};
    let currentIndex = 0;
    let busy = false;

    const scrollToBottom = () => {
        requestAnimationFrame(() => {
            chatBody.scrollTop = chatBody.scrollHeight;
        });
    };

    const createAvatar = (iconUrl) => {
        const avatar = document.createElement('div');
        avatar.className = 'dm-avatar';
        if (iconUrl) {
            const img = document.createElement('img');
            img.src = iconUrl;
            img.alt = '';
            avatar.appendChild(img);
        }
        return avatar;
    };

    const addMessage = (side, text, options) => {
        options = options || {};
        const isQuestion = options.isQuestion || false;

        const row = document.createElement('div');
        row.className = 'dm-msg-row ' + (side === 'user' ? 'user' : 'bot');

        if (side === 'bot') {
            row.appendChild(createAvatar(botIcon));
        }

        const bubble = document.createElement('div');
        bubble.className = 'dm-msg-bubble';
        if (isQuestion) {
            bubble.classList.add('dm-question-bubble');
        }
        bubble.textContent = text;
        row.appendChild(bubble);

        if (side === 'user') {
            row.appendChild(createAvatar(userIcon));
        }

        chatBody.appendChild(row);
        scrollToBottom();
    };

    const clearChoices = () => {
        while (choiceBar.firstChild) {
            choiceBar.removeChild(choiceBar.firstChild);
        }
    };

    const showQuestion = (q) => {
        if (!q) return;

        clearChoices();

        // 質問バブル（ボット側）
        addMessage('bot', q.text, { isQuestion: true });

        // 下部の選択肢ボタン
        (q.choices || []).forEach((choice) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'dm-choice-pill';
            btn.textContent = choice.label;

            btn.addEventListener('click', async () => {
                if (busy) return;
                busy = true;

                // ユーザーの回答を右側バブルで表示
                addMessage('user', choice.label);

                // 回答保存
                answers[q.id] = choice.value;

                clearChoices();
                currentIndex += 1;

                if (currentIndex < questions.length) {
                    // 次の質問へ
                    setTimeout(function () {
                        showQuestion(questions[currentIndex]);
                        busy = false;
                    }, 400);
                    return;
                }

                // 5問回答したのでスコア計算
                try {
                    addMessage('bot', 'ありがとう！結果を集計するね…');

                    const res = await fetch(scoreEndpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ answers: answers }),
                    });

                    // レスポンスのステータスを確認
                    if (!res.ok) {
                        let errorData = null;
                        try {
                            errorData = await res.json();
                        } catch (e) {
                            // JSONパースに失敗した場合
                            errorData = { message: `サーバーエラー (${res.status})` };
                        }
                        
                        console.error('Score API error', {
                            status: res.status,
                            statusText: res.statusText,
                            error: errorData
                        });
                        
                        const errorMessage = errorData?.message || '診断処理中にエラーが発生しました';
                        addMessage('bot', 'ごめんね、診断に失敗しちゃった…時間をおいてやり直してみてね。');
                        busy = false;
                        return;
                    }

                    const data = await res.json();

                    if (!data.result_id) {
                        console.error('Score API error: result_id missing', data);
                        addMessage('bot', 'ごめんね、診断に失敗しちゃった…時間をおいてやり直してみてね。');
                        busy = false;
                        return;
                    }

                    // ローディング画面を表示（広告表示）
                    const loadingOverlay = document.getElementById('dm-loading-overlay');
                    if (loadingOverlay) {
                        loadingOverlay.classList.add('is-visible');
                    }

                    // 4秒後に結果ページへ遷移
                    setTimeout(function() {
                        window.location.href = '/diagnose/result/' + data.result_id;
                    }, 4000);

                } catch (e) {
                    console.error('Score API error', e);
                    addMessage('bot', 'ネットワークエラーが起きたみたい…もう一度試してみてね。');
                    busy = false;
                }
            });

            choiceBar.appendChild(btn);
        });

        scrollToBottom();
    };

    const startSession = async () => {
        // 最初のあいさつ（左のボット）
        addMessage('bot', 'あなたにピッタリのお酒を提案するね！');

        try {
            const res = await fetch(startEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({}),
            });

            const data = await res.json();

            if (!res.ok || !Array.isArray(data.questions)) {
                console.error('Start API error', data);
                addMessage('bot', '質問の取得に失敗したため、診断を開始できませんでした。');
                return;
            }

            questions    = data.questions;
            answers      = {};
            currentIndex = 0;

            setTimeout(function () {
                showQuestion(questions[currentIndex]);
            }, 400);
        } catch (e) {
            console.error(e);
            addMessage('bot', '通信エラーのせいで診断を開始できませんでした…');
        }
    };

    startSession();
});
</script>
@endsection
