<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TableResource extends JsonResource
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
            'name' => $this->resource->name,
            'location' => $this->resource->location,
            'reserved' => $this->whenLoaded('reservations', function () {
                return $this->resource->reservations
                    ->where('date', '=', now()->format('Y-m-d'))
                    ->isNotEmpty();
            }),
            'reservations' => ReservationResource::collection($this->whenLoaded('reservations')),
        ];
    }
}
