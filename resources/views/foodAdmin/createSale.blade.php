@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">Record Sale</h2>

        <form action="{{ route('foodAdmin.storeSale') }}" method="POST">
            @csrf

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="user_id" class="form-label">User</label>
                    <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                        <option value="">Select a user</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Products</h5>
                </div>
                <div class="card-body">
                    @foreach ($products as $product)
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="products[]" value="{{ $product->id }}" id="product-{{ $product->id }}">
                                    <label class="form-check-label" for="product-{{ $product->id }}">
                                        {{ $product->name }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <select class="form-select quantity" name="quantities[]" disabled>
                                    @for ($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    @endforeach
                    @error('products')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @error('quantities')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Record Sale</button>
        </form>
    </div>
@endsection

@section('javascript')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            const quantities = document.querySelectorAll('.quantity');

            checkboxes.forEach(function(checkbox, index) {
                checkbox.addEventListener('change', function() {
                    quantities[index].disabled = !this.checked;
                    if (!this.checked) {
                        quantities[index].selectedIndex = 0;
                    }
                });
            });
        });
    </script>
@endsection
