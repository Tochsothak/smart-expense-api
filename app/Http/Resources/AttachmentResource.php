<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
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
        'transaction_id' => $this->transaction_id,
        'filename' => $this->filename,
        'file_path' => $this->file_path,
        'file_size' => $this->file_size,
        'mime_type' => $this->mime_type,
        'file_url' => url('storage/' . $this->file_path), // Use url() instead of asset()
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
    ];
}
}
