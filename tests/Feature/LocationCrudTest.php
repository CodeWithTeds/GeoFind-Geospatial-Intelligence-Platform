<?php

namespace Tests\Feature;

use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_location(): void
    {
        $response = $this->post(route('locations.store'), [
            'name' => 'Test Point',
            'latitude' => 12.345678,
            'longitude' => 98.765432,
        ]);

        $response->assertRedirect(route('locations.index'));
        $this->assertDatabaseHas('locations', [
            'name' => 'Test Point',
            'latitude' => 12.345678,
            'longitude' => 98.765432,
        ]);
        $response->assertSessionHas('success');
    }

    public function test_can_update_location(): void
    {
        $location = Location::create([
            'name' => 'Original',
            'latitude' => 1.234567,
            'longitude' => 2.345678,
        ]);

        $response = $this->put(route('locations.update', $location), [
            'name' => 'Updated Name',
            'latitude' => 9.876543,
            'longitude' => 8.765432,
        ]);

        $response->assertRedirect(route('locations.index'));
        $this->assertDatabaseHas('locations', [
            'id' => $location->id,
            'name' => 'Updated Name',
            'latitude' => 9.876543,
            'longitude' => 8.765432,
        ]);
        $response->assertSessionHas('success');
    }
}
