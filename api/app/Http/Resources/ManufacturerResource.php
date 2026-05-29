<?php

namespace App\Http\Resources;

use App\Support\OpenCartImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ManufacturerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->manufacturer_id,
            'name' => $this->name,
            'image' => OpenCartImage::url($this->image),
            'sort_order' => (int) $this->sort_order,
        ];
    }
}
