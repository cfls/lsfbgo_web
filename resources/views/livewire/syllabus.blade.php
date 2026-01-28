
@if($selectedTheme)

     {{-- Vista de tema específico --}}
     @include('livewire.syllabus.theme-detail')
@else

     {{-- Vista de listado de todos los temas --}}
     @include('livewire.syllabus.theme-list')
@endif