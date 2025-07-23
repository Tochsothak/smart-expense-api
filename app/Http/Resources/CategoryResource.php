<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=> $this->uuid,
            'name'=> $this->name,
            'code'=>$this->code,
            'description'=>$this->description,
            'colour_code'=> $this-> colour_code,
            'active' => $this->active ,
        ];
    }
}
