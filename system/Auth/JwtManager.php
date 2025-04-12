<?php
namespace System\Auth;

/**
 * Gestor de JSON Web Tokens (JWT)
 * 
 * Esta clase maneja la generación y validación de tokens JWT para la autenticación
 * de la API. Utiliza una clave secreta definida en el archivo .env para firmar
 * y verificar los tokens.
 */
class JwtManager {
    private string $secret;
    private const ALGORITHM = 'HS256';
    private const TOKEN_EXPIRATION = 3600; // 1 hora en segundos

    public function __construct() {
        $this->secret = $_ENV['JWT_SECRET'] ?? 'default-secret-key';
    }

    /**
     * Genera un nuevo token JWT para un usuario
     * 
     * @param array $payload Datos del usuario a incluir en el token
     * @return string Token JWT generado
     */
    public function generateToken(array $payload): string {
        $header = [
            'typ' => 'JWT',
            'alg' => self::ALGORITHM
        ];

        $payload['iat'] = time(); // Tiempo de emisión
        $payload['exp'] = time() + self::TOKEN_EXPIRATION; // Tiempo de expiración

        $headerEncoded = $this->base64UrlEncode(json_encode($header));
        $payloadEncoded = $this->base64UrlEncode(json_encode($payload));

        $signature = hash_hmac(
            'sha256',
            "$headerEncoded.$payloadEncoded",
            $this->secret,
            true
        );
        $signatureEncoded = $this->base64UrlEncode($signature);

        return "$headerEncoded.$payloadEncoded.$signatureEncoded";
    }

    /**
     * Valida y decodifica un token JWT
     * 
     * @param string $token Token JWT a validar
     * @return array|null Payload decodificado o null si el token es inválido
     */
    public function validateToken(string $token): ?array {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$headerEncoded, $payloadEncoded, $signatureProvided] = $parts;

        // Verificar firma
        $signature = hash_hmac(
            'sha256',
            "$headerEncoded.$payloadEncoded",
            $this->secret,
            true
        );
        $signatureEncoded = $this->base64UrlEncode($signature);

        if (!hash_equals($signatureProvided, $signatureEncoded)) {
            return null;
        }

        $payload = json_decode($this->base64UrlDecode($payloadEncoded), true);

        // Verificar expiración
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return null;
        }

        return $payload;
    }

    /**
     * Codifica datos en Base64URL
     */
    private function base64UrlEncode(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Decodifica datos en Base64URL
     */
    private function base64UrlDecode(string $data): string {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($data)) % 4));
    }
}