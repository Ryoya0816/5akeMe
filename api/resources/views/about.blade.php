{{-- resources/views/about.blade.php --}}
@extends('layouts.app')

@section('title', 'このサービスについて - 5akeMe')
@section('description', '5akeMeは簡単な質問に答えるだけで、あなたにぴったりのお酒を診断するサービスです。サービスの特徴や作った人について紹介します。')
@section('og_title', 'このサービスについて - 5akeMe')
@section('og_description', '5akeMeの特徴や作った人について紹介します。')

@section('content')
<div class="about-page">
    <style>
        .about-page {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .about-header {
            text-align: center;
            margin-bottom: 48px;
        }

        .about-logo {
            margin-bottom: 16px;
            display: flex;
            justify-content: center;
            color: var(--brand-main, #9c3f2e);
        }

        .about-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--brand-main, #9c3f2e);
            margin-bottom: 8px;
        }

        .about-subtitle {
            font-size: 14px;
            color: var(--text-sub, #8c6d57);
        }

        .about-section {
            background: var(--card-bg, #ffffff);
            border: 1px solid var(--line-soft, #f1dfd0);
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 24px;
            box-shadow: var(--shadow, 0 10px 20px rgba(0, 0, 0, 0.06));
        }

        .about-section-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--brand-main, #9c3f2e);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .about-section-icon {
            display: flex;
        }

        .about-text {
            font-size: 15px;
            line-height: 1.9;
            color: var(--text-main, #3f3f3f);
        }

        .about-text p {
            margin-bottom: 16px;
        }

        .about-text p:last-child {
            margin-bottom: 0;
        }

        /* Creator Section */
        .creator-card {
            display: flex;
            gap: 24px;
            align-items: flex-start;
        }

        .creator-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--brand-main) 0%, #c9644f 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            flex-shrink: 0;
        }

        .creator-info {
            flex: 1;
        }

        .creator-name {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-main, #3f3f3f);
            margin-bottom: 4px;
        }

        .creator-role {
            font-size: 13px;
            color: var(--text-sub, #8c6d57);
            margin-bottom: 12px;
        }

        .creator-bio {
            font-size: 14px;
            line-height: 1.8;
            color: var(--text-main, #3f3f3f);
            margin-bottom: 16px;
        }

        .creator-social {
            display: flex;
            gap: 12px;
        }

        .creator-social-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            background: var(--bg-soft, #fff7ee);
            border: 1px solid var(--line-soft, #f1dfd0);
            border-radius: var(--radius-full, 999px);
            color: var(--text-main, #3f3f3f);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all var(--transition-normal, 200ms ease-out);
        }

        .creator-social-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .creator-social-link--instagram:hover {
            background: linear-gradient(45deg, #f09433, #dc2743, #bc1888);
            color: #ffffff;
            border-color: transparent;
        }

        .creator-social-link--note:hover {
            background: #41c9b4;
            color: #ffffff;
            border-color: transparent;
        }

        /* Features */
        .about-features {
            display: grid;
            gap: 16px;
            margin-top: 16px;
        }

        .about-feature {
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .about-feature-icon {
            display: flex;
            align-items: flex-start;
            padding-top: 2px;
            color: var(--brand-main, #9c3f2e);
        }

        .about-feature-text {
            font-size: 14px;
            line-height: 1.6;
            color: var(--text-main, #3f3f3f);
        }

        .about-feature-title {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .about-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 32px;
            padding: 12px 24px;
            background: var(--bg-soft, #fff7ee);
            border: 1px solid var(--line-soft, #f1dfd0);
            border-radius: var(--radius-full, 999px);
            color: var(--brand-main, #9c3f2e);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all var(--transition-normal, 200ms ease-out);
        }

        .about-back:hover {
            background: var(--brand-main, #9c3f2e);
            color: #ffffff;
            transform: translateY(-2px);
        }

        @media (max-width: 640px) {
            .about-page {
                padding: 24px 16px;
            }

            .about-title {
                font-size: 24px;
            }

            .about-section {
                padding: 24px 20px;
            }

            .about-section-title {
                font-size: 18px;
            }

            .about-text {
                font-size: 14px;
            }

            .creator-card {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .creator-avatar {
                width: 80px;
                height: 80px;
                font-size: 36px;
            }

            .creator-social {
                justify-content: center;
            }
        }
    </style>

    <header class="about-header fade-in">
        <div class="about-logo"><x-svg-icon name="wine" size="48" /></div>
        <h1 class="about-title">このサービスについて</h1>
        <p class="about-subtitle">5akeMe - あなたにぴったりのお酒を見つけよう</p>
    </header>

    <!-- 5akeMeとは -->
    <section class="about-section fade-in">
        <h2 class="about-section-title">
            <span class="about-section-icon"><x-svg-icon name="sparkles" size="22" /></span>
            5akeMeとは
        </h2>
        <div class="about-text">
            <p>
                5akeMeは、簡単な質問に答えるだけで、あなたにぴったりのお酒を診断するサービスです。
            </p>
            <p>
                「お酒の種類が多すぎて何を選べばいいかわからない」<br>
                「自分の好みに合うお酒を知りたい」<br>
                そんな悩みを解決するために生まれました。
            </p>
            <p>
                <!-- TODO: プロジェクトの詳細な説明をここに追加 -->
                日本酒、焼酎、ワイン、ビール...さまざまなお酒の中から、あなたの好みや気分に合った一杯を見つけるお手伝いをします。
            </p>
        </div>
    </section>

    <!-- 特徴 -->
    <section class="about-section fade-in">
        <h2 class="about-section-title">
            <span class="about-section-icon"><x-svg-icon name="target" size="22" /></span>
            5akeMeの特徴
        </h2>
        <div class="about-features">
            <div class="about-feature fade-in stagger-1 hover-lift">
                <span class="about-feature-icon"><x-svg-icon name="message-square" size="22" /></span>
                <div class="about-feature-text">
                    <div class="about-feature-title">チャット形式で簡単診断</div>
                    5つの質問に答えるだけ。難しい知識は必要ありません。
                </div>
            </div>
            <div class="about-feature fade-in stagger-2 hover-lift">
                <span class="about-feature-icon"><x-svg-icon name="palette" size="22" /></span>
                <div class="about-feature-text">
                    <div class="about-feature-title">あなたの好みを可視化</div>
                    診断結果をレーダーチャートで表示。自分の好みの傾向がひと目でわかります。
                </div>
            </div>
            <div class="about-feature fade-in stagger-3 hover-lift">
                <span class="about-feature-icon"><x-svg-icon name="wine" size="22" /></span>
                <div class="about-feature-text">
                    <div class="about-feature-title">TOP5のおすすめ</div>
                    あなたに合ったお酒のタイプをランキング形式でご紹介します。
                </div>
            </div>
        </div>
    </section>

    <!-- 作った人 -->
    <section class="about-section fade-in">
        <h2 class="about-section-title">
            <span class="about-section-icon"><x-svg-icon name="code" size="22" /></span>
            作った人
        </h2>
        <div class="creator-card">
            <div class="creator-avatar">🐣</div>
            <div class="creator-info">
                <div class="creator-name">Ryoya</div>
                <div class="creator-role">Developer / Creator</div>
                <p class="creator-bio">
                    SEの卵。佐賀んもんでプログラミング言語と佐賀弁を勉強中。<br>
                    佐賀ゆめと幸陽閣を愛してる、ITで地元をじわじわ盛り上げるひよっこエンジニア🐣<br>
                    <strong>#HelloSAGAworld</strong>
                </p>
                <div class="creator-social">
                    <a 
                        href="https://www.instagram.com/hello.saga.world/" 
                        class="creator-social-link creator-social-link--instagram"
                        target="_blank" 
                        rel="noopener noreferrer"
                    >
                        <x-svg-icon name="camera" size="16" /> Instagram
                    </a>
                    <a 
                        href="https://note.com/hello_sagaworld" 
                        class="creator-social-link creator-social-link--note"
                        target="_blank" 
                        rel="noopener noreferrer"
                    >
                        <x-svg-icon name="edit" size="16" /> NOTE
                    </a>
                </div>
            </div>
        </div>
    </section>

    <a href="{{ route('top') }}" class="about-back">
        <x-svg-icon name="arrow-left" size="16" />
        <span>トップに戻る</span>
    </a>
</div>
@endsection
