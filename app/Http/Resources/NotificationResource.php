<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
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
            'type' => $this->resource->type,
            'channel' => $this->resource->channel,
            'receiver' => $this->resource->receiver,
            'message' => $this->resource->message,
            'rooms' => $this->whenPivotLoaded('notificationables', function () {
                return $this->resource->rooms->pluck('id');
            }),
            //            'tables' => $this->whenPivotLoaded('notificationables', function () {
            //                return $this->resource->tables->pluck('id');
            //            }),
        ];
    }
}
