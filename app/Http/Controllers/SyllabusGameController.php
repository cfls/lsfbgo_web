<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class SyllabusGameController extends Controller
{
    private const GAME_VIEWS = [
        'jeu-de-memoire' => 'games.memory',
        'jeu-de-lettres'  => 'games.dragdrop',
        'video-quiz'      => 'videointeractiva.videoquiz',

        'types' => [
            'video-choice' => 'types.video-choice',
            'choice'       => 'types.choice',
            'text'         => 'types.text',
            'yes-no'       => 'types.yes-no',
            'match'        => 'types.match',
        ],

        'syntheses' => [
            'video-choice' => 'syntheses.video-choice',
            'choice'       => 'syntheses.choice',
            'text'         => 'syntheses.text',
            'yes-no'       => 'syntheses.yes-no',
            'match'        => 'syntheses.match',
        ],
    ];

    public function index(string $slug, string $type, string $theme, ?string $subtheme = null)
    {



        $view = self::GAME_VIEWS['types'][$type] ?? 'syllabus.questions';



        return view($view, compact('slug', 'type', 'theme', 'subtheme'));
    }

    public function syntheses(string $slug, string $type, ?string $syntheses = null)
    {
        $view = self::GAME_VIEWS['syntheses'][$type];
        return view($view, compact('slug', 'type', 'syntheses'));
    }
}
