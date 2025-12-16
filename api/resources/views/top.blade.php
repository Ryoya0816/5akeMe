{{-- resources/views/top.blade.php --}}
@extends('layouts.app')

@section('title', '5akeMe トップ')

@section('content')
  {{-- ====== 暖簾アニメーション（ページ最初の演出） ====== --}}
  <div id="noren-overlay" class="noren-overlay">
    <div class="noren-panel"></div>
    <div class="noren-logo">5akeMe</div>
  </div>

  {{-- ====== メインコンテンツ ====== --}}
  <section class="top-hero">
    <h2 class="top-title">5akeMe お酒診断</h2>
    <p class="top-lead">あなたにピッタリのお酒を、5問で提案します。</p>

    <div class="top-main">
      {{-- 左：マスコット＋ふきだし --}}
      <div class="top-left">
        <div class="top-mascot-wrap">
          <img
            src="{{ asset('images/mascot.png') }}" {{-- ← ここを実際の画像ファイル名に合わせてね --}}
            alt="5akeMe マスコット"
            class="top-mascot-image"
          >
          <div class="top-speech">
            今日の一杯、<br>いっしょに探そ？
          </div>
        </div>
      </div>

      {{-- 右：でっかい「診断スタート」ボタン --}}
      <div class="top-right">
        <a href="{{ route('diagnose') }}" class="start-button-wrap">
          <div class="start-button">
            <span class="start-button-label">診断をはじめる</span>
          </div>
        </a>
        <p class="start-note">所要時間 3分 / 質問 5問</p>
      </div>
    </div>
  </section>

  {{-- ====== 暖簾を消すためのシンプルなJS ====== --}}
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const overlay = document.getElementById('noren-overlay');
      if (!overlay) return;

      // 1.2秒後に暖簾が上にスライド
      setTimeout(function () {
        overlay.classList.add('noren-overlay--hide');
        // フェードアウト後にDOMから完全に削除
        setTimeout(function () {
          overlay.remove();
        }, 600);
      }, 1200);
    });
  </script>
@endsection
