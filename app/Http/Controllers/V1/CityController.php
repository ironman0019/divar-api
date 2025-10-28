<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CityResource;
use App\Models\City;
use App\Traits\HttpResponse;
use Illuminate\Http\Request;

class CityController extends Controller
{
    use HttpResponse;

    public function index()
    {
        $cities = City::all();
        return $this->success(
            CityResource::collection($cities),
            __('messages.cities.retrieved')
        );
    }
}
