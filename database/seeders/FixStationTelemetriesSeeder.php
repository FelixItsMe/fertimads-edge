<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixStationTelemetriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $telemetries = [];

        $now = now();

        for ($i = 0; $i < 10; $i++) {
            $telemetries[] = [
                "garden_id" => "1",
                "samples" => json_encode([
                    "Humidity" => fake()->randomFloat(2, 0, 99),
                    "Temperature" => fake()->randomFloat(2, 17, 40),
                    "Ec" => fake()->randomFloat(2, 0, 99),
                    "Ph" => fake()->randomFloat(2, 0, 9),
                    "Nitrogen" => fake()->randomFloat(2, 0, 9),
                    "Phosporus" => fake()->randomFloat(2, 0, 9),
                    "Kalium" => fake()->randomFloat(2, 0, 10)
                ]),
                "created_at" => $now->addSeconds($i)
            ];
        }

        DB::table('fix_stations')
            ->insert($telemetries);
    }
}
