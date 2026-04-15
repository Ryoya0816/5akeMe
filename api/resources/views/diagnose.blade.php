@extends('layouts.app')

@section('title', 'お酒診断 - 5akeMe')
@section('description', 'チャット形式で5つの質問に答えるだけ。あなたの好みに合ったお酒のタイプを診断します。')
@section('og_title', 'お酒診断 - 5akeMe')
@section('og_description', 'チャット形式で5つの質問に答えるだけ！あなたにぴったりのお酒を見つけよう。')

@section('content')
@include('diagnose.partials._loading-overlay')

<div class="dm-page-wrap">

            @include('diagnose.partials._styles')

            @include('diagnose.partials._chat-card')

</div>

@include('diagnose.partials._script')
@endsection
