<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Native\Mobile\Facades\SecureStorage;
use Illuminate\Support\Facades\Log;

class ApiService
{
    protected string $baseUrl;
    protected bool $verifySsl;

    public function __construct()
    {
        $this->baseUrl = config('services.api.url') ?? '';
        $this->verifySsl = env('API_VERIFY_SSL', true);
    }

//    public function getAuthenticatedUser(): Response
//    {
//        return Http::withOptions([
//            'verify' => $this->verifySsl,
//        ])
//            ->asJson()
//            ->acceptJson()
//            ->withToken(SecureStorage::get('token'))
//            ->get($this->baseUrl . 'auth/user');
//    }



    /**
     * Realizar petición POST a la API
     */
  public function post(string $endpoint, array $data = []): Response
    {
        $stored = SecureStorage::get('data');              // ✅ Variable separada
        $token  = json_decode($stored, true)['token'] ?? null;

        Log::info('POST Request', [
            'endpoint' => $endpoint,
            'token'    => $token,
            'data'     => $data,  // Ahora sí muestra el payload real
        ]);

        $response = Http::withOptions(['verify' => $this->verifySsl])
            ->withToken($token)
            ->asJson()
            ->acceptJson()
            ->post($this->baseUrl . $endpoint, $data); // ✅ Envía el código correcto

        // Log::info('POST Response', [
        //     'status' => $response->status(),
        //     'body'   => $response->json(),
        // ]);

        return $response;
    }

    /**
     * Realizar petición GET a la API
     */
    public function get(string $endpoint, array $params = [], ?string $token = null): Response
    {
        // Si no se proporciona token, intentar obtenerlo
        if (!$token) {
            // Opción 1: Token directo
            $token = SecureStorage::get('token');

            // Opción 2: Token dentro de 'data'
            if (!$token) {
                $storedData = SecureStorage::get('data');
                if ($storedData) {
                    $data = json_decode($storedData, true);
                    $token = $data['token'] ?? null;
                }
            }
        }

        // Si aún no hay token, lanzar excepción
        if (!$token) {
            throw new \Exception('No authentication token found');
        }



        return Http::withOptions(['verify' => $this->verifySsl])
            ->asJson()
            ->acceptJson()
            ->withToken($token)
            ->get($this->baseUrl . $endpoint, $params);
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
        $storedData = SecureStorage::get('data');
        $data = json_decode($storedData, true);
        return $this->get('/v1/syllabus', [], $data['token'] ?? null);
    }

    public function verifycodeSyllabu($userId, $ue)
    {

        return $this->get('/v1/verify-codes/' . $userId . '/'. $ue);
    }

    public function verifycodeStatus($userId)
    {

        return $this->get('/v1/verify-codes/' . $userId);
    }

    public function MemberSyllabus(?string $token = null)
    {


        return $this->get('/v1/syllabus', [ 'token' => $token ]);
    }

     public function ThemeColor($ue)
    {

        return $this->get('/v1/syllabus/settings/'. $ue);
    }

    public function ThemeSyllabus($ue, $theme)
    {

        return $this->get('/v1/themes/'. $ue .'/'. $theme);
    }

    public function ThemeSign($ue,$theme)
    {

        return $this->get('/v1/themes/' . $ue . '/' . $theme);
    }

    public function FeedBack(array $data)
    {
        logger()->info('api_data: ' . print_r($data, true));
        return $this->post('/v1/feedback', [
            'user_id' => $data['user_id'] ?? null,
            'type' => $data['type'],
            'question_id' => $data['question_id'] ?? null,
            'message' => $data['message'],
            'status' => $data['status'] ?? 'pending',
        ]);
    }
    public function ProfilUser(?string $token = null)
    {
        // Obtener token directamente
        $storedData = SecureStorage::get('data');

        if (!$storedData) {
            throw new \Exception('No stored authentication data');
        }

        $data = json_decode($storedData, true);
        $token = $data['token'] ?? null;

        if (!$token) {
            throw new \Exception('Token not found in stored data');
        }



        // Pasar el token explícitamente
        return $this->get('/user', [], $token);
    }

    public function Code($userId, $code, $theme)
    {
   
       
      
        return $this->post('/v1/verify-code', [
            'user_id' => $userId,
            'code' => $code,
            'theme' => $theme,
        ]);
    }


    public function logout(?string $token = null): Response
    {
        // Si no se proporciona token, intentar obtenerlo
        if (!$token) {
            // Opción 1: Token directo
            $token = SecureStorage::get('token');

            // Opción 2: Token dentro de 'data'
            if (!$token) {
                $storedData = SecureStorage::get('data');
                if ($storedData) {
                    $data = json_decode($storedData, true);
                    $token = $data['token'] ?? null;
                }
            }
        }

        // Si aún no hay token, lanzar excepción
        if (!$token) {
            throw new \Exception('No authentication token found for logout');
        }

        return Http::withOptions(['verify' => $this->verifySsl])
            ->asJson()
            ->acceptJson()
            ->withToken($token)
            ->post($this->baseUrl . '/auth/logout');
    }





}