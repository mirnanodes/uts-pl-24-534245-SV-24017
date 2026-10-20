<?php

namespace App\Http\Resources;

use App\Models\Gear;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Gear
 */
class GearResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->category->value,
            'category_label' => $this->category->label(),
            'brand' => $this->brand,
            'daily_price' => $this->daily_price,
            'stock' => $this->stock,
            'condition' => $this->condition->value,
            'condition_label' => $this->condition->label(),
            'description' => $this->description,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
