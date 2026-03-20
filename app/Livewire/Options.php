<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Native\Mobile\Facades\SecureStorage;

class Options extends Component
{

    public $results = [];
    public $themes = [];
    public $doneThemesData = [];
    public $type;
    public ?string $ue = null;
    public $syllabusData; // Datos del syllabus desde API
    public $doneThemes;


    public function mount(?string $ue = null)
    {
        $this->ue = $ue;



        $storedData = SecureStorage::get('data');
        $data = json_decode($storedData, true);

        // Cargar datos del syllabus desde la API
        $syllabusResponse = Http::withOptions([
            'verify' => env('API_VERIFY_SSL', true),
        ])
            ->withToken($data['token'])
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

        $storedData = SecureStorage::get('data');
        $data = json_decode($storedData, true);

        if($this->type == 'tous') { /** OJO */
            // return view('syllabus.theme_all')->layout('components.layouts.app.home', [
            //     'title' => 'Questions Options',
            // ]);

            return view('syllabus.theme_nevel')->layout('components.layouts.app.home', [
                'title' => 'Questions Options',
            ]);

        } else {
            $response = Http::withOptions([
                'verify' => env('API_VERIFY_SSL', true),
            ])
                ->withToken($data['token'])
                ->acceptJson()
                ->get(config('services.api.url') . '/v1/themes/' . $this->ue);
            // save in public property
            $this->themes = $response->json('data', []);

            // pedir lista de themes ya resueltos
            $doneThemesResponse = Http::withOptions([
                'verify' => env('API_VERIFY_SSL', true),
            ])
                ->withToken($data['token'])
                ->acceptJson()
                ->get(config('services.api.url') . '/v1/quiz-results/' . $data['user']['id']);

            $this->doneThemesData = $doneThemesResponse->json('data', []);

            // Extraer solo los slugs ya completados
            $syllabusResponseResult = Http::withOptions([
                'verify' => env('API_VERIFY_SSL', true),
            ])
                ->withToken($data['token'])
                ->acceptJson()
                ->get(config('services.api.url') . '/v1/quiz-results/' . $data['user']['id']);

            $this->doneThemes = collect($syllabusResponseResult->json('data', []))
                ->map(fn($item) => [
                    'syllabus' => $item['syllabus'],
                    'theme' => $item['theme'] ?? null,
                    'type'  => $item['type'] ?? null,
                ])
                ->toArray();



            return view('livewire.options')->layout('components.layouts.app.home', [
                'title' => 'Questions Options',
            ]);


        }






    }
}
