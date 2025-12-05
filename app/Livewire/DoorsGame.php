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
    public $resultType = null; // 'gain', 'neutral', 'trap'
    public $fundsEarned = 0;
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
        $nombreReponses = count($this->reponses);

        // Déterminer le résultat selon les probabilités
        $random = rand(1, $nombreReponses);

        if ($random === 1) {
            // Cas rare (1/nombre de réponses) : piège - perte de funds
            $this->resultType = 'trap';
            $this->fundsEarned = rand(3, 5); // Montant perdu

            // Retirer les funds à l'utilisateur
            if (auth()->check()) {
                $user = auth()->user();
                $user->funds = max(0, ($user->funds ?? 0) - $this->fundsEarned); // Ne pas descendre en dessous de 0
                $user->save();
            }
        } else {
            // Autres cas : 50% gain, 50% neutre
            $randomChance = rand(1, 2);
            if ($randomChance === 1) {
                // Gain : entre 3 et 10 funds
                $this->resultType = 'gain';
                $this->fundsEarned = rand(3, 10);

                // Ajouter les funds à l'utilisateur
                if (auth()->check()) {
                    $user = auth()->user();
                    $user->funds = ($user->funds ?? 0) + $this->fundsEarned;
                    $user->save();
                }
            } else {
                // Neutre : rien
                $this->resultType = 'neutral';
                $this->fundsEarned = 0;
            }
        }

        $this->showResult = true;
        $this->dispatch('answer-selected', resultType: $this->resultType, fundsEarned: $this->fundsEarned);
    }

    public function nextQuestion()
    {
        $this->reset(['selectedAnswer', 'resultType', 'fundsEarned', 'showResult']);
        $this->loadNewQuestion();
        $this->dispatch('close-result-modal');
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
