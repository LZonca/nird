<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Question;

class DoorsGame extends Component
{
    public $currentQuestion;

    #[On('player-on-door')]
    public function triggerGame()
    {
        // 1) Choisir une question au hasard
        $question = Question::with('reponses')->inRandomOrder()->first();

        $this->currentQuestion = $question;

        // 2) Envoyer la question au JS (mini-jeu)
        $this->dispatch('start-doors-game', question: $question);
    }

    public function render()
    {
        return view('livewire.doors-game');
    }
}
