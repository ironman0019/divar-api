<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Support\JalaliDate;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of transactions
     */
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'advertisement']);

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('ref_id', 'like', '%' . $request->search . '%')
                  ->orWhere('authority', 'like', '%' . $request->search . '%')
                  ->orWhere('trace_no', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('name', 'like', '%' . $request->search . '%')
                                ->orWhere('mobile', 'like', '%' . $request->search . '%');
                  })
                  ->orWhereHas('advertisement', function($adQuery) use ($request) {
                      $adQuery->where('title', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment type
        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $startDate = JalaliDate::toGregorian($request->start_date);
            if ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            }
        }

        if ($request->filled('end_date')) {
            $endDate = JalaliDate::toGregorian($request->end_date);
            if ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            }
        }

        // Filter by amount range
        if ($request->filled('min_amount')) {
            $query->where('amount', '>=', $request->min_amount);
        }

        if ($request->filled('max_amount')) {
            $query->where('amount', '<=', $request->max_amount);
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $query->orderBy($sortBy, $sortDirection);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $payments = $query->paginate($perPage)->withQueryString();

        // Statistics
        $stats = [
            'total' => Payment::count(),
            'paid' => Payment::where('status', Payment::STATUS_PAID)->count(),
            'pending' => Payment::where('status', Payment::STATUS_PENDING)->count(),
            'failed' => Payment::where('status', Payment::STATUS_FAILED)->count(),
            'total_revenue' => Payment::where('status', Payment::STATUS_PAID)->sum('amount'),
        ];

        return view('admin.payment.transactions', compact('payments', 'stats'));
    }

    /**
     * Display a specific transaction
     */
    public function show(Payment $payment)
    {
        $payment->load(['user', 'advertisement']);
        
        return view('admin.payment.transaction-show', compact('payment'));
    }
}

