@php
    use Illuminate\Support\Str;
    $ue = Str::before($slug, '-');
    $ue = strtoupper(Str::replace('ue', 'UE ', $ue));
@endphp

<div class="bg-gradient-to-br from-teal-500 to-purple-600 text-white pt-[var(--inset-top)] rounded-none border-none">
    <div class="px-4">
        <div class="p-2 inline-block">
            @include('partials.quiz.svg.logo')
        </div>
    </div>
</div>