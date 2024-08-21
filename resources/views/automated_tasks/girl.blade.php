@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Tareas Automatizadas</h1>
    <h2>Plataforma: {{ $platform->name }}</h2>
    <h3>Chica: {{ $girl->name }}</h3>

    <!-- Aquí puedes añadir la lógica para mostrar las tareas automatizadas -->
    <p>Las tareas automatizadas para esta chica se mostrarán aquí.</p>
</div>
@endsection
