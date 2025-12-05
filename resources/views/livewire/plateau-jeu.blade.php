<div class="relative w-full h-screen overflow-hidden bg-gray-900">
    {{-- Background principal centré --}}
    <div class="absolute inset-0 flex items-center justify-center">
        <img src="{{ asset('images/pluto.png') }}" alt="Plateau" class="max-w-full max-h-full object-contain">
    </div>

    {{-- Circuit de cases en cercle --}}
    <div class="absolute inset-0 flex items-center justify-center">
        <div class="relative circuit-container" style="width: 600px; height: 600px;">
            @php
                $nombreCases = 20; // Nombre de cases sur le circuit
                $rayon = 250; // Rayon du cercle en pixels
            @endphp

            @for($i = 0; $i < $nombreCases; $i++)
                @php
                    // Calculer l'angle pour chaque case (en degrés)
                    $angle = ($i * 360 / $nombreCases) - 90; // -90 pour commencer en haut
                    $angleRad = deg2rad($angle);

                    // Calculer la position x et y
                    $x = $rayon * cos($angleRad);
                    $y = $rayon * sin($angleRad);
                @endphp

                <div class="absolute case-item transform -translate-x-1/2 -translate-y-1/2"
                     style="left: calc(50% + {{ $x }}px); top: calc(50% + {{ $y }}px);"
                     data-case-index="{{ $i }}">
                    <img src="{{ asset('images/case.png') }}" alt="Case {{ $i + 1 }}" class="w-16 h-16 object-contain drop-shadow-lg">

                    {{-- Numéro de la case --}}
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-white font-bold text-sm drop-shadow-lg">{{ $i + 1 }}</span>
                    </div>
                </div>
            @endfor

            {{-- Joueur --}}
            <div id="player" class="absolute transform -translate-x-1/2 -translate-y-1/2 transition-all duration-500 z-10"
                 style="left: calc(50% + {{ $rayon }}px); top: 50%;">
                <img src="{{ asset('images/character_back.png') }}" alt="Joueur" class="w-20 h-20 object-contain drop-shadow-2xl">
            </div>
        </div>
    </div>

    {{-- Contrôles du jeu --}}
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-20">
        <button wire:click="lancerDe" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-4 px-8 rounded-full text-lg shadow-2xl transform transition-all duration-300 hover:scale-110">
            🎲 Lancer le dé
        </button>
    </div>

    {{-- Affichage du résultat du dé --}}
    @if($deResultat)
        <div class="absolute top-8 left-1/2 transform -translate-x-1/2 z-20 bg-white/95 rounded-2xl shadow-2xl p-6 backdrop-blur-sm">
            <div class="text-center">
                <p class="text-4xl font-bold text-gray-800 mb-2">🎲 {{ $deResultat }}</p>
                <p class="text-sm text-gray-600">Résultat du dé</p>
            </div>
        </div>
    @endif
    <style>
        .circuit-container {
            position: relative;
        }

        .case-item {
            position: absolute;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .case-item:hover {
            transform: translate(-50%, -50%) scale(1.1);
        }

        #player {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('deplacer-joueur', ({ caseIndex, rayon, nombreCases }) => {
                const player = document.getElementById('player');
                if (player) {
                    // Calculer l'angle pour la case de destination
                    const angle = (caseIndex * 360 / nombreCases) - 90;
                    const angleRad = angle * Math.PI / 180;

                    // Calculer la nouvelle position
                    const x = rayon * Math.cos(angleRad);
                    const y = rayon * Math.sin(angleRad);

                    // Déplacer le joueur
                    player.style.left = `calc(50% + ${x}px)`;
                    player.style.top = `calc(50% + ${y}px)`;
                }
            });
        });
    </script>

</div>

