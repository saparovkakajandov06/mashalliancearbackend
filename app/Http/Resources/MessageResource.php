<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'chat_id' => $this->chat_id,
            'user' => ShowUserResource::make($this->user),
            'message' => $this->message,
            'is_read' => $this->is_read,
            'deleted_for' => ShowUserResource::make($this->deleted_user),
            'created_at' => $this->created_at
        ];
    }
}
