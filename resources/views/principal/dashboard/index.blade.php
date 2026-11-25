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

<div class="row g-3 mb-3">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #FF1744!important">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-uppercase small text-muted">Ventas hoy</div>
                        <div class="fs-3 fw-bold mt-1">S/ {{ number_format($salesToday, 2) }}</div>
                    </div>
                    <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width:48px;height:48px">
                        <i class="bi bi-currency-dollar fs-4"></i>
                    </div>
                </div>
                <small class="text-muted">{{ $salesTodayCount }} venta(s)</small>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #3B82F6!important">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-uppercase small text-muted">Ingresos del mes</div>
                        <div class="fs-3 fw-bold mt-1">S/ {{ number_format($salesMonth, 2) }}</div>
                    </div>
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width:48px;height:48px">
                        <i class="bi bi-bar-chart-line fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #20C997!important">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-uppercase small text-muted">Clientes</div>
                        <div class="fs-3 fw-bold mt-1">{{ $customersCount }}</div>
                    </div>
                    <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width:48px;height:48px">
                        <i class="bi bi-people fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #F59E0B!important">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-uppercase small text-muted">Stock crítico</div>
                        <div class="fs-3 fw-bold mt-1">{{ $lowStockCount }}</div>
                    </div>
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width:48px;height:48px">
                        <i class="bi bi-box-seam fs-4"></i>
                    </div>
                </div>
                <small class="text-muted">de {{ $medicinesCount }} medicamentos</small>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0">Ventas de los últimos 7 días</h6>
    </div>
    <div class="card-body">
        <canvas id="salesChart" height="150"></canvas>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">Ventas del mes por categoría</h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="categoryChart" height="230"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">Top 5 medicamentos más vendidos</h6>
            </div>
            <div class="card-body">
                <canvas id="topMedicinesChart" height="230"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/chart.umd.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const salesLabels = @json($salesChartLabels);
    const salesData = @json($salesChartData);
    const categoryLabels = @json($categorySalesLabels);
    const categoryData = @json($categorySalesData);
    const topLabels = @json($topMedicines->pluck('name'));
    const topData = @json($topMedicines->pluck('total_amount'));

    new Chart(document.getElementById('salesChart'), {
        type: 'line',
        data: { labels: salesLabels, datasets: [{ label: 'Ventas', data: salesData, borderColor: '#FF1744', backgroundColor: 'rgba(255,23,68,.12)', fill: true, tension: .35, pointRadius: 3, pointBackgroundColor: '#FF1744' }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.06)' } }, x: { grid: { display: false } } } }
    });

    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: { labels: categoryLabels.length ? categoryLabels : ['Sin ventas'], datasets: [{ data: categoryData.length ? categoryData : [1], backgroundColor: ['#FF1744','#1F2937','#3B82F6','#20C997','#F59E0B','#8B5CF6'], borderWidth: 3, borderColor: '#fff' }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '55%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true } } } }
    });

    new Chart(document.getElementById('topMedicinesChart'), {
        type: 'bar',
        data: { labels: topLabels.length ? topLabels : ['Sin datos'], datasets: [{ label: 'Unidades', data: topData.length ? topData : [0], backgroundColor: ['#FF1744','#4B5563','#9CA3AF','#FF6B86','#64748B'], borderRadius: 4 }] },
        options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: 'rgba(0,0,0,.06)' } }, y: { grid: { display: false } } } }
    });
});
</script>
@endpush