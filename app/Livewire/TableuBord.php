<?php

namespace App\Livewire;

use Livewire\Component;

class TableuBord extends Component
{
    public function render()
    {
        $featuredDemos = [

//            [
//                'title' => 'Sacnner QR Code',
//                'icon' => 'qr-code',
//                'route' => 'scanner',
//                'gradient' => 'from-indigo-500 to-blue-500 dark:from-indigo-400 dark:to-blue-400',
//            ],
            [
                'title' => 'Dictionnaire LSFB',
                'icon' => 'book-open',
                'route' => 'dictionary',
                'gradient' => 'from-indigo-500 to-blue-500 dark:from-indigo-400 dark:to-blue-400',
            ],
            [
                'title' => 'Exercices LSFB',
                'icon' => 'hand-thumb-up',
                'route' => 'practice',
                'gradient' => 'from-red-500 to-pink-500 dark:from-red-400 dark:to-pink-400',
            ],

        ];


        return view('livewire.tableu-bord', compact('featuredDemos'))
            ->layout('components.layouts.app.home', [
                'title' => 'Tableau de bord',
            ]);
    }
}
