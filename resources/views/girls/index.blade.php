@extends('layouts.app')

@section('content')
    <style>
        .group-card {
            cursor: pointer;
            transition: box-shadow 0.3s ease-in-out, transform 0.3s ease-in-out;
        }
        .group-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            transform: translateY(-5px);
        }
        .carousel-indicators {
            position: static;
            margin-top: 10px;
        }
        .carousel-indicators [data-bs-target] {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }
        .scroll-indicator {
            font-size: 0.8rem;
            color: #6c757d;
            text-align: center;
            margin-top: 5px;
        }
        .scroll-indicator i {
            animation: bounceLeftRight 2s infinite;
        }
        @keyframes bounceLeftRight {
            0%, 100% { transform: translateX(0); }
            50% { transform: translateX(10px); }
        }
    </style>

    <h5 class="mb-4 fw-light">
        <a class="text-reset" href="{{ url('dashboard') }}">{{ __('admin.dashboard') }}</a>
        <i class="bi-chevron-right me-1 fs-6"></i>
        <span class="text-muted">{{ __('admin.girls') }} ({{$girls->count()}})</span>
        <a href="{{ route('girls.create') }}" class="btn btn-sm btn-dark float-lg-end mt-1 mt-lg-0">
            <i class="bi-plus-lg"></i> {{ __('admin.create_girl') }}
        </a>
    </h5>

    <div class="content">
        <!-- Platform summary cards -->
        <div class="row mb-4">
            @foreach($platforms as $platform)
                <div class="col-md-4 mb-3">
                    <div class="card shadow-custom border-0">
                        <div class="card-body">
                            <h5 class="card-title">{{ $platform->name }}</h5>
                            <p class="card-text">{{ __('admin.girls') }}: {{ $platform->girls_count }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Horizontally scrollable groups in an accordion -->
        <div class="accordion mb-4" id="groupsAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="groupsHeading">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#groupsCollapse" aria-expanded="true" aria-controls="groupsCollapse">
                        {{ __('admin.groups') }}
                    </button>
                </h2>
                <div id="groupsCollapse" class="accordion-collapse collapse show" aria-labelledby="groupsHeading">
                    <div class="accordion-body">
                        <div id="groupsCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="false">
                            <div class="carousel-inner">
                                @foreach($groups->chunk(4) as $index => $chunk)
                                    <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                        <div class="row">
                                            @foreach($chunk as $group)
                                                <div class="col-md-3">
                                                    <div class="card shadow-custom border-0 group-card" data-group-id="{{ $group->id }}">
                                                        <div class="card-body">
                                                            <h5 class="card-title">{{ $group->name }}</h5>
                                                            <p class="card-text">{{ __('admin.girls') }}: {{ $group->girls_count }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#groupsCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#groupsCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                            <div class="carousel-indicators">
                                @foreach($groups->chunk(4) as $index => $chunk)
                                    <button type="button" data-bs-target="#groupsCarousel" data-bs-slide-to="{{ $index }}" class="{{ $loop->first ? 'active' : '' }}" aria-current="{{ $loop->first ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}"></button>
                                @endforeach
                            </div>
                        </div>
                        <div class="scroll-indicator">
                            <i class="bi bi-arrow-left-right"></i> {{ __('admin.scroll_to_see_more_groups') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search form -->
        <div class="row mb-4">
            <div class="col-md-12">
                <form action="{{ route('girls.index') }}" method="GET" class="form-inline">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="search" placeholder="{{ __('admin.search_by_name_or_code') }}" value="{{ request('search') }}">
                        <select class="form-select" name="platform">
                            <option value="">{{ __('admin.all_platforms') }}</option>
                            @foreach($platforms as $platform)
                                <option value="{{ $platform->id }}" {{ request('platform') == $platform->id ? 'selected' : '' }}>{{ $platform->name }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-outline-secondary" type="submit">{{ __('admin.search') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Girls table -->
        <div class="row">
            <div class="col-lg-12">
                @if (session('success_message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check2 me-1"></i> {{ session('success_message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                @endif

                <div class="card shadow-custom border-0">
                    <div class="card-body p-lg-4">
                        <div class="table-responsive p-0">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th class="active">{{ trans('admin.name') }}</th>
                                        <th class="active">{{ trans('admin.internal_id') }}</th>
                                        <th class="active">{{ trans('admin.username') }}</th>
                                        <th class="active">{{ trans('admin.platform') }}</th>
                                        <th class="active">{{ trans('admin.group') }}</th>
                                        <th class="active">{{ trans('admin.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($girls as $girl)
                                        <tr class="girl-row" data-group-id="{{ $girl->group_id }}">
                                            <td>{{ $girl->name }}</td>
                                            <td>{{ $girl->internal_id }}</td>
                                            <td>{{ $girl->username }}</td>
                                            <td>{{ $girl->platform->name }}</td>
                                            <td>{{ $girl->group->name }}</td>
                                            <td>
                                                <a href="{{ route('girls.edit', $girl) }}" class="btn btn-success rounded-pill btn-sm me-2">
                                                    <i class="bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('girls.destroy', $girl) }}" method="POST" class="d-inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger rounded-pill btn-sm actionDelete">
                                                        <i class="bi-trash-fill"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">{{ trans('admin.no_girls_found') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



<script>
    document.addEventListener('DOMContentLoaded', function() {
        const groupCards = document.querySelectorAll('.group-card');
        const girlRows = document.querySelectorAll('.girl-row');

        groupCards.forEach(card => {
            card.addEventListener('click', function() {
                const groupId = this.dataset.groupId;
                girlRows.forEach(row => {
                    if (row.dataset.groupId === groupId) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
                // Highlight the selected card
                groupCards.forEach(c => c.classList.remove('bg-light'));
                this.classList.add('bg-light');
            });
        });

        // Initialize the carousel
        new bootstrap.Carousel(document.querySelector('#groupsCarousel'), {
            interval: false // Disable auto-sliding
        });
    });
</script>
@endsection
