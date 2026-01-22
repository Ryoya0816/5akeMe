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
  /* Welcome 専用スタイル */
  .wrap.wrap--welcome { padding: 0 !important; max-width: none !important; margin: 0 !important; }
  .welcome-kv { position: relative; height: 100svh; overflow: hidden; background: #fbf3e8; display: grid; place-items: center; }
  .welcome-kv__bg { position: absolute; inset: 0; z-index: 0; background: radial-gradient(circle at 50% 18%, rgba(255,255,255,.72), rgba(255,255,255,0) 58%), linear-gradient(#fbf3e8, #f7e9dc); }
  .welcome-stage { position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); z-index: 1; }
  .noren { position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); z-index: 2; height: min(520px, 72svh); pointer-events: none; }
  .noren__inner { height: 100%; display: flex !important; flex-direction: row !important; justify-content: center; align-items: center; gap: 140px !important; transition: transform 600ms ease, opacity 600ms ease; }
  .noren__panel { flex: none; height: 100%; display: flex; align-items: center; justify-content: center; filter: drop-shadow(0 18px 30px rgba(0,0,0,.14)); transition: transform 1000ms cubic-bezier(.25,.46,.45,.94), filter 1000ms ease; }
  .noren__img { height: 100%; width: auto; object-fit: contain; display: block; }
  
  /* 暖簾をくぐる演出：クリック時に左右に開く */
  .noren--close.is-active .noren__panel { filter: drop-shadow(0 30px 60px rgba(0,0,0,.22)); }
  .noren--close.is-active .noren__panel.noren__left { transform: translateX(-80%) !important; }
  .noren--close.is-active .noren__panel.noren__right { transform: translateX(80%) !important; }
  
  /* 暖簾をくぐった後、奥に消える */
  .noren--close.is-vanish .noren__inner { opacity: 0; transform: scale(0.85) translateY(10%); }
  
  /* WELCOME ボタン */
  .welcome-enter { position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); z-index: 3; pointer-events: auto; text-decoration: none; width: clamp(160px, 18vw, 220px); aspect-ratio: 1 / 1; border-radius: 999px; display: grid; place-items: center; background: radial-gradient(circle at 35% 30%, rgba(255,255,255,.92), rgba(255,255,255,.78) 55%, rgba(255,255,255,.65) 100%); box-shadow: 0 18px 40px rgba(0,0,0,.12); transition: transform 140ms ease-out, box-shadow 140ms ease-out, opacity 300ms ease-out; cursor: pointer; }
  .welcome-enter:hover { transform: translate(-50%, -50%) translateY(-2px) scale(1.02); box-shadow: 0 22px 46px rgba(0,0,0,.16); }
  .welcome-enter.is-disabled { opacity: 0.6; transform: translate(-50%, -50%) scale(0.95); pointer-events: none; }
  .welcome-enter__text { letter-spacing: .12em; font-weight: 700; font-size: clamp(16px, 2vw, 20px); color: #9c3f2e; text-transform: uppercase; }
  
  @media (max-width: 640px) { .noren__inner { gap: 80px !important; } }
</style>
<div
  class="welcome-kv"
  id="welcomeKv"
  style="
    --noren-left: url('{{ asset('images/left_noren.png') }}');
    --noren-right: url('{{ asset('images/right_noren.png') }}');
  ">

  {{-- 背景（和紙っぽい） --}}
  <div class="welcome-kv__bg" aria-hidden="true"></div>

  {{-- 舞台（viewport基準で中央固定） --}}
  <div class="welcome-stage" aria-hidden="true"></div>

  {{-- 暖簾（主役：最初から表示） --}}
  <div class="noren noren--close is-ready" id="norenClose" aria-hidden="true">
    <div class="noren__inner">
      <div class="noren__panel noren__left">
        <img src="{{ asset('images/left_noren.png') }}" alt="" class="noren__img">
      </div>
      <div class="noren__panel noren__right">
        <img src="{{ asset('images/right_noren.png') }}" alt="暖簾（右）" class="noren__img" loading="eager">
      </div>
    </div>
  </div>

  {{-- WELCOMEボタン（クリックで：ちょい溜め → 暖簾をくぐる → 遷移） --}}
  <a href="{{ route('age.check') }}" class="welcome-enter js-enter" aria-label="Enter">
    <span class="welcome-enter__text">WELCOME</span>
  </a>

</div>

<script>
  // 暖簾をくぐる演出
  document.addEventListener('DOMContentLoaded', function() {
    var enter = document.querySelector('.js-enter');
    var norenClose = document.getElementById('norenClose');
    
    if (enter && norenClose) {
      enter.addEventListener('click', function(e) {
        e.preventDefault();
        var href = enter.getAttribute('href');
        
        // ボタンを無効化
        enter.classList.add('is-disabled');
        
        // 暖簾を左右に開く
        setTimeout(function() {
          norenClose.classList.add('is-active');
        }, 200);
        
        // 暖簾が奥に消える
        setTimeout(function() {
          norenClose.classList.add('is-vanish');
        }, 1200);
        
        // ページ遷移
        setTimeout(function() {
          window.location.href = href;
        }, 1800);
      });
    }
  });
</script>
@endsection
