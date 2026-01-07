<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Native\Mobile\Facades\Dialog;

class Syllabus extends Component
{
    public string $title = 'Mes Syllabus';
    public $verifyUser;
    public $selectUE = [];   // aquí debe ser array, no string
    public $selectedUE = null;
    public $search = '';
    public $results = [];
    public $selectedTheme = false;
    public $themes = [];
    public $showPaymentModal = false;
    public $selectedSyllabuForPayment = null;
    public $selectedLink = null;
    public ?string $ue = null;


    public function mount(?string $ue = null)
    {
        $this->ue = $ue;

        if ($this->ue) {
            // Lógica cuando hay un UE específico
            $this->loadTheme($this->ue);
        } else {
            // Lógica para listado general
            $this->loadAllThemes();
        }
    }

    public function loadAllThemes()
    {
        $responseUser = Http::withOptions([
            'verify' => env('API_VERIFY_SSL', true),
        ])
            ->withToken(session('data.token'))
            ->acceptJson()
            ->get(config('services.api.url') . '/v1/verify-codes/' . session('data.user.id'));
        // save in public property
        $verifyUser = $responseUser->json('data', []);

        $this->verifyUser = collect($verifyUser);


        $response = Http::withOptions([
            'verify' => env('API_VERIFY_SSL', true),
        ])
            ->withToken(session('data.token'))
            ->acceptJson()
            ->get(config('services.api.url') . '/v1/syllabus');
        // Aquí ya tienes un array asociativo
        $this->results = $response->json('data', []);
    }


    public function loadTheme($ue)
    {
        $response = Http::withOptions([
            'verify' => env('API_VERIFY_SSL', true),
        ])
            ->withToken(session('data.token'))
            ->acceptJson()
            ->get(config('services.api.url') . '/v1/themes/' . $ue);
        // save in public property

        $this->selectedTheme = $response->json('data', []);
    }


    // abrir modal
    public function openPaymentModal($slug, $link)
    {
        $this->selectedSyllabuForPayment = $slug;
        $this->selectedLink = $link;
        $this->showPaymentModal = true;
    }

// cerrar modal
    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->selectedSyllabuForPayment = null;
    }

    public function alert(){
        Dialog::alert('Alert', 'Is NativePHP the BEST way to build native mobile apps with PHP?', [
            'Yup ✅',
            'No Way! ⛔',
            "It's the best 😎!",
        ])->id('alert-demo');
    }

    public function render()
    {
        return view('livewire.syllabus', [
            'selectedTheme' => $this->selectedTheme,

        ])->layout('components.layouts.app.home',[
            'title' => 'Syllabus',
        ]);
    }
}
