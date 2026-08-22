<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Category\CategoryResource;
use App\Http\Resources\V1\Category\CategoryAttributeResource;
use App\Models\Category\Category;
use App\Support\CatalogCache;
use App\Traits\HttpResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use HttpResponse;

    public function index(Request $request)
    {
        try {
            $variant = $this->indexVariant($request);
            $key = CatalogCache::categoriesIndexKey($variant);

            $categories = CatalogCache::rememberCategories($key, function () use ($request) {
                $query = Category::active();

                if ($request->boolean('parents_only')) {
                    $query->parents();
                }

                if ($request->boolean('children_only')) {
                    $query->children();
                }

                if ($request->boolean('hierarchical')) {
                    return $query->with(['children' => function ($query) {
                        $query->active();
                    }])->parents()->get();
                }

                return $query->get();
            });

            return $this->success(
                CategoryResource::collection($categories),
                __('messages.categories.retrieved')
            );
        } catch (\Exception $e) {
            return $this->failed(null, __('messages.errors.server_error'), 500);
        }
    }

    public function show(int $id)
    {
        try {
            $category = CatalogCache::rememberCategories(
                CatalogCache::categoriesShowKey($id),
                fn () => Category::active()
                    ->with(['parent', 'children' => fn ($query) => $query->active()])
                    ->find($id)
            );

            if (!$category) {
                return $this->failed(null, __('messages.categories.not_found'), 404);
            }

            return $this->success(
                new CategoryResource($category),
                __('messages.categories.retrieved')
            );
        } catch (\Exception $e) {
            return $this->failed(null, __('messages.errors.server_error'), 500);
        }
    }

    public function attributes(int $id)
    {
        try {
            $cached = CatalogCache::rememberCategories(
                CatalogCache::categoriesAttributesKey($id),
                function () use ($id) {
                    $category = Category::active()->find($id);

                    if (!$category) {
                        return null;
                    }

                    $categoryWithAttributes = Category::with(['attributes.values' => function ($query) {
                        $query->active();
                    }])->where('id', $id)->first();

                    return [
                        'category' => $category,
                        'attributes' => $categoryWithAttributes->attributes,
                    ];
                }
            );

            if (!$cached) {
                return $this->failed(null, __('messages.categories.not_found'), 404);
            }

            return $this->success([
                'category' => new CategoryResource($cached['category']),
                'attributes' => CategoryAttributeResource::collection($cached['attributes']),
            ], __('messages.categories.attributes_retrieved'));
        } catch (\Exception $e) {
            return $this->failed(null, __('messages.errors.server_error'), 500);
        }
    }

    public function hierarchy()
    {
        try {
            $categories = CatalogCache::rememberCategories(
                CatalogCache::categoriesHierarchyKey(),
                fn () => Category::active()
                    ->with(['children' => fn ($query) => $query->active()])
                    ->parents()
                    ->get()
            );

            return $this->success(
                CategoryResource::collection($categories),
                __('messages.categories.hierarchy_retrieved')
            );
        } catch (\Exception $e) {
            return $this->failed(null, __('messages.errors.server_error'), 500);
        }
    }

    public function children(int $parentId)
    {
        try {
            $cached = CatalogCache::rememberCategories(
                CatalogCache::categoriesChildrenKey($parentId),
                function () use ($parentId) {
                    $parentCategory = Category::active()->find($parentId);

                    if (!$parentCategory) {
                        return null;
                    }

                    $children = Category::active()->where('parent_id', $parentId)->get();

                    return [
                        'parent' => $parentCategory,
                        'children' => $children,
                    ];
                }
            );

            if (!$cached) {
                return $this->failed(null, __('messages.categories.not_found'), 404);
            }

            return $this->success([
                'parent' => new CategoryResource($cached['parent']),
                'children' => CategoryResource::collection($cached['children']),
            ], __('messages.categories.retrieved'));
        } catch (\Exception $e) {
            return $this->failed(null, __('messages.errors.server_error'), 500);
        }
    }

    protected function indexVariant(Request $request): string
    {
        if ($request->boolean('hierarchical')) {
            return 'hierarchical';
        }

        if ($request->boolean('parents_only')) {
            return 'parents';
        }

        if ($request->boolean('children_only')) {
            return 'children';
        }

        return 'all';
    }
}
