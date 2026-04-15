<style>
    /* =========================================
       結果ページ専用スタイル（最小変更で"共通カラー"に寄せる）
       ※ app.css の :root（--bg-base など）を前提にしています
       ========================================= */

    .diagnose-result-page {
        display: flex;
        justify-content: center;
        padding: 32px 8px 48px;

        /* 以前：background: #fafafa;
           → 共通の和紙色に寄せる（統一） */
        background: var(--bg-base, #fbf3e8);
    }

    .dr-page {
        width: 100%;
        max-width: 800px;

        /* 以前：background: #fff;
           → 白を残しつつ、境界と影を"共通トーン"へ */
        background: var(--card-bg, #ffffff);
        border: 1px solid var(--line-soft, #f1dfd0);

        padding: 24px 16px 40px;
        border-radius: 16px;
        box-shadow: var(--shadow, 0 4px 20px rgba(0,0,0,0.04));
    }

    .dr-title {
        text-align: center;
        font-size: 20px;
        margin-bottom: 16px;

        /* タイトルもブランド寄せ */
        color: var(--brand-main, #9c3f2e);
        font-weight: 700;
    }

    /* 一番上の酒名（⭕️⭕️のイメージ） */
    .dr-name-pill {
        display: flex;
        justify-content: center;
        margin-bottom: 8px;
    }

    .dr-name-pill-inner {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 24px;
        border-radius: var(--radius-full, 999px);

        /* 以前：border: 2px solid #000;
           → ブランド色に寄せる */
        border: 2px solid var(--brand-main, #9c3f2e);

        font-size: 18px;
        font-weight: 700;
        letter-spacing: 0.08em;
        color: var(--brand-main, #9c3f2e);
        background: var(--bg-soft, #fff7ee);
    }

    .dr-step-label {
        text-align: center;
        font-size: 14px;
        margin-bottom: 4px;
        color: var(--text-sub, #8c6d57);
    }

    .dr-arrow {
        text-align: center;
        font-size: 20px;
        margin-bottom: 16px;
        color: var(--text-sub, #8c6d57);
    }

    .dr-hex-section {
        display: flex;
        justify-content: center;
        margin-bottom: 28px;
    }

    .dr-hex-wrap {
        width: 100%;
        max-width: 520px;
        margin: 0 auto;
        box-sizing: border-box;

        /* チャート周りの"台座"を追加して統一感UP（最小の見栄え改善） */
        background: var(--bg-soft, #fff7ee);
        border: 1px solid var(--line-soft, #f1dfd0);
        border-radius: var(--radius-md, 16px);
        box-shadow: var(--shadow-md, 0 8px 24px rgba(0,0,0,0.06));
        padding: 32px;
    }

    /* チャートだけ中央に表示 */
    #diagnose-chart {
        width: 100% !important;
        height: auto !important;
    }

    .dr-result-main {
        text-align: center;
        margin-bottom: 24px;
        line-height: 1.7;
    }

    .dr-result-main .dr-main-text {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 8px;
        color: var(--brand-main, #9c3f2e);
    }

    .dr-result-main .dr-sub-text {
        font-size: 15px;
        color: var(--text-main, #3f3f3f);
    }

    .dr-mood-text {
        font-size: 13px;
        color: var(--text-sub, #8c6d57);
        margin-bottom: 12px;
    }

    .dr-actions {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        margin-top: 8px;
        margin-bottom: 8px;
    }

    .dr-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 260px;
        padding: 10px 20px;
        border-radius: var(--radius-full, 999px);
        border: none;

        /* 以前：background: #222;
           → ブランド赤茶 */
        background: var(--brand-main, #9c3f2e);

        color: #fff;
        font-size: 15px;
        text-decoration: none;
        cursor: pointer;
        transition: transform 0.08s ease, box-shadow 0.08s ease, background 0.12s ease;
    }

    .dr-btn:hover {
        background: var(--brand-text, #8a3a28);
        box-shadow: 0 4px 12px rgba(0,0,0,0.18);
        transform: translateY(-1px);
    }

    .dr-btn-secondary {
        background: var(--bg-soft, #fff7ee);
        color: var(--brand-main, #9c3f2e);
        border: 1px solid var(--line-soft, #f1dfd0);
    }

    .dr-btn-secondary:hover {
        background: #f7eadf;
    }

    .dr-btn small {
        font-size: 12px;
        margin-right: 6px;
        opacity: 0.85;
    }

    .dr-note {
        margin-top: 20px;
        font-size: 12px;
        color: var(--text-sub, #8c6d57);
        text-align: center;
    }

    /* =========================================
       おすすめ店舗セクション
       ========================================= */
    .dr-stores-section {
        margin-top: 48px;
        padding-top: 32px;
        border-top: 2px dashed var(--line-soft, #f1dfd0);
    }

    .dr-stores-title {
        text-align: center;
        font-size: 20px;
        font-weight: 700;
        color: var(--brand-main, #9c3f2e);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .dr-stores-icon {
        display: flex;
    }

    .dr-stores-subtitle {
        text-align: center;
        font-size: 14px;
        color: var(--text-sub, #8c6d57);
        margin-bottom: 24px;
    }

    .dr-stores-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .dr-store-card {
        background: var(--bg-soft, #fff7ee);
        border: 1px solid var(--line-soft, #f1dfd0);
        border-radius: 16px;
        padding: 20px;
        transition: transform var(--transition-normal, 200ms ease-out), box-shadow var(--transition-normal, 200ms ease-out);
    }

    .dr-store-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }

    .dr-store-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 12px;
    }

    .dr-store-name {
        font-size: 17px;
        font-weight: 700;
        color: var(--text-main, #3f3f3f);
        margin: 0;
        flex: 1;
    }

    .dr-store-mood {
        font-size: 12px;
        padding: 4px 10px;
        background: var(--card-bg, #ffffff);
        border-radius: var(--radius-full, 999px);
        white-space: nowrap;
    }

    .dr-store-info {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 12px;
    }

    .dr-store-row {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        font-size: 13px;
        color: var(--text-main, #3f3f3f);
    }

    .dr-store-label {
        flex-shrink: 0;
    }

    .dr-store-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 12px;
    }

    .dr-store-tag {
        font-size: 11px;
        padding: 4px 10px;
        background: var(--brand-main, #9c3f2e);
        color: #ffffff;
        border-radius: var(--radius-full, 999px);
    }

    .dr-store-actions {
        display: flex;
        gap: 10px;
        margin-top: 12px;
    }

    .dr-store-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 16px;
        border-radius: var(--radius-full, 999px);
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all var(--transition-normal, 200ms ease-out);
    }

    .dr-store-btn-detail {
        flex: 1;
        background: var(--brand-main, #9c3f2e);
        color: #ffffff;
    }

    .dr-store-btn-detail:hover {
        background: var(--brand-text, #8a3a28);
        transform: translateY(-1px);
    }

    .dr-store-btn-map {
        background: #4285f4;
        color: #ffffff;
        gap: 4px;
    }

    .dr-store-btn-map:hover {
        background: #3367d6;
        transform: translateY(-1px);
    }

    .dr-stores-empty {
        text-align: center;
        padding: 32px;
        color: var(--text-sub, #8c6d57);
    }

    .dr-stores-note {
        margin-top: 20px;
        font-size: 12px;
        color: var(--text-sub, #8c6d57);
        text-align: center;
    }

    @media (max-width: 600px) {
        .diagnose-result-page {
            padding: 16px 4px 32px;
        }

        .dr-hex-wrap {
            padding: 20px;
        }

        .dr-btn {
            min-width: 220px;
            width: 100%;
            max-width: 320px;
        }

        .dr-stores-section {
            margin-top: 32px;
            padding-top: 24px;
        }

        .dr-stores-title {
            font-size: 18px;
        }

        .dr-store-card {
            padding: 16px;
        }

        .dr-store-name {
            font-size: 15px;
        }

        .dr-store-header {
            flex-direction: column;
            gap: 8px;
        }
    }

    /* =========================================
       フィードバックセクション
       ========================================= */
    .dr-feedback-section {
        margin-top: 48px;
        padding-top: 32px;
        border-top: 2px dashed var(--line-soft, #f1dfd0);
        text-align: center;
    }

    .dr-feedback-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--brand-main, #9c3f2e);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .dr-feedback-icon {
        display: flex;
    }

    .dr-feedback-subtitle {
        font-size: 14px;
        color: var(--text-sub, #8c6d57);
        margin-bottom: 24px;
    }

    .dr-feedback-form {
        max-width: 400px;
        margin: 0 auto;
    }

    .dr-feedback-stars {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-bottom: 8px;
    }

    .dr-star {
        font-size: 36px;
        background: none;
        border: none;
        cursor: pointer;
        filter: grayscale(100%);
        opacity: 0.4;
        transition: all 0.15s ease-out;
        padding: 4px;
    }

    .dr-star:hover {
        transform: scale(1.2);
    }

    .dr-star.active {
        filter: grayscale(0%);
        opacity: 1;
        transform: scale(1.1);
    }

    .dr-star.active:hover {
        transform: scale(1.25);
    }

    .dr-feedback-labels {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: var(--text-sub, #8c6d57);
        padding: 0 8px;
        margin-bottom: 16px;
    }

    .dr-feedback-selected {
        font-size: 15px;
        font-weight: 600;
        color: var(--brand-main, #9c3f2e);
        min-height: 24px;
        margin-bottom: 16px;
    }

    .dr-feedback-comment-wrap {
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .dr-feedback-comment {
        width: 100%;
        padding: 12px 14px;
        font-size: 14px;
        border: 1px solid var(--line-soft, #f1dfd0);
        border-radius: 12px;
        background: var(--bg-soft, #fff7ee);
        color: var(--text-main, #3f3f3f);
        resize: vertical;
        min-height: 80px;
        font-family: inherit;
        margin-bottom: 16px;
    }

    .dr-feedback-comment:focus {
        outline: none;
        border-color: var(--brand-main, #9c3f2e);
    }

    .dr-feedback-submit {
        width: 100%;
        padding: 14px 24px;
        background: var(--brand-main, #9c3f2e);
        color: #ffffff;
        border: none;
        border-radius: var(--radius-full, 999px);
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all var(--transition-normal, 200ms ease-out);
        box-shadow: 0 4px 12px rgba(156, 63, 46, 0.3);
    }

    .dr-feedback-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(156, 63, 46, 0.4);
    }

    .dr-feedback-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .dr-feedback-done {
        padding: 32px;
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        border-radius: 16px;
        animation: successPop 0.5s ease-out;
    }

    @keyframes successPop {
        0% { opacity: 0; transform: scale(0.9); }
        50% { transform: scale(1.02); }
        100% { opacity: 1; transform: scale(1); }
    }

    .dr-feedback-done-icon {
        font-size: 48px;
        margin-bottom: 12px;
    }

    .dr-feedback-done-text {
        font-size: 18px;
        font-weight: 700;
        color: #166534;
        margin-bottom: 8px;
    }

    .dr-feedback-done-sub {
        font-size: 14px;
        color: #15803d;
    }

    @media (max-width: 600px) {
        .dr-feedback-section {
            margin-top: 32px;
            padding-top: 24px;
        }

        .dr-feedback-title {
            font-size: 18px;
        }

        .dr-star {
            font-size: 32px;
        }
    }

    /* =========================================
       SNSシェアセクション
       ========================================= */
    .dr-share-section {
        margin-top: 32px;
        padding: 24px;
        background: linear-gradient(135deg, #fff7ee 0%, #fef3e2 100%);
        border: 2px dashed var(--brand-main, #9c3f2e);
        border-radius: 16px;
        text-align: center;
    }

    .dr-share-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--brand-main, #9c3f2e);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .dr-share-buttons {
        display: flex;
        justify-content: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .dr-share-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px 20px;
        border-radius: var(--radius-full, 999px);
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        color: #ffffff;
        transition: all var(--transition-normal, 200ms ease-out);
        min-width: 120px;
    }

    .dr-share-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }

    .dr-share-btn svg {
        width: 18px;
        height: 18px;
        fill: currentColor;
    }

    .dr-share-btn-twitter { background: #000000; }
    .dr-share-btn-twitter:hover { background: #333333; }
    .dr-share-btn-line { background: #06C755; }
    .dr-share-btn-line:hover { background: #05b04c; }
    .dr-share-btn-facebook { background: #1877F2; }
    .dr-share-btn-facebook:hover { background: #166fe5; }
    .dr-share-btn-copy { background: var(--brand-main, #9c3f2e); }
    .dr-share-btn-copy:hover { background: var(--brand-text, #8a3a28); }
    .dr-share-btn-copy.copied { background: #10b981; }

    .dr-share-note {
        margin-top: 12px;
        font-size: 12px;
        color: var(--text-sub, #8c6d57);
    }

    @media (max-width: 600px) {
        .dr-share-section { padding: 20px 16px; }
        .dr-share-buttons { gap: 10px; }
        .dr-share-btn { padding: 10px 16px; font-size: 13px; min-width: 100px; }
        .dr-share-btn svg { width: 16px; height: 16px; }
    }

    /* =========================================
       食事ペアリングセクション
       ========================================= */
    .dr-pairing-section {
        margin-top: 32px;
        padding: 28px 24px;
        background: linear-gradient(135deg, #fef9f3 0%, #fff7ee 100%);
        border: 2px solid var(--brand-main, #9c3f2e);
        border-radius: 20px;
        text-align: center;
    }

    .dr-pairing-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--brand-main, #9c3f2e);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .dr-pairing-icon { display: flex; }

    .dr-pairing-subtitle {
        font-size: 14px;
        color: var(--text-sub, #8c6d57);
        margin-bottom: 20px;
    }

    .dr-pairing-catch {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-main, #3f3f3f);
        margin-bottom: 20px;
        padding: 12px 16px;
        background: var(--card-bg, #ffffff);
        border-radius: 12px;
        border-left: 4px solid var(--brand-main, #9c3f2e);
    }

    .dr-pairing-list {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 12px;
        margin-bottom: 20px;
    }

    .dr-pairing-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 16px;
        background: var(--card-bg, #ffffff);
        border: 1px solid var(--line-soft, #f1dfd0);
        border-radius: var(--radius-full, 999px);
        font-size: 14px;
        font-weight: 500;
        color: var(--text-main, #3f3f3f);
        transition: all var(--transition-normal, 200ms ease-out);
    }

    .dr-pairing-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        border-color: var(--brand-main, #9c3f2e);
    }

    .dr-pairing-item-icon { font-size: 18px; }

    .dr-pairing-phrase {
        font-size: 14px;
        font-style: italic;
        color: var(--text-sub, #8c6d57);
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px dashed var(--line-soft, #f1dfd0);
    }

    @media (max-width: 600px) {
        .dr-pairing-section { padding: 20px 16px; }
        .dr-pairing-title { font-size: 16px; }
        .dr-pairing-catch { font-size: 14px; }
        .dr-pairing-list { gap: 8px; }
        .dr-pairing-item { padding: 8px 12px; font-size: 13px; }
    }
</style>
