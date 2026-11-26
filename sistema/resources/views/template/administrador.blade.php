<!doctype html>
<html lang="es" dir="ltr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="San Juan – Sistema de Venta de Medicamentos">
  <meta name="theme-color" content="#0d6efd">
  <link rel="manifest" href="{{ asset('manifest.json') }}">
  <link rel="icon" href="{{ asset('icons/icon-192.png') }}" type="image/png" sizes="192x192">
  <title>FARM SJB | @yield('title')</title>
  <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
  <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>

<body>

<div id="app-wrapper" class="d-flex">
 @include("partials.sidebar")
  <div class="main-wrapper d-flex flex-column flex-grow-1 overflow-hidden">
     @include("partials.header")
    <main class="app-content flex-grow-1 overflow-y-auto p-3 p-lg-4">
      <div class="container-fluid p-0">
        <div class="d-flex align-items-center justify-content-between flex-column flex-sm-row gap-2 mb-4">
          <p class="text-secondary mb-0">@yield('description')</p>
          <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;)" aria-label="Ruta de navegación">
            <ol class="breadcrumb mb-0 small">
              <li class="breadcrumb-item"><span>@yield('item_section')</span></li>
              <li class="breadcrumb-item text-muted fw-medium" aria-current="page"><a href="@yield('route_section')" class="text-decoration-none text-secondary d-flex align-items-center gap-1">@yield('section')</a></li>
              <li class="breadcrumb-item"><span class="text-secondary">@yield('module')</span></li>
            </ol>
          </nav>
        </div>
        @yield('content')
      </div>
    </main>
  </div>
</div>

<button id="installApplication" type="button" class="btn btn-primary shadow position-fixed bottom-0 end-0 m-3 d-none" style="z-index:1050">
  <i class="bi bi-download me-2"></i>Instalar aplicación
</button>
<div id="offlineAlert" class="alert alert-danger position-fixed bottom-0 start-50 translate-middle-x shadow d-none" style="z-index:1060">
  Sin conexión. Las ventas y cambios de inventario no se pueden guardar.
</div>

@include("partials.modal-logout")

<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script>
if ('serviceWorker' in navigator && (location.protocol === 'https:' || location.hostname === 'localhost' || location.hostname === '127.0.0.1')) {
    window.addEventListener('load', () => navigator.serviceWorker.register('{{ asset('sw.js') }}').catch(() => {}));
}
</script>

@stack('scripts')
</body>
</html>
