<?php

namespace App\Enums;

enum GearCategory: string
{
    case Tent = 'tent';
    case Backpack = 'backpack';
    case SleepingBag = 'sleeping_bag';
    case Cookware = 'cookware';
    case Lighting = 'lighting';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Tent => 'Tenda',
            self::Backpack => 'Carrier',
            self::SleepingBag => 'Sleeping Bag',
            self::Cookware => 'Alat Masak',
            self::Lighting => 'Penerangan',
            self::Other => 'Lainnya',
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
