console.log("PAIR-GAME.JS CHARGÉ !");

document.addEventListener("DOMContentLoaded", () => {

    console.log("DOM READY");

    const canvas = document.getElementById("pair-canvas");
    const ctx = canvas.getContext("2d");

    let selectedLeft = null;

    const leftItems = document.querySelectorAll(".left-item");
    const rightItems = document.querySelectorAll(".right-item");

    function centerOf(el, side = "left") {
        const rect = el.getBoundingClientRect();
        const crect = canvas.getBoundingClientRect();

        return {
            x: side === "left"
                ? rect.right - crect.left
                : rect.left - crect.left,
            y: rect.top - crect.top + rect.height / 2
        };
    }

    function drawLine(leftEl, rightEl) {
        const p1 = centerOf(leftEl, "left");
        const p2 = centerOf(rightEl, "right");

        ctx.beginPath();
        ctx.moveTo(p1.x, p1.y);
        ctx.lineTo(p2.x, p2.y);
        ctx.strokeStyle = "black";
        ctx.lineWidth = 3;
        ctx.stroke();
    }

    leftItems.forEach(item => {
        item.addEventListener("click", () => {
            selectedLeft = item;
            console.log("LEFT SELECTED", item.dataset.id);
        });
    });

    rightItems.forEach(item => {
        item.addEventListener("click", () => {
            if (!selectedLeft) return;

            console.log("RIGHT SELECTED", item.dataset.id);

            drawLine(selectedLeft, item);

            selectedLeft = null;
        });
    });

});
