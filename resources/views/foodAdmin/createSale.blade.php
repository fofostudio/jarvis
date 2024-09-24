@extends('layouts.app')

@section('content')
<div class="container-fluid mt-3">
    <h2 class="mb-3">Registrar Venta</h2>

    <form action="{{ route('foodAdmin.storeSale') }}" method="POST" id="saleForm">
        @csrf

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label for="user_id" class="form-label">Cliente</label>
                <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                    <option value="">Seleccionar cliente</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <input hidden type="date" class="form-control @error('sale_date') is-invalid @enderror"
                    id="sale_date" name="sale_date" value="{{ old('sale_date', date('Y-m-d')) }}" required>
                @error('sale_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <h5>Productos</h5>
            @foreach ($categories as $category)
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">{{ $category->name }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                            @foreach ($category->products as $product)
                                <div class="col">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input product-checkbox" type="checkbox"
                                                    name="products[]" value="{{ $product->id }}"
                                                    id="product-{{ $product->id }}" data-price="{{ $product->price }}"
                                                    data-is-vale="{{ $product->id == 16 ? 'true' : 'false' }}">
                                                <label class="form-check-label fw-bold"
                                                    for="product-{{ $product->id }}">
                                                    {{ $product->name }}
                                                </label>
                                            </div>
                                            @if ($product->id == 16)
                                                <div class="mb-2">
                                                    <label for="vale-value" class="form-label">Valor del Vale</label>
                                                    <input type="number" class="form-control vale-value"
                                                        id="vale-value" name="vale_value" min="0" value="0"
                                                        disabled>
                                                </div>
                                            @else
                                                <p class="card-text">Precio:
                                                    {{ number_format($product->price, 0, ',', '.') }} COP</p>
                                                <div class="mb-2">
                                                    <label for="quantity-{{ $product->id }}"
                                                        class="form-label">Cantidad</label>
                                                    <select class="form-select quantity"
                                                        name="quantities[{{ $product->id }}]"
                                                        id="quantity-{{ $product->id }}" disabled>
                                                        @for ($i = 1; $i <= 10; $i++)
                                                            <option value="{{ $i }}">{{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            @endif
                                            <p class="card-text">Total: <span class="product-total">0 COP</span></p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Resumen de la Venta</h5>
                <p class="card-text h4">Total: <span id="grandTotal">0 COP</span></p>
            </div>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary btn-lg" id="submitButton">Registrar Venta</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    function updateTotals() {
        let grandTotal = 0;
        $('.product-checkbox:checked').each(function() {
            const card = $(this).closest('.card-body');
            const isVale = $(this).data('is-vale') === true;
            let total;

            if (isVale) {
                total = parseFloat(card.find('.vale-value').val()) || 0;
            } else {
                const price = parseFloat($(this).data('price'));
                const quantity = parseInt(card.find('.quantity').val());
                total = price * quantity;
            }

            card.find('.product-total').text(total.toLocaleString('es-CO') + ' COP');
            grandTotal += total;
        });
        $('#grandTotal').text(grandTotal.toLocaleString('es-CO') + ' COP');
    }

    $('.product-checkbox').change(function() {
        const card = $(this).closest('.card-body');
        const isVale = $(this).data('is-vale') === true;

        if (isVale) {
            const valeValueInput = card.find('.vale-value');
            valeValueInput.prop('disabled', !this.checked);
            if (this.checked) {
                valeValueInput.val('0').focus();
            } else {
                valeValueInput.val('0');
            }
        } else {
            const quantitySelect = card.find('.quantity');
            quantitySelect.prop('disabled', !this.checked);
            if (!this.checked) {
                quantitySelect.val(1);
            }
        }
        updateTotals();
    });

    $('.quantity, .vale-value').on('change input', updateTotals);

    $('#saleForm').submit(function(e) {
        e.preventDefault();
        const form = $(this);

        if ($('.product-checkbox:checked').length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Por favor, seleccione al menos un producto.',
            });
            return;
        }

        const valeCheckbox = $('#product-16');
        if (valeCheckbox.is(':checked')) {
            const valeValue = parseFloat($('#vale-value').val());
            if (isNaN(valeValue) || valeValue <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Por favor, ingrese un valor válido para el Vale.',
                });
                return;
            }
        }

        $('#submitButton').prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Cargando...'
        );

        Swal.fire({
            title: 'Procesando venta',
            text: 'Por favor espere...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: form.attr('action'),
            method: form.attr('method'),
            data: form.serialize(),
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: 'Venta registrada correctamente',
                }).then((result) => {
                    window.location.href = "{{ route('foodAdmin.createSale') }}";
                });
            },
            error: function(xhr) {
                let errorMessage = 'Ha ocurrido un error al procesar la venta.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: errorMessage,
                });
            },
            complete: function() {
                $('#submitButton').prop('disabled', false).html('Registrar Venta');
            }
        });
    });
});
</script>
@endpush
