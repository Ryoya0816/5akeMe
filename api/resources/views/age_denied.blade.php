{{-- resources/views/age_denied.blade.php --}}
@extends('layouts.app')

@section('title','ご利用いただけません')

@section('content')
<div class="mx-auto max-w-md text-center py-16">

    <h2 class="text-xl font-bold mb-4 text-[var(--brand-main)]">
        ごめんね、大人になってまた来てね！
    </h2>

    <p class="text-gray-600 mb-6">
        安全のため、20歳未満の方はご利用いただけません。
    </p>

    <p class="text-gray-500 text-sm">
        3秒後に #HelloSAGAworld を検索したページに移動します…
    </p>
</div>

{{-- 3秒後に Google で「#HelloSAGAworld」を検索 --}}
<script>
    setTimeout(function() {
        window.location.href = "https://www.google.com/search?q=%23HelloSAGAworld";
    }, 3000);
</script>
@endsection
