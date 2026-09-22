<?php

declare(strict_types=1);

namespace App\Repositories;

use Turno;
use Illuminate\Database\Eloquent\Collection;

class TurnoRepository
{
    /**
     * Crea un nuevo turno en la base de datos
     */
    public function crear(array $datos): Turno
    {
        return Turno::create($datos);
    }

    /**
     * Obtiene todos los turnos con sus barberos ordenados por fecha descendente
     */
    public function obtenerTodosConBarberos(): Collection
    {
        return Turno::with('barbero')->orderBy('fecha', 'desc')->get();
    }
}
