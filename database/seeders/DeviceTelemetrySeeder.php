<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\DeviceTelemetry;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeviceTelemetrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $devices = Device::query()
            ->pluck('id');

        if (count($devices) > 0) {
            $MAX_COUNT = ceil(2500 / count($devices));
            $now = now();

            $values = [];
            foreach ($devices as $device) {
                for ($i=0; $i < $MAX_COUNT; $i++) {
                    $values[] = [
                        "device_id" => $device,
                        'telemetry' => json_encode(
                            (object) [
                                "FM1" => (object) [
                                    "F" => 0,
                                    "V" => 0
                                ],
                                "FM2" => (object) [
                                    "F" => 0,
                                    "V" => 0
                                ],
                                "FM3" => (object) [
                                    "F" => 0,
                                    "V" => 0
                                ],
                                "FM4" => (object) [
                                    "F" => 0,
                                    "V" => 0
                                ],
                                "SS1" => (object) [
                                    "H" => fake()->numberBetween(0, 100),
                                    "K" => fake()->numberBetween(0, 100),
                                    "N" => fake()->numberBetween(0, 100),
                                    "P" => fake()->numberBetween(0, 100),
                                    "T" => fake()->numberBetween(15, 60),
                                    "EC" => fake()->numberBetween(0, 100),
                                    "pH" => fake()->numberBetween(0, 100),
                                ],
                                "SS2" => (object) [
                                    "H" => fake()->numberBetween(0, 100),
                                    "K" => fake()->numberBetween(0, 100),
                                    "N" => fake()->numberBetween(0, 100),
                                    "P" => fake()->numberBetween(0, 100),
                                    "T" => fake()->numberBetween(15, 60),
                                    "EC" => fake()->numberBetween(0, 100),
                                    "pH" => fake()->numberBetween(0, 100),
                                ],
                                "SS3" => (object) [
                                    "H" => fake()->numberBetween(0, 100),
                                    "K" => fake()->numberBetween(0, 100),
                                    "N" => fake()->numberBetween(0, 100),
                                    "P" => fake()->numberBetween(0, 100),
                                    "T" => fake()->numberBetween(15, 60),
                                    "EC" => fake()->numberBetween(0, 100),
                                    "pH" => fake()->numberBetween(0, 100),
                                ],
                                "SS4" => (object) [
                                    "H" => fake()->numberBetween(0, 100),
                                    "K" => fake()->numberBetween(0, 100),
                                    "N" => fake()->numberBetween(0, 100),
                                    "P" => fake()->numberBetween(0, 100),
                                    "T" => fake()->numberBetween(15, 60),
                                    "EC" => fake()->numberBetween(0, 100),
                                    "pH" => fake()->numberBetween(0, 100),
                                ],
                                "DHT1" => (object) [
                                    "H" => fake()->numberBetween(0, 100),
                                    "T" => fake()->numberBetween(15, 60),
                                ],
                            ]
                        ),
                        'created_at' => $now
                    ];
                }
            }

            DeviceTelemetry::insert($values);
        }
    }
}
