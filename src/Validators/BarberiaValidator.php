<?php

declare(strict_types=1);

namespace App\Validators;

/**
 * VALIDADOR DE BARBERÍAS
 * ====================
 * Valida que los datos de una barbería y su staff cumplan con las reglas.
 * 
 * Verifica que:
 * - La barbería tenga datos válidos
 * - Al menos un barbero sea registrado
 * - Cada barbero tenga datos válidos
 */
class BarberiaValidator
{
    /**
     * Validar datos de registro de una barbería
     * 
     * @param array $data Datos de la barbería con sus barberos
     * @return array Array de errores (vacío si todo está bien)
     */
    public static function validarRegistro(array $data): array
    {
        $errores = [];
        // Extraer datos de barbería y barberos
        $barberia = $data['barberia'] ?? [];
        $barberos = $data['barberos'] ?? [];

        // ===== VALIDAR DATOS DE LA BARBERÍA =====

        // Validar nombre de la barbería
        if (empty($barberia['nombre']) || strlen($barberia['nombre']) < 3) {
            $errores[] = 'El nombre de la barbería debe tener al menos 3 caracteres.';
        }

        // Validar email de la barbería
        if (empty($barberia['email']) || !filter_var($barberia['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El email de la barbería es obligatorio y debe ser válido.';
        }

        // Validar teléfono de la barbería
        $telefono = $barberia['telefono'] ?? '';
        $largoTel = strlen($telefono);
        if ($largoTel < 8 || $largoTel > 15) {
            $errores[] = 'El teléfono de la barbería debe tener entre 8 y 15 dígitos.';
        }

        // Validar dirección de la barbería
        if (empty($barberia['direccion']) || strlen($barberia['direccion']) < 3) {
            $errores[] = 'La dirección de la barbería es obligatoria.';
        }

        // ===== VALIDAR BARBEROS =====

        // Verificar que haya al menos 1 barbero
        if (!is_array($barberos) || count($barberos) === 0) {
            $errores[] = 'Debes registrar al menos un barbero para la barbería.';
        } else {
            // Validar cada barbero del array
            foreach ($barberos as $index => $barbero) {
                // Número del barbero para mensajes de error (1, 2, 3...)
                $numeroBarb = $index + 1;

                // Validar nombre del barbero
                if (empty($barbero['nombre']) || strlen($barbero['nombre']) < 3) {
                    $errores[] = "El nombre del barbero #" . $numeroBarb . " debe tener al menos 3 caracteres.";
                }

                // Validar email del barbero
                if (empty($barbero['email']) || !filter_var($barbero['email'], FILTER_VALIDATE_EMAIL)) {
                    $errores[] = "El email del barbero #" . $numeroBarb . " es obligatorio y debe ser válido.";
                }

                // Validar teléfono del barbero
                $telefonoBarbero = $barbero['telefono'] ?? '';
                $largoTelBarbero = strlen($telefonoBarbero);
                if ($largoTelBarbero < 8 || $largoTelBarbero > 15) {
                    $errores[] = "El teléfono del barbero #" . $numeroBarb . " debe tener entre 8 y 15 dígitos.";
                }

                // Validar especialidad del barbero
                if (empty($barbero['especialidad']) || strlen($barbero['especialidad']) < 2) {
                    $errores[] = "La especialidad del barbero #" . $numeroBarb . " es obligatoria.";
                }

                // Validar años de experiencia
                $experiencia = (int) ($barbero['experiencia'] ?? 0);
                if ($experiencia < 0 || $experiencia > 70) {
                    $errores[] = "La experiencia del barbero #" . $numeroBarb . " debe estar entre 0 y 70 años.";
                }
            }
        }

        // Devolver todos los errores encontrados
        return $errores;
    }
}
