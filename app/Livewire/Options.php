<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Component;

class Options extends Component
{
    public ?string $ue = null;
    public ?string $type = null;
    public ?string $ueLabel = null;

    public function mount(?string $ue = null, ?string $type = null)
    {
        $this->ue   = $ue;
        $this->type = $type;

        if (!session('token') || !session('data')) {
            $this->redirect(route('home'), navigate: true);
        }
    }

    /** Cliente HTTP preconfigurado. */
    private function api(string $path): array
    {
        return Http::withOptions([
            'verify'          => env('API_VERIFY_SSL', true),
            'timeout'         => 30,
            'connect_timeout' => 10,
        ])
            ->withToken(session('token'))
            ->acceptJson()
            ->get(config('services.api.url') . $path)
            ->json('data', []);
    }

    private function gameTitle(): string
    {
        return match ($this->type) {
            'text'   => 'Traduis en français',
            'choice' => 'Choisis le bon mot',
            'match'  => 'Associer les paires',
            default  => 'Question surprise',
        };
    }

    private static function textColorForBg(string $hex): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;
        $luminance = 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;

        return $luminance > 0.5 ? '#000000' : '#ffffff';
    }

    public function render()
    {

        if ($this->type === 'recap') {
            return view('syllabus.theme_all',['ue' => $this->ue])
                ->layout('components.layouts.app.home', ['title' => 'Récapitulation']);
        }

        $userId = session('data')['user']['id'];

        $syllabus = $this->api('/v1/syllabus/settings/' . $this->ue);
        $bgClass  = $syllabus['attributes']['hex_color'] ?? '#e5e7eb';
        $this->ueLabel = strtoupper(
            Str::of($syllabus['attributes']['slug'] ?? $this->ue)
                ->before('-themes')
                ->replace('ue', 'UE ')
        );

        $themes     = $this->api('/v1/themes/' . $this->ue);
        $doneThemes = collect($this->api('/v1/quiz-results/' . $userId));

        $cards = collect($themes)->map(function ($theme, $index) use ($doneThemes, $bgClass) {
            $slug = $theme['attributes']['slug'];

            $done = $doneThemes->contains(fn ($item) =>
                ($item['theme'] ?? null)    === $slug &&
                ($item['type'] ?? null)     === $this->type &&
                ($item['syllabus'] ?? null) === $this->ue
            );

            $colorHex = $done ? '#519A66' : $bgClass;

            return [
                'iteration' => $index + 1,
                'title'     => ucfirst($theme['attributes']['title']),
                'colorHex'  => $colorHex,
                'textColor' => self::textColorForBg($colorHex),
                'done'      => $done,
                'link'      => route('syllabus.play', [
                    'ue'    => $this->ue,
                    'type'  => $this->type,
                    'theme' => $slug,
                ]),
            ];
        })->all();

        return view('livewire.options', [
            'title' => $this->gameTitle(),
            'cards' => $cards,
        ])->layout('components.layouts.app.home', ['title' => 'Questions Options']);
    }
}