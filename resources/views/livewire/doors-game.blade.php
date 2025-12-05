<div class="flex flex-col items-center justify-center min-h-screen bg-gradient-to-b from-blue-400 via-purple-500 to-pink-500 p-4">
    @if($showGame && $currentQuestion)
    <div class="w-full max-w-6xl">
        {{-- Titre du jeu --}}
        <div class="text-center mb-8 animate-bounce">
            <h1 class="text-5xl font-bold text-white drop-shadow-lg mb-2">🚪 Door Game 🚪</h1>
            <p class="text-xl text-white/90">Choisissez la bonne réponse !</p>
        </div>

        {{-- Affichage de la question --}}
        <div class="bg-white/95 rounded-3xl shadow-2xl p-8 mb-8 backdrop-blur-sm border-4 border-white/50 transform hover:scale-105 transition-transform">
            <div class="text-center">
                <div class="inline-block bg-gradient-to-r from-yellow-400 to-orange-400 text-white px-6 py-2 rounded-full font-bold mb-4">
                    QUESTION
                </div>
                <h2 class="text-3xl font-bold text-gray-800 mb-4">{{ $currentQuestion->contexte }}</h2>
                @if($currentQuestion->indice)
                    <p class="text-lg text-gray-600 italic">💡 Indice : {{ $currentQuestion->indice }}</p>
                @endif
            </div>
        </div>

        {{-- Affichage des portes avec les réponses --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            @foreach($reponses as $reponse)
                <button
                    wire:click="selectAnswer({{ $reponse->id }})"
                    @if($showResult) disabled @endif
                    class="door-button group relative transform transition-all duration-300 hover:scale-110
                           @if($showResult && $selectedAnswer === $reponse->id)
                               @if($isCorrect)
                                   ring-8 ring-green-400 scale-110
                               @else
                                   ring-8 ring-red-400 scale-110
                               @endif
                           @elseif($showResult && $reponse->resultat)
                               ring-8 ring-green-400
                           @endif
                           @if($showResult) cursor-not-allowed @else hover:shadow-2xl @endif"
                >
                    {{-- La porte --}}
                    <div class="relative aspect-[3/4] bg-gradient-to-br from-amber-700 via-amber-600 to-amber-800 rounded-3xl shadow-xl border-8 border-amber-900/50 overflow-hidden">
                        {{-- Texture de la porte --}}
                        <div class="absolute inset-4 border-4 border-amber-900/30 rounded-2xl"></div>

                        {{-- Poignée de porte --}}
                        <div class="absolute right-6 top-1/2 -translate-y-1/2 w-4 h-8 bg-yellow-600 rounded-full shadow-lg border-2 border-yellow-700"></div>

                        {{-- Numéro de la porte --}}
                        <div class="absolute top-4 left-1/2 -translate-x-1/2 w-16 h-16 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <span class="text-3xl font-bold text-white">{{ $loop->iteration }}</span>
                        </div>

                        {{-- Texte de la réponse --}}
                        <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/70 to-transparent">
                            <p class="text-white font-bold text-center text-lg leading-tight">{{ $reponse->proposition }}</p>
                        </div>

                        {{-- Icône de résultat --}}
                        @if($showResult)
                            @if($selectedAnswer === $reponse->id && $isCorrect)
                                <div class="absolute inset-0 flex items-center justify-center bg-green-500/90 backdrop-blur-sm animate-pulse">
                                    <span class="text-8xl">✓</span>
                                </div>
                            @elseif($selectedAnswer === $reponse->id && !$isCorrect)
                                <div class="absolute inset-0 flex items-center justify-center bg-red-500/90 backdrop-blur-sm animate-pulse">
                                    <span class="text-8xl">✗</span>
                                </div>
                            @elseif($reponse->resultat)
                                <div class="absolute inset-0 flex items-center justify-center bg-green-500/80 backdrop-blur-sm">
                                    <span class="text-6xl">✓</span>
                                </div>
                            @endif
                        @endif
                    </div>

                    {{-- Effet de hover --}}
                    @if(!$showResult)
                        <div class="absolute inset-0 bg-white/0 group-hover:bg-white/20 rounded-3xl transition-all duration-300 pointer-events-none"></div>
                    @endif
                </button>
            @endforeach
        </div>

        {{-- Message de résultat et correction --}}
        @if($showResult)
            <div class="bg-white/95 rounded-3xl shadow-2xl p-8 backdrop-blur-sm border-4 border-white/50 animate-fade-in">
                @if($isCorrect)
                    <div class="text-center">
                        <div class="text-7xl mb-4 animate-bounce">🎉</div>
                        <h3 class="text-4xl font-bold text-green-600 mb-2">Bravo !</h3>
                        <p class="text-xl text-gray-700">Bonne réponse !</p>
                    </div>
                @else
                    <div class="text-center">
                        <div class="text-7xl mb-4">😢</div>
                        <h3 class="text-4xl font-bold text-red-600 mb-2">Oups !</h3>
                        <p class="text-xl text-gray-700 mb-4">Ce n'est pas la bonne réponse...</p>
                        @php
                            $correctAnswer = collect($reponses)->firstWhere('resultat', true);
                        @endphp
                        @if($correctAnswer && $correctAnswer->correction)
                            <div class="bg-blue-50 rounded-xl p-4 mt-4">
                                <p class="font-semibold text-blue-800 mb-2">💡 Explication :</p>
                                <p class="text-gray-700">{{ $correctAnswer->correction }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="text-center mt-6">
                    <button
                        wire:click="nextQuestion"
                        class="bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-bold py-4 px-8 rounded-full text-xl shadow-lg transform transition-all duration-300 hover:scale-110"
                    >
                        Question suivante ➜
                    </button>
                </div>
            </div>
        @endif
    </div>
    @else
        <div class="text-center text-white">
            <div class="text-6xl mb-4">🎮</div>
            <h2 class="text-3xl font-bold mb-4">Chargement du jeu...</h2>
            <div class="animate-spin inline-block w-12 h-12 border-4 border-white/30 border-t-white rounded-full"></div>
        </div>
    @endif


        <style>
            @keyframes fade-in {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-fade-in {
                animation: fade-in 0.5s ease-out;
            }
        </style>

        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('answer-selected', ({ isCorrect }) => {
                    // Jouer un son de succès ou d'échec (si vous avez des sons)
                    console.log(isCorrect ? '✓ Bonne réponse !' : '✗ Mauvaise réponse');
                });

                Livewire.on('start-doors-game', () => {
                    console.log('Le jeu de portes commence !');
                });
            });
        </script>
</div>



