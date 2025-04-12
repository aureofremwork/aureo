<?php
namespace System;

/**
 * Clase base abstracta para todos los middleware del framework
 * 
 * Esta clase define la estructura básica que deben seguir todos los middleware
 * del sistema. Cada middleware debe implementar el método handle() que determina
 * si la solicitud puede continuar su procesamiento.
 */
abstract class Middleware {
    /**
     * Maneja la solicitud actual
     * 
     * @return bool true si la solicitud puede continuar, false si debe detenerse
     */
    abstract public function handle(): bool;

    /**
     * Método auxiliar para redirigir a una ruta específica
     * 
     * @param string $path Ruta a la que redirigir
     * @return void
     */
    protected function redirect(string $path): void {
        header('Location: ' . $path);
        exit;
    }

    /**
     * Método auxiliar para verificar si el usuario está autenticado
     * 
     * @return bool
     */
    protected function isAuthenticated(): bool {
        return isset($_SESSION['user_id']);
    }

    /**
     * Método auxiliar para obtener el usuario actual
     * 
     * @return array|null Datos del usuario o null si no está autenticado
     */
    protected function getCurrentUser(): ?array {
        return $_SESSION['user'] ?? null;
    }
}