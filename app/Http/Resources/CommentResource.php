<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        self::$wrap = "comments";
        return [
            'id' => $this->id,
            'video_id' => $this->video_id,
            'user' => ChannelResource::make($this->user),
            'text' => $this->text,
            'answers' => AnswerResource::collection($this->comment_answers),
            'created_at' => $this->created_at
        ];
    }
}
