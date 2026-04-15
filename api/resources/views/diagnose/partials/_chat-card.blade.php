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
