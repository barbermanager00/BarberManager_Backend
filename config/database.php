<?php

/**
 * CONFIGURACIÓN DE CONEXIÓN A LA BASE DE DATOS
 * ============================================
 * Este archivo configura Eloquent ORM (del framework Laravel)
 * que nos permite interactuar con la base de datos de forma orientada a objetos.
 * 
 * Eloquent convierte tablas de BD en modelos PHP que podemos usar fácilmente.
 */

// Importar el gestor de base de datos de Laravel (Eloquent ORM)
use Illuminate\Database\Capsule\Manager as Capsule;

// Incluir el autoloader de Composer (carga todas las dependencias)
require_once __DIR__ . '/../vendor/autoload.php';

// Crear una nueva instancia del gestor de base de datos
$capsule = new Capsule;

// Agregar la configuración de conexión a MySQL
$capsule->addConnection([
    'driver'    => 'mysql',              // Usar MySQL como base de datos
    'host'      => 'localhost',          // Servidor local
    'database'  => 'barber_manager_db',  // Nombre de nuestra BD
    'username'  => 'root',               // Usuario (root en XAMPP)
    'password'  => '',                   // Sin contraseña en XAMPP
    'charset'   => 'utf8mb4',            // Codificación Unicode completa
    'collation' => 'utf8mb4_unicode_ci', // Comparación sensible a acentos
    'prefix'    => '',                   // Sin prefijo en nombres de tablas
]);

// Configurar Eloquent como gestor global (disponible en toda la app)
$capsule->setAsGlobal();
// Inicializar Eloquent para que pueda ser usado
$capsule->bootEloquent();
