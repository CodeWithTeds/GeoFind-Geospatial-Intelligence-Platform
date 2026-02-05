<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Faker\Factory as Faker;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Rollback functionality: Clear existing questions
        Schema::disableForeignKeyConstraints();
        DB::table('questions')->truncate();
        Schema::enableForeignKeyConstraints();
        
        $faker = Faker::create();

        // Historical Locations in the Philippines
        $locations = [
            [
                'title' => 'Rizal Park (Luneta)',
                'description' => 'Where national hero Jose Rizal was executed in 1896.',
                'lat' => 14.5826,
                'lng' => 120.9787
            ],
            [
                'title' => 'Intramuros (Fort Santiago)',
                'description' => 'The oldest district and historic core of Manila.',
                'lat' => 14.5942,
                'lng' => 120.9704
            ],
            [
                'title' => 'Aguinaldo Shrine',
                'description' => 'Site of the Philippine Declaration of Independence in 1898.',
                'lat' => 14.4453,
                'lng' => 120.9213
            ],
            [
                'title' => 'Barasoain Church',
                'description' => 'Site of the First Philippine Republic.',
                'lat' => 14.8526,
                'lng' => 120.8146
            ],
            [
                'title' => 'Magellan\'s Cross',
                'description' => 'Christian cross planted by explorers in 1521.',
                'lat' => 10.2936,
                'lng' => 123.9019
            ],
            [
                'title' => 'MacArthur Landing Memorial',
                'description' => 'Where General Douglas MacArthur fulfilled his promise to return.',
                'lat' => 11.1712,
                'lng' => 125.0125
            ],
            [
                'title' => 'Corregidor Island',
                'description' => 'Historic fortress guarding the entrance to Manila Bay.',
                'lat' => 14.3853,
                'lng' => 120.5732
            ],
            [
                'title' => 'Dapitan City',
                'description' => 'Place of Jose Rizal\'s exile.',
                'lat' => 8.6534,
                'lng' => 123.4251
            ],
            [
                'title' => 'Calle Crisologo',
                'description' => 'Famous preserved Spanish colonial street in Vigan.',
                'lat' => 17.5714,
                'lng' => 120.3892
            ],
            [
                'title' => 'EDSA Shrine',
                'description' => 'Monument to the People Power Revolution of 1986.',
                'lat' => 14.5916,
                'lng' => 121.0596
            ],
            [
                'title' => 'Mactan Shrine',
                'description' => 'Site of the Battle of Mactan.',
                'lat' => 10.3113,
                'lng' => 124.0152
            ],
            [
                'title' => 'Biak-na-Bato National Park',
                'description' => 'Mountain hideout of revolutionary forces.',
                'lat' => 15.1118,
                'lng' => 121.0805
            ],
            [
                'title' => 'San Agustin Church',
                'description' => 'The oldest stone church in the Philippines.',
                'lat' => 14.5891,
                'lng' => 120.9750
            ],
            [
                'title' => 'Manila Cathedral',
                'description' => 'The mother church of the Philippines.',
                'lat' => 14.5917,
                'lng' => 120.9733
            ],
            [
                'title' => 'Blood Compact Shrine',
                'description' => 'Site of the first treaty of friendship between Spaniards and Filipinos.',
                'lat' => 9.6644,
                'lng' => 123.8741
            ],
            [
                'title' => 'Sultan Kudarat Monument',
                'description' => 'Monument honoring the Sultan of Maguindanao.',
                'lat' => 7.2223,
                'lng' => 124.2464
            ],
            [
                'title' => 'Quezon Memorial Circle',
                'description' => 'Mausoleum of Manuel L. Quezon.',
                'lat' => 14.6515,
                'lng' => 121.0493
            ],
            [
                'title' => 'Pinaglabanan Shrine',
                'description' => 'Commemorates the first battle of the Philippine Revolution.',
                'lat' => 14.6053,
                'lng' => 121.0306
            ],
            [
                'title' => 'Bataan Death March Marker',
                'description' => 'Marks the start of the infamous death march.',
                'lat' => 14.4402,
                'lng' => 120.5401
            ],
            [
                'title' => 'Zapote Bridge',
                'description' => 'Site of two major battles in Philippine history.',
                'lat' => 14.4633,
                'lng' => 120.9669
            ]
        ];

        // Difficulty settings per level range
        // Levels 1-10: Easy (Large tolerance)
        // Levels 11-20: Medium (Medium tolerance)
        // Levels 21-30: Hard (Small tolerance)

        for ($level = 1; $level <= 30; $level++) {
            
            // Determine difficulty settings based on level
            if ($level <= 10) {
                $difficulty = 'easy';
                $baseTolerance = 1000; // 1km
            } elseif ($level <= 20) {
                $difficulty = 'medium';
                $baseTolerance = 500; // 500m
            } else {
                $difficulty = 'hard';
                $baseTolerance = 100; // 100m
            }

            // Generate 1 question per level
            
            // Pick a deterministic location based on level to ensure variety across levels
            // Since we have 20 locations and 30 levels, we wrap around using modulo
            $locationIndex = ($level - 1) % count($locations);
            $location = $locations[$locationIndex];
            
            Question::create([
                'title' => $location['title'],
                'description' => $location['description'] . " [Level {$level} Challenge]",
                'answer_latitude' => $location['lat'],
                'answer_longitude' => $location['lng'],
                'tolerance_meters' => $baseTolerance - ($level * 2), // Slightly decrease tolerance as level increases
                'difficulty' => $difficulty,
                'level' => $level,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
