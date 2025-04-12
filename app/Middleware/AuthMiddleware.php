<?php
namespace App\Middleware;

use System\Middleware;

/**
 * Middleware de autenticación
 * 
 * Este middleware verifica que el usuario esté autenticado antes de permitir
 * el acceso a rutas protegidas. Si el usuario no está autenticado, será
 * redirigido a la página de inicio de sesión.
 */
class AuthMiddleware extends Middleware {
    /**
     * Verifica si el usuario está autenticado
     * 
     * @return bool
     */
    public function handle(): bool {
        if (!$this->isAuthenticated()) {
            // Si el usuario no está autenticado, redirigir al login
            $this->redirect('/login');
            return false;
        }

        // Usuario autenticado, permitir continuar
        return true;
    }
}