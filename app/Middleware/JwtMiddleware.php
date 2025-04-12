<?php
namespace App\Middleware;

use System\Middleware;
use System\Auth\JwtManager;

/**
 * Middleware para autenticación JWT
 * 
 * Verifica que las solicitudes a la API incluyan un token JWT válido
 * en el encabezado Authorization. Si el token es válido, permite el acceso
 * y establece el usuario actual en la solicitud.
 */
class JwtMiddleware extends Middleware {
    private JwtManager $jwtManager;

    public function __construct() {
        $this->jwtManager = new JwtManager();
    }

    /**
     * Verifica el token JWT en la solicitud
     * 
     * @return bool
     */
    public function handle(): bool {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';

        if (empty($authHeader) || !str_starts_with($authHeader, 'Bearer ')) {
            $this->sendUnauthorizedResponse('Token no proporcionado');
            return false;
        }

        $token = substr($authHeader, 7); // Remover 'Bearer '
        $payload = $this->jwtManager->validateToken($token);

        if ($payload === null) {
            $this->sendUnauthorizedResponse('Token inválido o expirado');
            return false;
        }

        // Establecer el usuario actual para la solicitud
        $_SESSION['api_user'] = $payload;
        return true;
    }

    /**
     * Envía una respuesta de error 401 Unauthorized
     * 
     * @param string $message Mensaje de error
     * @return void
     */
    private function sendUnauthorizedResponse(string $message): void {
        header('HTTP/1.1 401 Unauthorized');
        header('Content-Type: application/json');
        echo json_encode([
            'error' => 'Unauthorized',
            'message' => $message
        ]);
        exit;
    }
}