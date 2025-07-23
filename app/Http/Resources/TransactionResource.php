<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
        'id' => $this->uuid,
        'account' => new AccountResource($this->account),
        'category' => new CategoryResource($this->category),
        'description' => $this->description,
        'notes'=> $this->notes,
        'amount' => $this->amount,
        'type' => $this->type,
        'formatted_type' => $this->formatted_type,
        // 'type_display' => $this->type_display,
        'transaction_date' => $this->transaction_date->format('Y-m-d H:i'),
        'reference_number' => $this->reference_number,
        'active' => $this->active,
        ];
    }
}
