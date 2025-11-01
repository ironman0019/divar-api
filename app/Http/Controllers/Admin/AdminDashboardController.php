<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Advertisement\Advertisement;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // User Statistics
        $totalUsers = User::count();
        $newUsersThisMonth = User::whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->count();
        
        $newUsersLastMonth = User::whereBetween('created_at', [
            Carbon::now()->subMonth()->startOfMonth(),
            Carbon::now()->subMonth()->endOfMonth()
        ])->count();
        
        $userGrowthRate = $newUsersLastMonth > 0 
            ? (($newUsersThisMonth - $newUsersLastMonth) / $newUsersLastMonth) * 100 
            : ($newUsersThisMonth > 0 ? 100 : 0);

        // Revenue Statistics
        $todayRevenue = Payment::where('status', Payment::STATUS_PAID)
            ->whereDate('created_at', Carbon::today())
            ->sum('amount');
        
        $yesterdayRevenue = Payment::where('status', Payment::STATUS_PAID)
            ->whereDate('created_at', Carbon::yesterday())
            ->sum('amount');
        
        $revenueGrowthRate = $yesterdayRevenue > 0 
            ? (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100 
            : ($todayRevenue > 0 ? 100 : 0);

        // Monthly Revenue Chart Data (last 6 months)
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            
            $revenue = Payment::where('status', Payment::STATUS_PAID)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('amount');
            
            $monthlyRevenue[] = [
                'month' => $monthStart->toIso8601String(),
                'revenue' => $revenue ?? 0,
            ];
        }

        // Recent Activity
        $recentUsers = User::orderBy('created_at', 'desc')->limit(5)->get();
        $recentAds = Advertisement::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        $recentPayments = Payment::with('user', 'advertisement')
            ->where('status', Payment::STATUS_PAID)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Latest Advertisements with payments
        $latestAds = Advertisement::with(['user', 'payments' => function($query) {
            $query->where('status', Payment::STATUS_PAID)
                  ->orderBy('created_at', 'desc')
                  ->limit(1);
        }])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Most Viewed Advertisements
        $topViewedAds = Advertisement::with('user')
            ->orderBy('view', 'desc')
            ->limit(5)
            ->get();

        // Monthly Financial Report (last 6 months)
        $monthlyFinancial = [];
        $previousMonthRevenue = null;
        
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            
            $totalSales = Payment::where('status', Payment::STATUS_PAID)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('amount');
            
            $orderCount = Payment::where('status', Payment::STATUS_PAID)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();
            
            // Assuming 30% costs (you can adjust this)
            $costs = 0; //$totalSales * 0.30;
            $netProfit = $totalSales - $costs;
            
            // Calculate growth
            $growth = null;
            if ($previousMonthRevenue !== null && $previousMonthRevenue > 0) {
                $growth = (($totalSales - $previousMonthRevenue) / $previousMonthRevenue) * 100;
            }
            
            $monthlyFinancial[] = [
                'month' => $monthStart->toIso8601String(),
                'month_date' => $monthStart,
                'total_sales' => $totalSales ?? 0,
                'costs' => $costs ?? 0,
                'net_profit' => $netProfit ?? 0,
                'order_count' => $orderCount,
                'growth' => $growth,
            ];
            
            $previousMonthRevenue = $totalSales ?? 0;
        }

        return view('admin.index', compact(
            'totalUsers',
            'userGrowthRate',
            'todayRevenue',
            'revenueGrowthRate',
            'monthlyRevenue',
            'recentUsers',
            'recentAds',
            'recentPayments',
            'latestAds',
            'topViewedAds',
            'monthlyFinancial'
        ));
    }
}
