{{-- resources/views/top.blade.php --}}
@extends('layouts.app')

@section('title', '5akeMe - あなたにぴったりのお酒診断')
@section('description', '5つの質問に答えるだけで、あなたにぴったりのお酒が見つかる！日本酒、焼酎、ワイン、ビールなど、好みに合った一杯を診断します。')
@section('og_title', '5akeMe - あなたにぴったりのお酒診断')
@section('og_description', '5つの質問に答えるだけで、あなたにぴったりのお酒が見つかる！')

@php
    // 季節とイベントの判定
    $month = (int) date('n');
    $day = (int) date('j');
    
    // イベント優先（期間限定）
    $event = null;
    if ($month == 1 && $day <= 15) {
        $event = [
            'icon' => '🎍',
            'title' => '新年会シーズン',
            'message' => 'めでたい席には日本酒で乾杯！',
            'recommend' => '純米大吟醸、スパークリング日本酒',
        ];
    } elseif ($month == 2 && $day >= 10 && $day <= 14) {
        $event = [
            'icon' => '💝',
            'title' => 'バレンタイン',
            'message' => 'チョコレートと相性抜群のお酒',
            'recommend' => 'ウイスキー、赤ワイン',
        ];
    } elseif (($month == 3 && $day >= 20) || ($month == 4 && $day <= 15)) {
        $event = [
            'icon' => '🌸',
            'title' => 'お花見シーズン',
            'message' => '桜の下で楽しむ一杯',
            'recommend' => 'スパークリング日本酒、ロゼワイン',
        ];
    } elseif ($month >= 6 && $month <= 8) {
        $event = [
            'icon' => '🍺',
            'title' => 'ビアガーデンの季節',
            'message' => '暑い夜はキンキンに冷えたビール！',
            'recommend' => 'クラフトビール、冷酒',
        ];
    } elseif ($month == 10 && $day >= 20) {
        $event = [
            'icon' => '🎃',
            'title' => 'ハロウィン',
            'message' => 'パーティーを盛り上げるお酒',
            'recommend' => 'カクテル、スパークリングワイン',
        ];
    } elseif ($month == 12 && $day >= 1 && $day <= 25) {
        $event = [
            'icon' => '🎄',
            'title' => 'クリスマス＆忘年会',
            'message' => '特別な夜を彩る一杯',
            'recommend' => 'シャンパン、赤ワイン、日本酒',
        ];
    }
    
    // イベントがなければ季節
    if (!$event) {
        if ($month >= 3 && $month <= 5) {
            $event = [
                'icon' => '🌸',
                'title' => '春のおすすめ',
                'message' => '新生活の始まりに華やかな一杯',
                'recommend' => '日本酒（甘口）、白ワイン、カクテル',
            ];
        } elseif ($month >= 6 && $month <= 8) {
            $event = [
                'icon' => '🌻',
                'title' => '夏のおすすめ',
                'message' => '暑い日はキリッと冷えたお酒で',
                'recommend' => 'ビール、冷酒、ハイボール',
            ];
        } elseif ($month >= 9 && $month <= 11) {
            $event = [
                'icon' => '🍂',
                'title' => '秋のおすすめ',
                'message' => '食欲の秋、お酒も深い味わいを',
                'recommend' => 'ひやおろし、赤ワイン、焼酎',
            ];
        } else {
            $event = [
                'icon' => '❄️',
                'title' => '冬のおすすめ',
                'message' => '体の芯から温まる一杯を',
                'recommend' => '熱燗、ホットワイン、ウイスキー',
            ];
        }
    }
@endphp

@section('content')

  {{-- 背景演出用ステージ --}}
  <div id="top-bg-stage" class="top-bg-stage" aria-hidden="true"></div>

  {{-- 暖簾アニメーション --}}
  <div id="noren-overlay" class="noren-overlay">
    <div class="noren-panel"></div>
    <div class="noren-logo">5akeMe</div>
  </div>

  {{-- 季節バナー --}}
  @if($event)
  <div class="season-banner">
    <div class="season-banner-inner">
      <span class="season-banner-icon">{{ $event['icon'] }}</span>
      <div class="season-banner-content">
        <span class="season-banner-title">{{ $event['title'] }}</span>
        <span class="season-banner-message">{{ $event['message'] }}</span>
      </div>
      <span class="season-banner-recommend">{{ $event['recommend'] }}</span>
    </div>
  </div>
  @endif

  <section class="top-hero">
    <h2 class="top-title">5akeMe お酒診断</h2>
    <p class="top-lead">あなたにピッタリのお酒を、5問で提案します。</p>

    <div class="top-main">
      <div class="top-left">
        <div class="top-mascot-wrap">
          <img
            src="{{ asset('images/mascot.png') }}"
            alt="5akeMe マスコット - お酒診断のキャラクター"
            class="top-mascot-image"
            loading="lazy"
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
