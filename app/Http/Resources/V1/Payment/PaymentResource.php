<?php

namespace App\Http\Resources\V1\Payment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'user_id' => $this->user_id,
            'advertisement_id' => $this->advertisement_id,
            'amount' => $this->amount,
            'payment_type' => $this->payment_type,
            'payment_type_label' => $this->payment_type_label,
            'duration_days' => $this->duration_days,
            'description' => $this->description,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'authority' => $this->authority,
            'ref_id' => $this->ref_id,
            'card_pan' => $this->card_pan,
            'trace_no' => $this->trace_no,
            'gateway_response' => $this->gateway_response,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            
            // Relations
            'advertisement' => $this->whenLoaded('advertisement', function () {
                return [
                    'id' => $this->advertisement->id,
                    'title' => $this->advertisement->title,
                    'is_ladder' => $this->advertisement->is_ladder,
                    'is_special' => $this->advertisement->is_special,
                ];
            }),
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'mobile' => $this->user->mobile,
                ];
            }),
        ];
    }
}
