{{-- resources/views/top.blade.php --}}
@extends('layouts.app')

@section('title', '5akeMe トップ')

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
<style>
  /* TOP ページ専用スタイル */
  .top-bg-stage { position: fixed; inset: 0; z-index: 0; pointer-events: none; }
  .season-banner { margin: 0 0 24px; padding: 14px 18px; background: var(--bg-soft, #fff7ee); border: 1px solid var(--line-soft, #f1dfd0); border-radius: var(--radius-md, 16px); position: relative; z-index: 1; }
  .season-banner-inner { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; max-width: 960px; margin: 0 auto; }
  .season-banner-icon { font-size: 24px; flex-shrink: 0; }
  .season-banner-content { display: flex; flex-direction: column; gap: 2px; flex: 1; min-width: 0; }
  .season-banner-title { font-weight: 700; font-size: 14px; color: var(--brand-main, #9c3f2e); }
  .season-banner-message { font-size: 12px; color: var(--text-sub, #8c6d57); }
  .season-banner-recommend { font-size: 12px; color: var(--text-sub, #8c6d57); flex-shrink: 0; }
  .top-hero { max-width: 960px; margin: 48px auto 64px; padding: 0 20px; text-align: center; position: relative; z-index: 1; }
  .top-title { font-size: 26px; font-weight: 700; margin-bottom: 10px; color: var(--brand-main, #9c3f2e); letter-spacing: 0.02em; }
  .top-title ruby { ruby-position: over; }
  .top-title rt { font-size: 14px; font-weight: normal; color: var(--text-sub, #8c6d57); letter-spacing: 0.05em; }
  .top-lead { font-size: 15px; color: var(--text-sub, #8c6d57); margin-bottom: 36px; line-height: 1.7; }
  .top-main { display: flex; flex-direction: row; justify-content: center; align-items: center; gap: 48px; width: 100%; max-width: 720px; margin: 0 auto; }
  .top-left, .top-right { flex: 1; min-width: 0; display: flex; justify-content: center; align-items: center; }
  @keyframes mascot-float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
  }
  .top-mascot-wrap { position: relative; display: inline-block; max-width: 220px; }
  .top-mascot-image { width: 200px; max-width: 200px; height: auto; border-radius: var(--radius-md, 16px); object-fit: contain; display: block; animation: mascot-float 4s ease-in-out infinite; }
  @keyframes speech-pop {
    0% { opacity: 0; transform: scale(0.8) translateY(8px); }
    60% { transform: scale(1.04) translateY(-2px); }
    100% { opacity: 1; transform: scale(1) translateY(0); }
  }
  .top-speech {
    position: absolute; top: -24px; right: -44px; min-width: 160px; padding: 12px 16px;
    background: var(--card-bg, #ffffff); border-radius: var(--radius-md, 16px);
    border: 1px solid var(--line-soft, #f1dfd0);
    font-size: 13px; color: var(--text-main, #3f3f3f); line-height: 1.6;
    box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.04));
    animation: speech-pop 600ms 400ms var(--ease-out, ease-out) both;
  }
  .top-speech::after {
    content: ''; position: absolute; bottom: -8px; left: 24px;
    width: 16px; height: 16px; background: var(--card-bg, #ffffff);
    border-right: 1px solid var(--line-soft, #f1dfd0);
    border-bottom: 1px solid var(--line-soft, #f1dfd0);
    transform: rotate(45deg);
  }
  @keyframes btn-pulse {
    0%, 100% { box-shadow: 0 12px 24px rgba(185,68,52,0.35), 0 0 0 0 rgba(185,68,52,0.25); }
    50% { box-shadow: 0 12px 24px rgba(185,68,52,0.35), 0 0 0 16px rgba(185,68,52,0); }
  }
  .start-button-wrap { display: inline-flex; text-decoration: none; justify-content: center; }
  .start-button {
    width: 200px; height: 200px; border-radius: 50%;
    background: radial-gradient(circle at 30% 25%, #ffd3c8, #e97b6d 65%, #b04434 100%);
    box-shadow: 0 12px 24px rgba(185,68,52,0.35);
    display: flex; align-items: center; justify-content: center;
    transition: transform var(--transition-normal, 200ms ease-out), box-shadow var(--transition-normal, 200ms ease-out);
    animation: btn-pulse 2.5s ease-in-out infinite;
  }
  .start-button-wrap:hover .start-button { animation: none; }
  .start-button-label { color: #fff; font-weight: 700; font-size: 18px; line-height: 1.4; letter-spacing: 0.04em; }
  .start-button-wrap:hover .start-button { transform: translateY(-6px) scale(1.04); box-shadow: 0 20px 32px rgba(185,68,52,0.4); }
  @media (max-width: 768px) {
    .top-hero { margin: 32px auto 48px; }
    .top-main { flex-direction: column; gap: 28px; }
    .top-left, .top-right { flex: none; width: 100%; }
    .top-mascot-image { width: 160px; max-width: 160px; }
    .top-speech { right: -10px; }
    .start-button { width: 180px; height: 180px; }
  }
  @media (max-width: 640px) {
    .season-banner-inner { flex-direction: column; align-items: flex-start; }
    .top-title { font-size: 22px; }
  }
</style>

  {{-- 背景演出用ステージ --}}
  <div id="top-bg-stage" class="top-bg-stage" aria-hidden="true"></div>

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
    <h2 class="top-title fade-in">5akeMe お酒診断</h2>
    <p class="top-lead fade-in stagger-1">あなたにピッタリのお酒を、5問で提案します。</p>

    <div class="top-main">
      <div class="top-left fade-in stagger-2">
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

      <div class="top-right fade-in-scale stagger-3">
        <a href="{{ route('diagnose') }}" class="start-button-wrap">
          <div class="start-button press-effect">
            <span class="start-button-label">診断をはじめる</span>
          </div>
        </a>
      </div>
    </div>
  </section>

@endsection
