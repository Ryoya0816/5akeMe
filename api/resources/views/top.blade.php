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
  .season-banner { margin: 0 0 24px; padding: 12px 16px; background: #fff7ee; border: 1px solid #f1dfd0; border-radius: 12px; position: relative; z-index: 1; }
  .season-banner-inner { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; max-width: 960px; margin: 0 auto; }
  .season-banner-icon { font-size: 24px; flex-shrink: 0; }
  .season-banner-content { display: flex; flex-direction: column; gap: 2px; flex: 1; min-width: 0; }
  .season-banner-title { font-weight: 700; font-size: 14px; color: #9c3f2e; }
  .season-banner-message { font-size: 12px; color: #8c6d57; }
  .season-banner-recommend { font-size: 12px; color: #8c6d57; flex-shrink: 0; }
  .top-hero { max-width: 960px; margin: 40px auto 60px; padding: 0 20px; text-align: center; position: relative; z-index: 1; }
  .top-title { font-size: 24px; font-weight: bold; margin-bottom: 8px; color: #9c3f2e; }
  .top-lead { font-size: 14px; color: #8c6d57; margin-bottom: 32px; }
  .top-main { display: flex; flex-direction: row; justify-content: center; align-items: center; gap: 48px; width: 100%; max-width: 720px; margin: 0 auto; }
  .top-left, .top-right { flex: 1; min-width: 0; display: flex; justify-content: center; align-items: center; }
  .top-mascot-wrap { position: relative; display: inline-block; max-width: 220px; }
  .top-mascot-image { width: 200px; max-width: 200px; height: auto; border-radius: 16px; object-fit: contain; display: block; }
  .top-speech { position: absolute; top: -20px; right: -40px; min-width: 160px; padding: 10px 14px; background-color: #fff7dd; border-radius: 18px; border: 1px solid #fbbf24; font-size: 13px; color: #444; box-shadow: 0 4px 10px rgba(0,0,0,0.08); }
  .start-button-wrap { display: inline-flex; text-decoration: none; justify-content: center; }
  .start-button { width: 200px; height: 200px; border-radius: 50%; background: radial-gradient(circle at 30% 25%, #ffd3c8, #e97b6d 65%, #b04434 100%); box-shadow: 0 12px 24px rgba(185,68,52,0.35); display: flex; align-items: center; justify-content: center; transition: transform 0.12s ease-out, box-shadow 0.12s ease-out; }
  .start-button-label { color: #fff; font-weight: bold; font-size: 18px; line-height: 1.4; }
  .start-button-wrap:hover .start-button { transform: translateY(-4px) scale(1.03); box-shadow: 0 16px 26px rgba(185,68,52,0.45); }
  @media (max-width: 768px) {
    .top-main { flex-direction: column; gap: 24px; }
    .top-left, .top-right { flex: none; width: 100%; }
    .top-mascot-image { width: 160px; max-width: 160px; }
    .top-speech { right: -10px; }
    .start-button { width: 180px; height: 180px; }
  }
  @media (max-width: 640px) {
    .season-banner-inner { flex-direction: column; align-items: flex-start; }
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
