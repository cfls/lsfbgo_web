<?php

namespace App\Livewire;

use App\Services\ApiService;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Native\Mobile\Attributes\OnNative;
use Native\Mobile\Events\Alert\ButtonPressed;
use Native\Mobile\Facades\Browser;
use Native\Mobile\Facades\Browser as BrowserFacade;
use Native\Mobile\Facades\Dialog;

class SyllabusGames extends Component
{
    public string $title = 'Jeux LSFB';
    public int $optionGame = 1;
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
    public $sections = [];



    public function mount(?string $ue = null)
    {




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
        $api = app(ApiService::class);



        // Si allThemes() requiere autenticación, pasa el token
        $responseUser = $api->allThemes();
        $verifyUser = $responseUser->json('data', []);
        $this->verifyUser = collect($verifyUser);

        // Asegúrate de que el token sea consistente
        $token = session('data.token') ?? session('token');

        // Usa el mismo token
        $response = $api->MemberSyllabus($token);

        $this->results = $response->json('data', []);
    }


    public function loadTheme($ue)
    {


        $response = Http::withOptions([
            'verify' => env('API_VERIFY_SSL', true),
        ])
            ->withToken(session('data.token'))
            ->acceptJson()
            ->get(config('services.api.url') . '/v1/sections/' . $ue);
        // save in public property

        $this->results = $response->json('data', []);


    }


    // abrir modal
//    public function openPaymentModal($link)
//    {
//
//        $this->selectedLink = $link;
//
//        Dialog::alert(
//            'Accès Syllabus',
//            'Ce contenu nécessite l\'achat du livre Syllabus. Voulez-vous ouvrir la boutique maintenant?',
//            [
//                'Oui, ouvrir la boutique',
//                'Non, plus tard'
//            ]
//        )->id('alert-demo');;
//
//
//    }
//
//    #[OnNative(ButtonPressed::class)]
//    public function handleAlert(int $index, string $id): void
//    {
//        if ($id === 'alert-demo' && $index === 0 && $this->selectedLink) {
//            Browser::open($this->selectedLink);
//        }
//    }

    public function openInApp()
    {
        BrowserFacade::inApp('https://www.facebook.com/share/v/1BepzAgdKA');
    }


// cerrar modal
    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->selectedSyllabuForPayment = null;
    }


    public function render()
    {
        return view('livewire.syllabus-games')->layout('components.layouts.app.home', [
            'title' => 'Jeux LSFB',
        ]);
    }
}
