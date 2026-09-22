<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * SANITIZADOR DE BARBEROS
 * ======================
 * Se encarga de limpiar y preparar los datos de un barbero individual
 * para que sean seguros y tengan el formato correcto.
 */
class BarberoSanitizer
{
    /**
     * Sanitizar datos de registro de un barbero
     * 
     * @param array $input Datos del formulario
     * @return array Datos limpios
     */
    public static function registro(array $input): array
    {
        return [
            // Nombre: limpiar como texto
            'nombre'       => self::string($input['nombre'] ?? ''),
            // Email: convertir a minúsculas
            'email'        => self::email($input['email'] ?? ''),
            // Teléfono: solo dígitos
            'telefono'     => self::telefono($input['telefono'] ?? ''),
            // Especialidad: limpiar como texto (qué hace bien: cortes, afeitado, etc)
            'especialidad' => self::string($input['especialidad'] ?? ''),
            // Años de experiencia: convertir a número entero
            'experiencia'  => self::entero($input['experiencia'] ?? 0),
            // Nuevos barberos están activos por defecto
            'estado'       => true,
        ];
    }

    /**
     * Limpiar un string de texto
     */
    private static function string(string $valor): string
    {
        return htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Limpiar un email
     * Convierte a minúsculas para normalizar (ej: Juan@Email.com => juan@email.com)
     */
    private static function email(string $valor): string
    {
        return strtolower(trim($valor));
    }

    /**
     * Limpiar un teléfono
     * Solo mantiene los dígitos
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
