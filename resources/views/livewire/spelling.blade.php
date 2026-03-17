<div class="space-y-6">
    <!-- Header -->
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
        <div wire:key="player-{{ $this->index }}"
             x-data="{
                playing: true,
                letters: @js($this->letters),
                pos: 0,
                timer: null,
                nextTimer: null,
                isCorrect: @entangle('isCorrect'),
                showFeedback: false,
                speedIndex: 1,
                speeds: [1200, 600, 400],
                labels: ['×0.5', '×1', '×1.5'],

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
                        this.pos++;
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
                                $wire.restart();
                            }, 3000);
                        }
                    });
                },
                apiUrl: '{{ env('API_SITE') }}',
                srcFor(ch) { return `${this.apiUrl}/img/letters/${ch}.png`; }
             }">

            <!-- Progresión -->
          <div class="w-full max-w-2xl px-3">
            @php
                $percent = $this->roundTotal ? min(100, (int)(($this->score / $this->roundTotal) * 100)) : 0;
            @endphp
            <div class="bg-gray-200 dark:bg-zinc-700 h-3 rounded-full overflow-hidden">
                <div class="bg-emerald-500 h-3 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
            </div>
        </div>

            @if($this->currentWord)
                <!-- Imagen letra -->
                <div class="w-full max-w-2xl">
                   <div class="bg-white p-4">

                        {{-- Imagen letra --}}
                        <div class="flex items-center justify-center mb-4">
                            <template x-if="letters.length">
                                <img
                                    :src="srcFor(letters[pos])"
                                    :alt="`Lettre ${letters[pos]}`"
                                    class="object-contain select-none w-40 h-40 md:w-56 md:h-56 lg:w-64 lg:h-64"
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

                <!-- Input -->
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
                        @keydown.enter.prevent="$wire.checkAnswer()"
                    />
                </div>
                  {{-- Feedback debajo de botones --}}
                        <div class="flex justify-center mt-4 min-h-24">

                            <div
                                x-show="showFeedback && isCorrect"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 scale-75"
                                x-transition:enter-end="opacity-100 scale-100"
                                class="flex flex-col items-center gap-2"
                                style="display:none"
                            >
                                <img src="{{ asset('img/lsfgo/good.png') }}" alt="Correct" class="w-24 h-24 object-contain">
                                <span class="text-emerald-600 font-bold text-lg">Bravo ! C'est la bonne réponse.</span>
                            </div>

                            <div
                                x-show="showFeedback && !isCorrect"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 scale-75"
                                x-transition:enter-end="opacity-100 scale-100"
                                class="flex flex-col items-center gap-2"
                                style="display:none"
                            >
                                <img src="{{ asset('img/lsfgo/bad.png') }}" alt="Incorrect" class="w-24 h-24 object-contain">
                                <span class="text-red-500 font-bold text-lg">Dommage !</span>
                                <span class="text-gray-500 text-lg">
                                    La bonne réponse est : <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $this->currentWord }}</span>
                                </span>
                            </div>

                </div>

            @else
                <!-- Fin -->
                <div class="w-full max-w-2xl bg-white border rounded-xl shadow p-6 text-center">
                    <p class="text-xl font-semibold mb-2">Tu as terminé !</p>
                    <p class="text-gray-700 mb-4">Score : {{ $this->score }} / {{ $this->total }}</p>
                    <flux:button variant="primary" color="cyan" wire:click="restart">Rejouer</flux:button>
                </div>
            @endif

        </div>
    </div>
</div>