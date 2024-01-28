<?php

namespace App\Http\Resources;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public static $wrap = false;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $customer = $this->user->customer;
        $billing = $customer->billingAddress;
        $shipping = $customer->shippingAddress;

        return [
            'id' => $this->id,
            'status' => $this->status,
            'total_price' => $this->total_price,
            'items' => $this->items->map(fn($item) => [
                'id' => $item->id,
                'unit_price' => $item->unit_price,
                'quantity' => $item->quantity,
                'product' => [
                    'id' => $item->product->id,
                    'title' => $item->product->title,
                    'image' => $item->product->image,
                    'slug' => $item->product->slug,
                ],
            ]),
            'customer' => [
                'id' => $this->user->id,
                'email' => $this->user->email,
                'phone' => $customer->phone,
                'first_name' => $customer->first_name,
                'last_name' => $customer->last_name,
                'shippingAddress' => [
                    'id' => $shipping->id,
                    'address1' => $shipping->address1,
                    'address2' => $shipping->address2,
                    'city' => $shipping->city,
                    'state' => $shipping->state,
                    'zip_code' => $shipping->zip_code,
                    'country' => $shipping->country->name,
                ],
                'billingAddress' => [
                    'id' => $billing->id,
                    'address1' => $billing->address1,
                    'address2' => $billing->address2,
                    'city' => $billing->city,
                    'state' => $billing->state,
                    'zip_code' => $billing->zip_code,
                    'country' => $billing->country->name,
                ]
            ],
            'created_at' => (new DateTime($this->created_at))->format('Y-m-d H:i:s'),
            'updated_at' => (new DateTime($this->updated_at))->format('Y-m-d H:i:s'),
        ];
    }
}
