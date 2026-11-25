<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Detail;
use App\Models\Medicine;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $todaySales = Sale::whereDate('date', today())->get();
        $salesToday = $todaySales->sum('total');
        $salesTodayCount = $todaySales->count();

        $salesMonth = Sale::whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->sum('total');

        $customersCount = Customer::count();
        $medicinesCount = Medicine::count();
        $lowStockCount = Medicine::where('amount', '<=', 10)->count();

        $lowStockMedicines = Medicine::with('category')
            ->where('amount', '<=', 10)
            ->orderBy('amount')
            ->take(5)
            ->get();

        $recentSales = Sale::with(['customer', 'user'])
            ->latest('date')
            ->take(5)
            ->get();

        $salesChartLabels = [];
        $salesChartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $salesChartLabels[] = now()->subDays($i)->format('d/m');
            $salesChartData[] = (float) Sale::whereDate('date', $date)->sum('total');
        }

        $topMedicines = Detail::join('medicines', 'details.medicines_id', '=', 'medicines.id')
            ->select('medicines.name', DB::raw('SUM(details.amount) as total_amount'))
            ->groupBy('medicines.id', 'medicines.name')
            ->orderByDesc('total_amount')
            ->take(5)
            ->get();

        $categorySales = Detail::join('sales', 'details.sales_id', '=', 'sales.id')
            ->join('medicines', 'details.medicines_id', '=', 'medicines.id')
            ->join('categories', 'medicines.category_id', '=', 'categories.id')
            ->whereYear('sales.date', now()->year)
            ->whereMonth('sales.date', now()->month)
            ->select('categories.name', DB::raw('SUM(details.subtotal) as total_sales'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_sales')
            ->get();

        $categorySalesLabels = $categorySales->pluck('name');
        $categorySalesData = $categorySales->pluck('total_sales');

        return view('principal.dashboard.index', compact(
            'salesToday',
            'salesTodayCount',
            'salesMonth',
            'customersCount',
            'medicinesCount',
            'lowStockCount',
            'lowStockMedicines',
            'recentSales',
            'salesChartLabels',
            'salesChartData',
            'topMedicines',
            'categorySalesLabels',
            'categorySalesData'
        ));
    }
}