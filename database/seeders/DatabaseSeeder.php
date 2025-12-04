<?php

namespace Database\Seeders;

use App\Models\Base;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();



        Base::create([
            'name' => 'Default Base',
        ]);

        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123456789'),
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'base_id' => Base::first()->id,
        ]);
    }
}
