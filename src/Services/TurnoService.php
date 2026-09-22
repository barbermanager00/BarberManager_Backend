<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\TurnoRepository;
use App\Helpers\Sanitizer;
use App\Validators\TurnoValidator;
use Exception;

class TurnoService
{
    private TurnoRepository $turnoRepository;

    public function __construct()
    {
        $this->turnoRepository = new TurnoRepository();
    }

    /**
     * Procesa la creación de un turno, validando y guardando los datos.
     * Retorna un array con el resultado: ['ok' => bool, 'data' => mixed, 'errors' => array, 'message' => string]
     */
    public function crearTurno(array $datosPost): array
    {
        $datosLimpios = Sanitizer::turno($datosPost);
        $errores = TurnoValidator::validarTurno($datosLimpios);

        if (!empty($errores)) {
            return [
                'ok' => false,
                'errors' => $errores
            ];
        }

        try {
            $nuevoTurno = $this->turnoRepository->crear($datosLimpios);
            return [
                'ok' => true,
                'message' => 'Turno guardado con exito',
                'id' => $nuevoTurno->id,
                'item' => $datosLimpios
            ];
        } catch (Exception $e) {
            return [
                'ok' => false,
                'error' => 'Error al guardar en la base de datos: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene el listado de turnos formateado.
     */
    public function listarTurnos(): array
    {
        $turnos = $this->turnoRepository->obtenerTodosConBarberos();

        $turnosConBarbero = $turnos->map(function ($turno) {
            return [
                'id' => $turno->id,
                'clienteNombre' => $turno->clienteNombre,
                'clienteTelefono' => $turno->clienteTelefono,
                'barberoId' => $turno->barberoId,
                'nombreBarbero' => $turno->barbero ? $turno->barbero->nombre : 'Barbero no asignado',
                'fecha' => $turno->fecha,
                'hora' => $turno->hora,
                'servicio' => $turno->servicio
            ];
        });

        return $turnosConBarbero->toArray();
    }
}
