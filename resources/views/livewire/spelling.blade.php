<div class="space-y-6">
    <!-- Header with Gradient -->
    <div
            class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] rounded-none border-none ">
        <div class="px-4">
            <div class="p-2 inline-block">

                @include('partials.quiz.svg.logo')

                <flux:subheading class="text-white text-xl pb-4">
                    {{ $title }}
                </flux:subheading>
            </div>
        </div>
    </div>
    <div class="flex flex-col items-center">
        <div wire:key="player-{{ $this->index }}"
             x-data="{
                playing:true,
                letters: @js($this->letters),
                pos: 0,
                timer: null,
                nextTimer: null,
                isCorrect: @entangle('isCorrect'),
                speedIndex: 1, // 0 = 0.5x, 1 = 1x, 2 = 1.5x
                speeds: [1200, 600, 400],
                labels: ['0.5×', '1×', '1.5×'],

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
                toggleSpeed(){
                    this.speedIndex = (this.speedIndex + 1) % this.speeds.length;
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
                                // correcto → avanzar al siguiente
                                this.nextTimer = setTimeout(() => { $wire.next(); }, 1000);
                            } else if (v === false) {
                                // incorrecto → reiniciar todo
                                this.nextTimer = setTimeout(() => { $wire.restart(); }, 3000);
                            }
                        });
                },
                 apiUrl: '{{ env('API_SITE') }}',
                 srcFor(ch) { return `${this.apiUrl}/img/letters/${ch}.png`; }
             }">

            <!-- Titre et progression -->
            <div class="w-full max-w-2xl">

                @php
                    $percent = $this->roundTotal ? min(100, (int)(($this->score / $this->roundTotal) * 100)) : 0;
                @endphp
                <div class="bg-emerald-500 h-3 rounded-full transition-all" style="width: {{ $percent }}%"></div>
                <div class="text-sm text-gray-600 dark:text-white mb-6 flex justify-between">
                    <span>Exercice : {{ min($this->score + 1, $this->roundTotal) }} / {{ $this->roundTotal }}</span>
                    <span>Score : {{ $this->score }} / {{ $this->roundTotal }}</span>
                </div>
            </div>

            @if($this->currentWord)
                <!-- Lecteur d’épellation -->
                <div class="w-full max-w-2xl">
                    <div class="bg-white p-4">
                        <div class="flex items-center justify-center gap-3 mb-4">

                            <template x-if="letters.length">
                                <img
                                        :src="srcFor(letters[pos])"
                                        :alt="`Lettre ${letters[pos]}`"
                                        class="object-contain select-none

                           w-40 h-40          <!-- móvil -->
                           md:w-56 md:h-56    <!-- tablet / desktop -->
                           lg:w-64 lg:h-64    <!-- pantallas grandes -->
                    "
                                />
                            </template>

                        </div>

                        <!-- Control de velocidad -->
                        <div class="flex items-center justify-center gap-2">
                            <button class="px-3 py-2 bg-gray-100 rounded hover:bg-gray-200 dark:bg-gray-300 dark:hover:bg-gray-400"
                                    @click="toggleSpeed()"
                                    x-text="labels[speedIndex]">
                            </button>
                        </div>
                    </div>
                </div>


                <!-- Réponse -->
                <div class="w-full max-w-2xl h-[250px] px-3 py-6">

                    <form>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">Écris le mot :</label>
                        <input
                                type="text"
                                wire:model.defer="answer"
                                class="w-full rounded-xl border border-gray-300 bg-white text-black px-4 py-3 text-lg shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 transition"
                                autocomplete="off"
                        />

{{--                        @if(!is_null($this->isCorrect))--}}
{{--                            <div class="mt-3 text-sm {{ $this->isCorrect ? 'text-emerald-700' : 'text-red-700' }}">--}}
{{--                                @if($this->isCorrect)--}}
{{--                                    ✅ Correct !--}}
{{--                                @else--}}
{{--                                    ❌ Incorrect. Le mot était : <span class="font-semibold">{{ $this->currentWord }}</span>--}}
{{--                                @endif--}}
{{--                            </div>--}}
{{--                        @endif--}}

                        <div class="mt-4 flex items-center gap-2">
                            <flux:button variant="primary" color="orange" class="text-white" wire:click.prevent="checkAnswer">Vérifier</flux:button>
                            <flux:button variant="primary" color="blue" @click="repeat()">Répéter</flux:button>
                            <flux:button  variant="primary" color="sky" @click="restart()">Nouveau</flux:button>
                        </div>
                    </form>
                </div>
            @else
                <!-- Fin -->
                <div class="w-full max-w-2xl bg-white border rounded-xl shadow p-6 text-center">
                    <p class="text-xl font-semibold mb-2">Tu as terminé !</p>
                    <p class="text-gray-700 mb-4">Score : {{ $this->score }} / {{ $this->total }}</p>
                    <flux:button  variant="primary" color="cyan" wire:click="restart">Rejouer</flux:button>
                </div>
            @endif
        </div>
    </div>

</div>
