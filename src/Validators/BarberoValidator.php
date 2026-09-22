<?php

declare(strict_types=1);

namespace App\Validators;

/**
 * VALIDADOR DE BARBEROS
 * =====================
 * Valida que los datos de un barbero cumplan con las reglas de negocio.
 * 
 * Verifica que:
 * - El nombre sea válido
 * - El email sea válido
 * - El teléfono tenga un largo apropiado
 * - La especialidad exista
 * - Los años de experiencia sean realistas
 */
class BarberoValidator
{
    /**
     * Validar datos de registro de un barbero
     * 
     * @param array $data Datos del barbero sanitizados
     * @return array Array de errores (vacío si todo está bien)
     */
    public static function validarRegistro(array $data): array
    {
        $errores = [];

        // Validar nombre
        // Debe existir y tener al menos 3 caracteres
        if (empty($data['nombre']) || strlen($data['nombre']) < 3) {
            $errores[] = "El nombre debe tener al menos 3 caracteres.";
        }

        // Validar email
        // Debe existir y ser un formato válido
        if (empty($data['email'])) {
            $errores[] = "El email es obligatorio.";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El email no tiene un formato válido.";
        }

        // Validar teléfono
        // Debe tener entre 8 y 15 dígitos
        $largoTel = strlen($data['telefono']);
        if ($largoTel < 8 || $largoTel > 15) {
            $errores[] = "El teléfono debe tener entre 8 y 15 dígitos.";
        }

        // Validar especialidad
        // Debe existir y tener al menos 2 caracteres (ej: cortes, afeitado)
        if (empty($data['especialidad']) || strlen($data['especialidad']) < 2) {
            $errores[] = "La especialidad es obligatoria (mínimo 2 caracteres).";
        }

        // Validar experiencia
        // Debe estar entre 0 y 70 años (rangos realistas para una carrera)
        $experiencia = (int) $data['experiencia'];
        if ($experiencia < 0 || $experiencia > 70) {
            $errores[] = "Los años de experiencia deben estar entre 0 y 70.";
        }

        // Devolver errores encontrados
        return $errores;
    }
}
