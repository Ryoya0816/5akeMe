<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title','5akeMe')</title>
  {{-- 画面ごとのCSS/JSは @vite で差し込む --}}
  @vite(['resources/css/diagnose.css','resources/js/diagnose-chat.js'])
</head>
<body>
  <header class="app-header">
    <div class="logo">🍶</div>
    <h1 class="app-title">@yield('header','5akeMe 診断')</h1>
  </header>

  <main class="wrap">
    @yield('content')
  </main>
</body>
</html>
