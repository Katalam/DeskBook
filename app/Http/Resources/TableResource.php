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
            'room_name' => $this->whenLoaded('room', fn () => $this->resource->room->name),
            'reserved' => $this->whenLoaded('reservations', fn () => $this->isReserved($request)),
            'reservations' => $this->whenLoaded('reservations', fn () => ReservationResource::collection($this->resource->reservations)),
            'multiple_bookings' => $this->resource->multiple_bookings,
            'is_favorite' => $this->whenLoaded('favorites', fn () => $this->resource->favorites->contains('id', auth()->id())),

            $this->mergeWhen($request->routeIs('*.edit'), [
                'room_id' => $this->resource->room_id,
                'time_off_type_id' => $this->resource->time_off_type_id,
                'features' => $this->whenLoaded('features', fn () => $this->resource->features->pluck('id')),
            ]),
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
