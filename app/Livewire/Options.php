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
    public function mount(?string $ue = null)
    {
        $this->ue = $ue;



        if($this->type == 'tous') {

            $response = Http::withOptions([
                'verify' => env('API_VERIFY_SSL', true),
            ])
                ->withToken(session('data.token'))
                ->acceptJson()
                ->get(config('services.api.url').'/v1/questions/'.$ue);
        }
        else {

            $response = Http::withOptions([
                'verify' => env('API_VERIFY_SSL', true),
            ])
                ->withToken(session('data.token'))
                ->acceptJson()
                ->get(config('services.api.url').'/v1/sections/'.$ue.'-themes');
        }
        // Guardar la respuesta



        $this->results = $response->json('data', []);
    }





    public function render()
    {

        if($this->type == 'tous') {

            $response = Http::withOptions([
                'verify' => env('API_VERIFY_SSL', true),
            ])
                ->withToken(session('data.token'))
                ->acceptJson()
                ->get(config('services.api.url') . '/v1/questions/' . $this->ue);
            // save in public property
            $this->themes = $response->json('data', []);




            return view('syllabus.theme_all')->layout('components.layouts.app.home', [
                'title' => 'Questions Options',
            ]);

        } else {
            $response = Http::withOptions([
                'verify' => env('API_VERIFY_SSL', true),
            ])
                ->withToken(session('data.token'))
                ->acceptJson()
                ->get(config('services.api.url') . '/v1/themes/' . $this->ue . '-themes');
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
