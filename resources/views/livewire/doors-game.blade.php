<div>
    {{-- Zone des portes --}}
    <div id="doors-container">
        <div class="door left-door"></div>
        <div class="door right-door"></div>
    </div>
    {{-- Zone d’affichage de la question --}}
    <div id="question-area"></div>
    <div id="answers-area"></div>
</div>

<script>
    document.addEventListener('livewire:init', () => {

        // Livewire -> JS : quand Livewire dispatch 'start-doors-game'
        Livewire.on('start-doors-game', ({ question }) => {
            // on passe la question au objet JS doorsGame
            window.doorsGame.start(question);
        });

    });
</script>
@vite(['resources/js/app.js', 'resources/js/movement.js'])
