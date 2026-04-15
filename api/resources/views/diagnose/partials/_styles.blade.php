<style>
    .dm-page-wrap { max-width: 42rem; margin: 0 auto; padding: 1rem 1.5rem 2rem; }
    .dm-page-note { display: block; margin-top: 0.5rem; color: var(--text-sub, #8c6d57); font-size: 12px; }
    .dm-chat-card {
        border-radius: var(--radius-lg, 24px);
        border: 1px solid var(--line-soft, #f1dfd0);
        background: var(--bg-soft, #fff7ee);
        box-shadow: var(--shadow-md, 0 8px 24px rgba(0, 0, 0, 0.06));
        overflow: hidden;
    }

    .dm-chat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px 12px;
        background: var(--card-bg, #ffffff);
        border-bottom: 1px solid var(--line-soft, #f1dfd0);
    }

    .dm-chat-header-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--brand-main, #9c3f2e);
    }

    .dm-chat-header-sub {
        font-size: 12px;
        color: var(--text-sub, #8c6d57);
    }

    .dm-chat-body {
        padding: 16px 16px 8px;
        height: 420px;
        overflow-y: auto;
        background: var(--bg-soft, #fff7ee);
    }

    .dm-chat-footer {
        display: flex;
        justify-content: flex-end;
        padding: 8px 16px 16px;
        background: var(--bg-soft, #fff7ee);
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
        border-radius: var(--radius-full, 999px);
        background: var(--line-soft, #f1dfd0);
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
        border-radius: var(--radius-md, 16px);
        font-size: 14px;
        line-height: 1.5;
        word-break: break-word;
    }

    .dm-msg-row.bot .dm-msg-bubble {
        margin-left: 8px;
        border-bottom-left-radius: 4px;
        background: var(--card-bg, #ffffff);
        box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.04));
    }

    .dm-msg-row.user .dm-msg-bubble {
        margin-right: 8px;
        border-bottom-right-radius: 4px;
        background: var(--brand-main, #9c3f2e);
        color: #ffffff;
    }

    .dm-question-bubble {
        font-weight: 600;
        font-size: 15px;
    }

    .dm-choice-bar {
        padding: 10px 16px 6px;
        background: var(--bg-soft, #fff7ee);
        border-top: 1px solid var(--line-soft, #f1dfd0);
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .dm-choice-pill {
        border-radius: var(--radius-full, 999px);
        border: 1px solid var(--line-soft, #f1dfd0);
        background: var(--card-bg, #ffffff);
        padding: 8px 16px;
        font-size: 13px;
        font-weight: 500;
        color: var(--text-main, #3f3f3f);
        cursor: pointer;
        transition: all var(--transition-normal, 200ms ease-out);
    }

    .dm-choice-pill:hover {
        background: var(--brand-main, #9c3f2e);
        color: #ffffff;
        border-color: var(--brand-main, #9c3f2e);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(156, 63, 46, 0.25);
    }

    .dm-choice-pill:disabled {
        opacity: 0.4;
        cursor: default;
        box-shadow: none;
        transform: none;
    }

    .dm-restart-btn {
        font-size: 12px;
        color: var(--text-sub, #8c6d57);
        background: none;
        border: none;
        cursor: pointer;
        transition: color var(--transition-fast, 150ms ease-out);
    }
    .dm-restart-btn:hover {
        color: var(--brand-main, #9c3f2e);
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
