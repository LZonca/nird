<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pair;

class PairGame extends Component
{
    public $pairs;      // Les pairs complètes (pour vérifier les correspondances)
    public $leftList;   // Liste gauche
    public $rightList;  // Liste droite mélangée

    public function mount()
    {
        // 1. Charger les pairs
        $this->pairs = Pair::inRandomOrder()->take(5)->get();

        // 2. Extraire la liste gauche : [ ['id'=>1,'text'=>'Chien'], ... ]
        $this->leftList = $this->pairs->map(function ($pair) {
            return [
                'id' => $pair->id,
                'text' => $pair->gauche
            ];
        });

        // 3. Extraire la liste droite puis mélanger : [ ['id'=>1,'text'=>'Aboie'], ... ]
        $this->rightList = $this->pairs->map(function ($pair) {
            return [
                'id' => $pair->id,
                'text' => $pair->droite
            ];
        })->shuffle()->values(); // values() réindexe proprement
    }

    public function render()
    {
        return view('livewire.pair-game');
    }
}
