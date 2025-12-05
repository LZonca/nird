<div class="w-full flex justify-center items-start gap-10 mt-10">

    {{-- Colonne gauche --}}
    <div id="left-col" class="flex flex-col gap-4">
        @foreach($leftList as $item)
            <div class="left-item p-3 bg-blue-200 rounded cursor-pointer"
                 data-id="{{ $item['id'] }}">
                {{ $item['text'] }}
            </div>
        @endforeach
    </div>

    {{-- Canvas pour les lignes --}}
    <canvas id="pair-canvas" class="border" width="500" height="600"></canvas>

    {{-- Colonne droite --}}
    <div id="right-col" class="flex flex-col gap-4">
        @foreach($rightList as $item)
            <div class="right-item p-3 bg-pink-200 rounded cursor-pointer"
                 data-id="{{ $item['id'] }}">
                {{ $item['text'] }}
            </div>
        @endforeach
    </div>

</div>

<script src="./../../js/app.js"></script>
