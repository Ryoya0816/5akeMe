{{-- resources/views/terms.blade.php --}}
@extends('layouts.app')

@section('title', '利用規約 - 5akeMe')
@section('description', '5akeMeの利用規約です。本サービスをご利用いただく前にお読みください。')
@section('og_title', '利用規約 - 5akeMe')

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
        <h1 class="legal-title">利用規約</h1>
        <p class="legal-updated">最終更新日: {{ date('Y年n月j日') }}</p>
    </header>

    <div class="legal-content">
        <section class="legal-section">
            <h2 class="legal-section-title">第1条（適用）</h2>
            <div class="legal-text">
                <p>本利用規約（以下「本規約」）は、5akeMe運営（以下「当方」）が提供するお酒診断サービス「5akeMe」（以下「本サービス」）の利用条件を定めるものです。</p>
                <p>ユーザーの皆様（以下「ユーザー」）には、本規約に従って本サービスをご利用いただきます。</p>
            </div>
        </section>

        <section class="legal-section">
            <h2 class="legal-section-title">第2条（利用資格）</h2>
            <div class="legal-text">
                <p>本サービスは、日本国内において20歳以上の方のみご利用いただけます。</p>
                <p>20歳未満の方は、本サービスをご利用いただくことはできません。年齢確認画面にて、20歳以上であることをご確認いただいた上でサービスをご利用ください。</p>
            </div>
        </section>

        <section class="legal-section">
            <h2 class="legal-section-title">第3条（禁止事項）</h2>
            <div class="legal-text">
                <p>ユーザーは、本サービスの利用にあたり、以下の行為をしてはなりません。</p>
                <ul class="legal-list">
                    <li>法令または公序良俗に違反する行為</li>
                    <li>犯罪行為に関連する行為</li>
                    <li>当方のサーバーまたはネットワークの機能を破壊したり、妨害したりする行為</li>
                    <li>本サービスの運営を妨害するおそれのある行為</li>
                    <li>他のユーザーに関する個人情報等を収集または蓄積する行為</li>
                    <li>他のユーザーに成りすます行為</li>
                    <li>20歳未満の方が本サービスを利用する行為</li>
                    <li>当方のサービスに関連して、反社会的勢力に対して直接または間接に利益を供与する行為</li>
                    <li>その他、当方が不適切と判断する行為</li>
                </ul>
            </div>
        </section>

        <section class="legal-section">
            <h2 class="legal-section-title">第4条（本サービスの提供の停止等）</h2>
            <div class="legal-text">
                <p>当方は、以下のいずれかの事由があると判断した場合、ユーザーに事前に通知することなく本サービスの全部または一部の提供を停止または中断することができるものとします。</p>
                <ul class="legal-list">
                    <li>本サービスにかかるコンピュータシステムの保守点検または更新を行う場合</li>
                    <li>地震、落雷、火災、停電または天災などの不可抗力により、本サービスの提供が困難となった場合</li>
                    <li>コンピュータまたは通信回線等が事故により停止した場合</li>
                    <li>その他、当方が本サービスの提供が困難と判断した場合</li>
                </ul>
                <p>当方は、本サービスの提供の停止または中断により、ユーザーまたは第三者が被ったいかなる不利益または損害について、理由を問わず一切の責任を負わないものとします。</p>
            </div>
        </section>

        <section class="legal-section">
            <h2 class="legal-section-title">第5条（免責事項）</h2>
            <div class="legal-text">
                <p>当方は、本サービスに事実上または法律上の瑕疵（安全性、信頼性、正確性、完全性、有効性、特定の目的への適合性、セキュリティなどに関する欠陥、エラーやバグ、権利侵害などを含みます）がないことを明示的にも黙示的にも保証しておりません。</p>
                <p>本サービスで提供される診断結果は、エンターテインメント目的であり、お酒の選択を強制するものではありません。実際のお酒の選択は、ご自身の判断と責任において行ってください。</p>
                <p>当方は、本サービスによってユーザーに生じたあらゆる損害について、一切の責任を負いません。</p>
            </div>
        </section>

        <section class="legal-section">
            <h2 class="legal-section-title">第6条（サービス内容の変更等）</h2>
            <div class="legal-text">
                <p>当方は、ユーザーに通知することなく、本サービスの内容を変更しまたは本サービスの提供を中止することができるものとし、これによってユーザーに生じた損害について一切の責任を負いません。</p>
            </div>
        </section>

        <section class="legal-section">
            <h2 class="legal-section-title">第7条（利用規約の変更）</h2>
            <div class="legal-text">
                <p>当方は、必要と判断した場合には、ユーザーに通知することなくいつでも本規約を変更することができるものとします。</p>
                <p>変更後の利用規約は、当方ウェブサイトに掲示した時点から効力を生じるものとします。</p>
            </div>
        </section>

        <section class="legal-section">
            <h2 class="legal-section-title">第8条（準拠法・裁判管轄）</h2>
            <div class="legal-text">
                <p>本規約の解釈にあたっては、日本法を準拠法とします。</p>
                <p>本サービスに関して紛争が生じた場合には、当方の本店所在地を管轄する裁判所を専属的合意管轄とします。</p>
            </div>
        </section>

        <section class="legal-section">
            <h2 class="legal-section-title">第9条（お問い合わせ）</h2>
            <div class="legal-text">
                <p>本規約に関するお問い合わせは、以下の窓口までお願いいたします。</p>
                <p>サービス名: 5akeMe<br>
                運営: 5akeMe運営チーム<br>
                メール: contact@5akeme.com</p>
            </div>
        </section>
    </div>

    <a href="{{ route('top') }}" class="legal-back">
        <span>←</span>
        <span>トップに戻る</span>
    </a>
</div>
@endsection
