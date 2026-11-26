@extends('template.administrador')

@section('title', 'Reporte de ventas')
@section('name_section', 'Reporte de ventas')
@section('description', 'Ventas por rango de fechas y filtros')
@section('item_section', 'Inicio')
@section('route_section', route('reports.index'))
@section('section', 'Reportes')
@section('module', 'Principal')

@section('content')
@include('partials.alerts')

<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary mb-3 no-print">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.index') }}" class="row g-2 align-items-end">
            <div class="col-12 col-md-2">
                <label class="form-label small text-uppercase">Desde</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label small text-uppercase">Hasta</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label small text-uppercase">Cliente</label>
                <select name="customer_id" class="form-select">
                    <option value="">Todos</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" @selected(request('customer_id') == $customer->id)>{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label small text-uppercase">Vendedor</label>
                <select name="user_id" class="form-select">
                    <option value="">Todos</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label small text-uppercase">Categoría</label>
                <select name="category_id" class="form-select">
                    <option value="">Todos</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-1">
                <button class="btn btn-primary text-white w-100" type="submit"><i class="bi bi-funnel me-1"></i>Filtrar</button>
            </div>
            <div class="col-6 col-md-1">
                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary w-100" aria-label="Limpiar filtros"><i class="bi bi-x-lg"></i></a>
            </div>
            <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                <a href="{{ route('reports.export', request()->query()) }}" class="btn btn-success">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i>Exportar Excel
                </a>
                <button type="button" class="btn btn-danger" onclick="window.print()">
                    <i class="bi bi-file-earmark-pdf me-1"></i>Exportar PDF
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm border-5 border-top border-primary h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div><small class="text-uppercase text-muted">N.º de ventas</small><div class="fs-5 fw-bold">{{ $salesCount }}</div></div>
                <i class="bi bi-receipt fs-4 text-primary"></i>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm border-5 border-top border-info h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div><small class="text-uppercase text-muted">Total vendido</small><div class="fs-5 fw-bold">S/ {{ number_format($incomeTotal, 2) }}</div></div>
                <i class="bi bi-cash-stack fs-4 text-info"></i>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm border-5 border-top border-success h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div><small class="text-uppercase text-muted">IGV</small><div class="fs-5 fw-bold">S/ {{ number_format($igvTotal, 2) }}</div></div>
                <i class="bi bi-percent fs-4 text-success"></i>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm border-5 border-top border-danger h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div><small class="text-uppercase text-muted">Venta promedio</small><div class="fs-5 fw-bold">S/ {{ number_format($averageTicket, 2) }}</div></div>
                <i class="bi bi-bar-chart-line fs-4 text-danger"></i>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm border-5 border-top border-secondary mb-3">
    <div class="card-header bg-white"><h6 class="mb-0 text-uppercase small text-muted">Total vendido por día (S/)</h6></div>
    <div class="card-body">
        <div style="height: 300px">
            <canvas id="dailySalesChart"
                    data-labels="{{ $dailySalesLabels->toJson() }}"
                    data-values="{{ $dailySales->pluck('total_sales')->toJson() }}"></canvas>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm border-5 border-top border-secondary">
    <div class="card-body table-responsive">
        <table class="table table-sm table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>N.º</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Vendedor</th>
                    <th class="text-end">IGV</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sales as $sale)
                    <tr>
                        <td><a href="{{ route('sales.show', $sale) }}" class="text-danger">{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</a></td>
                        <td>{{ $sale->date->format('d/m/Y H:i') }}</td>
                        <td>{{ $sale->customer->name }}</td>
                        <td>{{ $sale->user->name }}</td>
                        <td class="text-end">S/ {{ number_format($sale->igv, 2) }}</td>
                        <td class="text-end">S/ {{ number_format($sale->total, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-3">No hay ventas en el periodo</td></tr>
                @endforelse
            </tbody>
            @if ($sales->isNotEmpty())
                <tfoot>
                    <tr class="fw-bold">
                        <td colspan="4" class="text-end">Totales</td>
                        <td class="text-end">S/ {{ number_format($igvTotal, 2) }}</td>
                        <td class="text-end">S/ {{ number_format($incomeTotal, 2) }}</td>
                    </tr>
                </tfoot>
            @endif
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

@push('scripts')
<script src="{{ asset('js/chart.umd.min.js') }}"></script>
<script>
    const dailySalesCanvas = document.getElementById('dailySalesChart');
    const dailySalesLabels = JSON.parse(dailySalesCanvas.dataset.labels);
    const dailySalesValues = JSON.parse(dailySalesCanvas.dataset.values);

    new Chart(dailySalesCanvas, {
        type: 'bar',
        data: {
            labels: dailySalesLabels,
            datasets: [{
                label: 'Ventas (S/)',
                data: dailySalesValues,
                backgroundColor: '#dc526b',
                borderRadius: 4,
                maxBarThickness: 36
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => `S/ ${Number(value).toFixed(2)}`
                    }
                }
            }
        }
    });
</script>
@endpush
