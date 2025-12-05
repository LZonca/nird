<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Models\Element;

class ElementSeeder extends Seeder
{
    public function run()
    {
        // Charger le fichier JSON dans storage/app/elements.json
        $json = Storage::disk('local')->get('elements.json');

        $data = json_decode($json, true);

        // Insérer chaque élément
        foreach ($data as $item) {
            Element::create([
                'name'      => $item['name'],
                'level_max' => $item['level_max'],
                'url'       => $item['url'],
            ]);
        }
    }
}
