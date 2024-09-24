<!-- resources/views/group-categories/points.blade.php -->
@extends('layouts.app')

@section('content')
    <h1>Puntos para la Categoría: {{ $groupCategory->name }}</h1>
    <p>Meta Mensual: {{ $groupCategory->monthly_goal }}</p>
    <p>Puntos Totales: {{ $totalPoints }}</p>
    <p>Progreso: {{ ($totalPoints / $groupCategory->monthly_goal) * 100 }}%</p>

    <h2>Grupos en esta Categoría</h2>
    <ul>
        @foreach($groupCategory->groups as $group)
            <li>{{ $group->name }} - {{ $group->points }} puntos</li>
        @endforeach
    </ul>
@endsection
