<?php

namespace App\Http\Resources;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
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
        Carbon::setLocale('ru');
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'public' => $this->public,
            'thumbnail' => $this->thumbnail,
            'video' => $this->video,
            'duration' => $this->duration,
            'user' => ChannelResource::make($this->user),
            'created_at' => $this->created_at->diffForHumans()
        ];
    }
}
