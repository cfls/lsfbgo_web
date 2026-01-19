@php
    use Illuminate\Support\Str;
    $ue = Str::before($slug, '-');
    $ue = strtoupper(Str::replace('ue', 'UE ', $ue));
@endphp

<div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] rounded-none border-none">
    <div class="px-3 py-2">
        <div class="flex items-center gap-2">
            @include('partials.quiz.svg.logo', ['class' => 'w-8 h-8'])
        </div>
    </div>
</div>