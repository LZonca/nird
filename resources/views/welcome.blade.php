<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pluto</title>

    <!-- Tailwind CDN (si tu n'utilises pas Vite pour cette page) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Police optionnelle -->
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,600,700" rel="stylesheet">
</head>

<body class="bg-gradient-to-b from-blue-50 to-white text-gray-900 flex flex-col items-center min-h-screen p-6">

<!-- Contenu principal -->
<div class="bg-white/80 backdrop-blur-xl shadow-2xl rounded-3xl p-12 max-h-[700px] flex flex-col lg:flex-row gap-12 items-center w-full max-w-6xl border border-blue-100">

    <!-- COLONNE GAUCHE -->
    <div class="lg:w-1/2 space-y-8">

        <h1 class="text-6xl lg:text-7xl font-extrabold tracking-tight text-center text-blue-900 drop-shadow-sm">
            Bienvenue sur Pluto !
        </h1>

        <p class="text-lg text-gray-700 leading-relaxed space-y-4">

                <span class="block">
                    <strong class="text-blue-700">Pluto</strong> est un jeu de plateau conçu pour guider les établissements
                    scolaires vers la démarche <strong class="text-green-700">NIRD</strong>.
                </span>

            <span class="block">
                    Sur cette planète symbolique, chaque joueur progresse en accomplissant des
                    <em>"NIRD Goals"</em>, étapes vers un numérique libre, durable et responsable.
                </span>

            <span class="block">
                    Vous devrez réussir des mini-jeux, obtenir des ressources et améliorer votre planète
                    pour montrer votre engagement envers le numérique responsable.
                </span>

            <span class="block">
                    L'objectif final : décrocher la <strong class="text-purple-700">Certification NIRD</strong> et devenir un modèle du numérique durable.
                </span>
        </p>

        <a href="{{ route('login') }}"
           class="block text-center text-xl font-semibold bg-blue-600 text-white py-3 rounded-xl shadow-md hover:bg-blue-700 transition">
            🚀 Es-tu prêt pour l’aventure ?
        </a>

    </div>

    <!-- COLONNE DROITE – IMAGE -->
    <div class="lg:w-1/2 flex justify-center relative">

        <div class="absolute w-72 h-72 bg-blue-200/50 blur-3xl rounded-full -z-10"></div>

        <img src="/planeteaccueil.png"
             alt="Planète Pluto"
             class="w-[700px] h-[700px] object-contain [filter:drop-shadow(0_25px_25px_rgba(0,0,0,0.25))]"
    </div>

</div>

</body>
</html>
