<?php

namespace Database\Factories;

use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition()
    {
        return [
            'contexte' => $this->faker->sentence(),
            'indice' => $this->faker->sentence(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
