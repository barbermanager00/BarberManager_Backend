<?php

declare(strict_types=1);

// Iniciar sesión para manejar datos del usuario logueado
session_start();

// 1. CARGA DE DEPENDENCIAS Y CONFIGURACIÓN
require_once __DIR__ . '/../vendor/autoload.php';      // Autoload de Composer (Eloquent)
require_once __DIR__ . '/../config/autoload.php';      // Autoload de clases App\*
require_once __DIR__ . '/../config/database.php';      // Conexión a la DB
require_once __DIR__ . '/../models/Barberia.php';      // Modelo de la tabla barberias
require_once __DIR__ . '/../models/Barbero.php';       // Modelo de la tabla barberos
require_once __DIR__ . '/../models/Turno.php';         // Modelo de la tabla turnos

// Importar controladores
use App\Controllers\TurnoController;
use App\Controllers\BarberoController;


// --- ROUTER ---

$method = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];
$proyecto_path = '/Barber_Manager';

// Obtener la ruta real de la URL y quitar el prefijo del proyecto
$path = str_replace($proyecto_path, '', $requestUri);
$path = parse_url($path, PHP_URL_PATH);

// Eliminar /public/ si viene en la ruta para no tener problemas de coincidencia
$path = str_replace('/public/', '/', $path);

// Normalizar la ruta para evitar barras repetidas y manejar la raíz correctamente
$path = '/' . trim($path, '/');
if ($path !== '/') {
    $path = rtrim($path, '/');
}

// ============================================
// RUTAS DE BIENVENIDA
// ============================================

// RUTA: Página de inicio con splash (GET /)
if ($method === 'GET' && ($path === '/' || $path === '')) {
    BarberoController::splash();
    exit;
}

// RUTA: Vista principal de bienvenida (GET /welcome)
if ($method === 'GET' && $path === '/welcome') {
    BarberoController::bienvenida();
    exit;
}

// RUTA: Vista de inicio de sesión (GET /login)
if ($method === 'GET' && $path === '/login') {
    BarberoController::login();
    exit;
}

// RUTA: Redirigir a Google para autenticación (GET /google-auth)
if ($method === 'GET' && $path === '/google-auth') {
    BarberoController::googleAuth();
    exit;
}

// RUTA: Callback de Google OAuth (GET /google-callback)
if ($method === 'GET' && $path === '/google-callback') {
    BarberoController::googleCallback();
    exit;
}

// RUTA: Solicitar clave temporal para login por email (POST /login-email)
if ($method === 'POST' && $path === '/login-email') {
    BarberoController::loginEmailRequest();
    exit;
}

// RUTA: Confirmar clave temporal para login por email (POST /login-email-confirm)
if ($method === 'POST' && $path === '/login-email-confirm') {
    BarberoController::loginEmailConfirm();
    exit;
}

// RUTA: Cerrar sesión (GET /logout)
if ($method === 'GET' && $path === '/logout') {
    BarberoController::logout();
    exit;
}

// RUTA: Selección de rol después del login (GET /seleccion-rol)
if ($method === 'GET' && $path === '/seleccion-rol') {
    BarberoController::seleccionRol();
    exit;
}

// ============================================
// RUTAS DE TURNOS
// ============================================

// RUTA: Crear turno (POST /turnos)
if ($method === 'POST' && $path === '/turnos') {
    TurnoController::crear();
    exit;
}

// RUTA: Listar turnos (GET /turnos)
if ($method === 'GET' && $path === '/turnos') {
    TurnoController::listar();
    exit;
}

// 404
http_response_code(404);
echo json_encode(["error" => "Ruta no encontrada"]);
