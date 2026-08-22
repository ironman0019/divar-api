<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePromotionPriceRequest;
use App\Http\Requests\Admin\UpdatePromotionPriceRequest;
use App\Models\Advertisement\PromotionPrice;
use App\Support\CatalogCache;
use Illuminate\Http\Request;

class PromotionPriceController extends Controller
{
    public function index(Request $request)
    {
        $query = PromotionPrice::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $promotionPrices = $query
            ->orderBy('type')
            ->orderBy('duration_days')
            ->paginate(15)
            ->withQueryString();

        return view('admin.payment.promotion-prices.index', compact('promotionPrices'));
    }

    public function create()
    {
        return view('admin.payment.promotion-prices.create');
    }

    public function store(StorePromotionPriceRequest $request)
    {
        PromotionPrice::create([
            'type' => $request->validated('type'),
            'duration_days' => $request->validated('duration_days'),
            'price' => $request->validated('price'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        CatalogCache::forgetPromotionPrices();

        return redirect()->route('admin.payment.promotion-prices.index')
            ->with('success', 'تعرفه تبلیغ با موفقیت ایجاد شد.');
    }

    public function edit(PromotionPrice $promotionPrice)
    {
        return view('admin.payment.promotion-prices.edit', compact('promotionPrice'));
    }

    public function update(UpdatePromotionPriceRequest $request, PromotionPrice $promotionPrice)
    {
        $promotionPrice->update([
            'type' => $request->validated('type'),
            'duration_days' => $request->validated('duration_days'),
            'price' => $request->validated('price'),
            'is_active' => $request->boolean('is_active'),
        ]);

        CatalogCache::forgetPromotionPrices();

        return redirect()->route('admin.payment.promotion-prices.index')
            ->with('success', 'تعرفه تبلیغ با موفقیت به‌روزرسانی شد.');
    }

    public function destroy(PromotionPrice $promotionPrice)
    {
        $promotionPrice->delete();

        CatalogCache::forgetPromotionPrices();

        return redirect()->route('admin.payment.promotion-prices.index')
            ->with('success', 'تعرفه تبلیغ با موفقیت حذف شد.');
    }

    public function toggleStatus(PromotionPrice $promotionPrice)
    {
        $promotionPrice->update(['is_active' => ! $promotionPrice->is_active]);

        CatalogCache::forgetPromotionPrices();

        $status = $promotionPrice->is_active ? 'فعال' : 'غیرفعال';

        return redirect()->back()
            ->with('success', "تعرفه {$status} شد.");
    }
}
