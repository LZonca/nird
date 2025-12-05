<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Question;
use App\Models\Reponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ImportQuestions extends Command
{
    /**
     * Nom de la commande.
     */
    protected $signature = 'import:questions
                            {--fresh : Vide les tables questions et réponses avant l\'import}';

    /**
     * Description.
     */
    protected $description = 'Importe les questions et réponses depuis un fichier JSON situé dans storage/app/questions.json';

    /**
     * Exécution de la commande.
     */
    public function handle()
    {
        $path = 'questions.json';
        $this->info("Chemin utilisé : " . storage_path("app/$path"));
        $this->info("Fichier existe ? " . (file_exists(storage_path("app/$path")) ? 'oui' : 'non'));

        if (!Storage::disk('local')->exists($path)) {
            $this->error("❌ Le fichier storage/app/{$path} est introuvable.");
            return Command::FAILURE;
        }

        $json = Storage::disk('local')->get($path);


        // Vérifier que le fichier existe
        if (!Storage::exists($path)) {
            $this->error("❌ Le fichier storage/app/{$path} est introuvable.");
            return Command::FAILURE;
        }

        // Option : vider les tables avant import
        if ($this->option('fresh')) {
            DB::table('reponses')->truncate();
            DB::table('questions')->truncate();
            $this->info("🔄 Tables vidées.");
        }

        // Charger et décoder le JSON
        $json = Storage::get($path);
        $data = json_decode($json, true);

        if ($data === null) {
            $this->error("❌ Le fichier JSON est invalide : " . json_last_error_msg());
            return Command::FAILURE;
        }

        $this->info("📥 Importation de " . count($data) . " questions...\n");

        foreach ($data as $index => $entry) {

            // Vérification minimale
            if (!isset($entry['question']) || !isset($entry['reponses'])) {
                $this->warn("⚠️ Entrée ignorée (structure incorrecte) à l'index $index.");
                continue;
            }

            // 1. Créer la question
            $question = Question::create([
                'contexte' => $entry['question'],      // Conversion JSON → modèle
                'indice'   => $entry['indice'] ?? null,
            ]);

            // 2. Créer les réponses liées
            foreach ($entry['reponses'] as $rep) {
                Reponse::create([
                    'question_id' => $question->id,
                    'proposition' => $rep['proposition'],
                    'resultat'    => (bool) $rep['resultat'],
                    'correction'  => $rep['correction'] ?? null,
                ]);
            }

            $this->info("✔️ Question importée : {$question->contexte}");
        }

        $this->newLine();
        $this->info("🎉 Import terminé avec succès !");
        return Command::SUCCESS;
    }
}
