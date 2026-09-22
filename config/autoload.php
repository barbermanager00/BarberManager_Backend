<?php

declare(strict_types=1);

/**
 * AUTOLOADER PERSONALIZADO DEL PROYECTO
 * =====================================
 * Este archivo registra un autoloader PSR-4 personalizado que permite cargar
 * automáticamente las clases del proyecto que usan el namespace 'App'
 * sin necesidad de hacer require/include manualmente.
 * 
 * Ejemplo: Si tienes App\Controllers\BarberoController,
 * este autoloader la cargará automáticamente desde src/Controllers/BarberoController.php
 */

spl_autoload_register(function ($class) {
    // Definir el prefijo del namespace que usamos (App)
    $prefix = 'App\\';
    // Definir la ruta base donde están las clases (carpeta src)
    $base_dir = __DIR__ . '/../src/';

    // Verificar si la clase tiene el prefijo 'App\', si no, ignorer
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // Obtener solo la parte del namespace sin el prefijo 'App\'
    // Ejemplo: App\Controllers\Barbero => Controllers\Barbero
    $relative_class = substr($class, $len);

    // Convertir el namespace a una ruta de archivo
    // Reemplaza las barras invertidas (\) por barras normales (/) para la ruta
    // Ejemplo: Controllers\Barbero => Controllers/Barbero
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // Verificar si el archivo existe y cargarlo
    if (file_exists($file)) {
        require $file;
    }
});
