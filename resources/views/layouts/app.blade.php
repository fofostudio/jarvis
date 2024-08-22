<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="#" />
    @yield('javascriptheader')


    <title>{{ __('admin.app_tittle') }}</title>


    <!-- Fonts -->

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    @include('includes.css_admin')
    <style>
        :root {
            --color-default: #000000;
        }

        body {
            font-family: 'Figtree', sans-serif;
        }


    </style>

    @yield('css')
</head>

<body>
    <div class="overlay" data-bs-toggle="offcanvas" data-bs-target="#sidebar-nav"></div>
    <div class="popout font-default"></div>

    <main>
        <div class="offcanvas offcanvas-start sidebar bg-dark text-white" tabindex="-1" id="sidebar-nav"
            data-bs-keyboard="false" data-bs-backdrop="false">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title"><img src="{{ asset('img/logo-1710784068.png') }}" width="100" /></h5>
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
                    <a class="toggle-menu d-block d-lg-none text-dark fs-3" data-bs-toggle="offcanvas"
                        data-bs-target="#sidebar-nav" href="#">
                        <i class="bi-list"></i>
                    </a>

                    <a class="text-dark animate-up-2" href="{{ url('/') }}">
                        {{ trans('admin.view_site') }} <i class="bi-arrow-up-right"></i>
                    </a>

                    <div class="flex-shrink-0 dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle"
                            id="dropdownUser2" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" width="32"
                                height="32" class="rounded-circle me-2">
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
            &copy; jarvis X | FofoStudio - {{ date('Y') }}
        </footer>
    </main>

    <!-- Scripts -->
    <script src="{{ asset('/js/core.min.js') }}"></script>
    <script src="{{ asset('/admin/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('/js/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('/admin/admin-functions.js') }}"></script>
    @yield('javascript')

    @if (session('success_update'))
        <script type="text/javascript">
            swal({
                title: "{{ session('success_update') }}",
                type: "success",
                confirmButtonText: "{{ trans('users.ok') }}"
            });
        </script>
    @endif

    @if (session('unauthorized'))
        <script type="text/javascript">
            swal({
                title: "{{ trans('general.error_oops') }}",
                text: "{{ session('unauthorized') }}",
                type: "error",
                confirmButtonText: "{{ trans('users.ok') }}"
            });
        </script>
    @endif
</body>

</html>
