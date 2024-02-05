<?php

namespace App\Http\Resources\Dashboard;

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
        return [
            'id' => $this->id,
            'items' => $this->items,
            'user_id' => $this->user_id,
            'last_name' => $this->last_name,
            'first_name' => $this->first_name,
            'total_price' => $this->total_price,
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}
