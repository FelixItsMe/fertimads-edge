<?php

namespace Database\Seeders;

use App\Models\CloudSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CloudSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (CloudSetting::count() == 0) {
            CloudSetting::create([
                'url' => 'http://fertimads.test/api/edge/fix-station/c3e7d9ff-91b3-4589-8463-7be0df8279d1',
                'headers' => (object) [
                    'X-Fertimads-Edge' => config('edge.token', null)
                ]
            ]);
        }
    }
}
