@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Crear Plan de Trabajo</h1>
    <p class="mb-4">Ingrese los detalles para crear un nuevo plan de trabajo.</p>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Nuevo Plan de Trabajo</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('work-detail.storeDetail') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="user_id">Usuario</label>
                    <select class="form-control" id="user_id" name="user_id" required>
                        <option value="">Seleccione un usuario</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="date">Fecha</label>
                    <input type="date" class="form-control" id="date" name="date" required>
                </div>
                <div class="form-group">
                    <label for="calendar_type">Tipo de Calendario</label>
                    <select class="form-control" id="calendar_type" name="calendar_type" required>
                        <option value="">Seleccione un tipo</option>
                        <option value="mensajes">Mensajes</option>
                        <option value="icebreakers">Icebreakers</option>
                        <option value="cartas">Cartas</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="cantidad">Cantidad</label>
                    <input type="number" class="form-control" id="cantidad" name="cantidad" required min="1">
                </div>
                <button type="submit" class="btn btn-primary">Crear Plan de Trabajo</button>
            </form>
        </div>
    </div>
</div>
@endsection