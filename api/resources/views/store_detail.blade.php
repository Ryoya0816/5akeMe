{{-- resources/views/store_detail.blade.php --}}
@extends('layouts.app')

@section('title', $store->name . ' - 5akeMe')
@section('description', $store->name . 'の店舗情報。' . ($store->address ?? '佐賀市') . 'のおすすめのお店です。')

@section('content')
<div class="store-detail-page">
    <style>
        .store-detail-page {
            max-width: 700px;
            margin: 0 auto;
            padding: 32px 16px;
        }

        .store-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            color: var(--text-sub, #8c6d57);
            text-decoration: none;
            margin-bottom: 24px;
            transition: color 0.2s;
        }

        .store-back:hover {
            color: var(--brand-main, #9c3f2e);
        }

        .store-card {
            background: var(--card-bg, #ffffff);
            border: 1px solid var(--line-soft, #f1dfd0);
            border-radius: 20px;
            padding: 32px;
            box-shadow: var(--shadow, 0 10px 20px rgba(0,0,0,0.06));
        }

        .store-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 24px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--line-soft, #f1dfd0);
        }

        .store-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--brand-main, #9c3f2e);
            margin: 0;
            flex: 1;
        }

        .store-mood-badge {
            font-size: 14px;
            padding: 8px 16px;
            background: var(--bg-soft, #fff7ee);
            border: 1px solid var(--line-soft, #f1dfd0);
            border-radius: 999px;
            white-space: nowrap;
        }

        .store-section {
            margin-bottom: 24px;
        }

        .store-section-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-sub, #8c6d57);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .store-info-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .store-info-row {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 15px;
            color: var(--text-main, #3f3f3f);
        }

        .store-info-icon {
            font-size: 18px;
            flex-shrink: 0;
            width: 24px;
            text-align: center;
        }

        .store-info-content {
            flex: 1;
        }

        .store-info-label {
            font-size: 12px;
            color: var(--text-sub, #8c6d57);
            margin-bottom: 2px;
        }

        .store-sake-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .store-sake-tag {
            font-size: 13px;
            padding: 6px 14px;
            background: var(--brand-main, #9c3f2e);
            color: #ffffff;
            border-radius: 999px;
        }

        .store-actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid var(--line-soft, #f1dfd0);
        }

        .store-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px 24px;
            border-radius: 999px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease-out;
        }

        .store-btn-primary {
            background: #4285f4;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(66, 133, 244, 0.3);
        }

        .store-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(66, 133, 244, 0.4);
        }

        .store-btn-secondary {
            background: var(--bg-soft, #fff7ee);
            color: var(--brand-main, #9c3f2e);
            border: 1px solid var(--line-soft, #f1dfd0);
        }

        .store-btn-secondary:hover {
            background: var(--brand-main, #9c3f2e);
            color: #ffffff;
            transform: translateY(-2px);
        }

        .store-btn-phone {
            background: #34c759;
            color: #ffffff;
        }

        .store-btn-phone:hover {
            background: #2db84d;
            transform: translateY(-2px);
        }

        .store-note {
            margin-top: 24px;
            font-size: 12px;
            color: var(--text-sub, #8c6d57);
            text-align: center;
        }

        @media (max-width: 640px) {
            .store-detail-page {
                padding: 20px 12px;
            }

            .store-card {
                padding: 24px 20px;
            }

            .store-header {
                flex-direction: column;
                gap: 12px;
            }

            .store-title {
                font-size: 20px;
            }

            .store-info-row {
                font-size: 14px;
            }

            .store-actions {
                gap: 10px;
            }

            .store-btn {
                padding: 12px 20px;
                font-size: 14px;
            }
        }
    </style>

    <a href="javascript:history.back()" class="store-back">
        ← 戻る
    </a>

    <div class="store-card">
        <header class="store-header">
            <h1 class="store-title">{{ $store->name }}</h1>
            <span class="store-mood-badge">
                @if($store->mood === 'lively')
                    🎉 にぎやか系
                @elseif($store->mood === 'calm')
                    🌙 落ち着き系
                @else
                    ✨ どちらもOK
                @endif
            </span>
        </header>

        <section class="store-section">
            <h2 class="store-section-title">📍 店舗情報</h2>
            <div class="store-info-list">
                @if($store->address)
                    <div class="store-info-row">
                        <span class="store-info-icon">🏠</span>
                        <div class="store-info-content">
                            <div class="store-info-label">住所</div>
                            <div>{{ $store->address }}</div>
                        </div>
                    </div>
                @endif

                @if($store->business_hours)
                    <div class="store-info-row">
                        <span class="store-info-icon">🕐</span>
                        <div class="store-info-content">
                            <div class="store-info-label">営業時間</div>
                            <div>{{ $store->business_hours }}</div>
                        </div>
                    </div>
                @endif

                @if($store->closed_days)
                    <div class="store-info-row">
                        <span class="store-info-icon">📅</span>
                        <div class="store-info-content">
                            <div class="store-info-label">定休日</div>
                            <div>{{ $store->closed_days }}</div>
                        </div>
                    </div>
                @endif

                @if($store->phone)
                    <div class="store-info-row">
                        <span class="store-info-icon">📞</span>
                        <div class="store-info-content">
                            <div class="store-info-label">電話番号</div>
                            <div>{{ $store->phone }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        @if($store->sake_types && count($store->sake_types) > 0)
            <section class="store-section">
                <h2 class="store-section-title">🍶 おすすめのお酒</h2>
                <div class="store-sake-tags">
                    @php
                        $sakeLabels = \App\Models\Store::sakeTypeOptions();
                    @endphp
                    @foreach($store->sake_types as $type)
                        <span class="store-sake-tag">{{ $sakeLabels[$type] ?? $type }}</span>
                    @endforeach
                </div>
            </section>
        @endif

        <div class="store-actions">
            @if($store->address)
                <a 
                    href="https://www.google.com/maps/search/?api=1&query={{ urlencode($store->address) }}" 
                    target="_blank" 
                    rel="noopener noreferrer" 
                    class="store-btn store-btn-primary"
                >
                    📍 Googleマップで見る
                </a>
            @endif

            @if($store->phone)
                <a href="tel:{{ $store->phone }}" class="store-btn store-btn-phone">
                    📞 電話する
                </a>
            @endif

            @if($store->website_url)
                <a 
                    href="{{ $store->website_url }}" 
                    target="_blank" 
                    rel="noopener noreferrer" 
                    class="store-btn store-btn-secondary"
                >
                    🌐 お店のサイトを見る
                </a>
            @endif
        </div>
    </div>

    <p class="store-note">
        ※ 店舗情報は変更される場合があります。お出かけ前にご確認ください。
    </p>

    <!-- 情報更新報告フォーム -->
    <div class="store-report">
        @if(session('reported'))
            <!-- 報告完了メッセージ -->
            <div class="report-success" id="report-success">
                <div class="report-success-icon">🎉</div>
                <h3 class="report-success-title">ご報告ありがとうございます！</h3>
                <p class="report-success-message">
                    管理者に報告が届きました！<br>
                    確認して店舗情報を更新します 🍶
                </p>
                <div class="report-success-note">
                    いつも5akeMeをご利用いただきありがとうございます
                </div>
            </div>
        @else
            <button type="button" class="report-toggle" id="report-toggle">
                📝 情報が違う？報告する
            </button>

            <div class="report-form-container" id="report-form" style="display: none;">
                <form 
                    class="report-form"
                    action="{{ route('store.report', $store->id) }}"
                    method="POST"
                >
                    @csrf

                    <h3 class="report-title">店舗情報の修正報告</h3>
                    <p class="report-desc">営業時間や定休日など、情報が変わっていたら教えてください！</p>

                    <div class="report-group">
                        <label class="report-label">どの情報が違いますか？</label>
                        <div class="report-checkboxes">
                            <label class="report-checkbox">
                                <input type="checkbox" name="update_types[]" value="営業時間">
                                <span>🕐 営業時間</span>
                            </label>
                            <label class="report-checkbox">
                                <input type="checkbox" name="update_types[]" value="定休日">
                                <span>📅 定休日</span>
                            </label>
                            <label class="report-checkbox">
                                <input type="checkbox" name="update_types[]" value="電話番号">
                                <span>📞 電話番号</span>
                            </label>
                            <label class="report-checkbox">
                                <input type="checkbox" name="update_types[]" value="住所">
                                <span>📍 住所</span>
                            </label>
                            <label class="report-checkbox">
                                <input type="checkbox" name="update_types[]" value="閉店">
                                <span>🚫 閉店した</span>
                            </label>
                            <label class="report-checkbox">
                                <input type="checkbox" name="update_types[]" value="その他">
                                <span>📝 その他</span>
                            </label>
                        </div>
                    </div>

                    <div class="report-group">
                        <label for="report-detail" class="report-label">正しい情報・詳細</label>
                        <textarea 
                            id="report-detail" 
                            name="detail" 
                            class="report-textarea" 
                            placeholder="例: 営業時間が18:00〜24:00に変更されていました"
                            rows="3"
                            required
                        ></textarea>
                    </div>

                    <button type="submit" class="report-submit">送信する 📨</button>
                </form>
            </div>
        @endif
    </div>

    <style>
        .store-report {
            margin-top: 24px;
        }

        .report-success {
            padding: 32px 24px;
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            border: 2px solid #86efac;
            border-radius: 20px;
            text-align: center;
            animation: successPop 0.5s ease-out;
        }

        @keyframes successPop {
            0% {
                opacity: 0;
                transform: scale(0.9);
            }
            50% {
                transform: scale(1.02);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .report-success-icon {
            font-size: 48px;
            margin-bottom: 16px;
            animation: bounce 1s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-8px);
            }
        }

        .report-success-title {
            font-size: 20px;
            font-weight: 700;
            color: #166534;
            margin: 0 0 12px 0;
        }

        .report-success-message {
            font-size: 15px;
            color: #166534;
            margin: 0 0 16px 0;
            line-height: 1.7;
        }

        .report-success-note {
            font-size: 12px;
            color: #15803d;
            opacity: 0.8;
        }

        .report-toggle {
            width: 100%;
            padding: 14px 20px;
            background: var(--bg-soft, #fff7ee);
            border: 1px dashed var(--line-soft, #f1dfd0);
            border-radius: 12px;
            font-size: 14px;
            color: var(--text-sub, #8c6d57);
            cursor: pointer;
            transition: all 0.2s;
        }

        .report-toggle:hover {
            background: var(--line-soft, #f1dfd0);
            color: var(--brand-main, #9c3f2e);
        }

        .report-toggle.active {
            border-style: solid;
            border-color: var(--brand-main, #9c3f2e);
            color: var(--brand-main, #9c3f2e);
        }

        .report-form-container {
            margin-top: 16px;
            padding: 24px;
            background: var(--card-bg, #ffffff);
            border: 1px solid var(--line-soft, #f1dfd0);
            border-radius: 16px;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .report-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--brand-main, #9c3f2e);
            margin: 0 0 8px 0;
        }

        .report-desc {
            font-size: 13px;
            color: var(--text-sub, #8c6d57);
            margin: 0 0 20px 0;
        }

        .report-group {
            margin-bottom: 20px;
        }

        .report-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-main, #3f3f3f);
            margin-bottom: 10px;
        }

        .report-checkboxes {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .report-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: var(--text-main, #3f3f3f);
            cursor: pointer;
            padding: 8px 12px;
            background: var(--bg-soft, #fff7ee);
            border-radius: 8px;
            transition: all 0.2s;
        }

        .report-checkbox:hover {
            background: var(--line-soft, #f1dfd0);
        }

        .report-checkbox input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--brand-main, #9c3f2e);
        }

        .report-checkbox input[type="checkbox"]:checked + span {
            color: var(--brand-main, #9c3f2e);
            font-weight: 600;
        }

        .report-textarea {
            width: 100%;
            padding: 12px 14px;
            font-size: 14px;
            border: 1px solid var(--line-soft, #f1dfd0);
            border-radius: 10px;
            background: var(--bg-soft, #fff7ee);
            color: var(--text-main, #3f3f3f);
            resize: vertical;
            font-family: inherit;
            box-sizing: border-box;
        }

        .report-textarea:focus {
            outline: none;
            border-color: var(--brand-main, #9c3f2e);
            background: #ffffff;
        }

        .report-submit {
            width: 100%;
            padding: 14px 20px;
            background: var(--brand-main, #9c3f2e);
            color: #ffffff;
            border: none;
            border-radius: 999px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .report-submit:hover {
            background: var(--brand-text, #8a3a28);
            transform: translateY(-1px);
        }


        @media (max-width: 640px) {
            .report-form-container {
                padding: 20px 16px;
            }

            .report-checkboxes {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('report-toggle');
            const form = document.getElementById('report-form');

            if (toggle && form) {
                toggle.addEventListener('click', function() {
                    const isHidden = form.style.display === 'none';
                    form.style.display = isHidden ? 'block' : 'none';
                    toggle.classList.toggle('active', isHidden);
                    toggle.textContent = isHidden ? '✕ 閉じる' : '📝 情報が違う？報告する';
                });
            }

        });
    </script>
</div>
@endsection
