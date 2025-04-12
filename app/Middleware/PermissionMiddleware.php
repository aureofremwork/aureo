<?php
namespace App\Middleware;

use System\Middleware;
use System\Database;

/**
 * Middleware para control de acceso basado en roles y permisos
 * 
 * Verifica que el usuario autenticado tenga los permisos necesarios
 * para acceder a una ruta específica. Los permisos requeridos se
 * especifican al registrar la ruta.
 */
class PermissionMiddleware extends Middleware {
    private \PDO $db;
    private array $requiredPermissions;

    /**
     * @param string|array $permissions Permiso o array de permisos requeridos
     */
    public function __construct($permissions) {
        $this->db = Database::getConnection();
        $this->requiredPermissions = is_array($permissions) ? $permissions : [$permissions];
    }

    /**
     * Verifica si el usuario tiene los permisos requeridos
     * 
     * @return bool
     */
    public function handle(): bool {
        // Verificar si hay un usuario autenticado
        if (!$this->isAuthenticated()) {
            $this->sendUnauthorizedResponse('Usuario no autenticado');
            return false;
        }

        $user = $this->getCurrentUser();
        if (!$user) {
            $this->sendUnauthorizedResponse('Usuario no encontrado');
            return false;
        }

        // Obtener los permisos del usuario según su rol
        $userPermissions = $this->getUserPermissions($user['id']);
        
        // Verificar si el usuario tiene todos los permisos requeridos
        foreach ($this->requiredPermissions as $permission) {
            if (!in_array($permission, $userPermissions)) {
                $this->sendForbiddenResponse('No tienes permiso para realizar esta acción');
                return false;
            }
        }

        return true;
    }

    /**
     * Obtiene los permisos del usuario según su rol
     * 
     * @param int $userId ID del usuario
     * @return array Lista de nombres de permisos
     */
    private function getUserPermissions(int $userId): array {
        $query = "
            SELECT DISTINCT p.name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            JOIN roles_permissions rp ON r.id = rp.role_id
            JOIN permissions p ON rp.permission_id = p.id
            WHERE u.id = ?
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Envía una respuesta de error 401 Unauthorized
     */
    private function sendUnauthorizedResponse(string $message): void {
        $this->sendErrorResponse($message, 401, 'Unauthorized');
    }

    /**
     * Envía una respuesta de error 403 Forbidden
     */
    private function sendForbiddenResponse(string $message): void {
        $this->sendErrorResponse($message, 403, 'Forbidden');
    }

    /**
     * Envía una respuesta de error JSON
     */
    private function sendErrorResponse(string $message, int $status, string $error): void {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode([
            'error' => $error,
            'message' => $message
        ]);
        exit;
    }
}