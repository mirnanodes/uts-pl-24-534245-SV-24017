<?php

namespace Database\Seeders;

use App\Enums\GearCategory;
use App\Enums\GearCondition;
use App\Models\Gear;
use Illuminate\Database\Seeder;

class GearSeeder extends Seeder
{
    /**
     * Seed a realistic starter catalogue, only when the table is empty.
     */
    public function run(): void
    {
        if (Gear::query()->exists()) {
            return;
        }

        $gears = [
            ['Tenda Dome Kapasitas 4 Orang', GearCategory::Tent, 'Consina', 45000, 5, GearCondition::Good, 'Double layer, frame alumunium, cocok untuk camping keluarga.'],
            ['Tenda Ultralight 2 Orang', GearCategory::Tent, 'Naturehike', 40000, 3, GearCondition::Good, 'Berat 1,6 kg, cocok untuk pendakian cepat.'],
            ['Carrier 60L', GearCategory::Backpack, 'Eiger', 35000, 6, GearCondition::Good, 'Dilengkapi rain cover dan back system yang bisa diatur.'],
            ['Carrier 45L', GearCategory::Backpack, 'Arei', 30000, 4, GearCondition::NeedsRepair, 'Resleting samping perlu diganti.'],
            ['Sleeping Bag Polar', GearCategory::SleepingBag, 'Rei', 15000, 10, GearCondition::Good, 'Nyaman hingga suhu 10°C.'],
            ['Sleeping Bag Mummy', GearCategory::SleepingBag, 'Naturehike', 25000, 4, GearCondition::Good, 'Comfort hingga 0°C, untuk gunung tinggi.'],
            ['Kompor Portable + Gas', GearCategory::Cookware, 'Kovar', 15000, 8, GearCondition::Good, 'Termasuk satu tabung gas 230 g.'],
            ['Nesting Set 2-3 Orang', GearCategory::Cookware, 'DS-308', 12000, 7, GearCondition::Good, 'Panci, wajan, dan mangkuk lipat.'],
            ['Headlamp 300 Lumen', GearCategory::Lighting, 'Petzl', 10000, 12, GearCondition::Good, 'Baterai isi ulang via USB-C.'],
            ['Trekking Pole (Sepasang)', GearCategory::Other, 'Consina', 15000, 6, GearCondition::Damaged, 'Satu pengunci rusak, menunggu suku cadang.'],
        ];

        foreach ($gears as [$name, $category, $brand, $price, $stock, $condition, $description]) {
            Gear::create([
                'name' => $name,
                'category' => $category,
                'brand' => $brand,
                'daily_price' => $price,
                'stock' => $stock,
                'condition' => $condition,
                'description' => $description,
            ]);
        }
    }
}
