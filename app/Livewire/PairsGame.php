<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pair;

class PairsGame extends Component
{
    public $pairs;
    public $leftCards = [];
    public $rightCards = [];

    public $selectedLeft = null;
    public $selectedRight = null;

    public $matches = 0;

    public $foundPairs = []; // ➜ Permet d'afficher l'icône ✔️

    public function mount()
    {
        $this->pairs = Pair::inRandomOrder()->limit(3)->get();

        // IMPORTANT : On indexe par l'ID pour éviter les collisions
        $this->leftCards = $this->pairs->map(fn($p) => ['id' => $p->id, 'text' => $p->gauche])
            ->shuffle()
            ->toArray();

        $this->rightCards = $this->pairs->map(fn($p) => ['id' => $p->id, 'text' => $p->droite])
            ->shuffle()
            ->toArray();
    }

    public function selectLeft($id)
    {
        $this->selectedLeft = $id;
        $this->checkMatch();
    }

    public function selectRight($id)
    {
        $this->selectedRight = $id;
        $this->checkMatch();
    }

    public function checkMatch()
    {
        if (!$this->selectedLeft || !$this->selectedRight) return;

        if ($this->selectedLeft == $this->selectedRight) {
            $this->matches++;

            $this->foundPairs[] = $this->selectedLeft;

            $this->leftCards = array_filter($this->leftCards, fn($c) => $c['id'] != $this->selectedLeft);
            $this->rightCards = array_filter($this->rightCards, fn($c) => $c['id'] != $this->selectedRight);

            $this->dispatch('match-found');

            // Vérifier si toutes les paires sont trouvées
            \Log::info('🔍 Vérification fin de jeu:', [
                'leftCards_count' => count($this->leftCards),
                'rightCards_count' => count($this->rightCards),
                'matches' => $this->matches
            ]);

            if (count($this->leftCards) === 0 && count($this->rightCards) === 0) {
                \Log::info('✅ TOUTES LES PAIRES TROUVÉES - Dispatch game-completed');
                $this->dispatch('game-completed');
                \Log::info('✅ Event game-completed dispatché');
            }
        } else {
            // ❌ mauvaise association → envoie un event JS
            $this->dispatch('wrong-pair');
        }

        $this->selectedLeft = null;
        $this->selectedRight = null;
    }

    public function render()
    {
        return view('livewire.pairs-game');
    }
}
