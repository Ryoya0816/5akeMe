@extends('layouts.app')

@section('title','5akeMe 診断')
@section('header','5akeMe 診断')

@section('content')
  {{-- data-* で JS に設定を渡す（Blade→JSの最小コスト連携） --}}
  <<div id="chat-root"
  data-start-endpoint="/api/diagnose/start"
  data-score-endpoint="/api/diagnose/score"
  data-bot-icon="/images/bot.png"
  data-user-icon="/images/user.png">

    <div id="chat" class="chat" aria-live="polite"></div>

    <div class="footer">
      <button id="restart" class="btn secondary">最初から</button>
      <div class="mutetip">※ 質問にタップで答えるだけ。所要1分。</div>
    </div>
  </div>
@endsection
