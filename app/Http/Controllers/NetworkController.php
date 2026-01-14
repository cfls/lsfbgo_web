<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Native\Mobile\Facades\Network;

class NetworkController extends Controller
{
    /**
     * Mostrar la pantalla de sin conexión
     */
    public function sinConexion()
    {
        return view('errors.sin-conexion');
    }

    /**
     * Verificar el estado de la red (para llamadas AJAX)
     */
    public function checkStatus()
    {
        $networkStatus = Network::status();

        if (!$networkStatus) {
            return response()->json([
                'connected' => false,
                'type' => 'unknown',
                'message' => 'No se pudo obtener el estado de la red'
            ]);
        }

        return response()->json([
            'connected' => $networkStatus->connected,
            'type' => $networkStatus->type,
            'isExpensive' => $networkStatus->isExpensive ?? false,
            'isConstrained' => $networkStatus->isConstrained ?? false,
        ]);
    }
}
