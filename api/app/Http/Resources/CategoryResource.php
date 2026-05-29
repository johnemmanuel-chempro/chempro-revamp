<?php

namespace App\Http\Resources;

use App\Support\OpenCartImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->category_id,
            'parent_id' => (int) $this->parent_id,
            'name' => $this->whenLoaded('description', fn () => $this->description?->name),
            'description' => $this->whenLoaded('description', fn () => $this->description?->description),
            'image' => OpenCartImage::url($this->image),
            'sort_order' => (int) $this->sort_order,
        ];
    }
}
