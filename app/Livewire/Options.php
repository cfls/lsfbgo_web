<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Http;
use Livewire\Component;

class Options extends Component
{

    public $results = [];
    public $themes = [];
    public $doneThemesData = [];
    public $type;
    public ?string $ue = null;
    public $syllabusData; // Datos del syllabus desde API


    public function mount(?string $ue = null)
    {
        $this->ue = $ue;

        // Cargar datos del syllabus desde la API
        $syllabusResponse = Http::withOptions([
            'verify' => env('API_VERIFY_SSL', true),
        ])
            ->withToken(session('data.token'))
            ->acceptJson()
            ->get(config('services.api.url') . '/v1/syllabus/settings/' . $this->ue);


        $this->syllabusData = $syllabusResponse->json('data', []);




//        if($this->type == 'tous') {
//
//            $response = Http::withOptions([
//                'verify' => env('API_VERIFY_SSL', true),
//            ])
//                ->withToken(session('data.token'))
//                ->acceptJson()
//                ->get(config('services.api.url').'/v1/questions/'.$this->ue);
//        }
//        else {
//
//            $response = Http::withOptions([
//                'verify' => env('API_VERIFY_SSL', true),
//            ])
//                ->withToken(session('data.token'))
//                ->acceptJson()
//                ->get(config('services.api.url').'/v1/sections/'.$this->ue);
//        }
//        // Guardar la respuesta
//
//
//
//
//        $this->results = $response->json('data', []);
    }





    public function render()
    {



        if($this->type == 'tous') {
            return view('syllabus.theme_all')->layout('components.layouts.app.home', [
                'title' => 'Questions Options',
            ]);

        } else {
            $response = Http::withOptions([
                'verify' => env('API_VERIFY_SSL', true),
            ])
                ->withToken(session('data.token'))
                ->acceptJson()
                ->get(config('services.api.url') . '/v1/themes/' . $this->ue);
            // save in public property
            $this->themes = $response->json('data', []);

            // pedir lista de themes ya resueltos
            $doneThemesResponse = Http::withOptions([
                'verify' => env('API_VERIFY_SSL', true),
            ])
                ->withToken(session('data.token'))
                ->acceptJson()
                ->get(config('services.api.url') . '/v1/quiz-results/' . session('data.user.id'));

            $this->doneThemesData = $doneThemesResponse->json('data', []);

            return view('livewire.options')->layout('components.layouts.app.home', [
                'title' => 'Questions Options',
            ]);


        }






    }
}
