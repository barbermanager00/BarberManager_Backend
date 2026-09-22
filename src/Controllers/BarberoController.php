<?php

declare(strict_types=1);

namespace App\Controllers;

use Barbero;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * CONTROLADOR DE BARBEROS Y BARBERÍAS (API)
 * =========================================
 * Este controlador expone únicamente endpoints JSON.
 */
class BarberoController
{
    /**
     * Devuelve el usuario actualmente autenticado
     * GET /api/auth/me
     */
    public static function me()
    {
        header('Content-Type: application/json');
        
        if (isset($_SESSION['google_user'])) {
            echo json_encode([
                'ok' => true,
                'user' => $_SESSION['google_user']
            ], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(401);
            echo json_encode([
                'ok' => false,
                'message' => 'No autorizado'
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Solicitar clave temporal para el login con email
     * POST /api/login-email
     */
    public static function loginEmailRequest()
    {
        header('Content-Type: application/json');

        $input = file_get_contents('php://input');
        $datos = json_decode($input, true);

        if (empty($datos) || !is_array($datos)) {
            $datos = $_POST;
        }

        $email = trim($datos['email'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode([
                'ok' => false,
                'message' => 'Ingresá un correo electrónico válido.'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $codigo = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        $_SESSION['email_login_pending'] = [
            'email' => $email,
            'codigo' => $codigo,
            'expires_at' => time() + 300,
        ];

        $config = require __DIR__ . '/../../config/config.php';
        $mailConfig = $config['mail'] ?? [];

        if (empty($mailConfig)) {
            http_response_code(500);
            echo json_encode([
                'ok' => false,
                'message' => 'Error: Configuración de correo no encontrada.'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $mailConfig['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $mailConfig['username'];
            $mail->Password   = $mailConfig['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = $mailConfig['port'];

            $mail->setFrom($mailConfig['username'], $mailConfig['from_name']);
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Tu clave temporal - Barber Manager';
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; padding: 20px; max-width: 600px; margin: auto; border: 1px solid #ddd; border-radius: 10px;'>
                    <h2 style='color: #333;'>¡Hola!</h2>
                    <p style='color: #555; font-size: 16px;'>Has solicitado iniciar sesión en Barber Manager.</p>
                    <p style='color: #555; font-size: 16px;'>Tu clave temporal de acceso es:</p>
                    <div style='background-color: #f4f4f4; padding: 15px; text-align: center; border-radius: 5px; margin: 20px 0;'>
                        <strong style='font-size: 24px; color: #000; letter-spacing: 5px;'>{$codigo}</strong>
                    </div>
                    <p style='color: #555; font-size: 14px;'>Esta clave expirará en 5 minutos. Si no fuiste tú, ignora este mensaje.</p>
                </div>
            ";
            $mail->AltBody = "Hola. Tu clave temporal de acceso es: {$codigo}. Esta clave expirará en 5 minutos.";

            $mail->send();

            echo json_encode([
                'ok' => true,
                'message' => 'Clave temporal enviada a tu correo.'
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'ok' => false,
                'message' => 'No se pudo enviar el correo: ' . $mail->ErrorInfo
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Validar clave temporal enviada por correo y completar login
     * POST /api/login-email-confirm
     */
    public static function loginEmailConfirm()
    {
        header('Content-Type: application/json');

        $input = file_get_contents('php://input');
        $datos = json_decode($input, true);

        if (empty($datos) || !is_array($datos)) {
            $datos = $_POST;
        }

        $email = trim($datos['email'] ?? '');
        $clave = trim($datos['clave'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode([
                'ok' => false,
                'message' => 'Correo inexistente o inválido.'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $pending = $_SESSION['email_login_pending'] ?? null;

        if (!$pending || ($pending['email'] ?? '') !== $email) {
            http_response_code(400);
            echo json_encode([
                'ok' => false,
                'message' => 'No hay una clave pendiente para ese correo.'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        if (($pending['expires_at'] ?? 0) < time()) {
            unset($_SESSION['email_login_pending']);
            http_response_code(400);
            echo json_encode([
                'ok' => false,
                'message' => 'La clave temporal expiró. Solicitá otra.'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        if (($pending['codigo'] ?? '') !== $clave) {
            http_response_code(400);
            echo json_encode([
                'ok' => false,
                'message' => 'La clave ingresada es incorrecta.'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $name = explode('@', $email)[0];
        $_SESSION['google_user'] = [
            'name' => ucfirst($name),
            'email' => $email,
            'picture' => ''
        ];

        unset($_SESSION['email_login_pending']);

        echo json_encode([
            'ok' => true,
            'message' => 'Inicio de sesión correcto.',
            'redirect' => '/Barber_Manager/seleccion_rol.html'
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Redirigir al usuario a Google para autenticación
     * GET /api/google-auth
     */
    public static function googleAuth()
    {
        $config = require __DIR__ . '/../../config/config.php';
        $google = $config['google'];

        $params = http_build_query([
            'client_id'     => $google['client_id'],
            'redirect_uri'  => $google['redirect_uri'],
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'prompt'        => 'select_account',
        ]);

        header('Location: https://accounts.google.com/o/oauth2/v2/auth?' . $params);
        exit;
    }

    /**
     * Manejar el callback de Google OAuth
     * GET /api/google-callback
     */
    public static function googleCallback()
    {
        if (!isset($_GET['code'])) {
            header('Location: /Barber_Manager/login.html');
            exit;
        }

        $config = require __DIR__ . '/../../config/config.php';
        $google = $config['google'];

        $tokenResponse = self::exchangeGoogleCode($_GET['code'], $google);

        if (!$tokenResponse || !isset($tokenResponse['access_token'])) {
            header('Location: /Barber_Manager/login.html?error=token');
            exit;
        }

        $userInfo = self::getGoogleUserInfo($tokenResponse['access_token']);

        if (!$userInfo || !isset($userInfo['email'])) {
            header('Location: /Barber_Manager/login.html?error=userinfo');
            exit;
        }

        $_SESSION['google_user'] = [
            'name'    => $userInfo['name'] ?? '',
            'email'   => $userInfo['email'],
            'picture' => $userInfo['picture'] ?? '',
        ];

        header('Location: /Barber_Manager/seleccion_rol.html');
        exit;
    }

    /**
     * Cerrar la sesión actual
     * POST /api/logout
     */
    public static function logout()
    {
        session_unset();
        session_destroy();

        header('Content-Type: application/json');
        echo json_encode([
            'ok' => true,
            'redirect' => '/Barber_Manager/login.html'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Guardar los datos de Mi Perfil
     * POST /api/perfil
     */
    public static function guardarPerfil()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['google_user'])) {
            http_response_code(401);
            echo json_encode(['ok' => false, 'message' => 'No autorizado']);
            return;
        }

        $input = file_get_contents('php://input');
        $datos = json_decode($input, true);

        if (empty($datos) || !is_array($datos)) {
            $datos = $_POST;
        }

        $nombre = trim($datos['nombre'] ?? '');
        $apellido = trim($datos['apellido'] ?? '');
        
        $_SESSION['google_user']['name'] = $nombre . ' ' . $apellido;
        $_SESSION['google_user']['dob'] = trim($datos['dob'] ?? '');
        $_SESSION['google_user']['gender'] = trim($datos['gender'] ?? '');
        $_SESSION['google_user']['phone'] = trim($datos['phone'] ?? '');

        echo json_encode([
            'ok' => true,
            'message' => 'Perfil guardado con éxito',
            'user' => $_SESSION['google_user']
        ]);
    }

    /**
     * Procesar guardado de nueva empresa
     * POST /api/nueva-empresa
     */
    public static function guardarEmpresa()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['google_user'])) {
            http_response_code(401);
            echo json_encode(['ok' => false, 'message' => 'No autorizado']);
            return;
        }

        $input = file_get_contents('php://input');
        $datos = json_decode($input, true);

        if (empty($datos) || !is_array($datos)) {
            $datos = $_POST;
        }

        $_SESSION['google_user']['empresa'] = [
            'nombre' => trim($datos['nombre'] ?? ''),
            'slogan' => trim($datos['slogan'] ?? ''),
            'direccion' => trim($datos['direccion'] ?? ''),
            'latitud' => trim($datos['latitud'] ?? ''),
            'longitud' => trim($datos['longitud'] ?? '')
        ];

        echo json_encode([
            'ok' => true,
            'message' => 'Empresa guardada con éxito',
            'redirect' => '/Barber_Manager/perfil.html'
        ]);
    }

    // ====================================================
    // MÉTODOS PRIVADOS PARA GOOGLE OAUTH
    // ====================================================

    private static function exchangeGoogleCode(string $code, array $google): ?array
    {
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'code'          => $code,
                'client_id'     => $google['client_id'],
                'client_secret' => $google['client_secret'],
                'redirect_uri'  => $google['redirect_uri'],
                'grant_type'    => 'authorization_code',
            ]),
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response ? json_decode($response, true) : null;
    }

    private static function getGoogleUserInfo(string $accessToken): ?array
    {
        $ch = curl_init('https://www.googleapis.com/oauth2/v2/userinfo');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $accessToken],
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response ? json_decode($response, true) : null;
    }

    /**
     * Listar todos los barberos activos
     * GET /api/barberos
     */
    public static function listar()
    {
        header('Content-Type: application/json');

        try {
            $service = new \App\Services\BarberoService();
            $barberos = $service->listarBarberos($_GET);

            echo json_encode($barberos, JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                "ok" => false,
                "error" => "Error al obtener barberos: " . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
}
