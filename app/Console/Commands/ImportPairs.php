<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Pair;

class ImportPairs extends Command
{
    protected $signature = 'import:pairs';
    protected $description = 'Importe les pairs depuis pairs.json';

    public function handle()
    {
        $path = 'pairs.json';

        // Debug chemin
        $this->info("Chemin utilisé : " . storage_path("app/$path"));
        $this->info("Fichier existe ? " . (file_exists(storage_path("app/$path")) ? 'oui' : 'non'));

        if (!Storage::disk('local')->exists($path)) {
            $this->error("❌ Le fichier storage/app/$path est introuvable.");
            return Command::FAILURE;
        }

        $json = Storage::disk('local')->get($path);
        $data = json_decode($json, true);

        if (!$data) {
            $this->error("❌ JSON invalide");
            return Command::FAILURE;
        }

        foreach ($data as $item) {
            Pair::create([
                'gauche' => $item['gauche'],
                'droite' => $item['droite'],
                'theme'  => $item['theme'],
            ]);
        }

        $this->info("✅ Import terminé : " . count($data) . " pairs ajoutées !");
        return Command::SUCCESS;
    }
}
