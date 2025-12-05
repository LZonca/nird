<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Question;

class DoorsGame extends Component
{
    public $currentQuestion;
    public $reponses = [];
    public $showGame = false;
    public $selectedAnswer = null;
    public $isCorrect = null;
    public $showResult = false;

    public function mount()
    {
        $this->loadNewQuestion();
    }

    public function loadNewQuestion()
    {
        // Choisir une question au hasard avec ses réponses
        $question = Question::with('reponses')->inRandomOrder()->first();

        if ($question) {
            $this->currentQuestion = $question;
            // Mélanger les réponses pour qu'elles ne soient pas toujours dans le même ordre
            $this->reponses = $question->reponses->shuffle();
            $this->showGame = true;
        }
    }

    public function selectAnswer($reponseId)
    {
        if ($this->showResult) {
            return; // Ne pas permettre de changer la réponse après validation
        }

        $this->selectedAnswer = $reponseId;

        // Vérifier si la réponse est correcte
        $reponse = collect($this->reponses)->firstWhere('id', $reponseId);
        $this->isCorrect = $reponse['resultat'] ?? false;
        $this->showResult = true;

        // Attendre 3 secondes puis charger une nouvelle question
        $this->dispatch('answer-selected', isCorrect: $this->isCorrect);
    }

    public function nextQuestion()
    {
        $this->reset(['selectedAnswer', 'isCorrect', 'showResult']);
        $this->loadNewQuestion();
    }

    #[On('player-on-door')]
    public function triggerGame()
    {
        $this->loadNewQuestion();
        $this->dispatch('start-doors-game');
    }

    public function render()
    {
        return view('livewire.doors-game');
    }
}
