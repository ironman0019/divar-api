<?php

namespace App\Http\Controllers\Admin\Advertisement;

use App\Http\Controllers\Controller;
use App\Models\Advertisement\Advertisement;
use Illuminate\Http\Request;

class AdvertisementStatusController extends Controller
{

    /**
     * Display pending advertisements
     */
    public function pending(Request $request)
    {
        $query = Advertisement::with(['user', 'category', 'city'])
            ->where('status', 3)
            ->withCount('galleries');

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        switch ($sortBy) {
            case 'title':
                $query->orderBy('title', $sortDirection);
                break;
            case 'price':
                $query->orderBy('price', $sortDirection);
                break;
            default:
                $query->orderBy('created_at', $sortDirection);
        }

        $advertisements = $query->paginate(15)->withQueryString();

        return view('admin.advertisements.pending', compact('advertisements'));
    }

    /**
     * Approve the advertisement
     */
    public function approve(Advertisement $advertisement)
    {
        if ($advertisement->status !== 3) {
            return redirect()->back()
                ->with('error', 'فقط آگهی‌های در انتظار قابل تایید هستند.');
        }

        $advertisement->update([
            'status' => 2,
            'published_at' => now()
        ]);

        return redirect()->back()
            ->with('success', 'آگهی با موفقیت تایید شد.');
    }

    /**
     * Reject the advertisement
     */
    public function reject(Advertisement $advertisement)
    {
        if ($advertisement->status !== 3) {
            return redirect()->back()
                ->with('error', 'فقط آگهی‌های در انتظار قابل رد هستند.');
        }

        $advertisement->update([
            'status' => 0
        ]);

        return redirect()->back()
            ->with('success', 'آگهی رد شد.');
    }

    /**
     * Toggle advertisement status between active and disabled
     */
    public function toggleStatus(Advertisement $advertisement)
    {
        if ($advertisement->status == 2) {
            $advertisement->update(['status' => 0]);
            $message = 'آگهی غیرفعال شد.';
        } elseif ($advertisement->status == 0) {
            $advertisement->update(['status' => 2]);
            $message = 'آگهی فعال شد.';
        } else {
            return redirect()->back()
                ->with('error', 'فقط آگهی‌های فعال یا غیرفعال قابل تغییر وضعیت هستند.');
        }

        return redirect()->back()
            ->with('success', $message);
    }

    /**
     * Set advertisement as expired
     */
    public function setExpired(Advertisement $advertisement)
    {
        $advertisement->update([
            'status' => 4,
            'expired_at' => now()
        ]);

        return redirect()->back()
            ->with('success', 'آگهی منقضی شد.');
    }

    /**
     * Bulk approve advertisements
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'advertisement_ids' => 'required|array',
            'advertisement_ids.*' => 'exists:advertisements,id'
        ]);

        $count = Advertisement::whereIn('id', $request->advertisement_ids)
            ->where('status', 3)
            ->update([
                'status' => 2,
                'published_at' => now()
            ]);

        return redirect()->back()
            ->with('success', "تعداد {$count} آگهی تایید شد.");
    }

    /**
     * Bulk reject advertisements
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'advertisement_ids' => 'required|array',
            'advertisement_ids.*' => 'exists:advertisements,id'
        ]);

        $count = Advertisement::whereIn('id', $request->advertisement_ids)
            ->where('status', 3)
            ->update(['status' => 0]);

        return redirect()->back()
            ->with('success', "تعداد {$count} آگهی رد شد.");
    }
}
