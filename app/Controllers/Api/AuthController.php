<?php
namespace App\Controllers\Api;

use System\Auth\JwtManager;
use System\Database;
use PDO;

/**
 * Controlador de Autenticación para la API
 * 
 * Maneja las operaciones de autenticación a través de la API REST:
 * - Registro de usuarios
 * - Inicio de sesión y generación de tokens JWT
 * - Validación de credenciales
 */
class AuthController {
    private JwtManager $jwtManager;
    private PDO $db;

    public function __construct() {
        $this->jwtManager = new JwtManager();
        $this->db = Database::getConnection();
    }

    /**
     * Registra un nuevo usuario vía API
     */
    public function register() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validar datos requeridos
        if (!isset($data['name'], $data['email'], $data['password'])) {
            $this->jsonResponse(['error' => 'Datos incompletos'], 400);
            return;
        }

        // Verificar si el email ya existe
        $stmt = $this->db->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$data['email']]);
        $existingUser = $stmt->fetch();

        if ($existingUser) {
            $this->jsonResponse(['error' => 'El email ya está registrado'], 400);
            return;
        }

        // Crear el usuario
        $stmt = $this->db->prepare('INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())');
        $stmt->execute([
            $data['name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT)
        ]);
        $userId = $this->db->lastInsertId();

        // Generar token JWT
        $token = $this->jwtManager->generateToken([
            'user_id' => $userId,
            'name' => $data['name'],
            'email' => $data['email']
        ]);

        $this->jsonResponse([
            'message' => 'Usuario registrado correctamente',
            'token' => $token
        ], 201);
    }

    /**
     * Inicia sesión y genera un token JWT
     */
    public function login() {
        $data = json_decode(file_get_contents('php://input'), true);

        // Validar datos requeridos
        if (!isset($data['email'], $data['password'])) {
            $this->jsonResponse(['error' => 'Credenciales incompletas'], 400);
            return;
        }

        // Buscar usuario
        $stmt = $this->db->prepare('SELECT id, name, email, password FROM users WHERE email = ?');
        $stmt->execute([$data['email']]);
        $user = $stmt->fetch();

        // Verificar credenciales
        if (!$user || !password_verify($data['password'], $user['password'])) {
            $this->jsonResponse(['error' => 'Credenciales inválidas'], 401);
            return;
        }

        // Generar token JWT
        $token = $this->jwtManager->generateToken([
            'user_id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email']
        ]);

        $this->jsonResponse([
            'message' => 'Inicio de sesión exitoso',
            'token' => $token
        ]);
    }

    /**
     * Envía una respuesta JSON
     * 
     * @param array $data Datos a enviar
     * @param int $status Código de estado HTTP
     */
    private function jsonResponse(array $data, int $status = 200): void {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
}