<div>
    @if($this->ue)
        {{-- Vista de tema específico --}}
        @include('livewire.syllabus.theme-options')
    @else
        {{-- Vista de listado de todos los temas --}}
        @include('livewire.syllabus.theme-list', ['optionGame' => $this->optionGame])
    @endif
</div>