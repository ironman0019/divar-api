<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Advertisement\Advertisement;
use App\Models\Payment;
use App\Models\Category\Category;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    /**
     * Display statistics dashboard
     */
    public function index(Request $request)
    {
        // Date range filter (default: last 30 days)
        $days = $request->get('days', 30);
        $startDate = Carbon::now()->subDays($days);
        $endDate = Carbon::now();

        // User Statistics
        $userStats = $this->getUserStatistics($startDate, $endDate);

        // Advertisement Statistics
        $advertisementStats = $this->getAdvertisementStatistics($startDate, $endDate);

        // Payment Statistics
        $paymentStats = $this->getPaymentStatistics($startDate, $endDate);

        // Category Statistics
        $categoryStats = $this->getCategoryStatistics();

        // City Statistics
        $cityStats = $this->getCityStatistics();

        // Daily Statistics for Charts
        $dailyStats = $this->getDailyStatistics($startDate, $endDate);

        // Recent Activity
        $recentActivity = $this->getRecentActivity();

        return view('admin.statistics.index', compact(
            'userStats',
            'advertisementStats',
            'paymentStats',
            'categoryStats',
            'cityStats',
            'dailyStats',
            'recentActivity',
            'days',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Get user statistics
     */
    private function getUserStatistics($startDate, $endDate)
    {
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $adminUsers = User::where('is_admin', true)->count();
        $newUsers = User::whereBetween('created_at', [$startDate, $endDate])->count();
        
        // Users registered this week
        $newUsersThisWeek = User::whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->count();

        // Users registered this month
        $newUsersThisMonth = User::whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->count();

        // Users by city (top 5)
        $usersByCity = User::select('cities.name', DB::raw('count(users.id) as count'))
            ->join('cities', 'users.city_id', '=', 'cities.id')
            ->groupBy('cities.name')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        return [
            'total' => $totalUsers,
            'active' => $activeUsers,
            'admin' => $adminUsers,
            'inactive' => $totalUsers - $activeUsers,
            'new' => $newUsers,
            'new_this_week' => $newUsersThisWeek,
            'new_this_month' => $newUsersThisMonth,
            'by_city' => $usersByCity,
        ];
    }

    /**
     * Get advertisement statistics
     */
    private function getAdvertisementStatistics($startDate, $endDate)
    {
        $totalAds = Advertisement::count();
        $activeAds = Advertisement::where('status', 2)->count();
        $pendingAds = Advertisement::where('ads_status', 0)->orWhere('ads_status', 1)->count();
        $publishedAds = Advertisement::whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->count();
        $featuredAds = Advertisement::where('is_special', true)->count();
        $ladderAds = Advertisement::where('is_ladder', true)->count();
        $newAds = Advertisement::whereBetween('created_at', [$startDate, $endDate])->count();

        // Total views
        $totalViews = Advertisement::sum('view');

        // Average views per ad
        $avgViews = $totalAds > 0 ? round($totalViews / $totalAds, 2) : 0;

        // Ads by status
        $adsByStatus = Advertisement::select('ads_status', DB::raw('count(*) as count'))
            ->groupBy('ads_status')
            ->get()
            ->mapWithKeys(function ($item) {
                $statusLabels = [
                    0 => 'در انتظار تایید',
                    1 => 'در حال بررسی',
                    2 => 'تایید شده',
                    3 => 'رد شده',
                    4 => 'منقضی شده',
                ];
                return [$statusLabels[$item->ads_status] ?? 'نامشخص' => $item->count];
            });

        // Top categories by ad count
        $adsByCategory = Advertisement::select('categories.name', DB::raw('count(advertisements.id) as count'))
            ->join('categories', 'advertisements.category_id', '=', 'categories.id')
            ->groupBy('categories.name')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        return [
            'total' => $totalAds,
            'active' => $activeAds,
            'pending' => $pendingAds,
            'published' => $publishedAds,
            'featured' => $featuredAds,
            'ladder' => $ladderAds,
            'new' => $newAds,
            'total_views' => $totalViews,
            'avg_views' => $avgViews,
            'by_status' => $adsByStatus,
            'by_category' => $adsByCategory,
        ];
    }

    /**
     * Get payment statistics
     */
    private function getPaymentStatistics($startDate, $endDate)
    {
        $totalPayments = Payment::count();
        $paidPayments = Payment::where('status', Payment::STATUS_PAID)->count();
        $pendingPayments = Payment::where('status', Payment::STATUS_PENDING)->count();
        $failedPayments = Payment::where('status', Payment::STATUS_FAILED)->count();

        // Total revenue (sum of paid payments)
        $totalRevenue = Payment::where('status', Payment::STATUS_PAID)->sum('amount');

        // Revenue in date range
        $revenueInRange = Payment::where('status', Payment::STATUS_PAID)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        // Revenue this month
        $revenueThisMonth = Payment::where('status', Payment::STATUS_PAID)
            ->whereBetween('created_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])
            ->sum('amount');

        // Payments by type
        $paymentsByType = Payment::select('payment_type', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
            ->where('status', Payment::STATUS_PAID)
            ->groupBy('payment_type')
            ->get()
            ->map(function ($item) {
                return [
                    'type' => $item->payment_type === Payment::TYPE_LADDER ? 'نردبان' : 'ویژه',
                    'count' => $item->count,
                    'total' => $item->total,
                ];
            });

        return [
            'total' => $totalPayments,
            'paid' => $paidPayments,
            'pending' => $pendingPayments,
            'failed' => $failedPayments,
            'total_revenue' => $totalRevenue,
            'revenue_in_range' => $revenueInRange,
            'revenue_this_month' => $revenueThisMonth,
            'by_type' => $paymentsByType,
        ];
    }

    /**
     * Get category statistics
     */
    private function getCategoryStatistics()
    {
        $totalCategories = Category::count();
        $activeCategories = Category::where('status', 1)->count();

        // Categories with most ads
        $topCategories = Category::select('categories.name', DB::raw('count(advertisements.id) as ad_count'))
            ->leftJoin('advertisements', 'categories.id', '=', 'advertisements.category_id')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('ad_count')
            ->limit(10)
            ->get();

        return [
            'total' => $totalCategories,
            'active' => $activeCategories,
            'top' => $topCategories,
        ];
    }

    /**
     * Get city statistics
     */
    private function getCityStatistics()
    {
        $totalCities = City::count();

        // Cities with most users
        $topCitiesByUsers = City::select('cities.name', DB::raw('count(users.id) as user_count'))
            ->leftJoin('users', 'cities.id', '=', 'users.city_id')
            ->groupBy('cities.id', 'cities.name')
            ->orderByDesc('user_count')
            ->limit(10)
            ->get();

        // Cities with most ads
        $topCitiesByAds = City::select('cities.name', DB::raw('count(advertisements.id) as ad_count'))
            ->leftJoin('advertisements', 'cities.id', '=', 'advertisements.city_id')
            ->groupBy('cities.id', 'cities.name')
            ->orderByDesc('ad_count')
            ->limit(10)
            ->get();

        return [
            'total' => $totalCities,
            'top_by_users' => $topCitiesByUsers,
            'top_by_ads' => $topCitiesByAds,
        ];
    }

    /**
     * Get daily statistics for charts
     */
    private function getDailyStatistics($startDate, $endDate)
    {
        $days = [];
        $usersData = [];
        $adsData = [];
        $paymentsData = [];

        $daysDiff = $startDate->diffInDays($endDate);
        
        // Limit data points to prevent performance issues
        // Max 60 data points for better chart performance
        $maxPoints = 60;
        
        if ($daysDiff > $maxPoints) {
            // For long periods, group data by intervals
            $intervalDays = ceil($daysDiff / $maxPoints);
            
            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate && count($days) < $maxPoints) {
                $intervalEnd = min($currentDate->copy()->addDays($intervalDays - 1), $endDate);
                
                // Format label
                if ($intervalDays == 1) {
                    $days[] = $currentDate->format('m/d');
                } else {
                    $days[] = $currentDate->format('m/d') . ' - ' . $intervalEnd->format('m/d');
                }

                // Aggregate data for this interval
                $usersData[] = User::whereBetween('created_at', [$currentDate, $intervalEnd])->count();
                $adsData[] = Advertisement::whereBetween('created_at', [$currentDate, $intervalEnd])->count();
                $paymentsData[] = Payment::where('status', Payment::STATUS_PAID)
                    ->whereBetween('created_at', [$currentDate, $intervalEnd])
                    ->sum('amount');

                $currentDate->addDays($intervalDays);
            }
        } else {
            // For short periods, use daily data
            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $day = $currentDate->format('Y-m-d');
                $days[] = $currentDate->format('m/d');

                // Users registered on this day
                $usersData[] = User::whereDate('created_at', $day)->count();

                // Ads created on this day
                $adsData[] = Advertisement::whereDate('created_at', $day)->count();

                // Payments made on this day
                $paymentsData[] = Payment::where('status', Payment::STATUS_PAID)
                    ->whereDate('created_at', $day)
                    ->sum('amount');

                $currentDate->addDay();
            }
        }

        return [
            'days' => $days,
            'users' => $usersData,
            'ads' => $adsData,
            'payments' => $paymentsData,
        ];
    }

    /**
     * Get recent activity
     */
    private function getRecentActivity()
    {
        $recentUsers = User::orderBy('created_at', 'desc')->limit(5)->get();
        $recentAds = Advertisement::with('user', 'category', 'city')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        $recentPayments = Payment::with('user', 'advertisement')
            ->where('status', Payment::STATUS_PAID)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return [
            'users' => $recentUsers,
            'ads' => $recentAds,
            'payments' => $recentPayments,
        ];
    }
}
