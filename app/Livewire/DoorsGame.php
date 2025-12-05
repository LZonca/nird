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
    public $questionNumber = 1; // Numéro de la question actuelle
    public $totalQuestions = 3; // Nombre total de questions par session

    public function mount()
    {
        $this->loadNewQuestion();
    }

    public function loadNewQuestion()
    {
        $user = auth()->user();

        // Récupérer les IDs des questions déjà résolues
        $resolvedQuestionIds = $user ? $user->resolvedQuestions()->pluck('question_id')->toArray() : [];

        // Essayer d'abord de trouver une question non résolue
        $question = Question::with('reponses')
            ->whereNotIn('id', $resolvedQuestionIds)
            ->inRandomOrder()
            ->first();

        // Si toutes les questions sont résolues, choisir n'importe quelle question
        if (!$question) {
            \Log::info('📝 Toutes les questions ont été résolues, rechargement d\'une question déjà jouée');
            $question = Question::with('reponses')->inRandomOrder()->first();
        }

        if ($question) {
            $this->currentQuestion = $question;
            // Mélanger les réponses pour qu'elles ne soient pas toujours dans le même ordre
            $this->reponses = $question->reponses->shuffle();
            $this->showGame = true;

            \Log::info('🎯 Question chargée:', [
                'question_id' => $question->id,
                'deja_resolue' => in_array($question->id, $resolvedQuestionIds)
            ]);
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

        // Marquer la question comme résolue si l'utilisateur a gagné
        if ($this->resultType === 'gain' && auth()->check()) {
            $user = auth()->user();

            // Vérifier si la question n'a pas déjà été résolue
            if (!$user->hasResolvedQuestion($this->currentQuestion->id)) {
                $user->resolvedQuestions()->attach($this->currentQuestion->id, [
                    'resolved_at' => now()
                ]);
                \Log::info('✅ Question résolue et marquée en BDD:', [
                    'user_id' => $user->id,
                    'question_id' => $this->currentQuestion->id
                ]);
            } else {
                \Log::info('ℹ️ Question déjà résolue auparavant:', [
                    'user_id' => $user->id,
                    'question_id' => $this->currentQuestion->id
                ]);
            }
        }

        $this->dispatch('answer-selected', resultType: $this->resultType, fundsEarned: $this->fundsEarned);
    }

    public function nextQuestion()
    {
        $this->reset(['selectedAnswer', 'resultType', 'fundsEarned', 'showResult']);
        $this->dispatch('close-result-modal');

        \Log::info('📊 Question suivante:', [
            'question_actuelle' => $this->questionNumber,
            'total_questions' => $this->totalQuestions
        ]);

        // Si on a fini les 3 questions, retourner au plateau
        if ($this->questionNumber >= $this->totalQuestions) {
            \Log::info('✅ Session terminée - Retour au plateau');
            $this->dispatch('retour-plateau');
        } else {
            // Sinon, charger la question suivante
            $this->questionNumber++;
            \Log::info('➡️ Chargement question ' . $this->questionNumber);
            $this->loadNewQuestion();
        }
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
