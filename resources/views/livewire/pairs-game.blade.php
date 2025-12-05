<div class="relative flex flex-col items-center min-h-screen w-full overflow-hidden"
     style="background-image: url('{{ asset('images/pairsgame.png') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;">

    <!-- CANVAS POUR LES LIGNES -->
    <canvas id="pairs-canvas"
            class="absolute inset-0 pointer-events-none w-full h-full"></canvas>

    <!-- TITRE -->
    <h2 class="text-5xl font-extrabold text-blue-300 drop-shadow-lg mt-10 mb-10 flex items-center gap-3">
        <img src="{{ asset('images/character.png') }}" class="w-10 h-10 drop-shadow-xl">
        Jeu des Paires
    </h2>

    <!-- CONTENU -->
    <div id="pairs-container" class="grid grid-cols-2 gap-10 relative w-full max-w-3xl mx-auto">

        {{-- COLONNE GAUCHE --}}
        <div class="space-y-3">
            <h3 class="text-xl font-bold text-blue-700 text-center">Mot</h3>

            @foreach($leftCards as $card)
                <button
                    id="left-{{ $card['id'] }}"
                    wire:click="selectLeft({{ $card['id'] }})"
                    class="pair-left w-full px-4 py-3 rounded-xl bg-white/80 border border-blue-300 shadow-md
                hover:bg-blue-50 hover:shadow-blue-300/50 hover:scale-105 transition-all duration-200
                backdrop-blur-sm relative font-semibold text-gray-800 text-base"
                >
                    {{ $card['text'] }}

                    @if(in_array($card['id'], $foundPairs))
                        <span class="absolute right-4 top-3 text-green-600 text-lg">✔️</span>
                    @endif
                </button>
            @endforeach
        </div>

        {{-- COLONNE DROITE --}}
        <div class="space-y-3">
            <h3 class="text-xl font-bold text-blue-700 text-center">Définition</h3>

            @foreach($rightCards as $card)
                <button
                    id="right-{{ $card['id'] }}"
                    wire:click="selectRight({{ $card['id'] }})"
                    class="pair-right w-full px-4 py-3 rounded-xl bg-white/80 border border-green-300 shadow-md
                hover:bg-green-50 hover:shadow-green-300/50 hover:scale-105 transition-all duration-200
                backdrop-blur-sm relative font-semibold text-gray-800 text-base"
                >
                    {{ $card['text'] }}

                    @if(in_array($card['id'], $foundPairs))
                        <span class="absolute right-4 top-3 text-green-600 text-lg">✔️</span>
                    @endif
                </button>
            @endforeach
        </div>

    </div>


    <!-- SCORE -->
    <div class="mt-12 text-2xl font-bold text-blue-200 drop-shadow-lg flex items-center gap-2">
        🎯 Paires trouvées :
        <span class="text-green-400">{{ $matches }}</span>
    </div>

</div>

<script src="{{ asset('js/pair-game.js') }}"></script>

<script>
    document.addEventListener("livewire:init", () => {
        Livewire.on("match-found", () => {
            window.PairGame.drawAllLines();
        });

        window.PairGame.drawAllLines();
    });
</script>
