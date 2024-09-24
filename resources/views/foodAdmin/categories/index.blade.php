<!-- resources/views/foodAdmin/categories/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Categories</h2>
        <a href="{{ route('foodAdmin.categories.create') }}" class="btn btn-primary mb-3">Create Category</a>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->description }}</td>
                        <td>
                            <a href="{{ route('foodAdmin.categories.edit', $category) }}" class="btn btn-sm btn-primary">Edit</a>
                            <form action="{{ route('foodAdmin.categories.destroy', $category) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this category?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
