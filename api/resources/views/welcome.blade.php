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

  {{-- TOPボタン（クリックで：ちょい溜め → 暖簾をくぐる → 遷移） --}}
  <a href="{{ route('age.check') }}" class="welcome-enter js-enter" aria-label="Enter">
    <span class="welcome-enter__text">TOP</span>
  </a>

</div>
@endsection
