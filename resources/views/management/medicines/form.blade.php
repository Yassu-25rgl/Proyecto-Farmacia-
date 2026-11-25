<div class="row">
    <div class="col-12 mb-3">
        <label for="name" class="form-label">Nombre</label>
        <input type="text" name="name" id="name" maxlength="120"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $medicine->name ?? '') }}">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6 mb-3">
        <label for="category_id" class="form-label">Categoría</label>
        <select name="category_id" id="category_id"
                class="form-select @error('category_id') is-invalid @enderror">
            <option value="">-- Seleccione --</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) old('category_id', $medicine->category_id ?? '') === (string) $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6 mb-3">
        <label for="amount" class="form-label">Cantidad</label>
        <input type="number" name="amount" id="amount" step="1" min="0"
               class="form-control @error('amount') is-invalid @enderror"
               value="{{ old('amount', $medicine->amount ?? 0) }}">
        @error('amount')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6 mb-3">
        <label for="price_unit" class="form-label">Precio Unitario (S/)</label>
        <input type="number" name="price_unit" id="price_unit" step="0.01" min="0"
               class="form-control @error('price_unit') is-invalid @enderror"
               value="{{ old('price_unit', $medicine->price_unit ?? '') }}">
        @error('price_unit')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6 mb-3">
        <label for="subtotal_preview" class="form-label">Subtotal (S/)</label>
        <input type="text" id="subtotal_preview" class="form-control bg-light" readonly
               value="{{ isset($medicine) ? number_format($medicine->subtotal, 2) : '0.00' }}">
    </div>

    <div class="col-12 mb-3">
        <label for="image" class="form-label">
            Imagen <small class="text-muted">(opcional, jpg/png/webp, máx. 2 MB)</small>
        </label>
        <input type="file" name="image" id="image" accept="image/*"
               class="form-control @error('image') is-invalid @enderror">
        @error('image')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        @if (!empty($medicine?->image))
            <div class="mt-2">
                <small class="text-muted d-block mb-1">Imagen actual:</small>
                <img src="{{ asset('storage/' . $medicine->image) }}" alt="Imagen medicamento"
                     class="img-thumbnail" style="max-height: 120px">
            </div>
        @endif
    </div>
</div>

<button type="submit" class="btn btn-primary text-white">Guardar</button>
<a href="{{ route('medicines.index') }}" class="btn btn-secondary">Cancelar</a>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const amountInput = document.getElementById('amount');
    const priceInput = document.getElementById('price_unit');
    const subtotalPreview = document.getElementById('subtotal_preview');

    function updateSubtotal() {
        const amount = parseFloat(amountInput?.value || 0);
        const price = parseFloat(priceInput?.value || 0);
        const total = (isNaN(amount) ? 0 : amount) * (isNaN(price) ? 0 : price);
        if (subtotalPreview) {
            subtotalPreview.value = total.toFixed(2);
        }
    }

    amountInput?.addEventListener('input', updateSubtotal);
    priceInput?.addEventListener('input', updateSubtotal);
    updateSubtotal();
});
</script>
@endpush