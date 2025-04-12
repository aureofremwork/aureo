<?php

if (!function_exists('view')) {
    /**
     * Renderiza una vista utilizando el layout de AdminLTE
     * 
     * @param string $view Ruta de la vista a renderizar
     * @param array $data Datos a pasar a la vista
     * @return void
     */
    function view($view, $data = []) {
        // Extraer los datos para que estén disponibles en la vista
        if (!empty($data)) {
            extract($data);
        }

        // Iniciar el buffer de salida
        ob_start();

        // Incluir la vista
        $viewPath = __DIR__ . '/../app/Views/' . $view . '.php';
        if (!file_exists($viewPath)) {
            throw new Exception("Vista no encontrada: {$view}");
        }
        require $viewPath;

        // Obtener el contenido del buffer
        $content = ob_get_clean();

        // Si no hay título definido, usar uno por defecto
        if (!isset($title)) {
            $title = 'Áureo Framework';
        }

        // Renderizar el layout con el contenido
        require __DIR__ . '/../app/Views/layouts/adminlte.php';
    }
}