<?php
namespace App\Controllers;

/**
 * Controlador de Autenticación
 * 
 * Maneja todas las operaciones relacionadas con la autenticación de usuarios:
 * - Registro de nuevos usuarios
 * - Inicio de sesión
 * - Cierre de sesión
 */
class AuthController {
    /**
     * Muestra el formulario de inicio de sesión
     */
    public function loginForm() {
        // Cargar la vista del formulario de login
        view('auth/login');
    }

    /**
     * Procesa el inicio de sesión
     */
    public function login() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        // Aquí iría la validación y verificación contra la base de datos
        // Por ahora, simulamos una autenticación básica
        if ($email === 'admin@example.com' && $password === 'password') {
            $_SESSION['user_id'] = 1;
            $_SESSION['user'] = [
                'id' => 1,
                'email' => $email,
                'name' => 'Administrador'
            ];
            
            // Redirigir al dashboard
            header('Location: /dashboard');
            exit;
        }

        // Si la autenticación falla, volver al formulario con un mensaje de error
        $_SESSION['error'] = 'Credenciales inválidas';
        header('Location: /login');
        exit;
    }

    /**
     * Muestra el formulario de registro
     */
    public function registerForm() {
        // Cargar la vista del formulario de registro
        view('auth/register');
    }

    /**
     * Procesa el registro de un nuevo usuario
     */
    public function register() {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        // Aquí iría la validación y guardado en la base de datos
        // Por ahora, simulamos un registro exitoso
        $_SESSION['success'] = 'Usuario registrado correctamente';
        header('Location: /login');
        exit;
    }

    /**
     * Cierra la sesión del usuario
     */
    public function logout() {
        // Destruir la sesión
        session_destroy();
        
        // Redirigir al inicio
        header('Location: /');
        exit;
    }
}