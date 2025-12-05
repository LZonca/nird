<?php

namespace App\Livewire;

use Livewire\Component;

class PlateauJeu extends Component
{
    public $caseActuelle = 0; // Position actuelle du joueur (0-19)
    public $deResultat = null; // Résultat du dernier lancer de dé
    public $nombreCases = 20; // Nombre total de cases
    public $rayon = 200; // Rayon du circuit en pixels

    public function lancerDe()
    {
        // Lancer un dé (1-6)
        $this->deResultat = rand(1, 6);

        // Calculer la nouvelle position
        $this->caseActuelle = ($this->caseActuelle + $this->deResultat) % $this->nombreCases;

        // Dispatcher l'événement pour déplacer le joueur visuellement
        $this->dispatch('deplacer-joueur',
            caseIndex: $this->caseActuelle,
            rayon: $this->rayon,
            nombreCases: $this->nombreCases
        );

        // Cacher le résultat après 3 secondes
        $this->dispatch('cacher-de-resultat');
    }

    public function render()
    {
        return view('livewire.plateau-jeu')
            ->layout('components.layouts.game')
            ->title('Plateau de Jeu');
    }
}
