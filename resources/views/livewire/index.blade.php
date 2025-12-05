<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-title" content="Snake Game">
  <title>🐍</title>
  <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
  <div id="app" class="app">
    <div class="start-screen animated bounceIn">
      <h2>🐍</h2>

      <div class="options">
        <h3>Choose Difficulty</h3>
        <p class="end-score"></p>
        <button data-difficulty="100" class="active">Easy</button>
        <button data-difficulty="75">Medium</button>
        <button data-difficulty="50">Hard</button>
      </div>

      <button class="play-btn">Play</button>
    </div>

    <canvas></canvas>

    <div class="score">0</div>
  </div>

  <script src="../../js/snake/app.js"></script>
</body>
</html>
