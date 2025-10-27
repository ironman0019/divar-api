<?php

namespace App\Http\Resources\V1\Advertisement;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class AdvertisementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'ads_type' => $this->ads_type,
            'ads_status' => $this->ads_status,
            'price' => $this->price,
            'price_formatted' => $this->price ? number_format($this->price) . ' تومان' : null,
            'contact' => $this->contact,
            'is_special' => $this->is_special,
            'is_ladder' => $this->is_ladder,
            'willing_to_trade' => $this->willing_to_trade,
            'view' => $this->view,
            'tags' => $this->tags ? explode(',', $this->tags) : [],
            'location' => [
                'lat' => $this->lat,
                'lng' => $this->lng,
            ],
            'images' => [
                'primary' => $this->image ? URL::to($this->image) : null,
                'gallery' => $this->galleries->map(function ($gallery) {
                    return URL::to($gallery->url);
                }),
            ],
            'category' => new \App\Http\Resources\V1\Category\CategoryResource($this->whenLoaded('category')),
            'city' => new \App\Http\Resources\V1\CityResource($this->whenLoaded('city')),
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'city' => $this->user->city ? $this->user->city->name : null,
            ],
            'attributes' => \App\Http\Resources\V1\Category\CategoryAttributeResource::collection($this->whenLoaded('categoryValues')),
            'dates' => [
                'published_at' => $this->published_at?->format('Y-m-d H:i:s'),
                'expired_at' => $this->expired_at?->format('Y-m-d H:i:s'),
                'created_at' => $this->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            ],
            'slug' => $this->slug,
        ];
    }
}
