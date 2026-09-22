<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * SANITIZADOR DE BARBERÍA
 * ======================
 * Se encarga de limpiar los datos de una barbería y su staff (barberos).
 * Divide los datos en dos secciones: datos de la barbería y datos de los barberos.
 */
class BarberiaSanitizer
{
    /**
     * Sanitizar datos de registro de una barbería con su staff
     * 
     * Recibe los datos del formulario y devuelve dos arrays:
     * - barberia: datos de la barbería
     * - barberos: array de datos de los barberos que trabajan ahí
     * 
     * @param array $input Datos del formulario
     * @return array Array con claves 'barberia' y 'barberos'
     */
    public static function registro(array $input): array
    {
        // Extraer los datos de la barbería del input
        $barberia = $input['barberia'] ?? [];
        // Extraer los datos de los barberos del input
        $barberos = $input['barberos'] ?? [];

        return [
            // Datos de la BARBERÍA
            'barberia' => [
                // Nombre de la barbería
                'nombre' => self::string($barberia['nombre'] ?? ''),
                // Email de contacto de la barbería
                'email' => self::email($barberia['email'] ?? ''),
                // Teléfono de la barbería
                'telefono' => self::telefono($barberia['telefono'] ?? ''),
                // Dirección física de la barbería
                'direccion' => self::string($barberia['direccion'] ?? ''),
                // La barbería está activa por defecto
                'estado' => true,
            ],

            // Datos de los BARBEROS
            // Procesa cada barbero del array y limpia sus datos
            'barberos' => array_values(array_map(function ($barbero) {
                return [
                    'nombre' => self::string($barbero['nombre'] ?? ''),
                    'email' => self::email($barbero['email'] ?? ''),
                    'telefono' => self::telefono($barbero['telefono'] ?? ''),
                    'especialidad' => self::string($barbero['especialidad'] ?? ''),
                    'experiencia' => self::entero($barbero['experiencia'] ?? 0),
                    'estado' => true, // Los nuevos barberos están activos
                ];
            }, is_array($barberos) ? $barberos : [])),
        ];
    }

    /**
     * Limpiar un string de texto
     * Elimina espacios y caracteres peligrosos
     */
    private static function string(string $valor): string
    {
        return htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Limpiar un email
     * Convierte a minúsculas
     */
    private static function email(string $valor): string
    {
        return strtolower(trim($valor));
    }

    /**
     * Limpiar un teléfono
     * Solo mantiene dígitos
     */
    private static function telefono(string $valor): string
    {
        return preg_replace('/\D/', '', trim($valor));
    }

    /**
     * Convertir a número entero
     */
    private static function entero(mixed $valor): int
    {
        return (int) filter_var($valor, FILTER_SANITIZE_NUMBER_INT);
    }
}
