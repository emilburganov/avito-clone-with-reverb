<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdResource extends JsonResource
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
            'status' => $this->status,
            'admin_message' => $this->admin_message,
            'title' => $this->title,
            'price' => $this->price,
            'categories' => CategoryResource::collection($this->categories),
            'photos' => PhotoResource::collection($this->photos),
        ];
    }
}
