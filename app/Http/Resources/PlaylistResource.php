<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlaylistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        self::$wrap = "playlists";
        return [
            'id' => $this->id,
            'title' => $this->title,
            'public' => $this->public,
            'created_at' => $this->photo_file,
        ];
    }
}
