<?php

namespace App\Http\Resources\V1\Category;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryAttributeResource extends JsonResource
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
            'name' => $this->name,
            'unit' => $this->unit,
            'type' => [
                'value' => $this->type,
                'label' => $this->type_label,
            ],
            'status' => [
                'value' => $this->status,
                'label' => $this->status ? 'فعال' : 'غیرفعال'
            ],
            'values' => $this->whenLoaded('values', function () {
                return $this->values->map(function ($value) {
                    return [
                        'id' => $value->id,
                        'value' => $value->value,
                        'type' => [
                            'value' => $value->type,
                            'label' => $value->type_label,
                        ],
                        'status' => [
                            'value' => $value->status,
                            'label' => $value->status ? 'فعال' : 'غیرفعال'
                        ],
                    ];
                });
            }),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
