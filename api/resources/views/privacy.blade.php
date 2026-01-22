{{-- resources/views/privacy.blade.php --}}
@extends('layouts.app')

@section('title', 'プライバシーポリシー - 5akeMe')
@section('description', '5akeMeのプライバシーポリシーです。個人情報の取り扱いについて説明しています。')
@section('og_title', 'プライバシーポリシー - 5akeMe')

@section('content')
<div class="legal-page">
    <style>
        .legal-page {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .legal-header {
            text-align: center;
            margin-bottom: 48px;
        }

        .legal-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--brand-main, #9c3f2e);
            margin-bottom: 8px;
        }

        .legal-updated {
            font-size: 13px;
            color: var(--text-sub, #8c6d57);
        }

        .legal-content {
            background: var(--card-bg, #ffffff);
            border: 1px solid var(--line-soft, #f1dfd0);
            border-radius: 16px;
            padding: 32px;
            box-shadow: var(--shadow, 0 10px 20px rgba(0, 0, 0, 0.06));
        }

        .legal-section {
            margin-bottom: 32px;
        }

        .legal-section:last-child {
            margin-bottom: 0;
        }

        .legal-section-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--brand-main, #9c3f2e);
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--line-soft, #f1dfd0);
        }

        .legal-text {
            font-size: 14px;
            line-height: 1.8;
            color: var(--text-main, #3f3f3f);
        }

        .legal-text p {
            margin-bottom: 12px;
        }

        .legal-text p:last-child {
            margin-bottom: 0;
        }

        .legal-list {
            list-style: none;
            padding: 0;
            margin: 12px 0;
        }

        .legal-list li {
            position: relative;
            padding-left: 20px;
            margin-bottom: 8px;
        }

        .legal-list li::before {
            content: '•';
            position: absolute;
            left: 0;
            color: var(--brand-main, #9c3f2e);
            font-weight: bold;
        }

        .legal-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 32px;
            padding: 12px 24px;
            background: var(--bg-soft, #fff7ee);
            border: 1px solid var(--line-soft, #f1dfd0);
            border-radius: 999px;
            color: var(--brand-main, #9c3f2e);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease-out;
        }

        .legal-back:hover {
            background: var(--brand-main, #9c3f2e);
            color: #ffffff;
            transform: translateY(-2px);
        }

        @media (max-width: 640px) {
            .legal-page {
                padding: 24px 16px;
            }

            .legal-title {
                font-size: 24px;
            }

            .legal-content {
                padding: 24px 20px;
            }

            .legal-section-title {
                font-size: 16px;
            }

            .legal-text {
                font-size: 13px;
            }
        }
    </style>

    <header class="legal-header">
        <h1 class="legal-title">プライバシーポリシー</h1>
        <p class="legal-updated">最終更新日: {{ date('Y年n月j日') }}</p>
    </header>

    <div class="legal-content">
        <section class="legal-section">
            <h2 class="legal-section-title">1. はじめに</h2>
            <div class="legal-text">
                <p>5akeMe運営（以下「当方」）は、お酒診断サービス「5akeMe」（以下「本サービス」）において、ユーザーの皆様のプライバシーを尊重し、個人情報の保護に努めております。</p>
                <p>本プライバシーポリシーは、本サービスにおける個人情報の取り扱いについて説明するものです。</p>
            </div>
        </section>

        <section class="legal-section">
            <h2 class="legal-section-title">2. 収集する情報</h2>
            <div class="legal-text">
                <p>当方は、本サービスの提供にあたり、以下の情報を収集することがあります。</p>
                <ul class="legal-list">
                    <li><strong>アカウント情報:</strong> 会員登録時にご提供いただくメールアドレス、パスワード（暗号化して保存）、ニックネーム</li>
                    <li><strong>年齢確認情報:</strong> 20歳以上であることの確認結果（Cookie情報）</li>
                    <li><strong>診断結果:</strong> お酒診断で回答いただいた内容および診断結果</li>
                    <li><strong>利用情報:</strong> アクセス日時、利用したページ、ブラウザの種類、IPアドレスなど</li>
                    <li><strong>Cookie情報:</strong> 年齢確認状態の保持、ログイン状態の維持、サービス改善のための情報</li>
                </ul>
            </div>
        </section>

        <section class="legal-section">
            <h2 class="legal-section-title">3. 情報の利用目的</h2>
            <div class="legal-text">
                <p>収集した情報は、以下の目的で利用いたします。</p>
                <ul class="legal-list">
                    <li>本サービスの提供および運営</li>
                    <li>アカウントの作成・認証・管理</li>
                    <li>年齢確認の実施および確認状態の保持</li>
                    <li>診断結果の表示、保存およびアカウントとの紐付け</li>
                    <li>サービスに関する重要なお知らせの送信</li>
                    <li>サービスの改善および新機能の開発</li>
                    <li>利用状況の分析</li>
                    <li>不正利用の防止</li>
                </ul>
            </div>
        </section>

        <section class="legal-section">
            <h2 class="legal-section-title">4. 第三者への提供</h2>
            <div class="legal-text">
                <p>当方は、以下の場合を除き、ユーザーの情報を第三者に提供することはありません。</p>
                <ul class="legal-list">
                    <li>ユーザーの同意がある場合</li>
                    <li>法令に基づく場合</li>
                    <li>人の生命、身体または財産の保護のために必要がある場合</li>
                    <li>公衆衛生の向上または児童の健全な育成の推進のために特に必要がある場合</li>
                    <li>国の機関もしくは地方公共団体またはその委託を受けた者が法令の定める事務を遂行することに対して協力する必要がある場合</li>
                </ul>
            </div>
        </section>

        <section class="legal-section">
            <h2 class="legal-section-title">5. Cookieの使用について</h2>
            <div class="legal-text">
                <p>本サービスでは、以下の目的でCookieを使用しています。</p>
                <ul class="legal-list">
                    <li><strong>年齢確認:</strong> 20歳以上であることの確認結果を保存し、再訪問時の年齢確認を省略するため（有効期限: 1年間）</li>
                    <li><strong>サービス改善:</strong> 利用状況を分析し、サービスの改善に役立てるため</li>
                </ul>
                <p>ブラウザの設定によりCookieを無効にすることができますが、その場合、本サービスの一部機能がご利用いただけない場合があります。</p>
            </div>
        </section>

        <section class="legal-section">
            <h2 class="legal-section-title">6. 情報の保護</h2>
            <div class="legal-text">
                <p>当方は、収集した情報の紛失、破壊、改ざん、漏洩等を防止するため、適切なセキュリティ対策を講じています。</p>
                <ul class="legal-list">
                    <li>パスワードは業界標準の暗号化方式（bcrypt）を用いてハッシュ化し保存します</li>
                    <li>通信はSSL/TLSにより暗号化されます</li>
                    <li>定期的なセキュリティ監査を実施します</li>
                </ul>
                <p>ただし、インターネット上のデータ送信は完全に安全であることを保証することはできません。</p>
            </div>
        </section>

        <section class="legal-section">
            <h2 class="legal-section-title">7. ユーザーの権利</h2>
            <div class="legal-text">
                <p>ユーザーは、ご自身の個人情報について、以下の権利を有します。</p>
                <ul class="legal-list">
                    <li><strong>アクセス権:</strong> ご自身のアカウント情報を確認・閲覧する権利</li>
                    <li><strong>訂正権:</strong> 不正確な情報を訂正する権利</li>
                    <li><strong>削除権:</strong> アカウントおよび関連データの削除を請求する権利</li>
                    <li><strong>データポータビリティ:</strong> ご自身の診断履歴データを取得する権利</li>
                </ul>
                <p>これらの権利の行使をご希望の場合は、アカウント設定ページから行うか、お問い合わせ窓口までご連絡ください。</p>
            </div>
        </section>

        <section class="legal-section">
            <h2 class="legal-section-title">8. お子様のプライバシー</h2>
            <div class="legal-text">
                <p>本サービスは20歳以上の方を対象としており、20歳未満の方からの情報を意図的に収集することはありません。</p>
                <p>20歳未満の方が本サービスを利用していることが判明した場合、当方は該当する情報（アカウントを含む）を速やかに削除いたします。</p>
            </div>
        </section>

        <section class="legal-section">
            <h2 class="legal-section-title">9. プライバシーポリシーの変更</h2>
            <div class="legal-text">
                <p>当方は、必要に応じて本プライバシーポリシーを変更することがあります。</p>
                <p>変更後のプライバシーポリシーは、本ページに掲載した時点から効力を生じるものとします。重要な変更がある場合は、登録いただいたメールアドレスへの通知または本サービス上でお知らせいたします。</p>
            </div>
        </section>

        <section class="legal-section">
            <h2 class="legal-section-title">10. お問い合わせ</h2>
            <div class="legal-text">
                <p>本プライバシーポリシーに関するお問い合わせは、以下の窓口までお願いいたします。</p>
                <p>サービス名: 5akeMe<br>
                運営: 5akeMe運営チーム<br>
                メール: hello.sagaworld816@gmail.com</p>
            </div>
        </section>
    </div>

    <a href="{{ route('top') }}" class="legal-back">
        <span>←</span>
        <span>トップに戻る</span>
    </a>
</div>
@endsection
