<?php

namespace App\Http\Controllers\Admin\Category;

use App\Http\Controllers\Controller;
use App\Models\Category\Category;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Services\ImageUploadService;
use App\Support\CatalogCache;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $imageUploadService;

    public function __construct(ImageUploadService $imageUploadService)
    {
        $this->imageUploadService = $imageUploadService;
    }

    /**
     * Display a listing of categories
     */
    public function index(Request $request)
    {
        $query = Category::with(['parent', 'children']);

        // Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('slug', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by parent
        if ($request->filled('parent_id')) {
            if ($request->parent_id == '0') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $request->parent_id);
            }
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $query->orderBy($sortBy, $sortDirection);

        $categories = $query->paginate(15)->withQueryString();

        // Get parent categories for filter
        $parentCategories = Category::whereNull('parent_id')->get();

        return view('admin.categories.index', compact('categories', 'parentCategories'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')->get();
        return view('admin.categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created category
     */
    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();

        // Handle icon upload
        if ($request->hasFile('icon')) {
            $data['icon'] = $this->imageUploadService->uploadImage($request->file('icon'));
        }

        // Set status default to true if not provided
        $data['status'] = $request->has('status') ? (bool) $request->status : true;

        Category::create($data);

        CatalogCache::forgetCategories();

        return redirect()->route('admin.categories.index')
            ->with('success', 'دسته‌بندی با موفقیت ایجاد شد.');
    }

    /**
     * Display the specified category
     */
    public function show(Category $category)
    {
        $category->load(['parent', 'children', 'attributes.values', 'advertisements']);
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category
     */
    public function edit(Category $category)
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified category
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $data = $request->validated();

        // Handle icon upload
        if ($request->hasFile('icon')) {
            // Delete old icon if exists
            if ($category->icon) {
                $this->imageUploadService->removeImage($category->icon);
            }
            $data['icon'] = $this->imageUploadService->uploadImage($request->file('icon'));
        }

        // Set status
        $data['status'] = $request->has('status') ? (bool) $request->status : false;

        $category->update($data);

        CatalogCache::forgetCategories();

        return redirect()->route('admin.categories.index')
            ->with('success', 'دسته‌بندی با موفقیت به‌روزرسانی شد.');
    }

    /**
     * Remove the specified category
     */
    public function destroy(Category $category)
    {
        // Delete icon if exists
        if ($category->icon) {
            $this->imageUploadService->removeImage($category->icon);
        }

        $category->delete();

        CatalogCache::forgetCategories();

        return redirect()->route('admin.categories.index')
            ->with('success', 'دسته‌بندی با موفقیت حذف شد.');
    }

    /**
     * Toggle category status
     */
    public function toggleStatus(Category $category)
    {
        $category->update(['status' => !$category->status]);

        CatalogCache::forgetCategories();

        $status = $category->status ? 'فعال' : 'غیرفعال';

        return redirect()->back()
            ->with('success', "دسته‌بندی {$status} شد.");
    }
}

