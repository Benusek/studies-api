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
            'login' => $this->login,
            'photo_file' => $this->photo_file,
            'created' => $this->created_at->diffForHumans(),
            'role_id' => $this->role->id,
            'subscribers' => [
                'items' =>$this->subscribers,
                'count' => $this->subscribers->count()
            ],
            'subscribe' => $this->subscribe
        ];
    }
}
