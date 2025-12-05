<?php

namespace App\Livewire;

use Livewire\Component;

class PlateauJeu extends Component
{
    public $caseActuelle; // Position actuelle du joueur (chargée depuis la BDD)
    public $nombreCases = 10; // Nombre total de cases
    public $rayon = 220; // Rayon du circuit en pixels
    public $yearActuel; // Compteur de tours
    public $isMoving = false; // Indique si le joueur est en train de se déplacer
    public $elements = []; // Éléments de la base avec leur niveau

    public function mount()
    {
        // Charger la position actuelle du joueur depuis la BDD
        $user = auth()->user();
        // Valeur par défaut : dernière case (base) = $nombreCases - 1
        $this->caseActuelle = $user->position !== null ? $user->position : ($this->nombreCases - 1);
        $this->yearActuel = $user->year ?? 1; // Par défaut tour 1

        // Charger les éléments de la base
        $this->loadElements();

        \Log::info('🚀 MOUNT PlateauJeu - Chargement initial:', [
            'user_id' => $user->id,
            'position_bdd' => $user->position,
            'year_bdd' => $user->year,
            'caseActuelle' => $this->caseActuelle,
            'yearActuel' => $this->yearActuel,
            'nombreCases' => $this->nombreCases,
            'derniere_case' => $this->nombreCases - 1,
            'elements_count' => count($this->elements)
        ]);
    }

    public function loadElements()
    {
        $user = auth()->user();

        if (!$user->base_id) {
            $this->elements = [];
            return;
        }

        // Récupérer les éléments initialisés pour cette base
        $this->elements = \DB::table('base_element')
            ->join('elements', 'base_element.element_id', '=', 'elements.id')
            ->where('base_element.base_id', $user->base_id)
            ->where('base_element.level', '>', 0)
            ->select('elements.name', 'elements.url', 'base_element.level')
            ->get()
            ->map(function($element) {
                return [
                    'name' => $element->name,
                    'url' => $element->url,
                    'level' => (int) $element->level,
                    'is_tree' => str_contains(strtolower($element->name), 'arbre') || str_contains(strtolower($element->name), 'tree'),
                    'is_radio' => str_contains(strtolower($element->name), 'radio')
                ];
            })
            ->toArray();
    }

    public function avancer()
    {
        // Empêcher les clics multiples
        if ($this->isMoving) {
            \Log::warning('⚠️ Déplacement déjà en cours - clic ignoré');
            return;
        }

        $this->isMoving = true;

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
        // Sur la case base, rediriger vers la page d'amélioration
        if ($this->caseActuelle === ($this->nombreCases - 1)) {
            \Log::info('🏠 Case base - Redirection vers amélioration');
            $this->redirect(route('base-upgrade'), navigate: false);
            return;
        }

        // Liste des mini-jeux disponibles
        $miniJeux = ['door-game', 'pairs-game'];

        // Choisir un mini-jeu aléatoirement
        $miniJeuChoisi = $miniJeux[array_rand($miniJeux)];

        \Log::info('🎮 Déclenchement mini-jeu aléatoire:', [
            'jeu' => $miniJeuChoisi,
            'case' => $this->caseActuelle,
            'jeux_disponibles' => $miniJeux
        ]);

        // Le bouton restera désactivé jusqu'à ce qu'on revienne du mini-jeu (le composant sera réinitialisé)
        // Rediriger vers le mini-jeu en utilisant la méthode Livewire
        $this->redirect(route($miniJeuChoisi), navigate: false);
    }

    public function render()
    {
        return view('livewire.plateau-jeu')
            ->title('Plateau de Jeu');
    }
}
