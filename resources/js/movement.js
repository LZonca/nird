if (player.position.x === targetX && player.position.y === targetY) {
    // Le joueur arrive sur la case
    Livewire.dispatch('player-on-door');
}
