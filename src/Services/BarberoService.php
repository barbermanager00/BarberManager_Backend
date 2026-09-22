<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\BarberoRepository;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class BarberoService
{
    private BarberoRepository $barberoRepository;

    public function __construct()
    {
        $this->barberoRepository = new BarberoRepository();
    }

    public function listarBarberos(array $filtros): array
    {
        $query = \Barbero::where('estado', true);
        if (isset($filtros['barberia_id'])) {
            $query->where('barberia_id', $filtros['barberia_id']);
        }
        return $query->select('id', 'barberia_id', 'nombre', 'especialidad', 'telefono')
                     ->orderBy('nombre')
                     ->get()
                     ->toArray();
    }
}
