<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Category;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    // Muestra ventas y totales filtrados por fechas, vendedor y categoría.
    public function index(Request $request)
    {
        $salesQuery = $this->applyFilters(
            Sale::query()->with(['customer', 'user']),
            $request
        );

        $sales = (clone $salesQuery)->latest('date')->get();
        $salesCount = $sales->count();
        $incomeTotal = $sales->sum('total');
        $igvTotal = $sales->sum('igv');
        $averageTicket = $salesCount > 0 ? $incomeTotal / $salesCount : 0;

        $dailySales = $this->applyFilters(Sale::query(), $request)
            ->selectRaw('DATE(date) as sale_day, SUM(total) as total_sales')
            ->groupByRaw('DATE(sales.date)')
            ->orderBy('sale_day')
            ->get();
        $dailySalesLabels = $dailySales->pluck('sale_day')
            ->map(fn ($day) => Carbon::parse($day)->format('d/m'));

        return view('principal.reports.index', [
            'sales' => $sales,
            'salesCount' => $salesCount,
            'incomeTotal' => $incomeTotal,
            'igvTotal' => $igvTotal,
            'averageTicket' => $averageTicket,
            'dailySales' => $dailySales,
            'dailySalesLabels' => $dailySalesLabels,
            'customers' => Customer::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'users' => User::orderBy('name')->get(),
        ]);
    }

    // Descarga el listado filtrado como CSV compatible con Excel.
    public function export(Request $request)
    {
        $sales = $this->applyFilters(Sale::with(['customer', 'user']), $request)
            ->latest('date')
            ->get();

        $lines = [["Venta", "Fecha", "Cliente", "Vendedor", "Total"]];
        foreach ($sales as $sale) {
            $lines[] = [
                str_pad((string) $sale->id, 6, '0', STR_PAD_LEFT),
                $sale->date->format('d/m/Y H:i'),
                $sale->customer->name,
                $sale->user->name,
                number_format((float) $sale->total, 2, '.', ''),
            ];
        }

        $csv = fopen('php://temp', 'r+');
        foreach ($lines as $line) {
            fputcsv($csv, $line);
        }
        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);

        return Response::make("\xEF\xBB\xBF" . $content, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="reporte-ventas.csv"',
        ]);
    }

    private function applyFilters(Builder $query, Request $request): Builder
    {
        return $query
            ->when($request->filled('start_date'), fn ($builder) => $builder->whereDate('date', '>=', $request->input('start_date')))
            ->when($request->filled('end_date'), fn ($builder) => $builder->whereDate('date', '<=', $request->input('end_date')))
            ->when($request->filled('customer_id'), fn ($builder) => $builder->where('customer_id', $request->input('customer_id')))
            ->when($request->filled('user_id'), fn ($builder) => $builder->where('users_id', $request->input('user_id')))
            ->when($request->filled('category_id'), fn ($builder) => $builder->whereHas(
                'details.medicine',
                fn ($medicineQuery) => $medicineQuery->where('category_id', $request->input('category_id'))
            ));
    }
}
