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

        addMessage('bot', q.text, { isQuestion: true });

        (q.choices || []).forEach((choice) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'dm-choice-pill';
            btn.textContent = choice.label;

            btn.addEventListener('click', async () => {
                if (busy) return;
                busy = true;

                addMessage('user', choice.label);

                answers[q.id] = choice.value;

                clearChoices();
                currentIndex += 1;

                if (currentIndex < questions.length) {
                    setTimeout(function () {
                        showQuestion(questions[currentIndex]);
                        busy = false;
                    }, 400);
                    return;
                }

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

                    if (!res.ok) {
                        let errorData = null;
                        try {
                            errorData = await res.json();
                        } catch (e) {
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

                    const loadingOverlay = document.getElementById('dm-loading-overlay');
                    if (loadingOverlay) {
                        loadingOverlay.classList.add('is-visible');
                    }

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
