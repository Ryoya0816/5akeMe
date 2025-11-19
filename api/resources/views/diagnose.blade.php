@extends('layouts.app')

@section('content')
<div id="chat-root"
     data-api-endpoint="{{ route('diagnose.score') }}"
     data-bot-icon="{{ asset('images/bot.png') }}"
     data-user-icon="{{ asset('images/user.png') }}">
  <div id="chat" class="chat-area"></div>
</div>
@endsection

@vite(['resources/js/diagnose-chat.js'])
