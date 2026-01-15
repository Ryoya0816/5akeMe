{{-- resources/views/age_check.blade.php --}}
@extends('layouts.app')

@section('title','年齢確認')

@section('content')
<div class="mx-auto max-w-md text-center py-16">

    <h2 class="text-xl font-bold mb-6">あなたは20歳以上ですか？</h2>

    {{-- 「はい」ボタン --}}
    <form method="POST" action="{{ route('age.verify') }}" class="mb-6">
        @csrf
        <button
            type="submit"
            class="px-6 py-3 rounded text-white bg-[var(--brand-main)] hover:bg-[var(--brand-text)]"
        >
            はい（20歳以上です）
        </button>
    </form>

    {{-- 「いいえ」リンク --}}
    <a
        href="{{ route('age.denied') }}"
        class="text-sm text-gray-500 underline"
    >
        いいえ（20歳未満です）
    </a>

</div>
@endsection
