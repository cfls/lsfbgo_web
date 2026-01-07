<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;
use Native\Mobile\Attributes\OnNative;
use Native\Mobile\Events\Alert\ButtonPressed;
use Native\Mobile\Facades\Browser;
use Native\Mobile\Facades\Dialog;
use Native\Mobile\Facades\System;

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
    public function openPaymentModal($link)
    {

        $this->selectedLink = $link;

        Dialog::alert(
            'Accès Syllabus',
            'Ce contenu nécessite l\'achat du livre Syllabus. Voulez-vous ouvrir la boutique maintenant?',
            [
                'Oui, ouvrir la boutique',
                'Non, plus tard'
            ]
        )->id('alert-demo');;


    }

    #[OnNative(ButtonPressed::class)]
    public function handleAlert(int $index, string $id): void
    {
        if ($id === 'alert-demo' && $index === 0 && $this->selectedLink) {
            Browser::open($this->selectedLink);
        }
    }

// cerrar modal
    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->selectedSyllabuForPayment = null;
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
