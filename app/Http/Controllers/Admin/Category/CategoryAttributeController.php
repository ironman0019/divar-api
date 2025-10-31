<?php

namespace App\Http\Controllers\Admin\Category;

use App\Http\Controllers\Controller;
use App\Models\Category\Category;
use App\Models\Category\CategoryAttribute;
use App\Http\Requests\Category\StoreCategoryAttributeRequest;
use App\Http\Requests\Category\UpdateCategoryAttributeRequest;
use Illuminate\Http\Request;

class CategoryAttributeController extends Controller
{
    /**
     * Display a listing of category attributes
     */
    public function index(Request $request)
    {
        $query = CategoryAttribute::with(['category']);

        // Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $query->orderBy($sortBy, $sortDirection);

        $attributes = $query->paginate(15)->withQueryString();

        // Get categories for filter
        $categories = Category::select('id', 'name')->get();

        return view('admin.categories.attributes.index', compact('attributes', 'categories'));
    }

    /**
     * Show the form for creating a new category attribute
     */
    public function create()
    {
        $categories = Category::select('id', 'name')->get();
        return view('admin.categories.attributes.create', compact('categories'));
    }

    /**
     * Store a newly created category attribute
     */
    public function store(StoreCategoryAttributeRequest $request)
    {
        $data = $request->validated();

        // Set status default to true if not provided
        $data['status'] = $request->has('status') ? (bool) $request->status : true;

        CategoryAttribute::create($data);

        return redirect()->route('admin.categories.attributes.index')
            ->with('success', 'ویژگی دسته‌بندی با موفقیت ایجاد شد.');
    }

    /**
     * Display the specified category attribute
     */
    public function show(CategoryAttribute $attribute)
    {
        $attribute->load(['category', 'values']);
        return view('admin.categories.attributes.show', compact('attribute'));
    }

    /**
     * Show the form for editing the specified category attribute
     */
    public function edit(CategoryAttribute $attribute)
    {
        $categories = Category::select('id', 'name')->get();
        return view('admin.categories.attributes.edit', compact('attribute', 'categories'));
    }

    /**
     * Update the specified category attribute
     */
    public function update(UpdateCategoryAttributeRequest $request, CategoryAttribute $attribute)
    {
        $data = $request->validated();

        // Set status
        $data['status'] = $request->has('status') ? (bool) $request->status : false;

        $attribute->update($data);

        return redirect()->route('admin.categories.attributes.index')
            ->with('success', 'ویژگی دسته‌بندی با موفقیت به‌روزرسانی شد.');
    }

    /**
     * Remove the specified category attribute
     */
    public function destroy(CategoryAttribute $attribute)
    {
        $attribute->delete();

        return redirect()->route('admin.categories.attributes.index')
            ->with('success', 'ویژگی دسته‌بندی با موفقیت حذف شد.');
    }

    /**
     * Toggle category attribute status
     */
    public function toggleStatus(CategoryAttribute $attribute)
    {
        $attribute->update(['status' => !$attribute->status]);

        $status = $attribute->status ? 'فعال' : 'غیرفعال';

        return redirect()->back()
            ->with('success', "ویژگی دسته‌بندی {$status} شد.");
    }
}
