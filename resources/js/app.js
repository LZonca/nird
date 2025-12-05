import "./pair-game.js"
window.doorsGame = {
    currentQuestion: null,

    start(question) {
        this.currentQuestion = question;

        // 1) On ferme les portes (si besoin) puis on les ouvre
        this.closeDoors();

        setTimeout(() => {
            this.openDoors();
        }, 200);

        // 2) On prépare l’affichage de la question après l’ouverture
        setTimeout(() => {
            this.showQuestion();
        }, 1400); // 1.2s d'animation + marge
    },

    openDoors() {
        const container = document.getElementById('doors-container');
        if (!container) return;

        container.classList.add('doors-open');
    },

    closeDoors() {
        const container = document.getElementById('doors-container');
        if (!container) return;

        container.classList.remove('doors-open');
    },

    showQuestion() {
        if (!this.currentQuestion) return;

        const questionEl = document.getElementById('question-area');
        const answersEl = document.getElementById('answers-area');

        if (questionEl) {
            questionEl.innerText = this.currentQuestion.contexte;
        }

        if (answersEl) {
            answersEl.innerHTML = '';

            this.currentQuestion.reponses.forEach((rep) => {
                const btn = document.createElement('button');
                btn.innerText = rep.proposition;

                btn.addEventListener('click', () => {
                    this.handleAnswer(rep);
                });

                answersEl.appendChild(btn);
            });
        }
    },

    handleAnswer(reponse) {
        if (reponse.resultat) {
            alert('Bonne réponse !');
            // tu peux lancer animation spéciale, etc.
        } else {
            alert('Mauvaise réponse : ' + (reponse.correction ?? ''));
        }
    }
};
