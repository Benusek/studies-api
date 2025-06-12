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
            'patronymic' => $this->patronymic,
            'email' => $this->email,
            'email_verify' => $this->email_veridied_at,
            'login' => $this->login,
            'photo_file' => $this->photo_file,
            'subscribers_count' => $this->subscribers->count(),
            'subscribers' => $this->subscribers->count() === 0 ? null : $this->subscribers
                ->map(function ($subscriber) {
                return ChannelResource::make($subscriber->user);
            }),
            'subscribe' => $this->subscribe->count() === 0 ? null : $this->subscribe
                ->map(function ($subscriber) {
                return ChannelResource::make($subscriber->user);
            })
        ];
    }
}
