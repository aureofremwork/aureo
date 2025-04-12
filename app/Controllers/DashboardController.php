<?php
namespace App\Controllers;

use System\View;

/**
 * Controlador Dashboard - Gestiona la interfaz administrativa
 * 
 * Este controlador maneja la visualización del panel de administración
 * utilizando la plantilla AdminLTE.
 */
class DashboardController {
    /**
     * Muestra el panel de control principal
     */
    public function index() {
        $view = new View();
        
        $data = [
            'title' => 'Panel de Control',
            'stats' => [
                'usuarios' => 150,
                'productos' => 53,
                'ventas' => 1250
            ],
            'username' => 'Administrador'
        ];

        echo $view->renderWithAdminLTE('dashboard/index', $data);
    }
}