<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DetailSale;
use App\Models\Medicine;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $startOfToday = now()->startOfDay();
        $endOfToday = now()->endOfDay();

        $activeSales = Sale::where('status', 'active');

        $metrics = [
            'sales_total' => (clone $activeSales)->count(),
            'sales_today' => (clone $activeSales)->whereBetween('date', [$startOfToday, $endOfToday])->count(),
            'customers' => Customer::count(),
            'medicines' => Medicine::count(),
            'users' => User::where('active', true)->count(),
            'income' => (clone $activeSales)->sum('total'),
            'income_today' => (clone $activeSales)->whereBetween('date', [$startOfToday, $endOfToday])->sum('total'),
        ];

        $lowStock = Medicine::with('category')
            ->where('stock', '<=', 10)
            ->orderBy('stock')
            ->limit(10)
            ->get();

        $expirySoon = Medicine::with('category')
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [now()->toDateString(), now()->addDays(30)->toDateString()])
            ->orderBy('expiry_date')
            ->limit(10)
            ->get();

        $latestSales = Sale::with(['customer', 'user'])->latest('date')->limit(8)->get();

        $monthlySales = Sale::query()
            ->where('status', 'active')
            ->selectRaw("DATE_FORMAT(date, '%Y-%m') as period, COUNT(*) as orders, SUM(total) as income")
            ->where('date', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        $dailySales = collect(range(6, 0))->map(function (int $daysAgo) {
            $day = now()->subDays($daysAgo)->startOfDay();
            $orders = Sale::where('status', 'active')->whereBetween('date', [$day, (clone $day)->endOfDay()])->count();
            $income = Sale::where('status', 'active')->whereBetween('date', [$day, (clone $day)->endOfDay()])->sum('total');

            return [
                'label' => $day->format('d/m'),
                'orders' => $orders,
                'income' => (float) $income,
            ];
        });

        $topMedicines = DetailSale::query()
            ->join('sales', 'sales.id', '=', 'detail_sales.sale_id')
            ->join('medicines', 'medicines.id', '=', 'detail_sales.medicine_id')
            ->where('sales.status', 'active')
            ->select('medicines.name', DB::raw('SUM(detail_sales.amount) as sold'))
            ->groupBy('medicines.id', 'medicines.name')
            ->orderByDesc('sold')
            ->limit(8)
            ->get();

        $salesByCategory = DetailSale::query()
            ->join('sales', 'sales.id', '=', 'detail_sales.sale_id')
            ->join('medicines', 'medicines.id', '=', 'detail_sales.medicine_id')
            ->join('categories', 'categories.id', '=', 'medicines.category_id')
            ->where('sales.status', 'active')
            ->select('categories.name', DB::raw('SUM(detail_sales.subtotal) as income'), DB::raw('COUNT(DISTINCT detail_sales.medicine_id) as medicines'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('income')
            ->get();

        $medicineCountByCategory = Medicine::query()
            ->join('categories', 'categories.id', '=', 'medicines.category_id')
            ->select('categories.name', DB::raw('COUNT(medicines.id) as total'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total')
            ->get();

        $chartData = [
            'months' => $monthlySales->pluck('period'),
            'orders' => $monthlySales->pluck('orders'),
            'income' => $monthlySales->pluck('income'),
            'sevenLabels' => $dailySales->pluck('label'),
            'sevenOrders' => $dailySales->pluck('orders'),
            'sevenIncome' => $dailySales->pluck('income'),
            'topNames' => $topMedicines->pluck('name'),
            'topSold' => $topMedicines->pluck('sold'),
            'categoryNames' => $salesByCategory->pluck('name'),
            'categoryIncome' => $salesByCategory->pluck('income'),
            'medicineCategoryNames' => $medicineCountByCategory->pluck('name'),
            'medicineCategoryTotals' => $medicineCountByCategory->pluck('total'),
        ];

        return view('principal.dashboard.index', compact(
            'metrics',
            'lowStock',
            'expirySoon',
            'latestSales',
            'chartData'
        ));
    }
}
