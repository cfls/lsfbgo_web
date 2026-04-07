<?php

namespace App\Livewire;

use App\Services\ApiService;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Native\Mobile\Facades\Browser as BrowserFacade;
use Native\Mobile\Facades\SecureStorage;

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
    public $syllabusData; // Datos del syllabus desde API
    public $color = '#000000'; // Color por defecto
    public $selectedSyllabus = null;


public function mount(?string $ue = null): void
{
    $this->ue = $ue; // ← faltaba esta línea

    $storedData = SecureStorage::get('data');
    $data = json_decode($storedData, true);

    if ($this->ue) {
        $syllabusResponse = Http::withOptions([
            'verify' => env('API_VERIFY_SSL', true),
            'timeout' => 60,
            'connect_timeout' => 10,
        ])
            ->withToken($data['token'])
            ->acceptJson()
            ->get(config('services.api.url') . '/v1/syllabus/settings/' . $this->ue);

         

        $this->syllabusData = $syllabusResponse->json('data', []);
    }



    $this->allThemes();
    $this->loadTheme('ue1-themes');
}

    public function allThemes()
    {
        $api = app(ApiService::class);
        $response = $api->allThemes();
    
    
        $this->themes = $response->json('data', []);

    }

    public function loadTheme($ue): void
    {
    
        $api = app(ApiService::class);
        $storedData = SecureStorage::get('data');
        $data = json_decode($storedData, true);

        $this->ue = $ue;

        $response = Http::withOptions(['verify' => env('API_VERIFY_SSL', true)])
            ->withToken($data['token'])
            ->acceptJson()
            ->get(config('services.api.url') . '/v1/sections/' . $this->ue);


        $this->results = $response->json('data', []);

        $syllabusData = $api->ThemeColor($this->ue);
        $this->color = $syllabusData->json('data.attributes.hex_color', '#000000');
    }


    public function updatedSelectedSyllabus($value): void
    {
        
        
        $this->loadTheme($value);
    }


    public function render()
    {
     
        return view('livewire.syllabus-games')->layout('components.layouts.app.home');
    }
}
