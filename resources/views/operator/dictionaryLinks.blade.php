@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Diccionario de Links</h1>

    <div class="row">
        @foreach($categoryLinks as $category)
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ $category->name }}</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @foreach($category->links as $link)
                                <li class="list-group-item d-flex align-items-center">
                                    <img src="{{ $link->favicon }}" alt="favicon" class="me-2" style="width: 16px; height: 16px;">
                                    <a href="{{ $link->url }}" target="_blank">{{ $link->title }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        height: 100%;
    }
    .card-body {
        overflow-y: auto;
        max-height: 300px;
    }
</style>
@endpush
