class SnakeGame {
    constructor() {
        this.$app = document.querySelector('#app');
        this.$canvas = this.$app.querySelector('canvas');
        this.ctx = this.$canvas.getContext('2d');
        this.$startScreen = this.$app.querySelector('.start-screen');
        this.$score = this.$app.querySelector('.score');
        this.segmentDirections = [];

        this.settings = {
            canvas: {
                width: window.innerWidth,
                height: window.innerHeight,
                background: '#010100ff',
                border: '#000',
                margin: 50  // <- marge autour de la zone de jeu
            },
            snake: {
                size: 60,
                background: '#73854A',
                border: '#000'
            }
        };


        this.game = {
            // "direction" (set in setUpGame())
            // "nextDirection" (set in setUpGame())
            // "score" (set in setUpGame())
            speed: 100,
            keyCodes: {
                38: 'up',
                40: 'down',
                39: 'right',
                37: 'left'
            }
        };

        this.soundEffects = {
            score: new Audio('./sounds/score.mp3'),
            aie: new Audio('./sounds/aie.mp3'),
            gameOver: new Audio('./sounds/game-over.mp3')
        };

        this.setUpGame();
        this.init();


    }

    init() {
        // Choose difficulty
        // Rather than using "this.$startScreen.querySelectorAll('button')" and looping over the node list
        // and attaching seperate event listeners on each item, it's more efficient to just listen in on the container and run a check at runtime
        this.$startScreen.querySelector('.options').addEventListener('click', event => {
            this.chooseDifficulty(event.target.dataset.difficulty);
        });

        // Play
        this.$startScreen.querySelector('.play-btn').addEventListener('click', () => {
            this.startGame();
        });
    }

    chooseDifficulty(difficulty) {
        if(difficulty) {
            this.game.speed = difficulty;
            this.$startScreen.querySelectorAll('.options button').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
        }
    }

    setUpGame() {
        // The snake starts off with 5 pieces
        // Each piece is 30x30 pixels
        // Each following piece must be n times as far from the first piece
        const x = 300;
        const y = 300;

        this.snake = [
            { x: x, y: y },
            { x: x - this.settings.snake.size, y: y },
            { x: x - (this.settings.snake.size * 2), y: y },
            { x: x - (this.settings.snake.size * 3), y: y },
            { x: x - (this.settings.snake.size * 4), y: y }
        ];

        this.food = {
            active: false,
            img: new Image(),
            size: this.settings.snake.size,
            coordinates: { x: 0, y: 0 }
        };

        this.malus = {
            active: false,
            img: new Image(),
            size: this.settings.snake.size,
            coordinates: { x: 0, y: 0 }
        };

        // Définir l'image du malus (différente de la pomme)
        this.malus.img.src = "./images/malus.png"; // mets ton image ici


        // Images pour la tête et le corps du serpent
        this.snakeImages = {
            head: new Image(),
            body: new Image(),
            turn: new Image(),
            turn_inverse: new Image(),
            tail: new Image(),
            size: this.settings.snake.size
        };

        this.snakeImages.head.src = "./images/head.png";
        this.snakeImages.body.src = "./images/body.png";
        this.snakeImages.turn.src = "./images/turn.png";
        this.snakeImages.turn_inverse.src = "./images/turn_inverse.png";
        this.snakeImages.tail.src = "./images/tail.png";

        // Charge une image alimentaire personnalisé (JPG, PNG…)
        this.food.img.src = "./images/apple.png";

        this.game.score = 0;
        this.game.direction = 'right';
        this.game.nextDirection = 'right';
        this.segmentDirections = ['right','right','right','right','right'];

    }

    startGame() {
        // Stop the game over sound effect if a new game was restarted quickly before it could end
        this.soundEffects.gameOver.pause();
        this.soundEffects.gameOver.currentTime = 0;

        // Reset a few things from the prior game
        this.$app.classList.add('game-in-progress');
        this.$app.classList.remove('game-over');
        this.$score.innerText = 0;

        this.generateSnake();

        this.startGameInterval = setInterval(() => {
            if(!this.detectCollision()) {
                this.generateSnake();
            } else {
                this.endGame();
            }
        }, this.game.speed);

        // Change direction
        document.addEventListener('keydown', event => {
            this.changeDirection(event.keyCode);
        });
    }

    changeDirection(keyCode) {
        const validKeyPress = Object.keys(this.game.keyCodes).includes(keyCode.toString()); // Only allow (up|down|left|right)

        if(validKeyPress && this.validateDirectionChange(this.game.keyCodes[keyCode], this.game.direction)) {
            this.game.nextDirection = this.game.keyCodes[keyCode];
        }
    }

    // When already moving in one direction snake shouldn't be allowed to move in the opposite direction
    validateDirectionChange(keyPress, currentDirection) {
        return (keyPress === 'left' && currentDirection !== 'right') ||
            (keyPress === 'right' && currentDirection !== 'left') ||
            (keyPress === 'up' && currentDirection !== 'down') ||
            (keyPress === 'down' && currentDirection !== 'up');
    }

    resetCanvas() {
        // Full screen
        this.$canvas.width = this.settings.canvas.width;
        this.$canvas.height = this.settings.canvas.height;

        // Fond général
        this.ctx.fillStyle = this.settings.canvas.background;
        this.ctx.fillRect(0, 0, this.$canvas.width, this.$canvas.height);

        // Zone de jeu encadrée
        const margin = this.settings.canvas.margin;
        this.ctx.strokeStyle = "#ffffff"; // couleur du cadre
        this.ctx.lineWidth = 5;
        this.ctx.strokeRect(
            margin,
            margin,
            this.$canvas.width - 2 * margin,
            this.$canvas.height - 2 * margin
        );
    }


    generateSnake() {
        let coordinate;

        switch(this.game.direction) {
            case 'right':
                coordinate = { x: this.snake[0].x + this.settings.snake.size, y: this.snake[0].y };
                break;
            case 'up':
                coordinate = { x: this.snake[0].x, y: this.snake[0].y - this.settings.snake.size };
                break;
            case 'left':
                coordinate = { x: this.snake[0].x - this.settings.snake.size, y: this.snake[0].y };
                break;
            case 'down':
                coordinate = { x: this.snake[0].x, y: this.snake[0].y + this.settings.snake.size };
        }

        // Déplace le serpent
        this.snake.unshift(coordinate);
        this.segmentDirections.unshift(this.game.nextDirection);
        this.segmentDirections.pop();

        this.resetCanvas();

        // Vérifie si le serpent a mangé la nourriture
        if(this.food.active &&
            this.snake[0].x === this.food.coordinates.x &&
            this.snake[0].y === this.food.coordinates.y) {

            this.segmentDirections.push(this.segmentDirections[this.segmentDirections.length - 1]);
            this.game.score += 10;
            this.$score.innerText = this.game.score;
            this.soundEffects.score.play();
            this.food.active = false;

        } else {
            this.snake.pop();
        }

        // Vérifie si le serpent a mangé le malus
        if(this.malus.active &&
            this.snake[0].x === this.malus.coordinates.x &&
            this.snake[0].y === this.malus.coordinates.y) {

            this.soundEffects.aie.play(); // joue le son du malus

            // Retire 3 segments si possible
            for (let i = 0; i < 3; i++) {
                if(this.snake.length > 1) {
                    this.snake.pop();
                    this.segmentDirections.pop();
                } else {
                    this.endGame(); // Game over si plus de segment
                    return;
                }
            }

            this.malus.active = false;
        }



        this.generateFood(); // génère nourriture et malus
        this.drawSnake();    // dessine le serpent
    }


    drawSnake() {
        const size = this.settings.snake.size;

        this.snake.forEach((segment, index) => {

            if (index === 0) {
                // Tête
                this.drawRotatedHead(
                    this.snakeImages.head,
                    segment.x,
                    segment.y,
                    size,
                    this.game.direction
                );
            }

            else if (index === this.snake.length - 1) {
                // Queue
                const tailDirection = this.segmentDirections[index - 1];

                this.drawRotated(
                    this.snakeImages.tail,
                    segment.x,
                    segment.y,
                    size,
                    tailDirection
                );
            }

            else {
                const prev = this.segmentDirections[index];
                const next = this.segmentDirections[index + 1];

                if (prev && next && prev !== next) {
                    //selon le type de virage, on choisit l'image appropriée
                    // vers la gauche, on utilise turn
                    if ((prev === 'up' && next === 'left') ||
                        (prev === 'left' && next === 'down') ||
                        (prev === 'down' && next === 'right') ||
                        (prev === 'right' && next === 'up')) {
                        this.drawTurnSprite(this.snakeImages.turn, segment.x, segment.y, size, prev, next);
                    }
                    // vers la droite, on utilise turn_inverse
                    else {
                        this.drawTurnSprite(this.snakeImages.turn_inverse, segment.x, segment.y, size, prev, next);
                    }
                } else {
                    this.drawRotated(this.snakeImages.body, segment.x, segment.y, size, next);
                }
            }



        });

        this.game.direction = this.game.nextDirection;
    }

    drawTurnSprite(img, x, y, size, prev, next) {
        const t = this.getTurnTransform(prev, next);

        this.ctx.save();
        this.ctx.translate(x + size/2, y + size/2);

        // Toujours valide car angle existe obligatoirement
        this.ctx.rotate(t.angle || 0);

        this.ctx.drawImage(img, -size/2, -size/2, size, size);
        this.ctx.restore();
    }


    getTurnTransform(prev, next) {
        if (!prev || !next) return { angle: 0 };

        const map = {

            "down-right": 0,
            "down-left": 0,

            // +90° rotations
            "left-down" : Math.PI / 2,
            "left-up": Math.PI / 2,

            // 180° rotations
            "up-left": Math.PI,
            "up-right": Math.PI,

            // -90° rotations
            "right-up": -Math.PI / 2,
            "right-down": -Math.PI / 2
        };

        return { angle: map[`${prev}-${next}`] ?? 0 };
    }





    drawRotatedHead(img, x, y, size, direction) {

        this.ctx.save(); // Sauvegarde le contexte avant rotation

        // Centre l'image sur son origine
        this.ctx.translate(x + size / 2, y + size / 2);

        // Angle selon la direction
        let angle = 0;

        switch (direction) {
            case "right": angle = 0; break;
            case "down": angle = Math.PI / 2; break;
            case "left": angle = Math.PI; break;
            case "up": angle = -Math.PI / 2; break;
        }

        this.ctx.rotate(angle);

        // Dessine l'image centrée
        this.ctx.drawImage(img, -size / 2, -size / 2, size, size);

        this.ctx.restore(); // Restaure avant la prochaine frame
    }
    generateFood() {
        const margin = this.settings.canvas.margin;  // <- ajoute ça ici
        const gridSize = this.settings.snake.size;
        const xMax = this.settings.canvas.width - margin - gridSize;
        const yMax = this.settings.canvas.height - margin - gridSize;

        // Nourriture
        if(!this.food.active) {
            let x = Math.round((Math.random() * (xMax - margin) + margin) / gridSize) * gridSize;
            let y = Math.round((Math.random() * (yMax - margin) + margin) / gridSize) * gridSize;

            // Évite que la nourriture apparaisse sur le serpent
            for (const segment of this.snake) {
                if(segment.x === x && segment.y === y) {
                    x = Math.round((Math.random() * (xMax - margin) + margin) / gridSize) * gridSize;
                    y = Math.round((Math.random() * (yMax - margin) + margin) / gridSize) * gridSize;
                }
            }

            this.food.coordinates.x = x;
            this.food.coordinates.y = y;
            this.food.active = true;
        }

        // Malus
        if(!this.malus.active && Math.random() < 0.02) {
            let x = Math.round((Math.random() * (xMax - margin) + margin) / gridSize) * gridSize;
            let y = Math.round((Math.random() * (yMax - margin) + margin) / gridSize) * gridSize;

            // Évite que le malus apparaisse sur le serpent ou sur la nourriture
            for (const segment of this.snake) {
                if(segment.x === x && segment.y === y) {
                    x = Math.round((Math.random() * (xMax - margin) + margin) / gridSize) * gridSize;
                    y = Math.round((Math.random() * (yMax - margin) + margin) / gridSize) * gridSize;
                }
            }
            if(x === this.food.coordinates.x && y === this.food.coordinates.y) return;

            this.malus.coordinates.x = x;
            this.malus.coordinates.y = y;
            this.malus.active = true;
        }

        // Dessiner nourriture et malus
        if(this.food.active) this.drawFood(this.food.coordinates.x, this.food.coordinates.y, this.food.img);
        if(this.malus.active) this.drawFood(this.malus.coordinates.x, this.malus.coordinates.y, this.malus.img);
    }


    drawFood(x, y, img) {
        const size = this.settings.snake.size;

        if(img.complete) {
            this.ctx.drawImage(img, x, y, size, size);
        } else {
            img.onload = () => {
                this.ctx.drawImage(img, x, y, size, size);
            };
        }
    }


    drawRotated(img, x, y, size, directionOrAngle, isAngle=false) {
        this.ctx.save();
        this.ctx.translate(x + size/2, y + size/2);

        let angle = 0;

        if (isAngle) {
            angle = directionOrAngle;
        } else {
            switch(directionOrAngle) {
                case 'right': angle = 0; break;
                case 'down': angle = Math.PI/2; break;
                case 'left': angle = Math.PI; break;
                case 'up': angle = -Math.PI/2; break;
            }
        }

        this.ctx.rotate(angle);
        this.ctx.drawImage(img, -size/2, -size/2, size, size);
        this.ctx.restore();
    }


    detectCollision() {
        const margin = this.settings.canvas.margin;
        const maxX = this.$canvas.width - margin - this.settings.snake.size;
        const maxY = this.$canvas.height - margin - this.settings.snake.size;
        const head = this.snake[0];

        // Collision avec le mur (zone encadrée)
        if(head.x < margin || head.y < margin || head.x > maxX || head.y > maxY) return true;

        // Collision avec soi-même
        for(let i = 4; i < this.snake.length; i++) {
            if(this.snake[i].x === head.x && this.snake[i].y === head.y) return true;
        }

        return false;
    }


    endGame() {
        this.soundEffects.gameOver.play();

        clearInterval(this.startGameInterval);

        this.$app.classList.remove('game-in-progress');
        this.$app.classList.add('game-over');
        this.$startScreen.querySelector('.options h3').innerText = 'Game Over';
        this.$startScreen.querySelector('.options .end-score').innerText = `Score: ${this.game.score}`;

        this.setUpGame();
    }
}

const snakeGame = new SnakeGame();
