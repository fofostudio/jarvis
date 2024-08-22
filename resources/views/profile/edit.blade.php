@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4">
        {{ __('admin.Profile') }}
    </h2>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">{{ __('admin.avatar') }}</div>
                <div class="card-body text-center">
                    @if ($user->avatar)
                        <img src="{{ asset('avatars/' . $user->avatar) }}" alt="Avatar" class="img-fluid rounded-circle mb-3" style="max-width: 150px;">
                    @else
                        <img src="{{ asset('default-avatar.png') }}" alt="Default Avatar" class="img-fluid rounded-circle mb-3" style="max-width: 150px;">
                    @endif
                    <input id="avatar" name="avatar" type="file" class="form-control">
                    @error('avatar')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">{{ __('admin.profile_info') }}</div>
                <div class="card-body">
                    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('patch')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">{{ __('admin.profile_name') }}</label>
                                <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required autofocus>
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">{{ __('admin.email') }}</label>
                                <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required readonly>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">{{ __('admin.phone') }}</label>
                                <input id="phone" name="phone" type="tel" class="form-control" value="{{ old('phone', $user->phone) }}" readonly>
                                @error('phone')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="address" class="form-label">{{ __('admin.address') }}</label>
                                <input id="address" name="address" type="text" class="form-control" value="{{ old('address', $user->address) }}" readonly>
                                @error('address')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="neighborhood" class="form-label">{{ __('admin.neighborhood') }}</label>
                            <input id="neighborhood" name="neighborhood" type="text" class="form-control" value="{{ old('neighborhood', $user->neighborhood) }}" readonly>
                            @error('neighborhood')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-dark mt-3 px-5 me-2">{{ __('admin.update_profile') }}</button>
                    </form>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">{{ __('admin.update_password') }}</div>
                <div class="card-body">
                    <form method="post" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">{{ __('admin.current_password') }}</label>
                            <input id="current_password" name="current_password" type="password" class="form-control" required>
                            @error('current_password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('admin.new_password') }}</label>
                            <input id="password" name="password" type="password" class="form-control" required>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">{{ __('admin.confirm_password') }}</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-dark mt-3 px-5 me-2">{{ __('admin.update_password') }}</button>
                    </form>
                </div>
            </div>

            <!-- You can add the delete account section here if needed -->
        </div>
    </div>
</div>
@endsection
