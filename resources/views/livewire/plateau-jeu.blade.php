<div class="relative w-full h-screen overflow-hidden bg-gray-900" style="background-image: url('{{ asset('images/space_background.jpg') }}'); background-size: cover; background-position: center;">
    {{-- Background principal centré --}}
    <div class="absolute inset-0 flex items-center justify-center">
        <img src="{{ asset('images/pluto.png') }}" alt="Plateau" class="max-w-full max-h-full object-contain">
    </div>

    {{-- Circuit de cases en cercle --}}
    <div class="absolute inset-0 flex items-center justify-center">
        <div class="relative circuit-container" style="width: 600px; height: 600px;">
            @php
                $nombreCases = $this->nombreCases; // Nombre de cases sur le circuit
                $rayon = $this->rayon; // Rayon du cercle en pixels
            @endphp

            @for($i = 0; $i < $nombreCases; $i++)
                @php
                    // Calculer l'angle pour chaque case (en degrés)
                    $angle = ($i * 360 / $nombreCases) - 90; // -90 pour commencer en haut
                    $angleRad = deg2rad($angle);

                    // Calculer la position x et y
                    $x = $rayon * cos($angleRad);
                    $y = $rayon * sin($angleRad);

                    // Déterminer l'image à utiliser
                    $isBase = ($i === $nombreCases - 1);

                    if ($isBase) {
                        $imageCase = 'base.png';
                    } else {
                        // Alterner entre les 3 types de cases : case.png, case-violet.png, case-rose.png
                        $caseTypes = ['case.png', 'case-violet.png', 'case-rose.png'];
                        $imageCase = $caseTypes[$i % 3];
                    }
                @endphp

                <div class="absolute case-item transform -translate-x-1/2 -translate-y-1/2"
                     style="left: calc(50% + {{ $x }}px); top: calc(50% + {{ $y }}px);"
                     data-case-index="{{ $i }}">
                    <img src="{{ asset('images/' . $imageCase) }}" alt="{{ $isBase ? 'Base' : 'Case ' . ($i + 1) }}" class="w-16 h-16 object-contain drop-shadow-lg">

                    {{-- Numéro de la case (sauf pour la base) --}}
                    @if(!$isBase)
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-white font-bold text-sm drop-shadow-lg">{{ $i + 1 }}</span>
                        </div>
                    @endif
                </div>
            @endfor

            {{-- Joueur - position chargée depuis la BDD --}}
            @php
                $startAngle = (($caseActuelle) * 360 / $nombreCases) - 90; // Position actuelle du joueur
                $startAngleRad = deg2rad($startAngle);
                $startX = $rayon * cos($startAngleRad);
                $startY = $rayon * sin($startAngleRad);
            @endphp
            <div id="player" class="absolute transform -translate-x-1/2 -translate-y-1/2 transition-all duration-500 z-10"
                 style="left: calc(50% + {{ $startX }}px); top: calc(50% + {{ $startY }}px);">
                <img src="{{ asset('images/character.png') }}" alt="Joueur" class="w-20 h-20 object-contain drop-shadow-2xl">
            </div>
        </div>
    </div>

    {{-- Informations du joueur --}}
    <div class="absolute top-8 left-8 z-20 bg-white/95 rounded-2xl shadow-2xl p-4 backdrop-blur-sm">
        <div class="text-center">
            <p class="text-lg font-bold text-gray-800">Tour : {{ $yearActuel }}</p>
            <p class="text-lg font-bold text-green-600">💰 {{ auth()->user()->funds ?? 0 }} Graines</p>
        </div>
    </div>

    {{-- Contrôles du jeu --}}
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-20">
        <button id="avancerBtn" wire:click="avancer" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-4 px-8 rounded-full text-lg shadow-2xl transform transition-all duration-300 hover:scale-110 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100">
            ➡️ Avancer
        </button>
    </div>
    <style>
        .circuit-container {
            position: relative;
        }

        .case-item {
            position: absolute;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        #player {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translate(-50%, -50%);
        }
    </style>

    <script>
        let playerCurrentCase = {{ $caseActuelle }}; // Position actuelle du joueur chargée depuis la BDD
        console.log('🎮 INIT - Position initiale du joueur:', playerCurrentCase);

        document.addEventListener('livewire:init', () => {
            Livewire.on('deplacer-joueur', ({ caseIndex, rayon, nombreCases, nombreSauts }) => {
                console.log('📡 EVENT REÇU - deplacer-joueur');
                console.log('  → caseIndex (destination):', caseIndex);
                console.log('  → nombreSauts:', nombreSauts);
                console.log('  → rayon:', rayon);
                console.log('  → nombreCases:', nombreCases);
                console.log('  → Position JS actuelle (avant déplacement):', playerCurrentCase);

                const player = document.getElementById('player');
                if (player) {
                    // Utiliser directement le nombre de sauts du dé
                    const casesToMove = nombreSauts;
                    console.log('🎲 Nombre de cases à parcourir:', casesToMove);

                    // Si pas de mouvement, on arrête
                    if (casesToMove === 0) {
                        console.warn('⚠️ Pas de mouvement à effectuer');
                        return;
                    }

                    // Déplacer case par case
                    let currentStep = 0;
                    console.log('🏃 Début du déplacement...');

                    const moveInterval = setInterval(() => {
                        if (currentStep >= casesToMove) {
                            clearInterval(moveInterval);
                            console.log('✅ Déplacement terminé - Position finale:', playerCurrentCase);
                            return;
                        }

                        // Calculer la prochaine case (toujours en avançant)
                        currentStep++;
                        const ancienneCase = playerCurrentCase;
                        playerCurrentCase = (playerCurrentCase + 1) % nombreCases;

                        console.log(`  Step ${currentStep}/${casesToMove}: Case ${ancienneCase} → Case ${playerCurrentCase}`);

                        // Calculer l'angle pour cette case
                        const angle = (playerCurrentCase * 360 / nombreCases) - 90;
                        const angleRad = angle * Math.PI / 180;

                        // Calculer la nouvelle position
                        const x = rayon * Math.cos(angleRad);
                        const y = rayon * Math.sin(angleRad);

                        console.log(`    Position: x=${x.toFixed(2)}, y=${y.toFixed(2)}, angle=${angle.toFixed(2)}°`);

                        // Ajouter une animation de saut (arc)
                        player.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                        player.style.left = `calc(50% + ${x}px)`;
                        player.style.top = `calc(50% + ${y}px)`;

                        // Animation de saut (scale)
                        player.style.transform = 'translate(-50%, -50%) scale(1.2)';
                        setTimeout(() => {
                            player.style.transform = 'translate(-50%, -50%) scale(1)';
                        }, 150);

                        currentStep++;
                    }, 400); // 400ms entre chaque case
                } else {
                    console.error('❌ Élément player non trouvé!');
                }
            });

            // Écouter l'événement pour attendre la fin du déplacement puis déclencher le mini-jeu
            Livewire.on('attendre-fin-deplacement', () => {
                console.log('⏳ Attente de la fin du déplacement...');
                // Attendre assez longtemps pour que l'animation soit terminée (400ms d'animation + 200ms de marge)
                setTimeout(() => {
                    console.log('✅ Déplacement terminé - Déclenchement du mini-jeu');
                    @this.call('declencherMiniJeu');
                }, 700);
            });
        });
    </script>
</div>

