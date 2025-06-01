<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'surname' => $this->surname,
            'patronymic' => $this->email,
            'login' => $this->login,
            'photo_file' => $this->photo_file,
            'subscribers_count' => $this->subscribers->count(),
            'subscribers' => $this->subscribers->count() === 0 ? null : $this->subscribers
                ->map(function ($subscriber) {
                return ChannelResource::make($subscriber->user);
            })
        ];
    }
}
