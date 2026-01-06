<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketMessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ticket_id' => $this->ticket_id,
            'body' => $this->body,
            'message_type' => $this->message_type,
            'user' => [
                'id' => $this->user?->id,
                'full_name' => $this->user?->full_name,
                'position' => $this->user?->position,
                'branch' => $this->user?->branch,
            ],
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
