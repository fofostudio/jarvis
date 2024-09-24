@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4">
        <h2 class="mb-4">Pagos y Balances de Operadores</h2>

        <div class="card mb-4">
            <div class="card-body">
                <h3 class="card-title">Balances de Operadores</h3>
                <div class="table-responsive">
                    <table class="table table-hover" id="balancesTable">
                        <thead class="table-light">
                            <tr>
                                <th>Operador</th>
                                <th>Balance Actual</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($operatorBalances as $balance)
                                <tr>
                                    <td>{{ $balance->user->name }}</td>
                                    <td data-order="{{ $balance->balance }}">
                                        {{ number_format($balance->balance, 0, ',', '.') }} COP</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @if (auth()->user()->role == 'coperative')
            <div class="mb-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPaymentModal">
                    Registrar Nuevo Pago
                </button>
            </div>
        @endif


        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Historial de Pagos</h3>
                <div class="table-responsive">
                    <table class="table table-hover" id="paymentsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Operador</th>
                                <th>Monto</th>
                                <th>Fecha de Pago</th>
                                <th>Notas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $payment)
                                <tr>
                                    <td>{{ $payment->user->name }}</td>
                                    <td>{{ number_format($payment->amount, 0, ',', '.') }} COP</td>
                                    <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                    <td>{{ $payment->notes ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para crear pago -->
    <div class="modal fade" id="createPaymentModal" tabindex="-1" aria-labelledby="createPaymentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createPaymentModalLabel">Registrar Nuevo Pago</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createPaymentForm">
                        @csrf
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Operador</label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="">Seleccionar operador</option>
                                @foreach ($operators as $operator)
                                    <option value="{{ $operator->id }}"
                                        data-balance="{{ $operatorBalances[$operator->id]->balance ?? 0 }}">
                                        {{ $operator->name }} - Balance:
                                        {{ number_format($operatorBalances[$operator->id]->balance ?? 0, 0, ',', '.') }}
                                        COP
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Monto del Pago</label>
                            <input type="number" class="form-control" id="amount" name="amount" required>
                        </div>
                        <div class="mb-3">
                            <label for="payment_date" class="form-label">Fecha de Pago</label>
                            <input type="date" class="form-control" id="payment_date" name="payment_date"
                                value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notas</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="submitPayment">Registrar Pago</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#balancesTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                },
                "order": [
                    [1, "desc"]
                ],
                "paging": false,
                "columnDefs": [{
                    "targets": 1,
                    "type": "num-fmt"
                }]
            });
            $('#paymentsTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                },
                "order": [
                    [2, "desc"]
                ],
                "paging": false
            });

            $('#user_id').change(function() {
                var selectedOption = $(this).find('option:selected');
                var balance = selectedOption.data('balance');
                $('#amount').attr('max', balance);
            });

            $('#submitPayment').click(function() {
                var formData = $('#createPaymentForm').serialize();
                $.ajax({
                    url: "{{ route('foodAdmin.storePayment') }}",
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#createPaymentModal').modal('hide');
                        location
                            .reload(); // Recargar la página para mostrar el nuevo pago y balances actualizados
                    },
                    error: function(xhr) {
                        // Manejar errores, mostrar mensajes de error, etc.
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>
@endpush
