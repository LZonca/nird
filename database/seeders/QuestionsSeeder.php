<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Reponse;

class QuestionsSeeder extends Seeder
{
    public function run(): void
    {
        $path = storage_path('app/questions.json');

        if (!file_exists($path)) {
            $this->command->error("❌ Fichier introuvable : $path");
            return;
        }

        $json = file_get_contents($path);
        $data = json_decode($json, true);

        if (!$data) {
            $this->command->error("❌ JSON invalide : " . json_last_error_msg());
            return;
        }

        $this->command->info("📥 Importation de ".count($data)." questions...");

        // Effacer les tables avant d'insérer
        Reponse::truncate();
        Question::truncate();

        foreach ($data as $entry) {

            // Créer la question
            $question = Question::create([
                'contexte' => $entry['question'],
                'indice'   => $entry['indice'] ?? null,
            ]);

            // Créer les réponses
            foreach ($entry['reponses'] as $rep) {
                Reponse::create([
                    'question_id' => $question->id,
                    'proposition' => $rep['proposition'],
                    'resultat'    => (bool) $rep['resultat'],
                    'correction'  => $rep['correction'] ?? null,
                ]);
            }
        }

        $this->command->info("🎉 Importation terminée avec succès !");
    }
}
