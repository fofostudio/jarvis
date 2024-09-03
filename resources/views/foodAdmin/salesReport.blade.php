@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Sales Report</h2>

        <form action="{{ route('foodAdmin.salesReport') }}" method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary form-control">Generate Report</button>
                </div>
            </div>
        </form>

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
