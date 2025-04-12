<?php
namespace App\Controllers;

use System\View;

/**
 * Controlador Home - Controlador de ejemplo de Áureo Framework
 * 
 * Este controlador maneja las acciones de la página principal y sirve
 * como ejemplo de cómo crear controladores en el framework.
 */
class HomeController {
    /**
     * Acción principal que muestra la página de inicio
     */
    public function index() {
        $view = new View();
        
        $data = [
            'title' => 'Bienvenido a Áureo Framework',
            'description' => 'Un framework PHP minimalista y elegante'
        ];

        echo $view->renderWithAdminLTE('home/index', $data);
    }
}