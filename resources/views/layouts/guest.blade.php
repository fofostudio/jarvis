<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />

    <title>{{ __('admin.app_tittle') }}</title>

    <!-- Preconnect para mejora de rendimiento -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">

    <!-- Fuentes -->
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet">

    <!-- CSS de bibliotecas externas -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- CSS personalizado -->
    @include('includes.css_admin')

    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }
    </style>
    @yield('css')
</head>

<body>
    <main>
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    @yield('javascript')
    @stack('scripts')

    @if (session('success_update'))
        <script type="text/javascript">
            Swal.fire({
                title: "{{ session('success_update') }}",
                icon: "success",
                confirmButtonText: "{{ trans('users.ok') }}"
            });
        </script>
    @endif

    @if (session('unauthorized'))
        <script type="text/javascript">
            Swal.fire({
                title: "{{ trans('general.error_oops') }}",
                text: "{{ session('unauthorized') }}",
                icon: "error",
                confirmButtonText: "{{ trans('users.ok') }}"
            });
        </script>
    @endif
</body>

</html>