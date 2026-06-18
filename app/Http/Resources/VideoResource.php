<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        self::$wrap = "videos";
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'public' => $this->public,
            'thumbnail' => $this->thumbnail,
            'video' => $this->video,
            'duration' => $this->duration,
            'category' => $this->category,
            'user' => ChannelResource::make($this->user),
            'tags' => TagResource::collection($this->tags),
            'created_at' => $this->created_at->diffForHumans()
        ];
    }
}
