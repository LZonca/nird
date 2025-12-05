<?php

namespace Database\Seeders;

use App\Models\Base;
use App\Models\Question;
use App\Models\Reponse;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Factories\QuestionFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Créer 30 questions (chacune aura automatiquement 3-4 réponses créées)
        //Question::factory(30)->create();
        $this->call([
            QuestionsSeeder::class,
            ElementSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123456789'),
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ]);
        $this->call([
            PairSeeder::class,
        ]);
        $this->call(PairSeeder::class);


    }
}
