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
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">

    <!-- CSS personalizado -->
    @include('includes.css_admin')

    <style>
        :root {
            --color-default: #000000;
        }

        body {
            font-family: 'Figtree', sans-serif;
        }

        .mobile-warning-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 0, 0, 0.9);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            color: white;
            text-align: center;
            font-family: Arial, sans-serif;
        }

        .mobile-warning-overlay h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .mobile-warning-overlay p {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .logo-jarvis {
            width: 200px;
            /* Tamaño predeterminado para escritorio */
        }

        @media (max-width: 768px) {
            .logo-jarvis {
                width: 100px;
                /* Tamaño para dispositivos móviles */
            }
        }
    </style>

    @yield('css')
    @stack('styles')

    @yield('javascriptheader')
    <style>
        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-3px);
        }
    </style>
</head>

<body>
    @if (auth()->check() && auth()->user()->role == 'operator' && \Jenssegers\Agent\Facades\Agent::isMobile())
        <div class="mobile-warning-overlay">
            <h1>Acceso no permitido</h1>
            <p>Jarvis no es accesible vía móvil para operadores.</p>
            <p>Por favor, accede desde un dispositivo de escritorio.</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-dark">
                    {{ __('admin.LogOut') }}
                </button>
            </form>
        </div>
    @else
        <div class="overlay" data-bs-toggle="offcanvas" data-bs-target="#sidebar-nav"></div>
        <div class="popout font-default"></div>

        <main>
            <div class="offcanvas offcanvas-start sidebar bg-dark text-white" tabindex="-1" id="sidebar-nav"
                data-bs-keyboard="false" data-bs-backdrop="false">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title"><img src="{{ asset('images/logojarvis.svg') }}" class="logo-jarvis"
                            alt="Logo Jarvis">
                    </h5>
                    <button type="button" class="btn-close btn-close-custom text-white toggle-menu d-lg-none"
                        data-bs-dismiss="offcanvas" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="offcanvas-body px-0 scrollbar">
                    <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-start list-sidebar" id="menu">
                        <!-- Aquí va el contenido del sidebar -->
                        @include('layouts.sidebar')
                    </ul>
                </div>
            </div>

            <header class="py-3 mb-3 shadow-custom bg-dark">
                <div class="container-fluid px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <a class="toggle-menu d-block d-lg-none fs-3" data-bs-toggle="offcanvas"
                            data-bs-target="#sidebar-nav" href="#">
                            <i class="bi-list"></i>
                        </a>

                        <a class="animate-up-2" href="{{ url('/') }}">
                        </a>

                        <div class="flex-shrink-0 dropdown">
                            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle"
                                id="dropdownUser2" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : asset('images/default-avatar.png') }}"
                                    alt="{{ Auth::user()->name }}" width="32" height="32"
                                    class="rounded-circle me-2">
                                <div class="d-none d-sm-block">
                                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                                        {{ Auth::user()->name }}</div>
                                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownUser2">
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        {{ __('admin.Profile') }}
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            {{ __('admin.LogOut') }}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </header>

            <div class="container-fluid">
                <div class="row">
                    <div class="col min-vh-100 admin-container p-4">
                        @yield('content')
                    </div>
                </div>
            </div>

            <footer class="admin-footer px-4 py-3 bg-white shadow-custom">
                &copy; Jarvis Bot V7.0 - {{ date('Y') }}
            </footer>
        </main>
    @endif
    <script>
        // Definir variables globales para textos de confirmación
        window.appTranslations = {
            delete_confirm: "{{ __('admin.delete_confirm') }}",
            yes_confirm: "{{ __('admin.yes_confirm') }}",
            cancel_confirm: "{{ __('admin.cancel_confirm') }}",
            login_as_user_warning: "{{ __('admin.login_as_user_warning') }}",
            yes: "{{ __('admin.yes') }}",
            // Añade aquí cualquier otra variable de texto que necesites
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@1.0.1/dist/chartjs-adapter-moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="{{ asset('/js/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('/admin/admin-functions.js') }}"></script>

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
