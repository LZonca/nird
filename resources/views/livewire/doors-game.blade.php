<div class="flex flex-col items-center justify-center min-h-screen p-6" style="background-image: url('{{ asset('images/doors-bg.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    @if($showGame && $currentQuestion)
    <div class="w-full max-w-7xl">
        {{-- Affichage de la question --}}
        <div class="bg-white/95 rounded-2xl shadow-xl p-5 mb-6 backdrop-blur-sm border-2 border-white/50">
            <div class="text-center">
                <h2 class="text-xl font-bold text-gray-800">{{ $currentQuestion->contexte }}</h2>
            </div>
        </div>

        {{-- Affichage des portes avec les réponses --}}
        <div class="flex flex-wrap justify-center gap-4 mb-8 w-full relative px-4">
            @foreach($reponses as $reponse)
                <div class="door-item w-full md:w-[calc(50%-0.5rem)] xl:w-[calc(25%-0.75rem)] max-w-[280px]" data-door-index="{{ $loop->index }}">
                    {{-- La porte cliquable --}}
                    <button
                        wire:click="selectAnswer({{ $reponse->id }})"
                        @if($showResult) disabled @endif
                        class="door-button-{{ $reponse->id }} group relative transform transition-all duration-200 hover:scale-102 w-full
                               @if($showResult) cursor-not-allowed @endif"
                    >
                        {{-- La porte avec image --}}
                        <div class="relative min-h-[280px] door-container" style="aspect-ratio: 3/4;">
                            {{-- Image de la porte en background --}}
                            <div class="absolute inset-0 door-image" style="background-image: url('{{ asset('images/doors.png') }}'); background-size: contain; background-position: center; background-repeat: no-repeat;">
                                {{-- Overlay lumineux pour animation --}}
                                <div class="door-flash absolute inset-0 bg-white opacity-0 transition-opacity duration-500"></div>



                            {{-- Panneau avec texte de la réponse sur la porte --}}
                            <div class="absolute inset-x-0 top-1/4 bottom-1/4 flex items-center justify-center p-4 z-10">
                                <div class="relative w-full max-w-[90%]">
                                    {{-- Image du panneau en background --}}
                                    <img src="{{ asset('images/panneau.png') }}" alt="Panneau" class="w-full h-auto drop-shadow-2xl">
                                    {{-- Texte par-dessus le panneau --}}
                                    <div class="absolute inset-0 flex items-center justify-center p-3">
                                        <p class="text-gray-800 font-bold text-center text-sm leading-tight">{{ $reponse->proposition }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Effet de hover --}}
                        @if(!$showResult)
                            <div class="absolute inset-0 bg-white/0  transition-all duration-300 pointer-events-none"></div>
                        @endif
                    </button>
                </div>
            @endforeach
        </div>

        {{-- Modal plein écran pour le résultat --}}
        @if($showResult)
            <div id="result-modal" class="fixed inset-0 z-50 flex items-center justify-center result-modal-hidden">
                {{-- Fond noir qui apparait --}}
                <div class="absolute inset-0 bg-black black-fade-in"></div>

                {{-- Image de fond selon le résultat avec zoom --}}
                <div class="absolute inset-0 flex items-center justify-center overflow-hidden">
                    @if($resultType === 'trap')
                        <img src="{{ asset('images/piege_doors.jpg') }}" alt="Piège" class="result-image-zoom object-cover w-full h-full">
                    @elseif($resultType === 'neutral')
                        <img src="{{ asset('images/nothing_doors.jpg') }}" alt="Neutre" class="result-image-zoom object-cover w-full h-full">
                    @else
                        <img src="{{ asset('images/nothing_doors.jpg') }}" alt="Gain" class="result-image-zoom object-cover w-full h-full">
                    @endif
                </div>

                {{-- Overlay semi-transparent pour la lisibilité --}}
                <div class="absolute inset-0 bg-black/50 result-overlay-fade"></div>

                {{-- Contenu du résultat --}}
                <div class="relative z-10 text-center animate-result-appear px-4">
                    @if($resultType === 'gain')
                        <div class="text-white">
                            <h3 class="text-5xl font-bold mb-4 drop-shadow-2xl text-yellow-300">Félicitations !</h3>
                            <p class="text-3xl font-bold drop-shadow-lg text-green-400">+{{ $fundsEarned }} Graines</p>
                            <p class="text-xl font-semibold mt-2 drop-shadow-lg">Vous avez gagné des fonds !</p>
                        </div>
                    @elseif($resultType === 'neutral')
                        <div class="text-white">
                            <h3 class="text-5xl font-bold mb-4 drop-shadow-2xl">Rien ici...</h3>
                            <p class="text-2xl font-semibold drop-shadow-lg">Cette porte était vide !</p>
                    @else
                        <div class="text-white">
                            <h3 class="text-5xl font-bold mb-4 drop-shadow-2xl text-red-400">Piège !</h3>
                            <p class="text-3xl font-bold drop-shadow-lg text-red-300">-{{ $fundsEarned }} Graines</p>
                            <p class="text-xl font-semibold mt-2 drop-shadow-lg">Vous avez perdu des fonds !</p>
                        </div>
                    @endif

                    <div class="mt-8">
                        <button
                            wire:click="nextQuestion"
                            class="bg-white text-gray-900 hover:bg-gray-100 font-bold py-3 px-8 rounded-full text-lg shadow-2xl transform transition-all duration-300 hover:scale-110"
                        >
                            Question suivante ➜
                        </button>
                    </div>
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

            /* Animation de flash lumineux sur la porte */
            @keyframes door-flash {
                0% { opacity: 0; }
                50% { opacity: 0.9; }
                100% { opacity: 0; }
            }

            /* Animation de fondu au noir */
            @keyframes black-fade {
                0% {
                    opacity: 0;
                }
                100% {
                    opacity: 1;
                }
            }

            .black-fade-in {
                animation: black-fade 0.5s ease-out forwards;
            }

            /* Animation d'apparition de l'overlay résultat */
            @keyframes overlay-appear {
                0% {
                    opacity: 0;
                }
                100% {
                    opacity: 1;
                }
            }

            /* Animation de zoom sur l'image de résultat */
            @keyframes zoom-center {
                0% {
                    transform: scale(1);
                }
                100% {
                    transform: scale(1.5);
                }
            }

            .result-image-zoom {
                animation: zoom-center 1.2s ease-out forwards;
                transform-origin: center center;
            }

            /* Fade de l'overlay */
            .result-overlay-fade {
                animation: overlay-appear 0.5s ease-out forwards;
            }

            /* Animation de fondu vers le noir pour transition */
            @keyframes fade-to-black {
                0% {
                    opacity: 0;
                }
                100% {
                    opacity: 1;
                }
            }

            .transition-fade-out {
                animation: fade-to-black 0.5s ease-out forwards;
            }

            /* Animation de disparition du résultat */
            @keyframes result-disappear {
                0% {
                    opacity: 1;
                    transform: scale(1);
                }
                100% {
                    opacity: 0;
                    transform: scale(0.9);
                }
            }

            .result-disappear {
                animation: result-disappear 0.5s ease-out forwards;
            }



            /* Animation d'apparition du contenu résultat */
            @keyframes result-appear {
                0% {
                    opacity: 0;
                    transform: scale(0.8) translateY(50px);
                }
                100% {
                    opacity: 1;
                    transform: scale(1) translateY(0);
                }
            }

            .animate-result-appear {
                animation: result-appear 0.4s ease-out 0.4s both;
            }

            /* Modal caché par défaut */
            .result-modal-hidden {
                display: none;
            }

            .result-modal-visible {
                display: flex;
            }


            /* Animation de l'overlay coloré */
            .result-overlay {
                animation: overlay-appear 0.5s ease-out 0.8s both;
            }

            /* Effet de hover sur les portes */
            .door-item:hover .door-container {
                transform: translateY(-3px);
                transition: transform 0.2s ease;
            }

            /* Scale plus subtil au hover */
            .hover\:scale-102:hover {
                transform: scale(1.02);
            }
        </style>

        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('answer-selected', ({ resultType, fundsEarned }) => {
                    console.log('Résultat:', resultType, 'Funds:', fundsEarned);

                    // Déclencher l'animation de flash lumineux et afficher le modal
                    setTimeout(() => {
                        const modal = document.getElementById('result-modal');
                        if (modal) {
                            modal.classList.remove('result-modal-hidden');
                            modal.classList.add('result-modal-visible');
                        }
                    }, 100);
                });

                Livewire.on('close-result-modal', () => {
                    const modal = document.getElementById('result-modal');
                    if (modal) {
                        // Ajouter la classe de disparition au contenu du modal
                        const modalContent = modal.querySelector('.animate-result-appear');
                        if (modalContent) {
                            modalContent.classList.add('result-disappear');
                        }

                        // Créer un overlay noir par-dessus tout
                        const fadeOverlay = document.createElement('div');
                        fadeOverlay.style.cssText = 'position: fixed; inset: 0; background-color: black; z-index: 100; opacity: 0; transition: opacity 0.6s ease-in-out;';
                        document.body.appendChild(fadeOverlay);

                        // Lancer le fondu au noir
                        requestAnimationFrame(() => {
                            requestAnimationFrame(() => {
                                fadeOverlay.style.opacity = '1';
                            });
                        });

                        // Fermer le modal pendant que c'est noir
                        setTimeout(() => {
                            modal.classList.remove('result-modal-visible');
                            modal.classList.add('result-modal-hidden');

                            // Retirer la classe result-disappear pour la prochaine fois
                            if (modalContent) {
                                modalContent.classList.remove('result-disappear');
                            }

                            // Faire disparaître le noir progressivement pour révéler la nouvelle question
                            setTimeout(() => {
                                fadeOverlay.style.opacity = '0';
                                setTimeout(() => {
                                    if (document.body.contains(fadeOverlay)) {
                                        document.body.removeChild(fadeOverlay);
                                    }
                                }, 600);
                            }, 100);
                        }, 600);
                    }
                });

                Livewire.on('start-doors-game', () => {
                    console.log('Le jeu de portes commence !');
                });

                // Déclencher le flash lumineux sur la porte cliquée
                document.addEventListener('click', (e) => {
                    const doorButton = e.target.closest('[class*="door-button-"]');
                    if (doorButton) {
                        const flash = doorButton.querySelector('.door-flash');
                        if (flash) {
                            flash.style.animation = 'door-flash 0.8s ease-out';
                            setTimeout(() => {
                                flash.style.animation = '';
                            }, 800);
                        }
                    }
                });


            });
        </script>
        <div id="character" class="fixed bottom-12 left-1/2  pointer-events-none z-30 w-40 h-40">
            <img src="{{ asset('images/character_back.png') }}" alt="Character" class="w-full h-full object-contain drop-shadow-2xl">
        </div>
    </div>



