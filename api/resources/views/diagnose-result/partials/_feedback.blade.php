<section class="dr-feedback-section" id="feedback-section">
    <h2 class="dr-feedback-title">
        <span class="dr-feedback-icon"><x-svg-icon name="message-square" size="22" /></span>
        この診断結果はいかがでしたか？
    </h2>
    <p class="dr-feedback-subtitle">あなたの評価が、診断の精度向上に役立ちます！</p>

    <div class="dr-feedback-form" id="feedback-form">
        <div class="dr-feedback-stars" id="feedback-stars">
            <button type="button" class="dr-star" data-rating="1" title="イマイチ">⭐</button>
            <button type="button" class="dr-star" data-rating="2" title="まあまあ">⭐</button>
            <button type="button" class="dr-star" data-rating="3" title="普通">⭐</button>
            <button type="button" class="dr-star" data-rating="4" title="良い">⭐</button>
            <button type="button" class="dr-star" data-rating="5" title="最高！">⭐</button>
        </div>
        <div class="dr-feedback-labels">
            <span>イマイチ</span>
            <span>最高！</span>
        </div>
        <p class="dr-feedback-selected" id="feedback-selected"></p>

        <div class="dr-feedback-comment-wrap" id="comment-wrap" style="display: none;">
            <textarea 
                id="feedback-comment" 
                class="dr-feedback-comment" 
                placeholder="コメント（任意）: この結果についてひとこと..."
                maxlength="500"
            ></textarea>
            <button type="button" class="dr-feedback-submit" id="feedback-submit">
                送信する
            </button>
        </div>
    </div>

    <div class="dr-feedback-done" id="feedback-done" style="display: none;">
        <div class="dr-feedback-done-icon">🎉</div>
        <p class="dr-feedback-done-text">フィードバックありがとうございます！</p>
        <p class="dr-feedback-done-sub">あなたの評価が5akeMeをより良くします</p>
    </div>
</section>
