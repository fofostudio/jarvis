@extends('layouts.guest')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 col-lg-5">
            <div class="text-center mb-4">
                <img src="{{ asset('images/logojarvis.svg') }}" alt="Logo Jarvis" class="logo-jarvis mb-3">
            </div>
            <div class="card bg-dark text-light shadow-lg border-0 rounded-lg">               
                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Correo SIC Information') }}</label>
                            <input id="email" type="email" class="form-control bg-secondary text-light @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('Contraseña') }}</label>
                            <input id="password" type="password" class="form-control bg-secondary text-light @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                {{ __('Recuerdame') }}
                            </label>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Ingresar') }}
                            </button>

                           
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
    body {
        background-color: #121212;
        color: #e0e0e0;
    }
    .card {
        transition: all 0.3s ease-in-out;
        background-color: #1e1e1e;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(255,255,255,.1)!important;
    }
    .btn-primary {
        background-color: #3c6faa;
        border-color: #3c6faa;
    }
    .btn-primary:hover {
        background-color: #2c5282;
        border-color: #2c5282;
    }
    .form-control {
        background-color: #2c2c2c;
        border-color: #444;
        color: #e0e0e0;
    }
    .form-control:focus {
        background-color: #3c3c3c;
        border-color: #555;
        color: #ffffff;
        box-shadow: 0 0 0 0.2rem rgba(60, 111, 170, 0.25);
    }
    .form-check-input {
        background-color: #2c2c2c;
        border-color: #444;
    }
    .form-check-input:checked {
        background-color: #3c6faa;
        border-color: #3c6faa;
    }
    .btn-link {
        color: #6c9bd1;
    }
    .btn-link:hover {
        color: #8bb3e5;
    }
    .logo-jarvis {
        max-width: 200px;
        height: auto;
        filter: brightness(0) invert(1); /* Hace el logo blanco para el tema oscuro */
    }
    @media (max-width: 768px) {
        .logo-jarvis {
            max-width: 150px;
        }
    }
</style>
@endsection