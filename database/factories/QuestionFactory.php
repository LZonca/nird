<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\Reponse;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition()
    {
        return [
            'contexte' => $this->faker->sentence() . '?',
            'indice' => $this->faker->optional(0.7)->sentence(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    /**
     * Configure la factory pour créer automatiquement des réponses
     */
    public function configure()
    {
        return $this->afterCreating(function (Question $question) {
            $nombreReponses = $this->faker->numberBetween(3, 4);
            $indexBonneReponse = $this->faker->numberBetween(0, $nombreReponses - 1);

            for ($i = 0; $i < $nombreReponses; $i++) {
                Reponse::create([
                    'question_id' => $question->id,
                    'proposition' => $this->faker->sentence(4),
                    'resultat' => ($i === $indexBonneReponse), // Une seule bonne réponse
                    'correction' => ($i === $indexBonneReponse)
                        ? $this->faker->optional(0.8)->sentence()
                        : null,
                ]);
            }
        });
    }
}
