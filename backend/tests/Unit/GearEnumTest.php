<?php

namespace Tests\Unit;

use App\Enums\GearCategory;
use App\Enums\GearCondition;
use PHPUnit\Framework\TestCase;

class GearEnumTest extends TestCase
{
    public function test_category_options_expose_value_and_label(): void
    {
        $options = GearCategory::options();

        $this->assertCount(count(GearCategory::cases()), $options);
        $this->assertSame(['value' => 'tent', 'label' => 'Tenda'], $options[0]);
    }

    public function test_condition_labels_are_in_indonesian(): void
    {
        $this->assertSame('Perlu Perbaikan', GearCondition::NeedsRepair->label());
    }
}
