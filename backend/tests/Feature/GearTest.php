<?php

namespace Tests\Feature;

use App\Enums\GearCategory;
use App\Enums\GearCondition;
use App\Models\Gear;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GearTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(User::factory()->create());
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Tenda Dome 4P',
            'category' => GearCategory::Tent->value,
            'brand' => 'Consina',
            'daily_price' => 45000,
            'stock' => 5,
            'condition' => GearCondition::Good->value,
            'description' => 'Double layer, frame alumunium.',
        ], $overrides);
    }

    public function test_can_list_gears_with_pagination(): void
    {
        Gear::factory()->count(15)->create();

        $this->getJson('/api/gears?per_page=10')
            ->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.total', 15)
            ->assertJsonStructure([
                'data' => [['id', 'name', 'category', 'category_label', 'daily_price', 'stock', 'condition', 'condition_label']],
                'links',
                'meta',
            ]);
    }

    public function test_can_search_gears_by_name(): void
    {
        Gear::factory()->create(['name' => 'Headlamp 300 Lumen', 'brand' => 'Petzl']);
        Gear::factory()->create(['name' => 'Carrier 60L', 'brand' => 'Eiger']);

        $this->getJson('/api/gears?search=headlamp')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Headlamp 300 Lumen');
    }

    public function test_can_show_single_gear(): void
    {
        $gear = Gear::factory()->create();

        $this->getJson("/api/gears/{$gear->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $gear->id)
            ->assertJsonPath('data.name', $gear->name);
    }

    public function test_show_returns_404_for_missing_gear(): void
    {
        $this->getJson('/api/gears/999')
            ->assertNotFound()
            ->assertJsonPath('message', 'Data tidak ditemukan.');
    }

    public function test_can_create_gear(): void
    {
        $this->postJson('/api/gears', $this->validPayload())
            ->assertCreated()
            ->assertJsonPath('message', 'Alat berhasil ditambahkan.')
            ->assertJsonPath('data.name', 'Tenda Dome 4P')
            ->assertJsonPath('data.category_label', 'Tenda');

        $this->assertDatabaseHas('gears', [
            'name' => 'Tenda Dome 4P',
            'category' => 'tent',
            'daily_price' => 45000,
        ]);
    }

    public function test_create_gear_returns_422_when_validation_fails(): void
    {
        $this->postJson('/api/gears', [
            'name' => '',
            'category' => 'kapal-selam',
            'daily_price' => -1,
            'stock' => 'banyak',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'category', 'daily_price', 'stock', 'condition']);

        $this->assertDatabaseCount('gears', 0);
    }

    public function test_can_update_gear(): void
    {
        $gear = Gear::factory()->create();

        $this->putJson("/api/gears/{$gear->id}", $this->validPayload([
            'name' => 'Tenda Dome 6P',
            'stock' => 2,
        ]))
            ->assertOk()
            ->assertJsonPath('message', 'Alat berhasil diperbarui.')
            ->assertJsonPath('data.name', 'Tenda Dome 6P')
            ->assertJsonPath('data.stock', 2);

        $this->assertDatabaseHas('gears', ['id' => $gear->id, 'name' => 'Tenda Dome 6P', 'stock' => 2]);
    }

    public function test_can_delete_gear(): void
    {
        $gear = Gear::factory()->create();

        $this->deleteJson("/api/gears/{$gear->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Alat berhasil dihapus.');

        $this->assertDatabaseMissing('gears', ['id' => $gear->id]);
    }

    public function test_can_fetch_gear_options(): void
    {
        $this->getJson('/api/gear-options')
            ->assertOk()
            ->assertJsonCount(count(GearCategory::cases()), 'data.categories')
            ->assertJsonCount(count(GearCondition::cases()), 'data.conditions');
    }
}
