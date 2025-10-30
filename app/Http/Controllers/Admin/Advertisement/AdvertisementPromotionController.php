<?php

namespace App\Http\Controllers\Admin\Advertisement;

use App\Http\Controllers\Controller;
use App\Models\Advertisement\Advertisement;
use App\Models\Advertisement\FeaturedAdvertisement;
use Illuminate\Http\Request;

class AdvertisementPromotionController extends Controller
{

    /**
     * Display featured advertisements
     */
    public function featured(Request $request)
    {
        $query = FeaturedAdvertisement::with(['advertisement.user', 'advertisement.category', 'advertisement.city'])
            ->where('is_active', true)
            ->where('expires_at', '>', now());

        // Filter by type
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        switch ($sortBy) {
            case 'expires_at':
                $query->orderBy('expires_at', $sortDirection);
                break;
            case 'type':
                $query->orderBy('type', $sortDirection);
                break;
            default:
                $query->orderBy('created_at', $sortDirection);
        }

        $featuredAdvertisements = $query->paginate(15)->withQueryString();

        return view('admin.advertisements.featured', compact('featuredAdvertisements'));
    }

    /**
     * Show promotion form
     */
    public function promoteForm(Advertisement $advertisement)
    {
        // Check if already featured
        $currentFeatured = $advertisement->featuredAdvertisements()
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->first();

        return view('admin.advertisements.promote-form', compact('advertisement', 'currentFeatured'));
    }

    /**
     * Promote advertisement manually
     */
    public function promote(Request $request, Advertisement $advertisement)
    {
        $request->validate([
            'type' => 'required|in:ladder,special',
            'duration_days' => 'required|integer|min:1|max:365',
        ]);

        // Check if already has active promotion of same type
        $existingFeatured = $advertisement->featuredAdvertisements()
            ->where('type', $request->type)
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->first();

        if ($existingFeatured) {
            return redirect()->back()
                ->with('error', 'این آگهی قبلاً از این نوع تبلیغ فعال دارد.');
        }

        // Create featured advertisement record
        $featuredAdvertisement = FeaturedAdvertisement::create([
            'advertisement_id' => $advertisement->id,
            'payment_id' => null, // Admin promotion, no payment required
            'type' => $request->type,
            'expires_at' => now()->addDays((int) $request->duration_days),
            'is_active' => true,
        ]);

        // Update advertisement flags
        if ($request->type === 'ladder') {
            $advertisement->update(['is_ladder' => true]);
        } else {
            $advertisement->update(['is_special' => true]);
        }

        return redirect()->route('admin.advertisements.featured')
            ->with('success', 'آگهی با موفقیت تبلیغ شد.');
    }

    /**
     * Remove featured status
     */
    public function removeFeatured(FeaturedAdvertisement $featured)
    {
        // Update advertisement flags
        $advertisement = $featured->advertisement;
        if ($featured->type === 'ladder') {
            $advertisement->update(['is_ladder' => false]);
        } else {
            $advertisement->update(['is_special' => false]);
        }

        // Soft delete featured advertisement
        $featured->delete();

        return redirect()->back()
            ->with('success', 'وضعیت تبلیغ حذف شد.');
    }

    /**
     * Toggle featured advertisement status
     */
    public function toggleFeaturedStatus(FeaturedAdvertisement $featured)
    {
        $featured->update([
            'is_active' => !$featured->is_active
        ]);

        $status = $featured->is_active ? 'فعال' : 'غیرفعال';
        return redirect()->back()
            ->with('success', "وضعیت تبلیغ {$status} شد.");
    }

    /**
     * Extend featured advertisement
     */
    public function extend(Request $request, FeaturedAdvertisement $featured)
    {
        $request->validate([
            'additional_days' => 'required|integer|min:1|max:365',
        ]);

        $featured->update([
            'expires_at' => $featured->expires_at->addDays((int) $request->additional_days)
        ]);

        return redirect()->back()
            ->with('success', 'مدت تبلیغ تمدید شد.');
    }
}
