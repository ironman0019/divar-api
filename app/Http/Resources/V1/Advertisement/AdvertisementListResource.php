<?php

namespace App\Http\Resources\V1\Advertisement;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class AdvertisementListResource extends JsonResource
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
            'price' => $this->price,
            'price_formatted' => $this->price ? number_format($this->price) . ' تومان' : null,
            'ads_type' => $this->ads_type,
            'ads_status' => $this->ads_status,
            'view' => $this->view,
            'is_special' => $this->is_special,
            'is_ladder' => $this->is_ladder,
            'willing_to_trade' => $this->willing_to_trade,
            'image' => $this->image ? URL::to($this->image) : null,
            'category' => [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'icon' => $this->category->icon,
            ],
            'city' => [
                'id' => $this->city->id,
                'name' => $this->city->name,
            ],
            'published_at' => $this->published_at?->format('Y-m-d H:i:s'),
            'slug' => $this->slug,
        ];
    }
}
