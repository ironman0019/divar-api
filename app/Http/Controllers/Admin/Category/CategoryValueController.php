<?php

namespace App\Http\Controllers\Admin\Category;

use App\Http\Controllers\Controller;
use App\Models\Category\CategoryAttribute;
use App\Models\Category\CategoryValue;
use App\Http\Requests\Category\StoreCategoryValueRequest;
use App\Http\Requests\Category\UpdateCategoryValueRequest;
use Illuminate\Http\Request;

class CategoryValueController extends Controller
{
    /**
     * Display a listing of category values
     */
    public function index(Request $request)
    {
        $query = CategoryValue::with(['categoryAttribute.category']);

        // Search
        if ($request->filled('search')) {
            $query->where('value', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by attribute
        if ($request->filled('category_attribute_id')) {
            $query->where('category_attribute_id', $request->category_attribute_id);
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $query->orderBy($sortBy, $sortDirection);

        $values = $query->paginate(15)->withQueryString();

        // Get attributes for filter
        $attributes = CategoryAttribute::with('category')->select('id', 'name', 'category_id')->get();

        return view('admin.categories.values.index', compact('values', 'attributes'));
    }

    /**
     * Show the form for creating a new category value
     */
    public function create()
    {
        $attributes = CategoryAttribute::with('category')->select('id', 'name', 'category_id')->get();
        return view('admin.categories.values.create', compact('attributes'));
    }

    /**
     * Store a newly created category value
     */
    public function store(StoreCategoryValueRequest $request)
    {
        $data = $request->validated();

        // Set status default to true if not provided
        $data['status'] = $request->has('status') ? (bool) $request->status : true;

        // Set type default to 0 if not provided
        $data['type'] = $request->has('type') ? (int) $request->type : 0;

        CategoryValue::create($data);

        return redirect()->route('admin.categories.values.index')
            ->with('success', 'مقدار ویژگی با موفقیت ایجاد شد.');
    }

    /**
     * Display the specified category value
     */
    public function show(CategoryValue $value)
    {
        $value->load(['categoryAttribute.category']);
        return view('admin.categories.values.show', compact('value'));
    }

    /**
     * Show the form for editing the specified category value
     */
    public function edit(CategoryValue $value)
    {
        $attributes = CategoryAttribute::with('category')->select('id', 'name', 'category_id')->get();
        return view('admin.categories.values.edit', compact('value', 'attributes'));
    }

    /**
     * Update the specified category value
     */
    public function update(UpdateCategoryValueRequest $request, CategoryValue $value)
    {
        $data = $request->validated();

        // Set status
        $data['status'] = $request->has('status') ? (bool) $request->status : false;

        // Set type default to 0 if not provided
        if (!isset($data['type'])) {
            $data['type'] = 0;
        }

        $value->update($data);

        return redirect()->route('admin.categories.values.index')
            ->with('success', 'مقدار ویژگی با موفقیت به‌روزرسانی شد.');
    }

    /**
     * Remove the specified category value
     */
    public function destroy(CategoryValue $value)
    {
        $value->delete();

        return redirect()->route('admin.categories.values.index')
            ->with('success', 'مقدار ویژگی با موفقیت حذف شد.');
    }

    /**
     * Toggle category value status
     */
    public function toggleStatus(CategoryValue $value)
    {
        $value->update(['status' => !$value->status]);

        $status = $value->status ? 'فعال' : 'غیرفعال';

        return redirect()->back()
            ->with('success', "مقدار ویژگی {$status} شد.");
    }
}

