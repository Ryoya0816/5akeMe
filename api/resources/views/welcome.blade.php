{{-- resources/views/welcome.blade.php --}}
@extends('layouts.app')

@section('title', '5akeMe - あなたにぴったりのお酒診断')
@section('description', '5akeMe（サケミー）は、5つの質問に答えるだけであなたにぴったりのお酒を診断するサービスです。日本酒、焼酎、ワイン、ビールなど、あなたの好みに合った一杯を見つけよう。')
@section('og_title', '5akeMe - あなたにぴったりのお酒診断')
@section('og_description', '5つの質問に答えるだけで、あなたにぴったりのお酒が見つかる！')
@section('body_class', 'page-welcome')
@section('main_class', 'wrap--welcome')
@section('hide_header', true)
@section('hide_footer', true)

@section('content')
<style>
  /* Welcome 専用スタイル - SAKEICEライクなデザイン */
  :root {
    --noren-bg: #f5e6d3;
    --noren-line: #9c3f2e;
    --brand-main: #9c3f2e;
    --footer-color: #9c3f2e;
    --page-bg: #fbf3e8;
  }
  
  .wrap.wrap--welcome { 
    padding: 0 !important; 
    max-width: none !important; 
    margin: 0 !important; 
  }
  
  .welcome-kv {
    position: relative;
    width: 100%;
    height: 100svh;
    overflow: hidden;
    background: var(--page-bg);
  }
  
  /* 暖簾コンテナ（画面全体を覆う） */
  .noren {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 15%; /* 下部の帯分を空ける */
    display: flex;
    justify-content: center;
    z-index: 1;
  }
  
  /* 左の暖簾 */
  .noren__left {
    position: absolute;
    top: 0;
    left: 0;
    width: 50%;
    height: 100%;
    background: var(--noren-bg);
    clip-path: polygon(0 0, 100% 0, 60% 100%, 0 100%);
    transition: transform 800ms cubic-bezier(.4, 0, .2, 1), opacity 600ms ease;
  }
  
  /* 左暖簾の縦ライン */
  .noren__left::after {
    content: '';
    position: absolute;
    top: 0;
    right: 35%;
    width: 3px;
    height: 100%;
    background: var(--noren-line);
  }
  
  /* 右の暖簾 */
  .noren__right {
    position: absolute;
    top: 0;
    right: 0;
    width: 50%;
    height: 100%;
    background: var(--noren-bg);
    clip-path: polygon(0 0, 100% 0, 100% 100%, 40% 100%);
    transition: transform 800ms cubic-bezier(.4, 0, .2, 1), opacity 600ms ease;
  }
  
  /* 右暖簾の縦ライン */
  .noren__right::after {
    content: '';
    position: absolute;
    top: 0;
    left: 35%;
    width: 3px;
    height: 100%;
    background: var(--noren-line);
  }
  
  /* 暖簾が開くアニメーション */
  .noren.is-active .noren__left {
    transform: translateX(-30%);
  }
  .noren.is-active .noren__right {
    transform: translateX(30%);
  }
  
  /* 暖簾が消えるアニメーション */
  .noren.is-vanish .noren__left,
  .noren.is-vanish .noren__right {
    opacity: 0;
  }
  
  /* 中央コンテンツエリア */
  .welcome-content {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 15%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    z-index: 5;
    pointer-events: none;
  }
  
  /* ロゴエリア */
  .welcome-logo {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
  }
  
  /* メインロゴテキスト */
  .welcome-logo__main {
    font-family: 'Georgia', 'Times New Roman', serif;
    font-size: clamp(40px, 8vw, 72px);
    font-weight: 400;
    letter-spacing: 0.2em;
    color: var(--brand-main);
  }
  
  /* サブテキスト */
  .welcome-logo__sub {
    font-size: clamp(12px, 2vw, 16px);
    letter-spacing: 0.6em;
    color: var(--brand-main);
    margin-top: 8px;
  }
  
  /* WELCOMEボタン */
  .welcome-enter {
    position: absolute;
    bottom: 22%;
    left: 50%;
    transform: translate(-50%, 0);
    z-index: 10;
    pointer-events: auto;
    text-decoration: none;
    width: 200px;
    height: 200px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: radial-gradient(circle at 40% 35%, 
      rgba(255,255,255,.75), 
      rgba(255,255,255,.65) 40%,
      rgba(245,240,235,.55) 100%);
    box-shadow: 
      0 8px 25px rgba(0,0,0,.08),
      0 3px 8px rgba(0,0,0,.04);
    transition: transform 200ms ease, box-shadow 200ms ease;
    cursor: pointer;
  }
  
  .welcome-enter:hover {
    transform: translate(-50%, 0) translateY(-3px) scale(1.03);
    background: radial-gradient(circle at 40% 35%, 
      rgba(255,255,255,.85), 
      rgba(255,255,255,.75) 40%,
      rgba(245,240,235,.65) 100%);
    box-shadow: 
      0 12px 35px rgba(0,0,0,.12),
      0 5px 12px rgba(0,0,0,.06);
  }
  
  .welcome-enter.is-disabled {
    opacity: 0.4;
    transform: translate(-50%, 0) scale(0.95);
    pointer-events: none;
  }
  
  .welcome-enter__text {
    letter-spacing: .12em;
    font-weight: 600;
    font-size: clamp(16px, 2.2vw, 22px);
    color: var(--brand-main);
    text-transform: uppercase;
    opacity: 0.85;
  }
  
  /* 下部の帯 */
  .welcome-footer {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 15%;
    background: var(--footer-color);
    z-index: 2;
  }
  
  /* 帯の上部にグラデーション */
  .welcome-footer::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: rgba(255,255,255,.2);
  }
  
  /* レスポンシブ */
  @media (max-width: 768px) {
    .noren__left::after {
      right: 30%;
    }
    .noren__right::after {
      left: 30%;
    }
    .welcome-enter {
      bottom: 20%;
      width: 180px;
      height: 180px;
    }
  }
  
  @media (max-width: 480px) {
    .noren__left {
      clip-path: polygon(0 0, 100% 0, 55% 100%, 0 100%);
    }
    .noren__right {
      clip-path: polygon(0 0, 100% 0, 100% 100%, 45% 100%);
    }
    .noren__left::after {
      right: 25%;
      width: 2px;
    }
    .noren__right::after {
      left: 25%;
      width: 2px;
    }
    .welcome-footer {
      height: 12%;
    }
    .welcome-enter {
      bottom: 18%;
      width: 160px;
      height: 160px;
    }
  }
</style>

<div class="welcome-kv" id="welcomeKv">
  
  {{-- 暖簾（左右でV字型） --}}
  <div class="noren" id="noren">
    <div class="noren__left"></div>
    <div class="noren__right"></div>
  </div>
  
  {{-- 中央コンテンツ --}}
  <div class="welcome-content">
    <div class="welcome-logo">
      {{-- メインロゴ --}}
      <span class="welcome-logo__main">5akeMe</span>
      
      {{-- サブテキスト --}}
      <span class="welcome-logo__sub">お 酒 診 断</span>
    </div>
  </div>
  
  {{-- WELCOMEボタン --}}
  <a href="{{ route('age.check') }}" class="welcome-enter js-enter" aria-label="Enter">
    <span class="welcome-enter__text">WELCOME</span>
  </a>
  
  {{-- 下部の帯 --}}
  <div class="welcome-footer"></div>
  
</div>

<script>
  // 暖簾をくぐる演出
  document.addEventListener('DOMContentLoaded', function() {
    var enter = document.querySelector('.js-enter');
    var noren = document.getElementById('noren');
    
    if (enter && noren) {
      enter.addEventListener('click', function(e) {
        e.preventDefault();
        var href = enter.getAttribute('href');
        
        // ボタンを無効化
        enter.classList.add('is-disabled');
        
        // 暖簾を左右に開く
        setTimeout(function() {
          noren.classList.add('is-active');
        }, 100);
        
        // 暖簾が消える
        setTimeout(function() {
          noren.classList.add('is-vanish');
        }, 800);
        
        // ページ遷移
        setTimeout(function() {
          window.location.href = href;
        }, 1200);
      });
    }
  });
</script>
@endsection
