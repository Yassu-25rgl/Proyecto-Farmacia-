<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Medicine;
use App\Models\Sale;

class DashboardController extends Controller
{
    public function index()
    {
        $salesToday = Sale::whereDate('date', today())->sum('total');
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

        return view('principal.dashboard.index', compact(
            'salesToday',
            'salesMonth',
            'customersCount',
            'medicinesCount',
            'lowStockCount',
            'lowStockMedicines',
            'recentSales'
        ));
    }
}