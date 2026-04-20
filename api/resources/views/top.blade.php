{{-- resources/views/top.blade.php --}}
@extends('layouts.app')

@section('title', '5akeMe トップ')

@section('content')

  {{-- 背景演出用ステージ --}}
  <div id="top-bg-stage" class="top-bg-stage" aria-hidden="true"></div>

  {{-- 暖簾アニメーション --}}
  <div id="noren-overlay" class="noren-overlay">
    <div class="noren-panel"></div>
    <div class="noren-logo">5akeMe</div>
  </div>

  <section class="top-hero">
    <h2 class="top-title">5akeMe お酒診断</h2>
    <p class="top-lead">あなたにピッタリのお酒を、5問で提案します。</p>

    <div class="top-main">
      <div class="top-left">
        <div class="top-mascot-wrap">
          <img
            src="{{ asset('images/mascot.png') }}"
            alt="5akeMe マスコット"
            class="top-mascot-image"
          >
          <div class="top-speech">
            今日の一杯、<br>いっしょに探そ？
          </div>
        </div>
      </div>

      <div class="top-right">
        <a href="{{ route('diagnose') }}" class="start-button-wrap">
          <div class="start-button">
            <span class="start-button-label">診断をはじめる</span>
          </div>
        </a>
      </div>
    </div>
  </section>

@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const overlay = document.getElementById('noren-overlay');
      if (!overlay) return;

      setTimeout(function () {
        overlay.classList.add('noren-overlay--hide');
        setTimeout(function () {
          overlay.remove();
        }, 600);
      }, 1200);
    });
  </script>
@endpush
