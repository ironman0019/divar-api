<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCityRequest;
use App\Http\Requests\Admin\UpdateCityRequest;
use App\Models\Advertisement\Advertisement;
use App\Models\City;
use App\Models\User;
use App\Support\CatalogCache;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $query = City::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $cities = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.cities.index', compact('cities'));
    }

    public function create()
    {
        return view('admin.cities.create');
    }

    public function store(StoreCityRequest $request)
    {
        City::create([
            'name' => $request->validated('name'),
            'status' => $request->boolean('status', true) ? 1 : 0,
        ]);

        CatalogCache::forgetCities();

        return redirect()->route('admin.cities.index')
            ->with('success', 'شهر با موفقیت ایجاد شد.');
    }

    public function edit(City $city)
    {
        return view('admin.cities.edit', compact('city'));
    }

    public function update(UpdateCityRequest $request, City $city)
    {
        $city->update([
            'name' => $request->validated('name'),
            'status' => $request->boolean('status') ? 1 : 0,
        ]);

        CatalogCache::forgetCities();

        return redirect()->route('admin.cities.index')
            ->with('success', 'شهر با موفقیت به‌روزرسانی شد.');
    }

    public function destroy(City $city)
    {
        if (User::where('city_id', $city->id)->exists()) {
            return redirect()->back()
                ->with('error', 'این شهر در پروفایل کاربران استفاده شده و قابل حذف نیست.');
        }

        if (Advertisement::where('city_id', $city->id)->exists()) {
            return redirect()->back()
                ->with('error', 'این شهر در آگهی‌ها استفاده شده و قابل حذف نیست.');
        }

        $city->delete();

        CatalogCache::forgetCities();

        return redirect()->route('admin.cities.index')
            ->with('success', 'شهر با موفقیت حذف شد.');
    }

    public function toggleStatus(City $city)
    {
        $city->update(['status' => $city->status ? 0 : 1]);

        CatalogCache::forgetCities();

        $status = $city->status ? 'فعال' : 'غیرفعال';

        return redirect()->back()
            ->with('success', "شهر {$status} شد.");
    }
}
