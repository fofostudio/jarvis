@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Diccionario de Links</h1>

    <div class="dictionary-grid">
        @foreach($categoryLinks as $category)
            <div class="grid-item">
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
    .dictionary-grid {
        width: 100%;
    }

    .grid-item {
        width: calc(25% - 20px);
        margin-bottom: 20px;
    }

    .card {
        height: 100%;
    }

    @media (max-width: 1200px) {
        .grid-item {
            width: calc(33.333% - 20px);
        }
    }

    @media (max-width: 992px) {
        .grid-item {
            width: calc(50% - 20px);
        }
    }

    @media (max-width: 768px) {
        .grid-item {
            width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var grid = document.querySelector('.dictionary-grid');
    var msnry = new Masonry(grid, {
        itemSelector: '.grid-item',
        columnWidth: '.grid-item',
        percentPosition: true,
        gutter: 20
    });

    // Reordena los elementos para que los más altos estén primero
    var gridItems = Array.from(grid.querySelectorAll('.grid-item'));
    gridItems.sort((a, b) => b.querySelector('.card').offsetHeight - a.querySelector('.card').offsetHeight);
    gridItems.forEach(item => grid.appendChild(item));

    // Reinicia Masonry después de reordenar
    msnry.reloadItems();
    msnry.layout();

    // Ajusta el layout cuando las imágenes se carguen
    imagesLoaded(grid).on('progress', function() {
        msnry.layout();
    });
});
</script>
@endpush
