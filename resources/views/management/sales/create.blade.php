@extends('template.administrador')

@section('title', 'Ventas')
@section('name_section', 'Ventas')
@section('description', 'Registrar nueva venta')
@section('item_section', 'Nueva')
@section('route_section', route('sales.index'))
@section('section', 'Ventas')
@section('module', 'Gestión')

@section('content')
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
    <div class="card-body">
        <form action="{{ route('sales.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <label for="customer_id" class="form-label">Cliente</label>
                    <select name="customer_id" id="customer_id"
                            class="form-select @error('customer_id') is-invalid @enderror">
                        <option value="">-- Seleccione --</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}"
                                @selected((string) old('customer_id') === (string) $customer->id)>
                                {{ $customer->name }} DNI {{ $customer->dni }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6 mb-3">
                    <label for="seller_name" class="form-label">Vendedor</label>
                    <input type="text" id="seller_name" class="form-control bg-light"
                           value="{{ auth()->user()->name }}" disabled>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-2 mb-2">
                <h6 class="text-uppercase small text-muted mb-0">Medicamentos</h6>
                <input type="search" id="medicine-search" class="form-control form-control-sm w-auto" placeholder="Buscar medicamento">
            </div>

            @if ($errors->has('items') || $errors->has('items.*'))
                <div class="alert alert-danger py-2">
                    @foreach ($errors->get('items*') as $messages)
                        @foreach ((array) $messages as $message)
                            <div>{{ $message }}</div>
                        @endforeach
                    @endforeach
                </div>
            @endif

            <table class="table table-sm align-middle">
                <thead>
                    <tr>
                        <th class="small text-uppercase">Medicamento</th>
                        <th class="small text-uppercase text-center" style="width: 120px">Cantidad</th>
                        <th class="small text-uppercase text-end" style="width: 120px">Precio</th>
                        <th class="small text-uppercase text-end" style="width: 120px">Subtotal</th>
                        <th style="width: 50px"></th>
                    </tr>
                </thead>
                <tbody id="items">
                    @foreach (old('items', []) as $i => $item)
                        @include('management.sales.item-row', ['index' => $i, 'item' => $item])
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end fw-semibold">Total (incluye IGV)</td>
                        <td class="text-end fw-bold" id="total">S/ 0.00</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>

            <button type="button" id="add-item" class="btn btn-outline-primary btn-sm mb-4">
                <i class="bi bi-plus-lg me-1"></i>Agregar medicamento
            </button>

            <div>
                <button type="submit" class="btn btn-primary text-white">Registrar venta</button>
                <a href="{{ route('sales.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<template id="item-template">
    @include('management.sales.item-row', ['index' => '__INDEX__', 'item' => []])
</template>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.getElementById('items');
    const templateEl = document.getElementById('item-template');
    const addBtn = document.getElementById('add-item');
    const totalEl = document.getElementById('total');
    const searchInput = document.getElementById('medicine-search');

    if (!tbody || !templateEl || !addBtn) return;

    const template = templateEl.innerHTML;
    let index = {{ count(old('items', [])) }};

    function recalculate() {
        let total = 0;
        tbody.querySelectorAll('tr').forEach(row => {
            const select = row.querySelector('select');
            const input = row.querySelector('input[type="number"]');
            const option = select ? select.selectedOptions[0] : null;
            const price = parseFloat(option?.dataset.price || 0);
            const amount = parseInt(input?.value || 0, 10);
            const subtotal = (isNaN(price) ? 0 : price) * (isNaN(amount) ? 0 : amount);

            const priceCell = row.querySelector('.price');
            const subtotalCell = row.querySelector('.subtotal');

            if (priceCell) priceCell.textContent = 'S/ ' + price.toFixed(2);
            if (subtotalCell) subtotalCell.textContent = 'S/ ' + subtotal.toFixed(2);

            total += subtotal;
        });

        if (totalEl) totalEl.textContent = 'S/ ' + total.toFixed(2);
    }

    addBtn.addEventListener('click', () => {
        tbody.insertAdjacentHTML('beforeend', template.replaceAll('__INDEX__', index));
        index++;
        recalculate();
    });

    tbody.addEventListener('click', e => {
        const removeBtn = e.target.closest('.remove-item');
        if (removeBtn) {
            removeBtn.closest('tr').remove();
            recalculate();
        }
    });

    tbody.addEventListener('change', recalculate);
    tbody.addEventListener('input', recalculate);

    searchInput.addEventListener('input', function () {
        const term = this.value.toLowerCase();
        tbody.querySelectorAll('select').forEach(select => {
            Array.from(select.options).forEach(option => {
                if (!option.value) return;
                option.hidden = !option.text.toLowerCase().includes(term);
            });
        });
    });

    if (index === 0 && tbody.children.length === 0) {
        addBtn.click();
    } else {
        recalculate();
    }
});

</script>
@endpush