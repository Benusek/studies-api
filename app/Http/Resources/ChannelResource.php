<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChannelResource extends JsonResource
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
            'name' => $this->name,
            'avatar' => $this->photo_file,
            'subscribers' => [
                'items' => ProfileResource::collection($this->subscribers),
                'count' => $this->subscribers->count()
            ],
            'subscribed' => $this->when(
                auth('api')->check(),
                fn () => $this->subscribers->contains('id', auth('api')->id())
            )
        ];
    }
}
