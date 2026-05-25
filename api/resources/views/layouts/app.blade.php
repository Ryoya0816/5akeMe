{{-- resources/views/layouts/app.blade.php --}}
<!doctype html>
<html lang="ja" prefix="og: https://ogp.me/ns#">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- クリティカルCSS（FOUC防止の最小限のみ） --}}
  <style>
    html, body { margin: 0; background: #fbf3e8; }
    .icon { display: inline-block; vertical-align: middle; flex-shrink: 0; }
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
          src="{{ asset('images/5akeme-header-transparent.svg') }}?v=20260501"
          alt="5akeMe"
          class="app-header-image"
        >
      </a>
      {{-- ユーザーメニュー --}}
      <div class="app-header-user">
        @auth
          <a href="{{ route('mypage') }}" class="app-header-user-link" title="マイページ">
            @if(auth()->user()->avatar)
              <img src="{{ strpos(auth()->user()->avatar, 'http') === 0 ? auth()->user()->avatar : asset(auth()->user()->avatar) }}" alt="" class="app-header-avatar">
            @else
              <span class="app-header-avatar-placeholder"><x-svg-icon name="user" size="24" /></span>
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
          <span class="footer-brand-icon"><x-svg-icon name="wine" size="22" /></span>
          <span class="footer-brand-text">5akeMe</span>
        </a>
        <p class="footer-copy">© {{ date('Y') }} Hello.Sagaworld</p>
      </div>

      <nav class="footer-nav" aria-label="Footer navigation">
        <a href="{{ route('top') }}" class="footer-nav-link">
          <span class="footer-nav-icon"><x-svg-icon name="home" /></span>
          <span>TOP</span>
        </a>
        <a href="{{ route('diagnose') }}" class="footer-nav-link">
          <span class="footer-nav-icon"><x-svg-icon name="search" /></span>
          <span>診断</span>
        </a>
        <a href="{{ route('about') }}" class="footer-nav-link">
          <span class="footer-nav-icon"><x-svg-icon name="book-open" /></span>
          <span>About</span>
        </a>
        <a href="{{ route('contact') }}" class="footer-nav-link">
          <span class="footer-nav-icon"><x-svg-icon name="mail" /></span>
          <span>お問い合わせ</span>
        </a>
        <a href="{{ route('terms') }}" class="footer-nav-link">
          <span class="footer-nav-icon"><x-svg-icon name="file-text" /></span>
          <span>利用規約</span>
        </a>
        <a href="{{ route('privacy') }}" class="footer-nav-link">
          <span class="footer-nav-icon"><x-svg-icon name="shield" /></span>
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
        <span class="footer-scroll-icon"><x-svg-icon name="arrow-up" /></span>
      </button>
    </div>
  </footer>
  @endunless

  @stack('scripts')

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // スクロールフェードイン
      var fadeEls = document.querySelectorAll('.fade-in, .fade-in-scale');
      if (fadeEls.length && 'IntersectionObserver' in window) {
        var obs = new IntersectionObserver(function(entries) {
          entries.forEach(function(e) {
            if (e.isIntersecting) {
              e.target.classList.add('is-visible');
              obs.unobserve(e.target);
            }
          });
        }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
        fadeEls.forEach(function(el) { obs.observe(el); });
      }

      // トップへ戻るボタン
      var scrollTopBtn = document.getElementById('scrollToTop');
      if (scrollTopBtn) {
        window.addEventListener('scroll', function() {
          scrollTopBtn.classList.toggle('visible', window.pageYOffset > 300);
        }, { passive: true });
        scrollTopBtn.addEventListener('click', function() {
          window.scrollTo({ top: 0, behavior: 'smooth' });
        });
      }
    });
  </script>
</body>
</html>
