<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Detail;
use App\Models\Medicine;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    // Muestra ventas y totales filtrados por fechas, cliente y vendedor.
    public function index(Request $request)
    {
        $salesQuery = Sale::query()
            ->with(['customer', 'user', 'details.medicine'])
            ->when($request->filled('start_date'), fn ($query) => $query->whereDate('date', '>=', $request->start_date))
            ->when($request->filled('end_date'), fn ($query) => $query->whereDate('date', '<=', $request->end_date))
            ->when($request->filled('customer_id'), fn ($query) => $query->where('customer_id', $request->customer_id))
            ->when($request->filled('user_id'), fn ($query) => $query->where('users_id', $request->user_id));

        $sales = (clone $salesQuery)->latest('date')->get();
        $salesCount = $sales->count();
        $incomeTotal = $sales->sum('total');

        $detailQuery = Detail::query()->whereHas('sale', function ($query) use ($request) {
            $query
                ->when($request->filled('start_date'), fn ($q) => $q->whereDate('date', '>=', $request->start_date))
                ->when($request->filled('end_date'), fn ($q) => $q->whereDate('date', '<=', $request->end_date))
                ->when($request->filled('customer_id'), fn ($q) => $q->where('customer_id', $request->customer_id))
                ->when($request->filled('user_id'), fn ($q) => $q->where('users_id', $request->user_id));
        });

        $productsSold = (clone $detailQuery)->sum('amount');
        $topMedicines = (clone $detailQuery)
            ->join('medicines', 'details.medicines_id', '=', 'medicines.id')
            ->selectRaw('medicines.name, SUM(details.amount) as total_amount, SUM(details.subtotal) as total_income')
            ->groupBy('medicines.id', 'medicines.name')
            ->orderByDesc('total_amount')
            ->get();

        $inventory = Medicine::with('category')->orderBy('amount')->get();

        return view('principal.reports.index', [
            'sales' => $sales,
            'salesCount' => $salesCount,
            'productsSold' => $productsSold,
            'incomeTotal' => $incomeTotal,
            'topMedicines' => $topMedicines,
            'inventory' => $inventory,
            'customers' => Customer::orderBy('name')->get(),
            'users' => User::orderBy('name')->get(),
        ]);
    }

    // Descarga el listado filtrado como CSV compatible con Excel.
    public function export(Request $request)
    {
        $sales = Sale::with(['customer', 'user'])
            ->when($request->filled('start_date'), fn ($query) => $query->whereDate('date', '>=', $request->start_date))
            ->when($request->filled('end_date'), fn ($query) => $query->whereDate('date', '<=', $request->end_date))
            ->when($request->filled('customer_id'), fn ($query) => $query->where('customer_id', $request->customer_id))
            ->when($request->filled('user_id'), fn ($query) => $query->where('users_id', $request->user_id))
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
}
