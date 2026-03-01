{{-- resources/views/layouts/app.blade.php --}}
<!doctype html>
<html lang="ja" prefix="og: https://ogp.me/ns#">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- ★ 重要：レイアウト用CSSを最優先で読み込む（@vite の成否に依存しない） --}}
  <style>
    :root {
      --brand-main: #9c3f2e;
      --brand-text: #8a3a28;
      --bg-base: #fbf3e8;
      --bg-soft: #fff7ee;
      --line-soft: #f1dfd0;
      --text-main: #3f3f3f;
      --text-sub: #8c6d57;
    }
    html, body { height: 100%; margin: 0; background: var(--bg-base); color: var(--text-main); font-family: system-ui, sans-serif; }
    .wrap { max-width: 960px; margin: 0 auto; padding: 28px 20px 64px; box-sizing: border-box; }
    header.app-header { height: 160px; border-bottom: 1px solid var(--line-soft); background: var(--bg-base); box-sizing: border-box; }
    .app-header .app-header-link { display: flex; align-items: center; height: 100%; }
    header.app-header .app-header-image { height: 90%; width: auto; max-width: 100%; min-width: 1100px; object-fit: contain; }
    .app-header-inner { display: flex; align-items: center; justify-content: space-between; padding: 0 16px 0 16px; height: 100%; width: 100%; box-sizing: border-box; }
    .app-header-user { display: flex; align-items: center; }
    .app-header-user-link { display: flex; align-items: center; }
    .app-header-avatar { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 2px solid var(--line-soft); }
    .app-header-avatar-placeholder { display: flex; align-items: center; justify-content: center; width: 48px; height: 48px; border-radius: 50%; background: var(--bg-soft); border: 2px solid var(--line-soft); font-size: 24px; }
    .app-header-login { padding: 8px 16px; background: var(--brand-main); color: #fff; border-radius: 999px; font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.2s; }
    .app-header-login:hover { opacity: 0.9; transform: translateY(-1px); }
    .app-footer { margin-top: 64px; padding: 32px 0 24px; border-top: 2px solid var(--line-soft); background: linear-gradient(to bottom, #faece0, var(--bg-base)); position: relative; }
    .footer-inner { max-width: 960px; margin: 0 auto; padding: 0 20px; display: grid; grid-template-columns: 1fr auto 1fr; align-items: center; gap: 32px; }
    .footer-brand-section { display: flex; flex-direction: column; gap: 4px; }
    .footer-brand-link { display: flex; align-items: center; gap: 8px; text-decoration: none; color: var(--brand-main); font-weight: 700; font-size: 20px; }
    .footer-brand-icon { font-size: 24px; }
    .footer-copy { font-size: 11px; color: var(--text-sub); margin: 0; }
    .footer-nav { display: flex; align-items: center; gap: 16px; justify-self: center; flex-wrap: wrap; }
    .footer-nav-link { display: flex; flex-direction: column; align-items: center; gap: 4px; padding: 8px 16px; border-radius: 12px; text-decoration: none; color: var(--text-main); font-size: 13px; }
    .footer-nav-link:hover { background: var(--bg-soft); color: var(--brand-main); }
    .footer-nav-icon { font-size: 20px; }
    .footer-social { display: flex; gap: 12px; justify-self: end; }
    .footer-social-link { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: var(--bg-soft); border: 1px solid var(--line-soft); color: var(--brand-main); }
    .footer-social-link:hover { background: var(--brand-main); color: #fff; border-color: var(--brand-main); }
    .footer-social-svg { width: 20px; height: 20px; }
    .footer-scroll-top { position: absolute; right: 20px; bottom: -60px; width: 48px; height: 48px; border-radius: 50%; background: var(--brand-main); color: #fff; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 20px; opacity: 0; visibility: hidden; transition: opacity 0.3s, visibility 0.3s; }
    .footer-scroll-top.visible { opacity: 1; visibility: visible; }
    .footer-scroll-top:hover { background: var(--brand-text); transform: translateY(-4px); }
    @media (max-width: 768px) { .footer-inner { grid-template-columns: 1fr; text-align: center; } .footer-brand-section, .footer-nav, .footer-social { justify-self: center; } }
    @media (max-width: 640px) { header.app-header { height: 120px; } header.app-header .app-header-image { height: 90%; max-width: 100%; min-width: 560px; } .app-header-avatar, .app-header-avatar-placeholder { width: 42px; height: 42px; } .app-header-avatar-placeholder { font-size: 20px; } .footer-inner { gap: 20px; } .footer-nav { gap: 12px; } .footer-nav-link { padding: 6px 12px; font-size: 12px; } }
  </style>

  <title>@yield('title', '5akeMe - あなたにぴったりのお酒診断')</title>

  {{-- SEO メタタグ --}}
  <meta name="description" content="@yield('description', '5つの質問に答えるだけで、あなたにぴったりのお酒が見つかる！日本酒、焼酎、ワイン、ビールなど、あなたの好みに合った一杯を診断します。')">
  <meta name="keywords" content="お酒診断, 日本酒, 焼酎, ワイン, ビール, 酒, 診断, おすすめ, 5akeMe">
  <meta name="author" content="5akeMe">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="{{ url()->current() }}">

  {{-- OGP (Open Graph Protocol) --}}
  <meta property="og:type" content="@yield('og_type', 'website')">
  <meta property="og:title" content="@yield('og_title', '5akeMe - あなたにぴったりのお酒診断')">
  <meta property="og:description" content="@yield('og_description', '5つの質問に答えるだけで、あなたにぴったりのお酒が見つかる！')">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:image" content="@yield('og_image', asset('images/ogp.png'))">
  <meta property="og:site_name" content="5akeMe">
  <meta property="og:locale" content="ja_JP">

  {{-- Twitter Card --}}
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="@yield('og_title', '5akeMe - あなたにぴったりのお酒診断')">
  <meta name="twitter:description" content="@yield('og_description', '5つの質問に答えるだけで、あなたにぴったりのお酒が見つかる！')">
  <meta name="twitter:image" content="@yield('og_image', asset('images/ogp.png'))">

  {{-- Favicon --}}
  <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">

  {{-- テーマカラー --}}
  <meta name="theme-color" content="#9c3f2e">
  <meta name="msapplication-TileColor" content="#9c3f2e">

  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="@yield('body_class')">

  @unless(View::hasSection('hide_header'))
  <header class="app-header" aria-label="5akeMe">
    <div class="app-header-inner">
      <a href="{{ route('top') }}" class="app-header-link">
        <img
          src="{{ asset('images/5akeme-header.png') }}"
          alt="5akeMe"
          class="app-header-image"
          style="height:90%;width:auto;max-width:100%;min-width:1100px;object-fit:contain;"
        >
      </a>
      {{-- ユーザーメニュー --}}
      <div class="app-header-user">
        @auth
          <a href="{{ route('mypage') }}" class="app-header-user-link" title="マイページ">
            @if(auth()->user()->avatar)
              <img src="{{ strpos(auth()->user()->avatar, 'http') === 0 ? auth()->user()->avatar : asset(auth()->user()->avatar) }}" alt="" class="app-header-avatar">
            @else
              <span class="app-header-avatar-placeholder">👤</span>
            @endif
          </a>
        @else
          <a href="{{ route('login') }}" class="app-header-login">ログイン</a>
        @endauth
      </div>
    </div>
  </header>
  @endunless

  <main class="wrap @yield('main_class')">
    @yield('content')
  </main>

  @unless(View::hasSection('hide_footer'))
  <footer class="app-footer">
    <div class="footer-inner">
      <div class="footer-brand-section">
        <a href="{{ route('top') }}" class="footer-brand-link">
          <span class="footer-brand-icon">🍶</span>
          <span class="footer-brand-text">5akeMe</span>
        </a>
        <p class="footer-copy">© {{ date('Y') }} Hello.Sagaworld</p>
      </div>

      <nav class="footer-nav" aria-label="Footer navigation">
        <a href="{{ route('top') }}" class="footer-nav-link">
          <span class="footer-nav-icon">🏠</span>
          <span>TOP</span>
        </a>
        <a href="{{ route('diagnose') }}" class="footer-nav-link">
          <span class="footer-nav-icon">🔍</span>
          <span>診断</span>
        </a>
        <a href="{{ route('about') }}" class="footer-nav-link">
          <span class="footer-nav-icon">📖</span>
          <span>About</span>
        </a>
        <a href="{{ route('contact') }}" class="footer-nav-link">
          <span class="footer-nav-icon">📮</span>
          <span>お問い合わせ</span>
        </a>
        <a href="{{ route('terms') }}" class="footer-nav-link">
          <span class="footer-nav-icon">📜</span>
          <span>利用規約</span>
        </a>
        <a href="{{ route('privacy') }}" class="footer-nav-link">
          <span class="footer-nav-icon">🔒</span>
          <span>プライバシー</span>
        </a>
      </nav>

      <div class="footer-social">
        <a href="https://www.instagram.com/hello.saga.world/" class="footer-social-link footer-social-instagram" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
          <svg class="footer-social-svg" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
          </svg>
        </a>
        <a href="https://note.com/hello_sagaworld" class="footer-social-link footer-social-note" target="_blank" rel="noopener noreferrer" aria-label="NOTE">
          <svg class="footer-social-svg" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm3.693 7.714c.263 0 .478.214.478.478v7.616a.479.479 0 01-.478.478h-1.204a.479.479 0 01-.478-.478v-4.794L9.5 16.286h-.014a.476.476 0 01-.332-.138.474.474 0 01-.147-.34V8.192c0-.264.215-.478.479-.478h1.203c.264 0 .479.214.479.478v4.794l4.51-5.272h.015z"/>
          </svg>
        </a>
      </div>

      <button class="footer-scroll-top" id="scrollToTop" aria-label="ページトップへ戻る">
        <span class="footer-scroll-icon">↑</span>
      </button>
    </div>
  </footer>
  @endunless

  @stack('scripts')

  <script>
    // トップへ戻るボタン
    document.addEventListener('DOMContentLoaded', function() {
      const scrollTopBtn = document.getElementById('scrollToTop');
      if (!scrollTopBtn) return;

      // スクロール位置に応じてボタンを表示/非表示
      function toggleScrollTop() {
        if (window.pageYOffset > 300) {
          scrollTopBtn.classList.add('visible');
        } else {
          scrollTopBtn.classList.remove('visible');
        }
      }

      // スクロールイベント
      window.addEventListener('scroll', toggleScrollTop);

      // クリックでトップへスムーズスクロール
      scrollTopBtn.addEventListener('click', function() {
        window.scrollTo({
          top: 0,
          behavior: 'smooth'
        });
      });
    });
  </script>
</body>
</html>
