<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'date' => $this->resource->date->format('Y m d'),
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->resource->user->id,
                    'name' => $this->resource->user->name,
                ];
            }),
        ];
    }
}
