@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Tareas Automatizadas - {{ $platform->name }}</h1>

    <div class="card">
        <div class="card-header">
            Información de la Plataforma
        </div>
        <div class="card-body">
            <p><strong>Nombre:</strong> {{ $platform->name }}</p>
            <p><strong>URL:</strong> <a href="{{ $platform->url }}" target="_blank">{{ $platform->url }}</a></p>
            <!-- Agrega más detalles de la plataforma si es necesario -->
        </div>
    </div>

    <h2 class="mt-4 mb-3">Tareas Disponibles</h2>

    <!-- Aquí puedes agregar una lista de tareas automatizadas -->
    <div class="list-group">
        <!-- Ejemplo de tarea, repite este bloque para cada tarea -->
        <a href="#" class="list-group-item list-group-item-action">
            <div class="d-flex w-100 justify-content-between">
                <h5 class="mb-1">Fav Automatico</h5>
                <small>Estado: Desactivado</small>
            </div>
            <p class="mb-1">Generar Favs de forma automatica en pantalla Principal.</p>
        </a>
        <!-- Fin del ejemplo de tarea -->
    </div>

    <!-- Puedes agregar más secciones según sea necesario -->

    <div class="mt-4">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Volver al Dashboard</a>
        <!-- Agrega más botones de acción si es necesario -->
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Puedes agregar JavaScript específico para esta vista aquí
</script>
@endsection
