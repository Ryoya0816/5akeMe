{{-- resources/views/welcome.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="welcome-kv" id="welcomeKv">

  {{-- 背景（和紙っぽい） --}}
  <div class="welcome-kv__bg" aria-hidden="true"></div>

  {{-- 舞台（viewport基準で中央固定 / 赤エリア相当） --}}
  <div class="welcome-stage" aria-hidden="true"></div>

  {{-- 暖簾（主役：最初から表示） --}}
<div class="noren noren--close is-ready" id="norenClose" aria-hidden="true">
  <div class="noren__inner">
    <div class="noren__panel noren__left"></div>
    <div class="noren__panel noren__right"></div>
  </div>
</div>


  {{-- WELCOMEボタン（クリックで：ちょい溜め → 暖簾オープン → 吸い込まれて消える → 遷移） --}}
  <a href="{{ route('age.check') }}" class="welcome-enter js-enter" aria-label="Enter">
    <span class="welcome-enter__text">WELCOME</span>
  </a>

</div>
@endsection
