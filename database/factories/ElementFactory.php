<?php

namespace Database\Factories;

use App\Models\Element;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ElementFactory extends Factory
{
    protected $model = Element::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'level' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
