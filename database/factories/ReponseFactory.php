<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\Reponse;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ReponseFactory extends Factory
{
    protected $model = Reponse::class;

    public function definition()
    {
        return [
            'proposition' => $this->faker->word(),
            'resultat' => $this->faker->boolean(),
            'correction' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'question_id' => Question::all()->random()->id,
        ];
    }
}
