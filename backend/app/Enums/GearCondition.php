<?php

namespace App\Enums;

enum GearCondition: string
{
    case Good = 'good';
    case NeedsRepair = 'needs_repair';
    case Damaged = 'damaged';

    public function label(): string
    {
        return match ($this) {
            self::Good => 'Baik',
            self::NeedsRepair => 'Perlu Perbaikan',
            self::Damaged => 'Rusak',
        };
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $case) => ['value' => $case->value, 'label' => $case->label()],
            self::cases(),
        );
    }
}
