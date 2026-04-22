<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeocodingAddressTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_address_returns_empty_pois_when_overpass_payload_is_empty(): void
    {
        $user = User::factory()->create();
        $location = Location::create([
            'name' => 'Test Point',
            'latitude' => 14.5995,
            'longitude' => 120.9842,
        ]);

        Http::fake([
            'https://nominatim.openstreetmap.org/reverse*' => Http::response([
                'display_name' => 'Manila, Metro Manila, Philippines',
                'lat' => '14.5995',
                'lon' => '120.9842',
                'address' => [
                    'city' => 'Manila',
                    'state' => 'Metro Manila',
                    'country' => 'Philippines',
                ],
            ], 200),
            'https://api.open-meteo.com/v1/forecast*' => Http::response([
                'current_weather' => [
                    'temperature' => 30,
                    'windspeed' => 8,
                    'winddirection' => 180,
                    'weathercode' => 1,
                    'time' => '2026-04-22T12:00',
                    'is_day' => 1,
                ],
                'daily' => [
                    'temperature_2m_max' => [33],
                    'temperature_2m_min' => [25],
                ],
            ], 200),
            'https://api.open-elevation.com/api/v1/lookup*' => Http::response([
                'results' => [
                    ['elevation' => 16],
                ],
            ], 200),
            'http://overpass-api.de/api/interpreter*' => Http::response('', 200),
        ]);

        $response = $this->actingAs($user)
            ->getJson(route('admin.locations.get-address', $location));

        $response->assertOk();
        $response->assertJsonPath('full_address', 'Manila, Metro Manila, Philippines');
        $response->assertJsonPath('points_of_interst', []);
    }
}
