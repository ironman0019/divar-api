<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CityResource;
use App\Support\CatalogCache;
use App\Traits\HttpResponse;

class CityController extends Controller
{
    use HttpResponse;

    public function index()
    {
        $cities = CatalogCache::activeCities();

        return $this->success(
            CityResource::collection($cities),
            __('messages.cities.retrieved')
        );
    }
}
