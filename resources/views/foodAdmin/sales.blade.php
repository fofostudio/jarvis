@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Sales</h2>
        <a href="{{ route('foodAdmin.createSale') }}" class="btn btn-primary mb-3">Record Sale</a>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Sale Date</th>
                    <th>Sold By</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sales as $sale)
                    <tr>
                        <td>{{ $sale->product->name }}</td>
                        <td>{{ $sale->quantity }}</td>
                        <td>{{ $sale->total_price }}</td>
                        <td>{{ $sale->sale_date }}</td>
                        <td>{{ $sale->user->name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
