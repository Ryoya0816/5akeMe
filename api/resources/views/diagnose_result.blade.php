@php
    // 先に必要な変数を定義
    $primaryType  = data_get($result, 'primary_type');
    $primaryLabel = data_get($result, 'primary_label');
    $master = config('diagnose_results', []);
    $detail = $primaryType && isset($master[$primaryType]) ? $master[$primaryType] : [];
    $pairingLabel = $detail['pairing_label'] ?? $detail['name'] ?? $primaryLabel ?? '○○ × ○○';
    
    // ペアリング情報
    $snacks = $detail['snacks'] ?? [];
    $catchCopy = $detail['catch_copy'] ?? '';
    $onePhrase = $detail['one_phrase'] ?? '';
    
    $shareUrl = url('/diagnose/result/' . ($result->result_id ?? ''));
    $shareTitle = '【5akeMe診断結果】私にぴったりのお酒は「' . $pairingLabel . '」でした！';
    $shareDescription = '5つの質問に答えるだけで、あなたにぴったりのお酒が見つかる！あなたも診断してみよう🍶';
@endphp

@extends('layouts.app')

@section('title', '診断結果 - ' . $pairingLabel)

@section('og_type', 'article')
@section('og_title', $shareTitle)
@section('og_description', $shareDescription)

@section('content')
<div class="diagnose-result-page">

    <style>
        /* =========================================
           結果ページ専用スタイル（最小変更で“共通カラー”に寄せる）
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
               → 白を残しつつ、境界と影を“共通トーン”へ */
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

            /* チャート周りの“台座”を追加して統一感UP（最小の見栄え改善） */
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

        /* Twitter/X */
        .dr-share-btn-twitter {
            background: #000000;
        }

        .dr-share-btn-twitter:hover {
            background: #333333;
        }

        /* LINE */
        .dr-share-btn-line {
            background: #06C755;
        }

        .dr-share-btn-line:hover {
            background: #05b04c;
        }

        /* Facebook */
        .dr-share-btn-facebook {
            background: #1877F2;
        }

        .dr-share-btn-facebook:hover {
            background: #166fe5;
        }

        /* コピーボタン */
        .dr-share-btn-copy {
            background: var(--brand-main, #9c3f2e);
        }

        .dr-share-btn-copy:hover {
            background: var(--brand-text, #8a3a28);
        }

        .dr-share-btn-copy.copied {
            background: #10b981;
        }

        .dr-share-note {
            margin-top: 12px;
            font-size: 12px;
            color: var(--text-sub, #8c6d57);
        }

        @media (max-width: 600px) {
            .dr-share-section {
                padding: 20px 16px;
            }

            .dr-share-buttons {
                gap: 10px;
            }

            .dr-share-btn {
                padding: 10px 16px;
                font-size: 13px;
                min-width: 100px;
            }

            .dr-share-btn svg {
                width: 16px;
                height: 16px;
            }
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

        .dr-pairing-icon {
            display: flex;
        }

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

        .dr-pairing-item-icon {
            font-size: 18px;
        }

        .dr-pairing-phrase {
            font-size: 14px;
            font-style: italic;
            color: var(--text-sub, #8c6d57);
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px dashed var(--line-soft, #f1dfd0);
        }

        @media (max-width: 600px) {
            .dr-pairing-section {
                padding: 20px 16px;
            }

            .dr-pairing-title {
                font-size: 16px;
            }

            .dr-pairing-catch {
                font-size: 14px;
            }

            .dr-pairing-list {
                gap: 8px;
            }

            .dr-pairing-item {
                padding: 8px 12px;
                font-size: 13px;
            }
        }
    </style>

    @php
        /**
         * $result は今こういう想定：
         * - Eloquentモデル App\Models\DiagnoseResult
         *   - primary_type   (例: sake_dry)
         *   - primary_label  (例: 日本酒・辛口)
         *   - mood           (lively/chill/silent/light/strong)
         *   - candidates     (type/score/label の配列)
         *   - top5           (type/score/label の配列)  ← ★追加：チャートはこっちを使う
         */

        // モデルでも配列でも data_get で安全に取れるようにしておく
        $primaryType  = data_get($result, 'primary_type');
        $primaryLabel = data_get($result, 'primary_label');
        $mood         = data_get($result, 'mood');

        // ★候補（テキスト表示などに使うなら残す）
        $candidates   = data_get($result, 'candidates', []);

        // ★チャート用：上位5（無ければ candidates から5件フォールバック）
        $top5 = data_get($result, 'top5', []);
        if (!is_array($top5) || empty($top5)) {
            // candidatesから上位5件を取得（既にスコア降順でソートされている想定）
            if (is_array($candidates) && !empty($candidates)) {
                // スコアでソート（念のため）
                $sortedCandidates = $candidates;
                usort($sortedCandidates, function($a, $b) {
                    $scoreA = $a['score'] ?? 0;
                    $scoreB = $b['score'] ?? 0;
                    return $scoreB <=> $scoreA; // 降順
                });
                $top5 = array_slice($sortedCandidates, 0, 5);
            } else {
                $top5 = [];
            }
        }

        // 診断結果マスタ（存在しなければ空配列）
        $master = config('diagnose_results', []);
        $detail = $primaryType && isset($master[$primaryType]) ? $master[$primaryType] : [];

        // 表示用ラベル
        $pairingLabel = $detail['pairing_label']
            ?? $detail['name']
            ?? $primaryLabel
            ?? '○○ × ○○';

        // moodテキスト（任意）
        $moodLabels = [
            'lively' => '今日は、みんなでわいわい飲みたい気分みたい。そんなあなたに…',
            'chill'  => '今日は、少人数でしっぽり語りたい気分みたい。そんなあなたに…',
            'silent' => '今日は、ひとりで静かに飲みたい気分みたい。そんなあなたに…',
            'light'  => '今日は、サクッと軽く飲みたい気分みたい。そんなあなたに…',
            'strong' => '今日は、がっつり飲みたい気分みたい。そんなあなたに…',
        ];
        $moodText = $mood ? ($moodLabels[$mood] ?? null) : null;

        // -----------------------------------------
        // レーダーチャート用データ
        // 1. マスタに chart_labels / chart_values があればそちら優先
        // 2. なければ top5 を使う（★仕様どおり）
        // -----------------------------------------
        if (!empty($detail['chart_labels']) && !empty($detail['chart_values'])) {
            $chartLabels = $detail['chart_labels'];
            $chartValues = $detail['chart_values'];
        } else {
            $chartLabels = [];
            $chartValues = [];

            if (!empty($top5) && is_array($top5)) {
                foreach ($top5 as $row) {
                    if (is_array($row)) {
                        $chartLabels[] = $row['label'] ?? ($row['type'] ?? 'タイプ');
                        $chartValues[] = isset($row['score']) ? round((float)$row['score'], 1) : 0;
                    }
                }
            }

            // 万が一何もない場合のフォールバック
            if (empty($chartLabels) || empty($chartValues)) {
                // candidatesから再度試行
                if (is_array($candidates) && !empty($candidates)) {
                    $chartLabels = [];
                    $chartValues = [];
                    $sortedCandidates = $candidates;
                    usort($sortedCandidates, function($a, $b) {
                        $scoreA = $a['score'] ?? 0;
                        $scoreB = $b['score'] ?? 0;
                        return $scoreB <=> $scoreA;
                    });
                    foreach (array_slice($sortedCandidates, 0, 5) as $row) {
                        if (is_array($row)) {
                            $chartLabels[] = $row['label'] ?? ($row['type'] ?? 'タイプ');
                            $chartValues[] = isset($row['score']) ? round((float)$row['score'], 1) : 0;
                        }
                    }
                }
                
                // それでも空の場合はデフォルト値
                if (empty($chartLabels)) {
                    $chartLabels = ['タイプA', 'タイプB', 'タイプC', 'タイプD', 'タイプE'];
                    $chartValues = [3, 4, 2, 5, 3];
                }
            }
        }
    @endphp

    <div class="dr-page">
        {{-- タイトル --}}
        <h1 class="dr-title">あなたへのおすすめのお酒は、、、</h1>

        {{-- 一番上の⭕️⭕️ゾーン → 酒名を表示 --}}
        <div class="dr-name-pill">
            <div class="dr-name-pill-inner">
                {{ $pairingLabel }}
            </div>
        </div>

        <div class="dr-step-label"></div>
        <div class="dr-arrow"></div>

        {{-- チャートのみ --}}
        <section class="dr-hex-section">
            <div class="dr-hex-wrap">
                <canvas id="diagnose-chart"></canvas>
            </div>
        </section>

        <section class="dr-result-main">
            @if($moodText)
                <div class="dr-mood-text">
                    {{ $moodText }}
                </div>
            @endif

            <div class="dr-main-text">
                ペアリングのおすすめは、 {{ $pairingLabel }}
            </div>
        </section>

        {{-- 食事ペアリングセクション --}}
        @if(!empty($snacks))
        <section class="dr-pairing-section">
            <h2 class="dr-pairing-title">
                <span class="dr-pairing-icon"><x-svg-icon name="utensils" size="22" /></span>
                相性バツグンのおつまみ
            </h2>
            <p class="dr-pairing-subtitle">{{ $pairingLabel }}と一緒に楽しみたい料理</p>

            @if($catchCopy)
            <div class="dr-pairing-catch">
                「{{ $catchCopy }}」
            </div>
            @endif

            <div class="dr-pairing-list">
                @foreach($snacks as $snack)
                <div class="dr-pairing-item">
                    <span class="dr-pairing-item-icon">🥢</span>
                    <span>{{ $snack }}</span>
                </div>
                @endforeach
            </div>

            @if($onePhrase)
            <p class="dr-pairing-phrase">
                {{ $onePhrase }}
            </p>
            @endif
        </section>
        @endif

        <div class="dr-actions">
            <button type="button" class="dr-btn" id="btn-show-stores">
                <small>②</small>
                <span>{{ $pairingLabel }} が飲めるお店を見る</span>
            </button>

            <a href="{{ url('/diagnose') }}" class="dr-btn dr-btn-secondary">
                <small>③</small>
                <span>もう一度診断する</span>
            </a>
        </div>

        <div class="dr-note">
            ※ グラフは、あなたの回答から算出した「上位5種類のお酒タイプ」をチャートで表示しています。
        </div>

        {{-- SNSシェアセクション --}}
        <section class="dr-share-section">
            <h2 class="dr-share-title">
                <span><x-svg-icon name="share" size="20" /></span>
                診断結果をシェアしよう！
            </h2>
            <div class="dr-share-buttons">
                {{-- Twitter/X --}}
                <a 
                    href="https://twitter.com/intent/tweet?text={{ urlencode($shareTitle . "\n\n" . $shareDescription) }}&url={{ urlencode($shareUrl) }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="dr-share-btn dr-share-btn-twitter"
                >
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                    <span>X</span>
                </a>

                {{-- LINE --}}
                <a 
                    href="https://social-plugins.line.me/lineit/share?url={{ urlencode($shareUrl) }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="dr-share-btn dr-share-btn-line"
                >
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.349 0 .63.283.63.63 0 .344-.281.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.627-.63h2.386c.349 0 .63.285.63.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.104.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .63.285.63.63v4.771zm-5.741 0c0 .344-.282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.627-.63.349 0 .631.285.631.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.756c.348 0 .629.283.629.63 0 .344-.282.629-.629.629M24 10.314C24 4.943 18.615.572 12 .572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.766.038 1.08l-.164 1.02c-.045.301-.24 1.186 1.049.645 1.291-.539 6.916-4.078 9.436-6.975C23.176 14.393 24 12.458 24 10.314"/>
                    </svg>
                    <span>LINE</span>
                </a>

                {{-- Facebook --}}
                <a 
                    href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="dr-share-btn dr-share-btn-facebook"
                >
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    <span>Facebook</span>
                </a>

                {{-- URLコピー --}}
                <button 
                    type="button"
                    class="dr-share-btn dr-share-btn-copy"
                    id="copy-url-btn"
                    data-url="{{ $shareUrl }}"
                >
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/>
                    </svg>
                    <span id="copy-btn-text">URLをコピー</span>
                </button>
            </div>
            <p class="dr-share-note">友達にもおすすめのお酒を見つけてもらおう！</p>
        </section>

        {{-- おすすめ店舗セクション --}}
        <section class="dr-stores-section" id="stores-section">
            <h2 class="dr-stores-title">
                <span class="dr-stores-icon"><x-svg-icon name="wine" size="22" /></span>
                佐賀駅周辺のおすすめ店舗
            </h2>
            <p class="dr-stores-subtitle">あなたの診断結果にぴったりのお店を厳選しました</p>

            @if(isset($stores) && $stores->count() > 0)
                <div class="dr-stores-list">
                    @foreach($stores as $store)
                        <div class="dr-store-card">
                            <div class="dr-store-header">
                                <h3 class="dr-store-name">{{ $store->name }}</h3>
                                <span class="dr-store-mood">
                                    @if($store->mood === 'lively')
                                        🎉 にぎやか
                                    @elseif($store->mood === 'calm')
                                        🌙 落ち着き
                                    @else
                                        ✨ 両方OK
                                    @endif
                                </span>
                            </div>

                            <div class="dr-store-info">
                                @if($store->address)
                                    <div class="dr-store-row">
                                        <span class="dr-store-label"><x-svg-icon name="map-pin" size="14" /></span>
                                        <span>{{ $store->address }}</span>
                                    </div>
                                @endif

                                @if($store->business_hours)
                                    <div class="dr-store-row">
                                        <span class="dr-store-label"><x-svg-icon name="clock" size="14" /></span>
                                        <span>{{ $store->business_hours }}</span>
                                    </div>
                                @endif

                                @if($store->closed_days)
                                    <div class="dr-store-row">
                                        <span class="dr-store-label"><x-svg-icon name="calendar" size="14" /></span>
                                        <span>定休日: {{ $store->closed_days }}</span>
                                    </div>
                                @endif
                            </div>

                            @php
                                $displaySakeTypes = $store->sake_types ? array_values(array_diff($store->sake_types, \App\Models\Store::getDisplayOnlyTags())) : [];
                            @endphp
                            @if(count($displaySakeTypes) > 0)
                                <div class="dr-store-tags">
                                    @foreach(array_slice($displaySakeTypes, 0, 3) as $type)
                                        <span class="dr-store-tag">{{ \App\Models\Store::getSakeTypeLabel($type) }}</span>
                                    @endforeach
                                    @if(count($displaySakeTypes) > 3)
                                        <span class="dr-store-tag">+{{ count($displaySakeTypes) - 3 }}</span>
                                    @endif
                                </div>
                            @endif

                            <div class="dr-store-actions">
                                <a href="{{ route('store.detail', $store->id) }}" class="dr-store-btn dr-store-btn-detail">
                                    詳細を見る →
                                </a>
                                @if($store->address)
                                    <a 
                                        href="https://www.google.com/maps/search/?api=1&query={{ urlencode($store->address) }}" 
                                        target="_blank" 
                                        rel="noopener noreferrer" 
                                        class="dr-store-btn dr-store-btn-map"
                                    >
                                        <x-svg-icon name="map-pin" size="14" /> MAP
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="dr-stores-empty">
                    <p>現在、おすすめ店舗の情報を準備中です。</p>
                </div>
            @endif

            <p class="dr-stores-note">
                ※ 店舗情報は変更される場合があります。お出かけ前にご確認ください。
            </p>
        </section>

        {{-- フィードバックセクション --}}
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
    </div>

    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const chartLabels = @json($chartLabels);
        const chartValues = @json($chartValues);

        const ctx = document.getElementById('diagnose-chart');

        if (ctx) {
            new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'お酒タイプのバランス',
                        data: chartValues,

                        /* 見た目も“世界観”に寄せる（色はCSS変数から取得） */
                        fill: true,
                        borderWidth: 2,
                        pointRadius: 3,

                        /* Chart.js はCSS変数を直接は読めないのでJSで読む */
                        borderColor: getComputedStyle(document.documentElement).getPropertyValue('--brand-main').trim() || '#9c3f2e',
                        backgroundColor: 'rgba(156, 63, 46, 0.12)',
                        pointBackgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--brand-main').trim() || '#9c3f2e',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        r: {
                            suggestedMin: 0,
                            suggestedMax: 5,
                            ticks: {
                                stepSize: 1,
                                backdropColor: 'transparent',
                                font: { size: 11 },
                                color: '#a08070',
                                z: 1
                            },
                            grid: {
                                circular: true,
                                color: 'rgba(0,0,0,0.06)'
                            },
                            angleLines: {
                                color: 'rgba(0,0,0,0.08)'
                            },
                            pointLabels: {
                                font: { size: 14, weight: '600' },
                                color: '#5a4030',
                                padding: 18
                            }
                        }
                    }
                }
            });
        }

        const btnShowStores = document.getElementById('btn-show-stores');
        if (btnShowStores) {
            btnShowStores.addEventListener('click', function () {
                const storesSection = document.getElementById('stores-section');
                if (storesSection) {
                    storesSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        }

        // =========================================
        // フィードバック機能
        // =========================================
        const resultId = @json($result->result_id ?? null);
        const feedbackStars = document.querySelectorAll('.dr-star');
        const feedbackSelected = document.getElementById('feedback-selected');
        const commentWrap = document.getElementById('comment-wrap');
        const feedbackComment = document.getElementById('feedback-comment');
        const feedbackSubmit = document.getElementById('feedback-submit');
        const feedbackForm = document.getElementById('feedback-form');
        const feedbackDone = document.getElementById('feedback-done');

        const ratingLabels = {
            1: 'イマイチ 😕',
            2: 'まあまあ 🤔',
            3: '普通 😐',
            4: '良い 😊',
            5: '最高！ 🎉'
        };

        let selectedRating = 0;

        // 既にフィードバック済みかチェック
        if (resultId) {
            fetch(`/api/diagnose/feedback/${resultId}/check`)
                .then(res => res.json())
                .then(data => {
                    if (data.has_feedback) {
                        feedbackForm.style.display = 'none';
                        feedbackDone.style.display = 'block';
                        feedbackDone.querySelector('.dr-feedback-done-text').textContent = 
                            `評価済み: ${ratingLabels[data.rating] || ''}`;
                    }
                })
                .catch(() => {});
        }

        // 星をクリック
        feedbackStars.forEach(star => {
            star.addEventListener('click', function() {
                selectedRating = parseInt(this.dataset.rating);
                
                // 全ての星を更新
                feedbackStars.forEach((s, index) => {
                    if (index < selectedRating) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });

                // ラベル表示
                feedbackSelected.textContent = ratingLabels[selectedRating] || '';

                // コメント欄を表示
                commentWrap.style.display = 'block';
            });

            // ホバー時のプレビュー
            star.addEventListener('mouseenter', function() {
                const hoverRating = parseInt(this.dataset.rating);
                feedbackStars.forEach((s, index) => {
                    if (index < hoverRating) {
                        s.style.filter = 'grayscale(0%)';
                        s.style.opacity = '0.8';
                    }
                });
            });

            star.addEventListener('mouseleave', function() {
                feedbackStars.forEach((s, index) => {
                    if (!s.classList.contains('active')) {
                        s.style.filter = 'grayscale(100%)';
                        s.style.opacity = '0.4';
                    } else {
                        s.style.filter = 'grayscale(0%)';
                        s.style.opacity = '1';
                    }
                });
            });
        });

        // 送信
        if (feedbackSubmit) {
            feedbackSubmit.addEventListener('click', async function() {
                if (!selectedRating || !resultId) return;

                this.disabled = true;
                this.textContent = '送信中...';

                try {
                    const response = await fetch(`/api/diagnose/feedback/${resultId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        },
                        body: JSON.stringify({
                            rating: selectedRating,
                            comment: feedbackComment.value || null,
                        }),
                    });

                    const data = await response.json();

                    if (data.success) {
                        feedbackForm.style.display = 'none';
                        feedbackDone.style.display = 'block';
                    } else {
                        alert(data.message || 'エラーが発生しました');
                        this.disabled = false;
                        this.textContent = '送信する';
                    }
                } catch (error) {
                    alert('通信エラーが発生しました');
                    this.disabled = false;
                    this.textContent = '送信する';
                }
            });
        }

        // =========================================
        // URLコピー機能
        // =========================================
        const copyUrlBtn = document.getElementById('copy-url-btn');
        const copyBtnText = document.getElementById('copy-btn-text');

        if (copyUrlBtn) {
            copyUrlBtn.addEventListener('click', async function() {
                const url = this.dataset.url;
                
                try {
                    await navigator.clipboard.writeText(url);
                    
                    // ボタンの表示を変更
                    copyUrlBtn.classList.add('copied');
                    copyBtnText.textContent = 'コピーしました！';
                    
                    // 2秒後に元に戻す
                    setTimeout(() => {
                        copyUrlBtn.classList.remove('copied');
                        copyBtnText.textContent = 'URLをコピー';
                    }, 2000);
                } catch (err) {
                    // フォールバック（古いブラウザ用）
                    const textarea = document.createElement('textarea');
                    textarea.value = url;
                    textarea.style.position = 'fixed';
                    textarea.style.opacity = '0';
                    document.body.appendChild(textarea);
                    textarea.select();
                    
                    try {
                        document.execCommand('copy');
                        copyUrlBtn.classList.add('copied');
                        copyBtnText.textContent = 'コピーしました！';
                        
                        setTimeout(() => {
                            copyUrlBtn.classList.remove('copied');
                            copyBtnText.textContent = 'URLをコピー';
                        }, 2000);
                    } catch (e) {
                        alert('コピーに失敗しました。URLを手動でコピーしてください。');
                    }
                    
                    document.body.removeChild(textarea);
                }
            });
        }
    </script>
</div>
@endsection
