<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ApiService
{
    protected string $baseUrl;
    protected bool $verifySsl;

    public function __construct()
    {
        $this->baseUrl = config('services.api.url');
        $this->verifySsl = env('API_VERIFY_SSL', true);
    }

    public function getAuthenticatedUser(): Response
    {
        return Http::withOptions([
            'verify' => $this->verifySsl,
        ])
            ->asJson()
            ->acceptJson()
            ->withToken(session('token'))
            ->get($this->baseUrl . 'auth/user');
    }



    /**
     * Realizar petición POST a la API
     */
    public function post(string $endpoint, array $data = []): Response
    {
        return Http::withOptions(['verify' => $this->verifySsl])
            ->asJson()
            ->acceptJson()
            ->post($this->baseUrl.$endpoint, $data);
    }

    /**
     * Realizar petición GET a la API
     */
    public function get(string $endpoint, array $params = [], ?string $token = null): Response
    {
        $token = $token ?? session('data.token') ?? session('token');

        return Http::withOptions(['verify' => $this->verifySsl])
            ->asJson()
            ->acceptJson()
            ->withToken($token)
            ->get($this->baseUrl.$endpoint, $params);
    }

    /**
     * Realizar petición con token de autenticación
     */


    /**
     * Login del usuario
     */
    public function login(string $email, string $password, string $deviceName = 'web'): Response
    {
        return $this->post('/auth/login', [
            'email' => $email,
            'password' => $password,
            'device_name' => $deviceName,
        ]);
    }

    /**
     * Registro del usuario
     */
    public function register(array $data): Response
    {
        return $this->post('/auth/register', array_merge($data, [
            'device_name' => $data['device_name'] ?? 'web',
        ]));
    }

    public function sendPasswordResetLink(string $email): Response
    {

        return $this->post('/auth/forgot-password', [
            'email' => $email,
        ]);
    }

    public function verifyEmail(string $email, string $code): Response
    {
        return $this->post('/auth/verify-email', [
            'email' => $email,
            'code' => $code,
        ]);
    }

    public function sendVerificationCode(string $email): Response
    {
        return $this->post('/auth/verify-code', [
            'email' => $email,
        ]);
    }


    /**
     * Obtener palabras para ejercicio de ortografía
     */
    public function getSpellings(?string $token = null): Response
    {
        return Http::withOptions([
            'verify' => env('API_VERIFY_SSL', true),
        ])
            ->withToken($token)
            ->acceptJson()
            ->get(config('services.api.url') . '/v1/spellings');
    }

    public function allThemes()
    {

        return $this->get('/v1/verify-codes/' . session('data.user.id'));
    }

    public function MemberSyllabus(?string $token = null)
    {

        return $this->get('/v1/syllabus', [ 'token' => $token ]);
    }


    public function ThemeSyllabus($ue, $theme)
    {

        return $this->get('/v1/themes/'. $ue .'/'. $theme);
    }

    public function ThemeSign($ue,$theme)
    {

        return $this->get('/v1/themes/' . $ue . '/' . $theme);
    }




}