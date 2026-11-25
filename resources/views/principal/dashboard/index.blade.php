@extends('template.administrador')

@section('title', 'Dashboard')
@section('name_section', 'Dashboard')
@section('description', 'Resumen general del sistema')
@section('item_section', 'Inicio')
@section('route_section', route('dashboard.index'))
@section('section', 'Dashboard')
@section('module', 'Principal')

@section('content')
@include('partials.alerts')

<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card bg-white border-0 shadow-sm border-5 border-top border-primary h-100">
            <div class="card-body">
                <div class="text-uppercase small text-muted mb-1">Ventas hoy</div>
                <div class="fs-4 fw-bold">S/ {{ number_format($salesToday, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card bg-white border-0 shadow-sm border-5 border-top border-success h-100">
            <div class="card-body">
                <div class="text-uppercase small text-muted mb-1">Ventas del mes</div>
                <div class="fs-4 fw-bold">S/ {{ number_format($salesMonth, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card bg-white border-0 shadow-sm border-5 border-top border-info h-100">
            <div class="card-body">
                <div class="text-uppercase small text-muted mb-1">Clientes registrados</div>
                <div class="fs-4 fw-bold">{{ $customersCount }}</div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card bg-white border-0 shadow-sm border-5 border-top border-warning h-100">
            <div class="card-body">
                <div class="text-uppercase small text-muted mb-1">Stock bajo (≤ 10)</div>
                <div class="fs-4 fw-bold">{{ $lowStockCount }} <span class="fs-6 fw-normal text-muted">/ {{ $medicinesCount }} med.</span></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-7">
        <div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="text-uppercase small text-muted mb-0">Últimas ventas</h6>
                <a href="{{ route('sales.create') }}" class="btn btn-sm btn-primary text-white">Nueva venta</a>
            </div>
            <div class="card-body">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="small text-uppercase">Fecha</th>
                            <th class="small text-uppercase">Cliente</th>
                            <th class="small text-uppercase">Vendedor</th>
                            <th class="small text-uppercase text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentSales as $sale)
                            <tr>
                                <td><a href="{{ route('sales.show', $sale) }}" class="text-decoration-none">{{ $sale->date->format('d/m/Y H:i') }}</a></td>
                                <td>{{ $sale->customer->name }}</td>
                                <td>{{ $sale->user->name }}</td>
                                <td class="text-end">S/ {{ number_format($sale->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">Sin ventas registradas aún</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-5">
        <div class="card bg-white border-0 shadow-sm border-5 border-top border-danger h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="text-uppercase small text-muted mb-0">Medicamentos con stock bajo</h6>
                <a href="{{ route('medicines.index') }}" class="btn btn-sm btn-outline-secondary">Ver todos</a>
            </div>
            <div class="card-body">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="small text-uppercase">Medicamento</th>
                            <th class="small text-uppercase text-center">Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lowStockMedicines as $medicine)
                            <tr>
                                <td>{{ $medicine->name ?? ($medicine->category->name ?? 'Medicamento #' . $medicine->id) }}</td>
                                <td class="text-center fw-bold text-danger">{{ $medicine->amount }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-3">Todo el inventario tiene buen stock</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection