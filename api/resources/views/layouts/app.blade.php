{{-- resources/views/layouts/app.blade.php --}}
<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', '5akeMe')</title>

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
        >
      </a>
    </div>
  </header>
  @endunless

  <main class="wrap @yield('main_class')">
    @yield('content')
  </main>

  @unless(View::hasSection('hide_footer'))
  <footer class="app-footer">
    <div class="footer-inner">
      <nav class="footer-left" aria-label="Footer navigation">
        <a href="{{ route('top') }}" class="footer-link">TOP</a>
        <a href="{{ route('diagnose') }}" class="footer-link">診断</a>
      </nav>

      <div class="footer-center">
        <p class="footer-brand">5akeMe</p>
        <p class="footer-copy">© {{ date('Y') }} 5akeMe</p>
      </div>

      <div class="footer-right">
        <a href="#" class="sns-link" aria-label="X">X</a>
        <a href="#" class="sns-link" aria-label="Instagram">IG</a>
      </div>
    </div>
  </footer>
  @endunless

  @stack('scripts')
</body>
</html>
