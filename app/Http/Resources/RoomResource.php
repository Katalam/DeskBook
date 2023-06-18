<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
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
            'tables' => TableResource::collection($this->whenLoaded('tables')),

            $this->mergeWhen($request->routeIs('*.edit'), [
                'is_outside' => $this->resource->is_outside,
            ]),
        ];
    }
}
