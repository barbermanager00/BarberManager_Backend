<?php

declare(strict_types=1);

namespace App\Repositories;

use Barberia;

class BarberiaRepository
{
    /**
     * Crea una nueva barbería
     */
    public function crear(array $datos): Barberia
    {
        return Barberia::create($datos);
    }
}
