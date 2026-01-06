<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'status' => $this->status,
            'priority' => $this->priority,
            'category' => $this->category,
            'source_branch' => $this->source_branch,
            'target_branch' => $this->target_branch,
            'created_by' => [
                'id' => $this->creator?->id,
                'full_name' => $this->creator?->full_name,
                'branch' => $this->creator?->branch,
            ],
            'assigned_to' => [
                'id' => $this->assignee?->id,
                'full_name' => $this->assignee?->full_name,
                'branch' => $this->assignee?->branch,
            ],
            'last_message_at' => $this->last_message_at?->format('Y-m-d H:i:s'),
            'closed_at' => $this->closed_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
