<div class="relative w-full h-screen overflow-hidden" style="background-image: url('{{ asset('images/fond.png') }}'); background-size: cover; background-position: center;">
    {{-- Background de la planète Pluto au centre --}}
    <div class="absolute inset-0 flex items-center justify-center">
        <img src="{{ asset('images/pluto.png') }}" alt="Pluto" class="max-w-2xl max-h-2xl object-contain">
    </div>

    {{-- Informations du joueur en haut à gauche --}}
    <div class="absolute top-8 left-8 z-20 bg-white/95 rounded-2xl shadow-2xl p-4 backdrop-blur-sm max-w-md">
        <div class="text-center">
            <p class="text-lg font-bold text-gray-800">🏠 {{ auth()->user()->base->name ?? 'Base' }}</p>
            <p class="text-lg font-bold text-green-600">💰 {{ $userFunds }} Graines</p>
            <p class="text-sm text-gray-600 mt-2">Année: {{ $currentYear }}</p>
            <p class="text-sm font-semibold mt-1 {{ $canUpgradeThisTurn ? 'text-green-600' : 'text-red-600' }}">
                🔧 Améliorations: {{ $canUpgradeThisTurn ? '0/1' : '1/1' }}
            </p>
            @if(!$canUpgradeThisTurn)
                <p class="text-xs text-orange-600 mt-1">⏸️ Plus d'amélioration ce tour</p>
            @endif
        </div>
    </div>

    {{-- Debug: Liste des éléments (à supprimer après debug) --}}
    @if(count($elements) > 0)
        <div class="absolute top-8 right-8 z-20 bg-white/95 rounded-2xl shadow-2xl p-4 backdrop-blur-sm max-w-md max-h-96 overflow-y-auto">
            <h3 class="text-lg font-bold text-gray-800 mb-3">📋 Éléments disponibles</h3>
            @foreach($elements as $element)
                <div class="mb-3 p-2 bg-gray-50 rounded-lg">
                    <p class="font-semibold text-sm">{{ $element['name'] }}</p>
                    <p class="text-xs text-gray-600">
                        Niveau: {{ $element['current_level'] }}/{{ $element['level_max'] }}
                        | Coût: 💰 {{ $element['upgrade_cost'] }}
                    </p>
                    <p class="text-xs {{ $element['is_tree'] ? 'text-green-600' : 'text-blue-600' }}">
                        Type: {{ $element['is_tree'] ? '🌳 Arbre (extérieur)' : '🏢 Bâtiment (centre)' }}
                    </p>
                    @if($userFunds >= $element['upgrade_cost'] && $element['can_upgrade'])
                        <span class="text-xs text-green-600 font-bold">✅ Améliorable!</span>
                    @elseif(!$element['can_upgrade'])
                        <span class="text-xs text-orange-600">🔒 Niveau max</span>
                    @else
                        <span class="text-xs text-red-600">❌ Pas assez de graines</span>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- DEBUG: Afficher les informations sur les éléments --}}
    <div class="absolute bottom-8 right-8 z-20 bg-red-500 text-white px-4 py-2 rounded-lg text-sm">
        <p>Total éléments: {{ count($elements) }}</p>
        @php
            $notInitialized = collect($elements)->filter(fn($e) => !$e['is_initialized'])->values();
        @endphp
        <p>Non initialisés: {{ count($notInitialized) }}</p>
    </div>

    {{-- Affichage des boutons d'initialisation pour les éléments non initialisés --}}
    @if(count($elements) > 0 && count($notInitialized) > 0)
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
            <div class="relative w-full max-w-4xl pointer-events-auto">
                <div class="bg-white/95 rounded-2xl shadow-2xl p-8 backdrop-blur-sm">
                    <h2 class="text-3xl font-bold text-center text-gray-800 mb-2">🏗️ Initialisation de votre Base</h2>
                    <p class="text-center text-gray-600 mb-6">Cliquez sur chaque élément pour l'ajouter à votre base (niveau 1)</p>

                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($notInitialized as $element)
                            <button
                                wire:click="initializeElement({{ $element['id'] }})"
                                class="group relative transform transition-all duration-200 hover:scale-105 bg-gradient-to-br from-blue-50 to-purple-50 hover:from-blue-100 hover:to-purple-100 rounded-xl p-4 shadow-md hover:shadow-xl"
                            >
                                {{-- Image de l'élément --}}
                                <div class="flex justify-center mb-2">
                                    <img src="{{ asset($element['url']) }}"
                                         alt="{{ $element['name'] }}"
                                         class="w-20 h-20 object-contain drop-shadow-lg">
                                </div>

                                {{-- Nom de l'élément --}}
                                <p class="text-sm font-bold text-gray-800 text-center mb-1">{{ $element['name'] }}</p>


                                {{-- Bouton --}}
                                <div class="bg-blue-500 group-hover:bg-blue-600 text-white text-xs font-bold py-2 px-3 rounded-lg transition-colors">
                                    ➕ Ajouter
                                </div>
                            </button>
                        @endforeach
                    </div>

                    <p class="text-center text-sm text-gray-500 mt-6">
                        {{ count($notInitialized) }} élément(s) à initialiser
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Messages flash --}}
    @if (session()->has('success'))
        <div class="absolute top-8 left-1/2 transform -translate-x-1/2 z-30 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="absolute top-8 left-1/2 transform -translate-x-1/2 z-30 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
            {{ session('error') }}
        </div>
    @endif

    {{-- Éléments au centre de la planète (non-arbres) --}}
    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
        <div class="relative w-96 h-96">
            @php
                $centerElements = collect($elements)->filter(fn($el) => !$el['is_tree'])->values();
                $centerCount = count($centerElements);
                $centerRadius = 120; // Rayon plus petit pour le centre
            @endphp

            @foreach($centerElements as $index => $element)
                @php
                    $angle = ($index * 360 / max($centerCount, 1)) - 90;
                    $angleRad = deg2rad($angle);
                    $x = $centerRadius * cos($angleRad);
                    $y = $centerRadius * sin($angleRad);
                @endphp

                <div class="absolute transform -translate-x-1/2 -translate-y-1/2 pointer-events-auto"
                     style="left: calc(50% + {{ $x }}px); top: calc(50% + {{ $y }}px);">
                    <button
                        wire:click="upgradeElement({{ $element['id'] }})"
                        @if(!$canUpgradeThisTurn || !$element['can_upgrade'] || $userFunds < $element['upgrade_cost']) disabled @endif
                        class="group relative transform transition-all duration-200 hover:scale-110 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{-- Image de l'élément --}}
                        <img src="{{ asset($element['url']) }}"
                             alt="{{ $element['name'] }}"
                             class="w-20 h-20 object-contain drop-shadow-2xl">

                        {{-- Badge de niveau --}}
                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center border-2 border-white shadow-lg">
                            <span class="text-white font-bold text-xs">{{ $element['current_level'] }}</span>
                        </div>

                        {{-- Info au survol --}}
                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                            <div class="bg-black/90 text-white px-3 py-2 rounded-lg text-sm whitespace-nowrap">
                                <p class="font-bold">{{ $element['name'] }}</p>
                                <p>Niveau: {{ $element['current_level'] }}/{{ $element['level_max'] }}</p>
                                <p class="text-yellow-300">💰 {{ $element['upgrade_cost'] }} graines</p>
                            </div>
                        </div>
                    </button>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Arbres sur l'extérieur --}}
    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
        <div class="relative w-[800px] h-[800px]">
            @php
                $trees = collect($elements)->filter(fn($el) => $el['is_tree'])->values();
                $treeCount = count($trees);
                $treeRadius = 350; // Rayon plus grand pour les arbres
            @endphp

            @foreach($trees as $index => $tree)
                @php
                    $angle = ($index * 360 / max($treeCount, 1)) - 90;
                    $angleRad = deg2rad($angle);
                    $x = $treeRadius * cos($angleRad);
                    $y = $treeRadius * sin($angleRad);
                @endphp

                <div class="absolute transform -translate-x-1/2 -translate-y-1/2 pointer-events-auto"
                     style="left: calc(50% + {{ $x }}px); top: calc(50% + {{ $y }}px);">
                    <button
                        wire:click="upgradeElement({{ $tree['id'] }})"
                        @if(!$canUpgradeThisTurn || !$tree['can_upgrade'] || $userFunds < $tree['upgrade_cost']) disabled @endif
                        class="group relative transform transition-all duration-200 hover:scale-110 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{-- Image de l'arbre --}}
                        <img src="{{ asset('storage/' . $tree['url']) }}"
                             alt="{{ $tree['name'] }}"
                             class="w-24 h-24 object-contain drop-shadow-2xl">

                        {{-- Badge de niveau --}}
                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center border-2 border-white shadow-lg">
                            <span class="text-white font-bold text-xs">{{ $tree['current_level'] }}</span>
                        </div>

                        {{-- Info au survol --}}
                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                            <div class="bg-black/90 text-white px-3 py-2 rounded-lg text-sm whitespace-nowrap">
                                <p class="font-bold">{{ $tree['name'] }}</p>
                                <p>Niveau: {{ $tree['current_level'] }}/{{ $tree['level_max'] }}</p>
                                <p class="text-yellow-300">💰 {{ $tree['upgrade_cost'] }} graines</p>
                            </div>
                        </div>
                    </button>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Bouton retour au plateau --}}
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-20">
        <a href="{{ route('plateau') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-4 px-8 rounded-full text-lg shadow-2xl transform transition-all duration-300 hover:scale-110">
            🚀 Retour au Plateau
        </a>
    </div>

    {{-- Personnage centré en bas --}}
    <div id="character" class="fixed bottom-12 pointer-events-none z-30" style="left: 50%; transform: translateX(-50%); width: 80px; height: 80px;">
        <img src="{{ asset('images/character.png') }}" alt="Character" class="w-full h-full object-contain drop-shadow-2xl">
    </div>
</div>

