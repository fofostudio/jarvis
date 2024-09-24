<!-- resources/views/group-categories/edit.blade.php -->
@extends('layouts.app')

@section('content')
    <h1>Editar Categoría de Grupo</h1>
    <form action="{{ route('group-categories.update', $groupCategory) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Nombre</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $groupCategory->name }}" required>
        </div>
        <div class="form-group">
            <label for="order">Orden</label>
            <input type="number" class="form-control" id="order" name="order" value="{{ $groupCategory->order }}" required>
        </div>
        <div class="form-group">
            <label for="monthly_goal">Meta Mensual</label>
            <input type="number" class="form-control" id="monthly_goal" name="monthly_goal" value="{{ $groupCategory->monthly_goal }}" required>
        </div>
        <div class="form-group">
            <label for="task_description">Descripción de la Tarea</label>
            <textarea class="form-control" id="task_description" name="task_description" required>{{ $groupCategory->task_description }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
@endsection
