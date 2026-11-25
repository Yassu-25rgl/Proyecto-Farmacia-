@extends('template.administrador')

@section('title', 'Reportes')
@section('name_section', 'Reportes')
@section('description', 'Reporte de ventas e inventario')
@section('item_section', 'Principal')
@section('route_section', route('reports.index'))
@section('section', 'Reportes')
@section('module', 'Principal')

@section('content')
@include('partials.alerts')

<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary mb-3 no-print">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.index') }}" class="row g-3">
            <div class="col-12 col-md-3">
                <label class="form-label small text-uppercase">Fecha inicio</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label small text-uppercase">Fecha fin</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label small text-uppercase">Cliente</label>
                <select name="customer_id" class="form-select">
                    <option value="">Todos</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" @selected(request('customer_id') == $customer->id)>{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label small text-uppercase">Vendedor</label>
                <select name="user_id" class="form-select">
                    <option value="">Todos</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 d-flex gap-2">
                <button class="btn btn-primary text-white" type="submit"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                <a href="{{ route('reports.export', request()->query()) }}" class="btn btn-success"><i class="bi bi-download me-1"></i>CSV</a>
                <button type="button" class="btn btn-outline-dark" onclick="window.print()"><i class="bi bi-printer me-1"></i>Imprimir</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm border-5 border-top border-primary h-100"><div class="card-body"><small class="text-uppercase text-muted">Ventas</small><div class="fs-3 fw-bold">{{ $salesCount }}</div></div></div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm border-5 border-top border-info h-100"><div class="card-body"><small class="text-uppercase text-muted">Productos vendidos</small><div class="fs-3 fw-bold">{{ $productsSold }}</div></div></div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm border-5 border-top border-success h-100"><div class="card-body"><small class="text-uppercase text-muted">Ingresos</small><div class="fs-3 fw-bold">S/ {{ number_format($incomeTotal, 2) }}</div></div></div>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm border-5 border-top border-secondary h-100">
            <div class="card-header bg-white"><h6 class="mb-0 text-uppercase small text-muted">Ventas del periodo</h6></div>
            <div class="card-body table-responsive">
                <table class="table table-sm table-hover align-middle">
                    <thead><tr><th>N°</th><th>Fecha</th><th>Cliente</th><th>Vendedor</th><th class="text-end">Total</th></tr></thead>
                    <tbody>
                        @forelse ($sales as $sale)
                            <tr><td>{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</td><td>{{ $sale->date->format('d/m/Y H:i') }}</td><td>{{ $sale->customer->name }}</td><td>{{ $sale->user->name }}</td><td class="text-end">S/ {{ number_format($sale->total, 2) }}</td></tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">No hay ventas en el periodo</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm border-5 border-top border-danger h-100">
            <div class="card-header bg-white"><h6 class="mb-0 text-uppercase small text-muted">Medicamentos más vendidos</h6></div>
            <div class="card-body table-responsive">
                <table class="table table-sm align-middle">
                    <thead><tr><th>Medicamento</th><th class="text-center">Cant.</th><th class="text-end">Ingresos</th></tr></thead>
                    <tbody>
                        @forelse ($topMedicines as $medicine)
                            <tr><td>{{ $medicine->name }}</td><td class="text-center">{{ $medicine->total_amount }}</td><td class="text-end">S/ {{ number_format($medicine->total_income, 2) }}</td></tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">Sin datos</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm border-5 border-top border-warning mt-3">
    <div class="card-header bg-white"><h6 class="mb-0 text-uppercase small text-muted">Inventario actual</h6></div>
    <div class="card-body table-responsive">
        <table class="table table-sm table-hover align-middle">
            <thead><tr><th>Medicamento</th><th>Categoría</th><th class="text-center">Stock</th><th class="text-end">Precio</th></tr></thead>
            <tbody>
                @foreach ($inventory as $medicine)
                    <tr><td>{{ $medicine->name }}</td><td>{{ $medicine->category->name ?? '-' }}</td><td class="text-center {{ $medicine->amount <= 10 ? 'fw-bold text-danger' : '' }}">{{ $medicine->amount }}</td><td class="text-end">S/ {{ number_format($medicine->price_unit, 2) }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
@media print {
    .no-print, #sidebar, .navbar, .modal { display: none !important; }
    #app-wrapper, .main-wrapper, .app-content { display: block !important; height: auto !important; overflow: visible !important; }
}
</style>
@endsection
