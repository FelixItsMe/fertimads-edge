<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (User::query()->where('role', 'su')->count() == 0) {
            User::factory()->create([
                'name' => 'Super User',
                'role' => 'su',
                'email' => 'superbuser@example.com',
            ]);
        }

        User::factory()->create();

        User::factory()->create([
            'role' => 'control',
        ]);
        User::factory()->create([
            'role' => 'care',
        ]);
    }
}
