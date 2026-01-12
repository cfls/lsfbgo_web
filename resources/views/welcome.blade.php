<x-layouts.app.home>
    <div class="flex flex-col items-center justify-center min-h-screen
            bg-gradient-to-b dark:bg-[var(--color-primary-foreground)]">

        <!-- Logo / Marca -->
        <div class="flex flex-col items-center gap-2">
            @include('partials.quiz.svg.logo', ['class' => 'w-48 h-48 mb-4'])

            <p class="text-zinc-600 dark:text-zinc-300 text-center max-w-md w-1/2">
                Apprenez la langue des signes d’une manière ludique, étape par étape!
            </p>
        </div>

        <!-- Botones -->
        <div class="mt-10  max-w-sm flex flex-col gap-4 text-center">


         <a href="{{ route('access.login') }}" class="bg-orange-500 p-2 text-sm rounded-sm text-white"> Se connecter</a>
         <a href="{{ route('access.register') }}" class="bg-orange-500 text-sm p-2 rounded-sm text-white">S'inscrire gratuitement</a>

        </div>

        <!-- Footer -->
        <div class="mt-8 text-sm text-zinc-500 dark:text-zinc-400">
            <span>© {{ date('Y') }} LSFBGo </span>
        </div>
    </div>
</x-layouts.app.home>