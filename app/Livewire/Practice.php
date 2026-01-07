<?php

namespace App\Livewire;

use Livewire\Component;

class Practice extends Component
{
    public string $title = 'Exercices LSFB';
    public function render()
    {

        $topics = [
            [
                'title' => 'Les chiffres de 0 à 1000',
                'icon' => 'numbered-list',
                'route' => 'numbers.practice',
                'gradient' => 'from-indigo-500 to-blue-500 dark:from-indigo-400 dark:to-blue-400',
            ],
            [
                'title' => 'Comprendre et écrire les mots épelés',
                'icon' => 'hand-raised',
                'route' => 'alphabet.practice',
                'gradient' => 'from-red-500 to-pink-500 dark:from-red-400 dark:to-pink-400',
            ],
            [
                'title' => 'Jeux',
                'icon' => 'hand-thumb-up',
                'route' => 'syllabus',
                'gradient' => 'from-red-500 to-pink-500 dark:from-red-400 dark:to-pink-400',
            ],


        ];



        return view('livewire.practice', compact('topics'))->layout('components.layouts.app.home', [
            'title' => 'Exercices LSFB',
        ]);
    }
}
