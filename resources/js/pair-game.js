window.PairGame = {

    canvas: null,
    ctx: null,

    init() {
        this.canvas = document.getElementById("pairs-canvas");
        if (!this.canvas) return;

        this.ctx = this.canvas.getContext("2d");
        this.resizeCanvas();

        window.addEventListener("resize", () => {
            this.resizeCanvas();
            this.drawAllLines();
        });
    },

    resizeCanvas() {
        this.canvas.width = this.canvas.offsetWidth;
        this.canvas.height = this.canvas.offsetHeight;
    },

    // -----------------------------
    // 🔵 Dessine une ligne entre 2 cartes trouvées
    // -----------------------------
    drawLineBetween(leftId, rightId) {
        const leftBtn = document.getElementById(`left-${leftId}`);
        const rightBtn = document.getElementById(`right-${rightId}`);

        if (!leftBtn || !rightBtn) return;

        const rectLeft = leftBtn.getBoundingClientRect();
        const rectRight = rightBtn.getBoundingClientRect();
        const canvasRect = this.canvas.getBoundingClientRect();

        const startX = rectLeft.right - canvasRect.left;
        const startY = rectLeft.top + rectLeft.height / 2 - canvasRect.top;

        const endX = rectRight.left - canvasRect.left;
        const endY = rectRight.top + rectRight.height / 2 - canvasRect.top;

        this.ctx.beginPath();
        this.ctx.moveTo(startX, startY);
        this.ctx.lineTo(endX, endY);
        this.ctx.strokeStyle = "#4ade80"; // vert joli
        this.ctx.lineWidth = 4;
        this.ctx.shadowColor = "#166534";
        this.ctx.shadowBlur = 8;
        this.ctx.stroke();
    },

    // -----------------------------
    // 🔵 Redessine toutes les lignes
    // -----------------------------
    drawAllLines() {
        if (!window.Livewire) return;

        // on efface le canvas
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);

        // récupérer les paires trouvées depuis Livewire
        const foundPairs = Livewire.find(
            document.querySelector("[wire\\:id]").getAttribute("wire:id")
        ).foundPairs;

        foundPairs.forEach(id => {
            this.drawLineBetween(id, id);
        });
    },

    // -----------------------------
    // 🔴 Popup si mauvaise association
    // -----------------------------
    showErrorPopup() {
        const popup = document.createElement("div");

        popup.innerHTML = `
            <div style="
                position: fixed;
                top: 20px;
                left: 50%;
                transform: translateX(-50%);
                background: #fee2e2;
                color: #b91c1c;
                padding: 12px 20px;
                border-radius: 12px;
                font-weight: bold;
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
                z-index: 9999;
                opacity: 0;
                transition: opacity .3s ease;
            ">
                ❌ Ce n’est pas la bonne paire !
            </div>
        `;

        document.body.appendChild(popup);
        const box = popup.firstElementChild;

        setTimeout(() => box.style.opacity = "1", 50);

        setTimeout(() => {
            box.style.opacity = "0";
            setTimeout(() => popup.remove(), 300);
        }, 1400);
    }
};


// ----------------------------------
// 🎯 Hooks Livewire
// ----------------------------------
document.addEventListener("livewire:init", () => {

    window.PairGame.init();

    // quand paire correcte → on redessine
    Livewire.on("match-found", () => {
        window.PairGame.drawAllLines();
    });

    // quand paire incorrecte → popup ❌
    Livewire.on("wrong-pair", () => {
        window.PairGame.showErrorPopup();
    });
});
