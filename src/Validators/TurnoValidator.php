<?php

declare(strict_types=1);

namespace App\Validators;

use DateTime;

/**
 * VALIDADOR DE TURNOS
 * ===================
 * Valida que los datos de un turno cumplan con las reglas de negocio.
 * 
 * Validar significa verificar que los datos sean correctos según las reglas
 * del negocio, no solo que sean seguros (eso es sanitizar).
 * 
 * Ejemplo de reglas:
 * - El nombre debe tener al menos 3 caracteres
 * - El teléfono debe tener 8-10 dígitos
 * - No se puede reservar para una fecha/hora que ya pasó
 */
class TurnoValidator
{
    /**
     * Validar datos de un turno
     * 
     * @param array $data Datos del turno sanitizados
     * @return array Array de errores (vacío si todo está bien)
     */
    public static function validarTurno(array $data): array
    {
        $errores = [];

        // Validar nombre del cliente
        // Debe existir y tener al menos 3 caracteres
        if (empty($data['clienteNombre']) || strlen($data['clienteNombre']) < 3) {
            $errores[] = "El nombre debe tener al menos 3 caracteres.";
        }

        // Validar teléfono
        // Debe tener entre 8 y 10 dígitos
        $largoTel = strlen($data['clienteTelefono']);
        if ($largoTel < 8 || $largoTel > 10) {
            $errores[] = "El telefono debe tener entre 8 y 10 digitos.";
        }

        // Validar fecha y hora
        // Ambas deben estar presentes
        if (empty($data['fecha']) || empty($data['hora'])) {
            $errores[] = "Fecha y hora son obligatorios.";
        } else {
            // Verificar que la fecha/hora no sea en el pasado
            // Obtener la fecha/hora actual
            $ahora = new DateTime();
            // Crear un objeto DateTime con la fecha y hora del turno
            $fechaTurno = new DateTime($data['fecha'] . ' ' . $data['hora']);

            // Si el turno es antes de ahora, es inválido
            if ($fechaTurno < $ahora) {
                $errores[] = "No se puede sacar un turno para una fecha o horario que ya paso.";
            }
        }

        // Validar barbero
        // El ID debe ser mayor a 0
        if ($data['barberoId'] <= 0) {
            $errores[] = "Debe seleccionar un barbero valido.";
        }

        // Devolver todos los errores encontrados
        // Si está vacío, significa que todo es válido
        return $errores;
    }
}
