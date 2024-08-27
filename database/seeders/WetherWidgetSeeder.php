<?php

namespace Database\Seeders;

use App\Models\WetherWidget;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WetherWidgetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (WetherWidget::count() == 0) {
            WetherWidget::create([
                'aws_device_id' => null,
                'open_api' => null
            ]);
        }
    }
}
