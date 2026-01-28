<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class SyllabusGameController extends Controller
{
    private const GAME_VIEWS = [
        'jeu-de-memoire' => 'games.memory',
        'jeu-de-lettres' => 'games.dragdrop',
        'video-quiz' => 'videointeractiva.videoquiz',
        'video-choice' => 'types.video-choice',
        'choice' => 'types.choice',
        'text' => 'types.text',
        'yes-no' => 'types.yes-no',
        'match' => 'types.match'
    ];

    public function index(string $slug, string $type, string $theme)
    {


         //dd(self::GAME_VIEWS[$type]);

        $view = self::GAME_VIEWS[$type] ?? 'syllabus.questions';



        return view($view, compact('slug', 'type', 'theme'));
    }
}
