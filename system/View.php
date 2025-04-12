<?php
namespace System;

/**
 * Clase View - Sistema de vistas de Áureo Framework
 * 
 * Esta clase maneja la renderización de vistas y la integración con AdminLTE
 */
class View {
    /**
     * Ruta base para las vistas
     */
    private string $viewPath;

    /**
     * Constructor de la clase View
     */
    public function __construct() {
        $this->viewPath = BASE_PATH . '/app/Views/';
    }

    /**
     * Renderiza una vista con la plantilla AdminLTE
     * 
     * @param string $view Nombre de la vista a renderizar
     * @param array $data Datos para pasar a la vista
     * @return string Contenido HTML renderizado
     */
    public function renderWithAdminLTE(string $view, array $data = []): string {
        // Extraer los datos para que estén disponibles en la vista
        extract($data);

        // Iniciar el buffer de salida
        ob_start();

        // Cargar la vista principal
        $viewFile = $this->viewPath . str_replace('/', DIRECTORY_SEPARATOR, $view) . '.php';
        if (!file_exists($viewFile)) {
            throw new \RuntimeException("Vista no encontrada: {$view}");
        }

        // Cargar el layout de AdminLTE
        require_once $this->viewPath . 'layouts/adminlte.php';

        // Obtener el contenido del buffer y limpiarlo
        $content = ob_get_clean();

        return $content;
    }

    /**
     * Renderiza una vista sin plantilla
     * 
     * @param string $view Nombre de la vista a renderizar
     * @param array $data Datos para pasar a la vista
     * @return string Contenido HTML renderizado
     */
    public function render(string $view, array $data = []): string {
        // Extraer los datos para que estén disponibles en la vista
        extract($data);

        // Iniciar el buffer de salida
        ob_start();

        // Cargar la vista
        $viewFile = $this->viewPath . str_replace('/', DIRECTORY_SEPARATOR, $view) . '.php';
        if (!file_exists($viewFile)) {
            throw new \RuntimeException("Vista no encontrada: {$view}");
        }

        require_once $viewFile;

        // Obtener el contenido del buffer y limpiarlo
        return ob_get_clean();
    }
}