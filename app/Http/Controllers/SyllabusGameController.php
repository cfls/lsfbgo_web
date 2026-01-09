<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class SyllabusGameController extends Controller
{
    private const GAME_VIEWS = [
        'jeu-de-memoire' => 'games.memory',
        'jeu-de-lettres' => 'games.dragdrop',
        'video-quiz' => 'videointeractiva.videoquiz',
    ];

    public function index(string $slug, string $type, string $theme)
    {
        $view = self::GAME_VIEWS[$type] ?? 'syllabus.questions';



        return view($view, compact('slug', 'type', 'theme'));
    }
}
