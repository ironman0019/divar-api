<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Category\CategoryResource;
use App\Models\Category\Category;
use App\Traits\HttpResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use HttpResponse;

    /**
     * Display a listing of categories.
     */
    public function index(Request $request)
    {
        try {
            $query = Category::active();

            // If requesting only parent categories
            if ($request->boolean('parents_only')) {
                $query->parents();
            }

            // If requesting only child categories
            if ($request->boolean('children_only')) {
                $query->children();
            }

            // If requesting hierarchical structure
            if ($request->boolean('hierarchical')) {
                $categories = $query->with(['children' => function ($query) {
                    $query->active();
                }])->parents()->get();
            } else {
                $categories = $query->get();
            }

            return $this->success(
                CategoryResource::collection($categories),
                __('messages.categories.retrieved')
            );

        } catch (\Exception $e) {
            return $this->failed(null, __('messages.errors.server_error'), 500);
        }
    }

    /**
     * Display the specified category.
     */
    public function show(int $id)
    {
        try {
            $category = Category::active()
                ->with(['parent', 'children' => function ($query) {
                    $query->active();
                }])
                ->find($id);

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

    /**
     * Get category attributes with their values.
     */
    public function attributes(int $id)
    {
        try {
            $category = Category::active()->find($id);

            if (!$category) {
                return $this->failed(null, __('messages.categories.not_found'), 404);
            }

            $categoryWithAttributes = Category::with(['attributes.values' => function ($query) {
                $query->active();
            }])
            ->where('id', $id)
            ->first();

            return $this->success([
                'category' => new CategoryResource($category),
                'attributes' => \App\Http\Resources\V1\Category\CategoryAttributeResource::collection(
                    $categoryWithAttributes->attributes
                ),
            ], __('messages.categories.attributes_retrieved'));

        } catch (\Exception $e) {
            return $this->failed(null, __('messages.errors.server_error'), 500);
        }
    }

    /**
     * Get category hierarchy (parents with children).
     */
    public function hierarchy()
    {
        try {
            $categories = Category::active()
                ->with(['children' => function ($query) {
                    $query->active();
                }])
                ->parents()
                ->get();

            return $this->success(
                CategoryResource::collection($categories),
                __('messages.categories.hierarchy_retrieved')
            );

        } catch (\Exception $e) {
            return $this->failed(null, __('messages.errors.server_error'), 500);
        }
    }

    /**
     * Get child categories of a specific parent.
     */
    public function children(int $parentId)
    {
        try {
            $parentCategory = Category::active()->find($parentId);

            if (!$parentCategory) {
                return $this->failed(null, __('messages.categories.not_found'), 404);
            }

            $children = Category::active()
                ->where('parent_id', $parentId)
                ->get();

            return $this->success([
                'parent' => new CategoryResource($parentCategory),
                'children' => CategoryResource::collection($children),
            ], __('messages.categories.retrieved'));

        } catch (\Exception $e) {
            return $this->failed(null, __('messages.errors.server_error'), 500);
        }
    }
}
