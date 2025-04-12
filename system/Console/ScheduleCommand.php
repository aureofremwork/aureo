<?php
namespace System\Console;

use System\Scheduler;

/**
 * Comando para ejecutar tareas programadas
 */
class ScheduleCommand {
    private Scheduler $scheduler;

    public function __construct() {
        $this->scheduler = new Scheduler();
    }

    /**
     * Ejecuta las tareas programadas
     */
    public function run(): void {
        // Cargar el archivo de configuración de tareas
        $schedulePath = dirname(dirname(__DIR__)) . '/schedule.php';
        if (file_exists($schedulePath)) {
            require $schedulePath;
        }

        // Ejecutar las tareas programadas
        $this->scheduler->run();
    }

    /**
     * Obtiene la descripción del comando
     */
    public function getDescription(): string {
        return 'Ejecuta las tareas programadas';
    }

    /**
     * Obtiene el nombre del comando
     */
    public function getName(): string {
        return 'schedule:run';
    }
}