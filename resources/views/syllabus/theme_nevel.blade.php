<div class="grid grid-cols-2 gap-3 p-4">
    @foreach([
        ['niveau' => 1, 'label' => 'Débutant',      'desc' => 'Premiers signes de base',   'color' => 'green'],
        ['niveau' => 2, 'label' => 'Élémentaire',   'desc' => 'Vocabulaire courant',        'color' => 'green'],
        ['niveau' => 3, 'label' => 'Intermédiaire', 'desc' => 'Phrases et expressions',     'color' => 'blue'],
        ['niveau' => 4, 'label' => 'Avancé',        'desc' => 'Conversations fluides',      'color' => 'blue'],
        ['niveau' => 5, 'label' => 'Expert',        'desc' => 'Sujets complexes',           'color' => 'amber'],
        ['niveau' => 6, 'label' => 'Maître',        'desc' => 'Niveau natif complet',       'color' => 'red'],
    ] as $n)
        @php
            $badge = match($n['color']) {
                'green' => 'bg-green-100 text-green-700',
                'blue'  => 'bg-blue-100 text-blue-700',
                'amber' => 'bg-amber-100 text-amber-700',
                'red'   => 'bg-red-100 text-red-700',
                default => '',
            };
        @endphp

        <a href="#"
           class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl p-4 flex flex-col gap-2 hover:border-gray-400 transition">
            <span class="text-xs font-medium px-2 py-1 rounded-lg w-fit {{ $badge }}">
                Niveau {{ $n['niveau'] }}
            </span>
            <p class="font-medium text-gray-900 dark:text-white text-sm">{{ $n['label'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $n['desc'] }}</p>
        </a>
    @endforeach
</div>