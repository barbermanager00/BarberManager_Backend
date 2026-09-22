<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * SANITIZADOR DE TURNOS
 * ====================
 * Sanitizar significa "limpiar" los datos de entrada.
 * Este archivo se encarga de limpiar y preparar los datos de un turno
 * para asegurar que sean seguros y tengan el formato correcto.
 * 
 * Por ejemplo:
 * - Convierte a mayúsculas/minúsculas cuando es necesario
 * - Elimina caracteres peligrosos
 * - Convierte a tipos de datos correctos (string, int, etc)
 */
class Sanitizer
{
    /**
     * Sanitizar datos de un turno
     * 
     * Recibe un array con los datos del formulario y devuelve
     * los mismos datos pero limpios y seguros.
     * 
     * @param array $input Datos crudos del formulario ($_POST)
     * @return array Datos limpios y seguros para guardar
     */
    public static function turno(array $input): array
    {
        return [
            // Nombre del cliente: limpiar espacios y caracteres especiales
            'clienteNombre'   => self::string($input['clienteNombre']   ?? ''),
            // Teléfono: solo mantener dígitos (elimina guiones, espacios, etc)
            'clienteTelefono' => self::telefono($input['clienteTelefono'] ?? ''),
            // ID del barbero: convertir a número entero
            'barberoId'       => self::entero($input['barberoId']       ?? 0),
            // Fecha: limpiar como texto
            'fecha'           => self::string($input['fecha']           ?? ''),
            // Hora: limpiar como texto
            'hora'            => self::string($input['hora']            ?? ''),
            // Servicio: limpiar como texto (puede estar vacío)
            'servicio'        => self::string($input['servicio']        ?? ''),
        ];
    }

    /**
     * Sanitizar un string de texto
     * 
     * Elimina espacios al inicio/final y convierte caracteres
     * especiales HTML en entidades seguras.
     * 
     * @param string $valor Texto a limpiar
     * @return string Texto limpio y seguro
     */
    private static function string(string $valor): string
    {
        // htmlspecialchars: convierte caracteres especiales en entidades HTML
        // Ejemplo: < se convierte en &lt; (así no se puede ejecutar código)
        return htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitizar un número de teléfono
     * 
     * Solo mantiene los dígitos del teléfono.
     * Elimina guiones, espacios, paréntesis, etc.
     * 
     * @param string $valor Teléfono a limpiar
     * @return string Solo los dígitos del teléfono
     */
    private static function telefono(string $valor): string
    {
        // preg_replace con \D elimina todo lo que NO sea dígito
        // Ejemplo: "11-2233-4455" se convierte en "1122334455"
        return preg_replace('/\D/', '', trim($valor));
    }

    /**
     * Sanitizar un número entero
     * 
     * Convierte el valor a un número entero.
     * Si no es un número válido, devuelve 0.
     * 
     * @param mixed $valor Valor a convertir a número
     * @return int Número entero seguro
     */
    private static function entero(mixed $valor): int
    {
        // filter_var: valida y convierte a número entero
        return (int) filter_var($valor, FILTER_SANITIZE_NUMBER_INT);
    }
}
