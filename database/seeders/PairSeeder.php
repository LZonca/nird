<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Models\Pair;

class PairSeeder extends Seeder
{
    public function run()
    {
        $json = Storage::disk('local')->get('pairs.json');
        $data = json_decode($json, true);

        Pair::truncate();

        foreach ($data as $item) {
            Pair::create([
                'gauche' => $item['gauche'],
                'droite' => $item['droite'],
                'theme'  => $item['theme'],
            ]);
        }
    }
}
