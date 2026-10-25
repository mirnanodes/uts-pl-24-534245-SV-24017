<?php

namespace App\Models;

use App\Enums\GearCategory;
use App\Enums\GearCondition;
use Database\Factories\GearFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A piece of hiking equipment available for rent.
 */
class Gear extends Model
{
    /** @use HasFactory<GearFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'category',
        'brand',
        'daily_price',
        'stock',
        'condition',
        'description',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => GearCategory::class,
            'condition' => GearCondition::class,
            'daily_price' => 'integer',
            'stock' => 'integer',
        ];
    }

    /**
     * Filter by a case-insensitive keyword on name or brand.
     *
     * @param  Builder<Gear>  $query
     */
    public function scopeSearch(Builder $query, ?string $keyword): void
    {
        if (blank($keyword)) {
            return;
        }

        $query->where(function (Builder $query) use ($keyword) {
            $query->where('name', 'like', "%{$keyword}%")
                ->orWhere('brand', 'like', "%{$keyword}%");
        });
    }

    /**
     * @param  Builder<Gear>  $query
     */
    public function scopeOfCategory(Builder $query, ?string $category): void
    {
        if (blank($category)) {
            return;
        }

        $query->where('category', $category);
    }
}
