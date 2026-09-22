<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\TurnoService;

class TurnoController
{
    private static function getService(): TurnoService
    {
        return new TurnoService();
    }

    public static function crear()
    {
        header('Content-Type: application/json');
        
        $service = self::getService();
        $resultado = $service->crearTurno($_POST);

        if ($resultado['ok']) {
            http_response_code(201);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } elseif (isset($resultado['errors'])) {
            http_response_code(400);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        }
    }

    public static function listar()
    {
        header('Content-Type: application/json');
        $service = self::getService();
        $turnos = $service->listarTurnos();
        echo json_encode($turnos, JSON_UNESCAPED_UNICODE);
    }
}
