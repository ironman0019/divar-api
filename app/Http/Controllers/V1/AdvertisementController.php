<?php

namespace App\Http\Controllers\V1;

use App\Traits\HttpResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Advertisement\Gallery;
use App\Http\Services\ImageUploadService;
use App\Http\Services\AdvertisementService;
use App\Models\Advertisement\Advertisement;
use App\Http\Requests\V1\StoreAdvertisementRequest;
use App\Http\Resources\V1\Advertisement\AdvertisementResource;
use App\Http\Resources\V1\Advertisement\AdvertisementListResource;

class AdvertisementController extends Controller
{
    use HttpResponse;

    protected AdvertisementService $advertisementService;

    public function __construct(AdvertisementService $advertisementService)
    {
        $this->advertisementService = $advertisementService;
    }

    /**
     * Display a listing of advertisements with filters.
     */
    public function index(Request $request)
    {
        try {
            $advertisements = $this->advertisementService->getFilteredAdvertisements($request);

            return $this->success([
                'data' => AdvertisementListResource::collection($advertisements->items()),
                'pagination' => [
                    'current_page' => $advertisements->currentPage(),
                    'last_page' => $advertisements->lastPage(),
                    'per_page' => $advertisements->perPage(),
                    'total' => $advertisements->total(),
                    'from' => $advertisements->firstItem(),
                    'to' => $advertisements->lastItem(),
                ],
                'filters' => $this->advertisementService->getFilterOptions(),
            ], __('messages.advertisements.retrieved'));

        } catch (\Exception $e) {
            return $this->failed(null, __('messages.errors.server_error'), 500);
        }
    }

    /**
     * Display the specified advertisement.
     */
    public function show(Advertisement $advertisement)
    {
        try {
            // Check if advertisement is active and published
            if ($advertisement->status !== 2 || !$advertisement->published_at || $advertisement->published_at > now()) {
                return $this->failed(null, __('messages.advertisements.not_found'), 404);
            }

            // Check if advertisement is not expired
            if ($advertisement->expired_at && $advertisement->expired_at <= now()) {
                return $this->failed(null, __('messages.advertisements.not_found'), 404);
            }

            // Load relationships
            $advertisement->load([
                'city',
                'category',
                'user',
                'galleries',
                'categoryValues.categoryAttribute'
            ]);

            // Increment view count
            $this->advertisementService->incrementView($advertisement);
            $advertisement->view = $this->advertisementService->getDisplayViewCount($advertisement);

            // Get related advertisements
            $relatedAdvertisements = $this->advertisementService->getRelatedAdvertisements($advertisement);

            return $this->success([
                'advertisement' => new AdvertisementResource($advertisement),
                'related_advertisements' => AdvertisementListResource::collection($relatedAdvertisements),
            ], __('messages.advertisements.details_retrieved'));

        } catch (\Exception $e) {
            return $this->failed(null, __('messages.errors.server_error'), 500);
        }
    }

    /**
     * Display advertisements by category.
     */
    public function category(int $categoryId, Request $request)
    {
        try {
            // Check if category exists
            $category = \App\Models\Category\Category::active()->find($categoryId);
            if (!$category) {
                return $this->failed(null, __('messages.categories.not_found'), 404);
            }

            $advertisements = $this->advertisementService->getAdvertisementsByCategory($categoryId, $request);

            // Get category attributes for filtering
            $categoryAttributes = $this->advertisementService->getCategoryAttributes($categoryId);

            return $this->success([
                'category' => new \App\Http\Resources\V1\Category\CategoryResource($category),
                'data' => AdvertisementListResource::collection($advertisements->items()),
                'pagination' => [
                    'current_page' => $advertisements->currentPage(),
                    'last_page' => $advertisements->lastPage(),
                    'per_page' => $advertisements->perPage(),
                    'total' => $advertisements->total(),
                    'from' => $advertisements->firstItem(),
                    'to' => $advertisements->lastItem(),
                ],
                'attributes' => $categoryAttributes ? new \App\Http\Resources\V1\Category\CategoryResource($categoryAttributes) : null,
                'filters' => $this->advertisementService->getFilterOptions(),
            ], __('messages.advertisements.category_ads_retrieved'));

        } catch (\Exception $e) {
            return $this->failed(null, __('messages.errors.server_error'), 500);
        }
    }

    /**
     * Search advertisements.
     */
    public function search(Request $request)
    {
        try {
            $searchTerm = $request->get('q');
            
            if (!$searchTerm || strlen($searchTerm) < 2) {
                return $this->failed(null, __('messages.errors.search_too_short'), 400);
            }

            // Map 'q' to the 'search' parameter expected by the service
            $request->merge(['search' => $searchTerm]);

            $advertisements = $this->advertisementService->getFilteredAdvertisements($request);

            return $this->success([
                'search_term' => $searchTerm,
                'data' => AdvertisementListResource::collection($advertisements->items()),
                'pagination' => [
                    'current_page' => $advertisements->currentPage(),
                    'last_page' => $advertisements->lastPage(),
                    'per_page' => $advertisements->perPage(),
                    'total' => $advertisements->total(),
                    'from' => $advertisements->firstItem(),
                    'to' => $advertisements->lastItem(),
                ],
                'filters' => $this->advertisementService->getFilterOptions(),
            ], __('messages.advertisements.search_results'));

        } catch (\Exception $e) {
            return $this->failed(null, __('messages.errors.server_error'), 500);
        }
    }

    /**
     * Get filter options.
     */
    public function filters()
    {
        try {
            return $this->success(
                $this->advertisementService->getFilterOptions(),
                __('messages.success.data_retrieved')
            );
        } catch (\Exception $e) {
            return $this->failed(null, __('messages.errors.server_error'), 500);
        }
    }

    /**
     * Store a newly created advertisement.
     */
    public function store(StoreAdvertisementRequest $request, ImageUploadService $imageUploadService)
    {
        try {
            DB::beginTransaction();

            // Get authenticated user
            $user = auth('api')->user();
            
            if (!$user) {
                return $this->failed(null, __('messages.errors.unauthorized'), 401);
            }

            // Prepare advertisement data
            $advertisementData = $request->validated();
            $advertisementData['user_id'] = $user->id;
            $advertisementData['status'] = 3; // Pending status

            // Handle main image upload if provided
            if ($request->hasFile('image')) {
                $advertisementData['image'] = $imageUploadService->uploadImage($request->file('image'));
            }

            // Create advertisement
            $advertisement = Advertisement::create($advertisementData);

            // Handle gallery images upload
            if ($request->hasFile('images')) {
                $uploadedImages = $imageUploadService->uploadMultipleImages($request->file('images'));
                
                foreach ($uploadedImages as $imagePath) {
                    Gallery::create([
                        'advertisement_id' => $advertisement->id,
                        'url' => $imagePath,
                    ]);
                }
            }

            // Handle category values if provided
            if ($request->has('category_values') && !empty($request->category_values)) {
                $advertisement->categoryValues()->attach($request->category_values);
            }

            // Load relationships for response
            $advertisement->load([
                'city',
                'category',
                'user',
                'galleries',
                'categoryValues.categoryAttribute'
            ]);

            DB::commit();

            return $this->success([
                'advertisement' => new AdvertisementResource($advertisement),
            ], __('messages.advertisements.created'));

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Clean up uploaded images if any error occurs
            if (isset($uploadedImages)) {
                $imageUploadService->removeMultipleImages($uploadedImages);
            }
            if (isset($advertisementData['image'])) {
                $imageUploadService->removeImage($advertisementData['image']);
            }

            return $this->failed(null, __('messages.errors.server_error'), 500);
        }
    }
}
