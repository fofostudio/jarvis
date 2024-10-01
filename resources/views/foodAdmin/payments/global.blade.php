@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <h2 class="mb-4">Vista Global de Pagos y Balances</h2>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Resumen Financiero</h5>
                    <p class="card-text">Deuda Total: <strong>{{ number_format(abs($totalDebt), 0, ',', '.') }} COP</strong></p>
                    <p class="card-text">Total de Pagos Realizados: <strong>{{ number_format($totalPayments, 0, ',', '.') }} COP</strong></p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h3 class="card-title">Balances de Operadores</h3>
            <div class="table-responsive">
                <table class="table table-hover" id="balancesTable">
                    <thead class="table-light">
                        <tr>
                            <th>Operador</th>
                            <th>Deuda Total</th>
                            <th>Responsable</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($operatorBalances as $balance)
                            <tr>
                                <td>{{ $balance->user->name }}</td>
                                <td data-order="{{ $balance->balance }}">
                                    {{ number_format(abs($balance->balance), 0, ',', '.') }} COP
                                </td>
                                <td>{{ $balance->responsible->name }}</td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm" 
                                            onclick="preparePayment({{ $balance->user_id }}, {{ $balance->responsible_id }}, {{ abs($balance->balance) }})">
                                        Realizar Abono
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h3 class="card-title">Historial de Abonos</h3>
            <div class="table-responsive">
                <table class="table table-hover" id="paymentsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Operador</th>
                            <th>Monto Abonado</th>
                            <th>Fecha de Abono</th>
                            <th>Responsable</th>
                            <th>Notas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $payment)
                            <tr>
                                <td>{{ $payment->user->name }}</td>
                                <td>{{ number_format($payment->amount, 0, ',', '.') }} COP</td>
                                <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                <td>{{ $payment->responsible->name }}</td>
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

<!-- Modal para crear abono -->
<div class="modal fade" id="createPaymentModal" tabindex="-1" aria-labelledby="createPaymentModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createPaymentModalLabel">Realizar Abono</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createPaymentForm">
                    @csrf
                    <input type="hidden" id="user_id" name="user_id">
                    <input type="hidden" id="responsible_id" name="responsible_id">
                    <div class="mb-3">
                        <label for="amount" class="form-label">Monto del Abono</label>
                        <input type="number" class="form-control" id="amount" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="payment_date" class="form-label">Fecha de Abono</label>
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
                <button type="button" class="btn btn-primary" id="submitPayment">Registrar Abono</button>
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
            "order": [[1, "desc"]],
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
            "order": [[2, "desc"]],
            "paging": false
        });

        $('#submitPayment').click(function() {
            var formData = $('#createPaymentForm').serialize();
            $.ajax({
                url: "{{ route('foodAdmin.storePaymentGlobal') }}",
                method: 'POST',
                data: formData,
                success: function(response) {
                    $('#createPaymentModal').modal('hide');
                    if (response.reload) {
                        location.reload();
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert('Error al registrar el abono. Por favor, intente de nuevo.');
                }
            });
        });
    });

    function preparePayment(userId, responsibleId, maxAmount) {
        $('#user_id').val(userId);
        $('#responsible_id').val(responsibleId);
        $('#amount').attr('max', maxAmount);
        $('#createPaymentModal').modal('show');
    }
</script>
@endpush