<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pair;

class PairSeeder extends Seeder
{
    public function run(): void
    {
        $pairs = [
            // Thème : Animaux
            ['gauche' => 'Chien',      'droite' => 'Aboie',        'theme' => 'animaux'],
            ['gauche' => 'Chat',       'droite' => 'Miaule',       'theme' => 'animaux'],
            ['gauche' => 'Vache',      'droite' => 'Meugle',       'theme' => 'animaux'],
            ['gauche' => 'Mouton',     'droite' => 'Bêle',         'theme' => 'animaux'],

            // Thème : Pays et Capitales
            ['gauche' => 'France',     'droite' => 'Paris',        'theme' => 'pays'],
            ['gauche' => 'Italie',     'droite' => 'Rome',         'theme' => 'pays'],
            ['gauche' => 'Japon',      'droite' => 'Tokyo',        'theme' => 'pays'],
            ['gauche' => 'Canada',     'droite' => 'Ottawa',       'theme' => 'pays'],

            // Thème : Maths
            ['gauche' => '2 + 2',      'droite' => '4',            'theme' => 'maths'],
            ['gauche' => '5 × 3',      'droite' => '15',           'theme' => 'maths'],
            ['gauche' => '9 − 7',      'droite' => '2',            'theme' => 'maths'],
            ['gauche' => '12 ÷ 4',     'droite' => '3',            'theme' => 'maths'],
        ];

        foreach ($pairs as $pair) {
            Pair::create($pair);
        }
    }
}
