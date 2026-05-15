<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] rounded-none border-none">
        <div class="px-3 py-2">
            <div class="flex items-center gap-2">
                @include('partials.quiz.svg.logo', ['class' => 'w-20 h-20'])
                <flux:subheading size="xl" class="text-white text-base">
                    {{ $title }}
                </flux:subheading>
            </div>
        </div>
    </div>

    <div class="flex flex-col items-center">

        {{-- ✅ Selector de nivel --}}
        <div class="w-full max-w-2xl px-3 mb-2">
            <div class="flex items-center justify-center gap-2">

                <div class="flex gap-2 mt-2">
                    @foreach(['easy' => '🟢 Facile', 'medium' => '🟡 Moyen', 'hard' => '🔴 Difficile'] as $level => $label)
                        <button
                                wire:click="$set('difficulty', '{{ $level }}')"
                                class="px-3 py-1.5 rounded-full text-sm font-semibold border transition active:scale-95"
                                @class([
                                    'bg-teal-500 border-teal-500 text-white'                                                          => $difficulty === $level,
                                    'bg-white dark:bg-zinc-800 border-gray-200 dark:border-zinc-700 text-gray-700 dark:text-gray-200' => $difficulty !== $level,
                                ])
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div wire:key="player-{{ $this->index }}-{{ $difficulty }}"
             x-data="{
                playing: true,
                letters: @js($this->letters),
                pos: 0,
                timer: null,
                nextTimer: null,
                isCorrect: @entangle('isCorrect'),
                showFeedback: false,
                speedIndex: 2,
                speeds: [1200, 600, 400],
                labels: ['×0.5', '×1', '×1.5'],
                showLetter: true,
                blankMs: 200,

                speedMs(){ return this.speeds[this.speedIndex] },

                 start(){
                    this.stop();
                    if (this.letters.length === 0) return;
                    this.playing = true;
                    this.timer = setInterval(() => {
                        if (this.pos >= this.letters.length - 1) {
                            this.stop();
                            return;
                        }
                        const next = this.pos + 1;

                        // Same letter coming up → flash a blank before advancing
                        if (this.letters[next] === this.letters[this.pos]) {
                            this.showLetter = false;
                            setTimeout(() => {
                                this.pos = next;
                                this.showLetter = true;
                            }, this.blankMs);
                        } else {
                            this.pos = next;
                        }
                    }, this.speedMs());
                    },
                stop(){
                    if(this.timer){ clearInterval(this.timer); this.timer = null; }
                    this.playing = false;
                },
                setSpeed(index){
                    this.speedIndex = index;
                    if(this.playing){ this.start(); }
                },
                repeat(){
                    this.stop();
                    this.pos = 0;
                    this.start();
                },
                restart(){ $wire.restart(); },

                init(){
                    this.start();
                    this.$watch('isCorrect', (v) => {
                        if (this.nextTimer) {
                            clearTimeout(this.nextTimer);
                            this.nextTimer = null;
                        }

                        if (v === true) {
                            this.showFeedback = true;
                            this.nextTimer = setTimeout(() => {
                                this.showFeedback = false;
                                $wire.next();
                            }, 1800);
                        } else if (v === false) {
                            this.showFeedback = true;
                            this.nextTimer = setTimeout(() => {
                                this.showFeedback = false;
                                $wire.retryNext(); // ← antes era $wire.restart()
                            }, 3000);
                        }
                    });
                },
                apiUrl: '{{ env('API_SITE') }}',
                srcFor(ch) { return `${this.apiUrl}/img/letters/${ch}.png`; }
             }">

            {{-- Progresión --}}
            <div class="w-full max-w-2xl px-3 mb-5">
                @php
                    $percent = $this->roundTotal ? min(100, (int)(($this->score / $this->roundTotal) * 100)) : 0;
                @endphp
                <div class="flex items-center justify-between mb-1 px-1">
                    <div class="flex items-center gap-3 text-xs">
                        <span class="text-emerald-500 font-semibold">✅ {{ $this->score }}</span>
                        <span class="text-red-500 font-semibold">❌ {{ $this->incorrect }}</span>
                        <span class="text-gray-400">/ {{ $this->roundTotal }}</span>
                    </div>
                    <span class="text-xs font-semibold
                        @if($difficulty === 'easy') text-teal-500
                        @elseif($difficulty === 'medium') text-amber-500
                        @else text-red-500 @endif">
                        @if($difficulty === 'easy') 🟢 Facile
                        @elseif($difficulty === 'medium') 🟡 Moyen
                        @else 🔴 Difficile @endif
                     </span>
                </div>
                {{-- Barra dividida en correctas + incorrectas --}}
                <div class="bg-gray-200 dark:bg-zinc-700 h-3 rounded-full overflow-hidden flex">
                    <div class="h-3 bg-emerald-500 transition-all duration-500"
                         style="width: {{ $this->roundTotal ? min(100, ($this->score / $this->roundTotal) * 100) : 0 }}%">
                    </div>
                    <div class="h-3 bg-red-400 transition-all duration-500"
                         style="width: {{ $this->roundTotal ? min(100, ($this->incorrect / $this->roundTotal) * 100) : 0 }}%">
                    </div>
                </div>
            </div>

            @if($this->currentWord)
                {{-- Imagen letra --}}
                <div class="w-full max-w-2xl">
                    <div class="bg-white dark:bg-zinc-800 p-4 rounded-xl shadow">

                        <div class="flex items-center justify-center mb-4 w-40 h-40 md:w-56 md:h-56 lg:w-64 lg:h-64 mx-auto">

                            <template x-if="letters.length">
                                <img
                                        x-show="showLetter"
                                        :src="srcFor(letters[pos])"
                                        :alt="`Lettre ${letters[pos]}`"
                                        class="object-contain select-none w-full h-full"
                                />
                            </template>
                        </div>

                        {{-- Botones velocidad + repetir --}}
                        <div class="flex items-center justify-center gap-3">
                            <template x-for="(label, index) in labels" :key="index">
                                <button
                                        class="h-10 px-4 rounded-full border text-sm font-bold transition active:scale-95 shadow-sm"
                                        :class="speedIndex === index
                                        ? 'bg-teal-500 border-teal-500 text-white'
                                        : 'bg-white dark:bg-zinc-800 border-gray-200 dark:border-zinc-700 text-gray-700 dark:text-gray-200'"
                                        @click="setSpeed(index)"
                                        x-text="label"
                                ></button>
                            </template>

                            <div class="w-px h-7 bg-gray-200 dark:bg-zinc-700"></div>

                            <button
                                    class="h-10 px-4 rounded-full border text-sm font-bold bg-white dark:bg-zinc-800 border-gray-200 dark:border-zinc-700 text-gray-700 dark:text-gray-200 transition active:scale-95 shadow-sm"
                                    @click="repeat()"
                            >
                                🔁
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Input --}}
                <div class="w-full max-w-2xl px-3 py-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2 pl-2">
                        Écris le mot :
                    </label>
                    <input
                            type="text"
                            wire:model.defer="answer"
                            inputmode="text"
                            enterkeyhint="done"
                            class="w-full rounded-xl border border-gray-300 bg-white text-black px-4 py-3 text-lg shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 transition"
                            autocomplete="off"
                            autocorrect="off"
                            autocapitalize="none"
                            spellcheck="false"
                            @keydown.enter.prevent="$el.blur(); $wire.checkAnswer()"
                    />
                </div>

                {{-- Feedback --}}
                <div
                        x-show="showFeedback"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-full"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-full"
                        class="fixed inset-x-0 bottom-16 z-50 flex flex-col items-center gap-3 pb-8 pt-6 rounded-t-3xl shadow-[0_-4px_16px_rgba(0,0,0,0.1)]"
                        :class="isCorrect
                        ? 'bg-emerald-50 dark:bg-emerald-900'
                        : 'bg-red-50 dark:bg-red-900'"
                        style="display:none"
                >
                    <template x-if="isCorrect">
                        <div class="flex flex-col items-center gap-2">
                            <img src="{{ asset('img/lsfbgo/good.png') }}" alt="Correct" class="w-20 h-20 object-contain">
                            <span class="text-emerald-600 dark:text-emerald-300 font-bold text-xl">Bravo ! C'est la bonne réponse.</span>
                        </div>
                    </template>
                    <template x-if="!isCorrect">
                        <div class="flex flex-col items-center gap-2">
                            <img src="{{ asset('img/lsfbgo/bad.png') }}" alt="Incorrect" class="w-20 h-20 object-contain">
                            <span class="text-red-500 font-bold text-xl">Dommage !</span>
                            <span class="text-gray-600 dark:text-gray-300 text-base">
                                La bonne réponse est :
                                <span class="font-semibold text-red-700 dark:text-red-300">{{ $this->currentWord }}</span>
                            </span>
                        </div>
                    </template>
                </div>

            @else
           
                  {{-- Fin de ronda --}}
                        <div class="w-full max-w-2xl bg-white dark:bg-zinc-800 border rounded-xl shadow p-6 text-center">
                            {{-- Icono según resultado --}}
                            @if($this->score >= 6)
                                <div class="text-5xl mb-3">🎉</div>
                                <p class="text-xl font-semibold mb-1 dark:text-white text-emerald-500">Excellent !</p>
                            @else
                                <img src="{{ asset('img/lsfbgo/bad.png') }}" alt="Dommage" class="w-20 h-20 object-contain mx-auto mb-3">
                                <p class="text-xl font-semibold mb-1 dark:text-white text-red-500">Dommage !</p>
                            @endif

                            <p class="text-gray-500 dark:text-gray-400 mb-1">
                                ✅ <span class="font-bold text-teal-500">{{ $this->score }}</span>
                                &nbsp;·&nbsp;
                                ❌ <span class="font-bold text-red-400">{{ $this->incorrect }}</span>
                                &nbsp;/ {{ $this->total }}
                            </p>
                            <p class="text-sm text-gray-400 dark:text-gray-500 mb-5">
                                Niveau :
                                @if($difficulty === 'easy') 🟢 Facile
                                @elseif($difficulty === 'medium') 🟡 Moyen
                                @else 🔴 Difficile @endif
                            </p>
                            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                                <flux:button variant="primary" color="cyan" wire:click="restart">
                                    🔁 Rejouer ce niveau
                                </flux:button>
                                @if($difficulty !== 'hard')
                                    <flux:button variant="outline" wire:click="$set('difficulty', '{{ $difficulty === 'easy' ? 'medium' : 'hard' }}')">
                                        ⬆️ Niveau supérieur
                                    </flux:button>
                                @endif
                            </div>
                        </div>
            @endif

        </div>
    </div>
</div>