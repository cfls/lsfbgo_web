<div class="space-y-6">
    <!-- Header with Gradient -->
    <div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] rounded-none border-none">
        <div class="px-3 py-2">
            <div class="flex items-center gap-2">
                <a wire:navigate href="{{ route('practice') }}" class="text-white inline-flex items-center gap-2">
                    <flux:icon.arrow-left-circle class="size-5"/>
                    @include('partials.quiz.svg.logo', ['class' => 'w-8 h-8'])
                </a>
                <flux:subheading class="text-white text-base">
                    {{$title}}
                </flux:subheading>
            </div>
        </div>
    </div>
    <flux:card class="space-y-6"  x-data="{
        slow: false,
        fast: false,
        paused: false,

        setSpeed(video, speed) {
            video.playbackRate = speed;
            this.slow   = (speed === 0.5);
            this.fast   = (speed === 2);
        },

        togglePause(video) {
            if (this.paused) {
                video.play();
            } else {
                video.pause();
            }
            this.paused = !this.paused;
        }
     }">




        <!-- Título -->


        <!-- Video -->

            <video x-ref="video"
                   class=" rounded mb-6"
                   autoplay
                   muted
                   loop
                   playsinline>
                <source src="https://res.cloudinary.com/dmhdsjmzf/video/upload/v1748439117/LES_CHIFFRES_ohbdhd.mp4"
                        type="video/mp4">
            </video>


        <!-- Controles -->
        <div class="flex flex-col sm:flex-row justify-center gap-3 mt-4 w-full px-4">

            <!-- Lent (0.5x) -->
            <button class="w-full sm:w-auto px-4 py-2 bg-yellow-500 text-white rounded shadow"
                    @click="setSpeed($refs.video, 0.5)">
                Lent
            </button>

            <!-- Normal (1x) -->
            <button class="w-full sm:w-auto px-4 py-2 bg-blue-500 text-white rounded shadow"
                    @click="setSpeed($refs.video, 1)">
                Normal
            </button>

            <!-- Rapide (1.5x) -->
            <button class="w-full sm:w-auto px-4 py-2 bg-green-600 text-white rounded shadow"
                    @click="setSpeed($refs.video, 1.5)">
                Rapide
            </button>

            <!-- Pause / Reprendre -->
            <button class="w-full sm:w-auto px-4 py-2 bg-red-600 text-white rounded shadow"
                    @click="togglePause($refs.video)">
                <span x-text="paused ? 'Reprendre' : 'Pause'"></span>
            </button>

        </div>


    </flux:card>

</div>