<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>

<!-- 🌟 Même thème visuel que la page d'accueil Pluto -->
<body class="min-h-screen bg-gradient-to-b from-blue-50 to-white text-gray-900 flex flex-col items-center justify-center p-6">

<div class="bg-white/80 backdrop-blur-xl shadow-2xl rounded-3xl p-10 w-full max-w-md border border-blue-100">

    <div class="flex flex-col items-center gap-6">

        <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
                <span class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 shadow-md">
                    <x-app-logo-icon class="size-9 text-blue-700" />
                </span>
            <span class="sr-only">{{ config('app.name', 'Pluto') }}</span>
        </a>

        <!-- ⭐ Ton contenu ne change pas -->
        <div class="flex flex-col gap-6">
            {{ $slot }}
        </div>
    </div>

</div>

@fluxScripts
</body>
</html>
