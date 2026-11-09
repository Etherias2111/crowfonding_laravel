<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'CROWFONDING | Ingeniería y Artes Gráficas' }}</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}"><link rel="stylesheet" href="{{ asset('css/crowfonding.css') }}"><link rel="stylesheet" href="{{ asset('css/laravel-crow.css') }}?v={{ filemtime(public_path('css/laravel-crow.css')) }}">
</head>
<body>
<header id="navbar" class="site-header">
  <a class="logo-container" href="{{ route('home') }}"><img class="logo-img" src="{{ asset('images/logo.png') }}" alt=""><span class="brand-name">CROWFONDING</span></a>
  <button id="menu-toggle" class="menu-toggle" type="button" aria-label="Abrir menú" aria-controls="main-nav" aria-expanded="false"><span class="menu-toggle-icon">☰</span></button>
  <nav id="main-nav" class="main-nav"><ul class="main-nav-links">
    <li><a href="{{ route('services') }}">Servicios</a></li><li><a href="{{ route('home') }}#equipos">Equipos en Venta</a></li><li><a href="{{ route('home') }}#mugs">Mugs</a></li><li><a href="{{ route('components.index') }}">Componentes</a></li><li><a href="{{ route('home') }}#publicidad">Publicidad y Diseño</a></li>
  </ul><div class="nav-utilities"><button id="theme-toggle" class="theme-toggle-btn" type="button">☀️ Modo Claro</button>
    @auth
      <div class="account-menu" id="account-menu">
        <button class="account-menu-trigger" id="account-menu-toggle" type="button" aria-label="Menú de {{ auth()->user()->username ?: auth()->user()->name }}" aria-haspopup="true" aria-expanded="false" aria-controls="account-dropdown">
          <img class="account-avatar" src="{{ auth()->user()->avatarUrl() }}" alt="">
          <span class="account-username">{{ auth()->user()->username ?: auth()->user()->name }}</span>
          <span class="account-menu-chevron" aria-hidden="true">▾</span>
        </button>
        <div class="account-dropdown" id="account-dropdown" aria-label="Opciones de cuenta">
          @if(auth()->user()->role === 'admin')<a href="{{ route('admin') }}">Administración</a>@endif
          <a href="{{ route('dashboard') }}">Mis tickets</a>
          <a href="{{ route('profile') }}">Mi perfil</a>
          <form method="POST" action="{{ route('logout') }}">@csrf<button class="account-logout" type="submit">Cerrar sesión</button></form>
        </div>
      </div>
    @else<a class="nav-account-link" href="{{ route('login') }}">Iniciar sesión</a><a class="btn-nav-ticket" href="{{ route('register') }}">Registrarse</a>@endauth
  </div></nav>
</header>
@if(session('status'))<div class="notice success" role="status">{{ session('status') }}</div>@endif
@if(session('mail_warning'))<div class="notice error" role="alert">{{ session('mail_warning') }}</div>@endif
<main>@yield('content')</main>
<footer class="crow-footer"><p>&copy; {{ date('Y') }} CROWFONDING. Mantenimiento y Publicidad. Todos los derechos reservados.</p><p><a href="{{ route('terms') }}">Términos y condiciones</a> · Léelos antes de usar nuestros servicios.</p></footer>
<script src="{{ asset('js/crowfonding.js') }}"></script>
@stack('scripts')
</body></html>
