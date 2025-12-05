<?php

namespace App\Livewire;

use Livewire\Component;

class PlateauJeu extends Component
{
    public $caseActuelle; // Position actuelle du joueur (chargée depuis la BDD)
    public $nombreCases = 10; // Nombre total de cases
    public $rayon = 200; // Rayon du circuit en pixels
    public $yearActuel; // Compteur de tours

    public function mount()
    {
        // Charger la position actuelle du joueur depuis la BDD
        $user = auth()->user();
        // Valeur par défaut : dernière case (base) = $nombreCases - 1
        $this->caseActuelle = $user->position !== null ? $user->position : ($this->nombreCases - 1);
        $this->yearActuel = $user->year ?? 1; // Par défaut tour 1

        \Log::info('🚀 MOUNT PlateauJeu - Chargement initial:', [
            'user_id' => $user->id,
            'position_bdd' => $user->position,
            'year_bdd' => $user->year,
            'caseActuelle' => $this->caseActuelle,
            'yearActuel' => $this->yearActuel,
            'nombreCases' => $this->nombreCases,
            'derniere_case' => $this->nombreCases - 1
        ]);
    }

    public function avancer()
    {
        \Log::info('🚶 === AVANCER ===');
        \Log::info('Position avant avancer:', ['caseActuelle' => $this->caseActuelle]);

        // Avancer d'une case
        $nouvellePosition = ($this->caseActuelle + 1) % $this->nombreCases;
        \Log::info('Calcul nouvelle position:', [
            'ancienne' => $this->caseActuelle,
            'nouvelle' => $nouvellePosition,
        ]);

        // Sauvegarder la nouvelle position en BDD
        $user = auth()->user();
        $user->position = $nouvellePosition;

        // Si on revient à la case base (dernière case), incrémenter le compteur de tours
        if ($nouvellePosition === ($this->nombreCases - 1)) {
            $user->year = ($user->year ?? 1) + 1;
            $this->yearActuel = $user->year;
            \Log::info('🏁 Tour complété! Nouveau year:', ['year' => $this->yearActuel, 'case_base' => $this->nombreCases - 1]);
        }

        $user->save();
        \Log::info('💾 Position sauvegardée en BDD:', ['position' => $user->position]);

        // Mettre à jour la position locale
        $this->caseActuelle = $nouvellePosition;

        // Dispatcher l'événement pour déplacer le joueur visuellement
        \Log::info('📡 Dispatch event deplacer-joueur:', [
            'caseIndex' => $this->caseActuelle,
            'rayon' => $this->rayon,
            'nombreCases' => $this->nombreCases,
            'nombreSauts' => 1
        ]);

        $this->dispatch('deplacer-joueur',
            caseIndex: $this->caseActuelle,
            rayon: $this->rayon,
            nombreCases: $this->nombreCases,
            nombreSauts: 1
        );

        // Après l'animation de déplacement, déclencher un mini-jeu aléatoire
        // On attend 500ms pour que l'animation soit terminée
        $this->dispatch('attendre-fin-deplacement');
    }

    public function declencherMiniJeu()
    {
        // Ne pas déclencher de mini-jeu sur la case base
        if ($this->caseActuelle === ($this->nombreCases - 1)) {
            \Log::info('🏠 Case base - Pas de mini-jeu');
            return;
        }

        // Liste des mini-jeux disponibles
        $miniJeux = ['door-game']; // On pourra ajouter d'autres jeux plus tard

        // Choisir un mini-jeu aléatoirement
        $miniJeuChoisi = $miniJeux[array_rand($miniJeux)];

        \Log::info('🎮 Déclenchement mini-jeu:', ['jeu' => $miniJeuChoisi, 'case' => $this->caseActuelle]);

        // Rediriger vers le mini-jeu en utilisant la méthode Livewire
        $this->redirect(route($miniJeuChoisi), navigate: false);
    }

    public function render()
    {
        return view('livewire.plateau-jeu')
            ->title('Plateau de Jeu');
    }
}
