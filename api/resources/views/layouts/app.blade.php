<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title','5akeMe')</title>
  {{-- 画面ごとのCSS/JSは @vite で差し込む --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
  <header class="app-header">
    <a href="{{ route('top') }}" class="header-logo-link">
      <img
        src="{{ asset('images/5akeme-header.png') }}"
        alt="5akeMe トップへ"
        class="header-logo-image"
      >
    </a>

    <h1 class="app-title">@yield('header','5akeMe')</h1>
  </header>

  <main class="wrap">
    @yield('content')
  </main>

  {{-- ✔ フッターは body の “中” に置く --}}
  <footer class="app-footer">
    <div class="footer-inner">

      {{-- 左：ナビ（プライバシーポリシーなど） --}}
      <div class="footer-left">
        <a href="/privacy" class="footer-link">プライバシーポリシー</a>
      </div>

      {{-- 中央：ブランドロゴやスローガン --}}
      <div class="footer-center">
        <p class="footer-brand">Hello, SAGA World.</p>
        <p class="footer-copy">© 5akeMe project</p>
      </div>

      {{-- 右：SNSリンク --}}
      <div class="footer-right">
        <a href="https://x.com/" target="_blank" class="sns-link">X</a>
        <a href="https://instagram.com/" target="_blank" class="sns-link">Instagram</a>
        <a href="https://note.com/hello_sagaworld" target="_blank" class="sns-link">note</a>
        <a href="https://github.com/Ryoya0816" target="_blank" class="sns-link">GitHub</a>
      </div>

    </div>
  </footer>

</body>
</html>
