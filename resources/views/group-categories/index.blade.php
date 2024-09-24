<!-- resources/views/group-categories/index.blade.php -->
@extends('layouts.app')

@section('content')
    <h1>Categorías de Grupos</h1>
    <a href="{{ route('group-categories.create') }}" class="btn btn-primary">Crear Nueva Categoría</a>

    <table class="table mt-3">
        <thead>
            <tr>
                <th>Orden</th>
                <th>Nombre</th>
                <th>Meta Mensual</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
                <tr>
                    <td>{{ $category->order }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->monthly_goal }}</td>
                    <td>
                        <a href="{{ route('group-categories.edit', $category) }}" class="btn btn-sm btn-primary">Editar</a>
                        <a href="{{ route('group-categories.points', $category) }}" class="btn btn-sm btn-info">Ver Puntos</a>
                        <form action="{{ route('group-categories.destroy', $category) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
