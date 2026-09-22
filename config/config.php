<?php

/**
 * CONFIGURACIÓN GENERAL DEL PROYECTO
 * ==================================
 * Este archivo contiene la configuración principal de la aplicación.
 * Aquí se definen los datos de conexión a la base de datos y la URL base de la app.
 */

return [
    // Configuración de la base de datos
    'db' => [
        'driver'   => 'mysql',              // Tipo de base de datos (MySQL)
        'host'     => 'localhost',          // Servidor donde está la BD (local)
        'database' => 'barber_manager_db',  // Nombre de la base de datos
        'username' => 'root',               // Usuario de acceso a la BD
        'password' => '',                   // Contraseña (vacía en XAMPP por defecto)
        'charset'  => 'utf8',               // Codificación de caracteres
        'collation' => 'utf8_unicode_ci',   // Comparación de caracteres
        'prefix'   => '',                   // Prefijo para las tablas (opcional)
    ],

    // Configuración de la aplicación
    'app' => [
        'url' => 'https://127.0.0.1/Barber_Manager', // URL base del proyecto
    ],

    // Configuración de Google OAuth
    'google' => [
        'client_id'     => getenv('BARBER_MANAGER_GOOGLE_CLIENT_ID') ?: '',
        'client_secret' => getenv('BARBER_MANAGER_GOOGLE_CLIENT_SECRET') ?: '',
        'redirect_uri'  => getenv('BARBER_MANAGER_GOOGLE_REDIRECT_URI') ?: 'http://localhost/Barber_Manager/google-callback',
    ],

    // Configuración de correo (SMTP)
    'mail' => [
        'host'       => getenv('BARBER_MANAGER_MAIL_HOST') ?: 'smtp.gmail.com',
        'username'   => getenv('BARBER_MANAGER_MAIL_USERNAME') ?: 'admin@barbermanager.local',
        'password'   => getenv('BARBER_MANAGER_MAIL_PASSWORD') ?: '',
        'port'       => (int) (getenv('BARBER_MANAGER_MAIL_PORT') ?: 465),
        'from_name'  => getenv('BARBER_MANAGER_MAIL_FROM_NAME') ?: 'Barber Manager',
    ]
];
