<?php

namespace App\Http\Controllers\Admin\Advertisement;

use App\Http\Controllers\Controller;
use App\Models\Advertisement\Advertisement;
use App\Models\Category\Category;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdvertisementController extends Controller
{

    /**
     * Display a listing of advertisements
     */
    public function index(Request $request)
    {
        $query = Advertisement::with(['user', 'category', 'city', 'galleries'])
            ->withCount('galleries');

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->byCategory($request->category_id);
        }

        // Filter by city
        if ($request->filled('city_id')) {
            $query->byCity($request->city_id);
        }

        // Filter by ads type
        if ($request->filled('ads_type')) {
            $query->byAdsType($request->ads_type);
        }

        // Filter by ads status
        if ($request->filled('ads_status')) {
            $query->byAdsStatus($request->ads_status);
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        switch ($sortBy) {
            case 'price':
                $query->orderBy('price', $sortDirection);
                break;
            case 'view':
                $query->orderBy('view', $sortDirection);
                break;
            case 'published_at':
                $query->orderBy('published_at', $sortDirection);
                break;
            default:
                $query->orderBy('created_at', $sortDirection);
        }

        $advertisements = $query->paginate(15)->withQueryString();

        // Get filter options
        $categories = Category::select('id', 'name')->get();
        $cities = City::select('id', 'name')->get();

        return view('admin.advertisements.index', compact(
            'advertisements',
            'categories',
            'cities'
        ));
    }

    /**
     * Display the specified advertisement
     */
    public function show(Advertisement $advertisement)
    {
        $advertisement->load([
            'user',
            'category',
            'city',
            'galleries',
            'payments' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'featuredAdvertisements' => function ($query) {
                $query->where('is_active', true)
                    ->where('expires_at', '>', now())
                    ->orderBy('created_at', 'desc');
            }
        ]);

        return view('admin.advertisements.show', compact('advertisement'));
    }

    /**
     * Show the form for editing the advertisement
     */
    public function edit(Advertisement $advertisement)
    {
        $categories = Category::select('id', 'name')->get();
        $cities = City::select('id', 'name')->get();

        return view('admin.advertisements.edit', compact('advertisement', 'categories', 'cities'));
    }

    /**
     * Update the specified advertisement
     */
    public function update(Request $request, Advertisement $advertisement)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'ads_type' => 'nullable|string|max:255',
            'ads_status' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'city_id' => 'required|exists:cities,id',
            'price' => 'nullable|integer|min:0',
            'contact' => 'nullable|string|max:255',
            'tags' => 'nullable|string|max:500',
            'lat' => 'nullable|string|max:50',
            'lng' => 'nullable|string|max:50',
            'willing_to_trade' => 'boolean',
            'status' => 'required|in:0,1,2,3,4',
            'expired_at' => 'nullable|date|after:now',
        ]);

        $data = $request->all();
        $data['willing_to_trade'] = $request->has('willing_to_trade');

        // Handle published_at based on status
        if ($data['status'] == 2 && !$advertisement->published_at) {
            $data['published_at'] = now();
        }

        $advertisement->update($data);

        return redirect()->route('admin.advertisements.show', $advertisement)
            ->with('success', 'آگهی با موفقیت به‌روزرسانی شد.');
    }

    /**
     * Remove the specified advertisement
     */
    public function destroy(Advertisement $advertisement)
    {
        // Delete main image
        if ($advertisement->image && Storage::disk('public')->exists($advertisement->image)) {
            Storage::disk('public')->delete($advertisement->image);
        }

        // Delete gallery images
        foreach ($advertisement->galleries as $gallery) {
            if (Storage::disk('public')->exists($gallery->url)) {
                Storage::disk('public')->delete($gallery->url);
            }
        }

        $advertisement->delete();

        return redirect()->route('admin.advertisements.index')
            ->with('success', 'آگهی با موفقیت حذف شد.');
    }
}
