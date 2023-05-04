<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ChatListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $last_message = $this->messages()
            ->where('deleted_id', null)
            ->orWhere('deleted_id', '!=', Auth::id())
            ->orderByDesc('id')
            ->first();
        
        return [
            'auth' => Auth::id(),
            'id' => $this->id,
            'owner' => ShowUserResource::make($this->owner),
            'user' => ShowUserResource::make($this->user),
            'last_message' => MessageResource::make($last_message),
            'owner_new_message_count' => $this->messages()
                ->where('user_id', $this->user_id)
                ->where('is_read', false)
                ->count(),
            'user_new_message_count' => $this->messages()
                ->where('user_id', $this->owner_id)
                ->where('is_read', false)
                ->count(),
            'created_at' => $this->created_at,
        ];
    }
}
