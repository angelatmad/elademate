<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $avatar = null;
        if ($this->profile_photo) {
            if (Storage::disk('public')->exists($this->profile_photo)) {
                $avatar = asset('storage/'.$this->profile_photo);
            } else {
                $avatar = asset('storage/'.$this->profile_photo);
            }
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'first_name' => $this->first_name,
            'initials' => $this->initials,
            'email' => $this->email,
            'profile_photo' => $this->profile_photo,
            'avatar_url' => $avatar,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}