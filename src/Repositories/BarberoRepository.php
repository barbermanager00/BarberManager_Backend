<?php

declare(strict_types=1);

namespace App\Repositories;

use Barbero;

class BarberoRepository
{
    /**
     * Busca un barbero por email
     */
    public function buscarPorEmail(string $email): ?Barbero
    {
        return Barbero::where('email', $email)->first();
    }

    /**
     * Busca un barbero por ID
     */
    public function buscarPorId(int $id): ?Barbero
    {
        return Barbero::find($id);
    }

    /**
     * Actualiza un barbero
     */
    public function actualizar(Barbero $barbero, array $datos): bool
    {
        return $barbero->update($datos);
    }
}
