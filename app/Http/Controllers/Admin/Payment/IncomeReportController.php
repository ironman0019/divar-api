<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class IncomeReportController extends Controller
{
    /**
     * Display income report
     */
    public function index(Request $request)
    {
        // Date range filter (default: last 30 days)
        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date) 
            : Carbon::now()->subDays(30);
        
        $endDate = $request->filled('end_date') 
            ? Carbon::parse($request->end_date)->endOfDay() 
            : Carbon::now()->endOfDay();

        // Filter by payment type
        $paymentType = $request->get('payment_type');

        // Overall statistics
        $totalRevenue = Payment::where('status', Payment::STATUS_PAID)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($paymentType, function ($query) use ($paymentType) {
                return $query->where('payment_type', $paymentType);
            })
            ->sum('amount');

        $totalPayments = Payment::where('status', Payment::STATUS_PAID)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($paymentType, function ($query) use ($paymentType) {
                return $query->where('payment_type', $paymentType);
            })
            ->count();

        // Revenue by payment type
        $revenueByType = Payment::select('payment_type', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->where('status', Payment::STATUS_PAID)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($paymentType, function ($query) use ($paymentType) {
                return $query->where('payment_type', $paymentType);
            })
            ->groupBy('payment_type')
            ->get()
            ->map(function ($item) {
                return [
                    'type' => $item->payment_type === Payment::TYPE_LADDER ? 'نردبان' : 'ویژه',
                    'type_value' => $item->payment_type,
                    'total' => $item->total,
                    'count' => $item->count,
                ];
            });

        // Daily revenue for chart
        $dailyRevenue = $this->getDailyRevenue($startDate, $endDate, $paymentType);

        // Monthly revenue
        $monthlyRevenue = Payment::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->where('status', Payment::STATUS_PAID)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($paymentType, function ($query) use ($paymentType) {
                return $query->where('payment_type', $paymentType);
            })
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->orderBy(DB::raw('YEAR(created_at)'), 'asc')
            ->orderBy(DB::raw('MONTH(created_at)'), 'asc')
            ->get();

        // Top earning advertisements
        $topAds = Payment::select('advertisements.id', 'advertisements.title', DB::raw('SUM(payments.amount) as total'), DB::raw('COUNT(payments.id) as payment_count'))
            ->join('advertisements', 'payments.advertisement_id', '=', 'advertisements.id')
            ->where('payments.status', Payment::STATUS_PAID)
            ->whereBetween('payments.created_at', [$startDate, $endDate])
            ->when($paymentType, function ($query) use ($paymentType) {
                return $query->where('payments.payment_type', $paymentType);
            })
            ->groupBy('advertisements.id', 'advertisements.title')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Revenue comparison (previous period)
        $previousStartDate = $startDate->copy()->subDays($startDate->diffInDays($endDate));
        $previousEndDate = $startDate->copy()->subSecond();
        
        $previousRevenue = Payment::where('status', Payment::STATUS_PAID)
            ->whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->when($paymentType, function ($query) use ($paymentType) {
                return $query->where('payment_type', $paymentType);
            })
            ->sum('amount');

        $growthRate = $previousRevenue > 0 
            ? (($totalRevenue - $previousRevenue) / $previousRevenue) * 100 
            : ($totalRevenue > 0 ? 100 : 0);

        return view('admin.payment.income-report', compact(
            'totalRevenue',
            'totalPayments',
            'revenueByType',
            'dailyRevenue',
            'monthlyRevenue',
            'topAds',
            'previousRevenue',
            'growthRate',
            'startDate',
            'endDate',
            'paymentType'
        ));
    }

    /**
     * Get daily revenue data
     */
    private function getDailyRevenue($startDate, $endDate, $paymentType = null)
    {
        $days = [];
        $revenue = [];
        $count = [];

        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $dayRevenue = Payment::where('status', Payment::STATUS_PAID)
                ->whereDate('created_at', $currentDate->format('Y-m-d'))
                ->when($paymentType, function ($query) use ($paymentType) {
                    return $query->where('payment_type', $paymentType);
                })
                ->sum('amount');

            $dayCount = Payment::where('status', Payment::STATUS_PAID)
                ->whereDate('created_at', $currentDate->format('Y-m-d'))
                ->when($paymentType, function ($query) use ($paymentType) {
                    return $query->where('payment_type', $paymentType);
                })
                ->count();

            $days[] = $currentDate->format('Y-m-d');
            $revenue[] = $dayRevenue;
            $count[] = $dayCount;

            $currentDate->addDay();
        }

        return [
            'days' => $days,
            'revenue' => $revenue,
            'count' => $count,
        ];
    }
}

