<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
            'reserved' => $this->whenLoaded('reservations', fn() => $this->isReserved($request)),
            'reservations' => ReservationResource::collection($this->whenLoaded('reservations')),
        ];
    }

    private function isReserved(Request $request): bool
    {
        $selectedDate = today();

        if ($request->has('date')) {
            $selectedDate = Carbon::parse($request->input('date'));
        }

        return $this->resource->reservations
            ->where('date', $selectedDate)
            ->isNotEmpty();
    }
}
