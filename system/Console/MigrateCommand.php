<?php

namespace System\Console;

class MigrateCommand {
    protected $db;
    protected $migrationsPath;
    protected $seedersPath;

    public function __construct() {
        $this->db = new \System\Database();
        $this->migrationsPath = __DIR__ . '/../../database/migrations/';
        $this->seedersPath = __DIR__ . '/../../database/seeders/';
    }

    public function migrate() {
        if (!is_dir($this->migrationsPath)) {
            echo "Error: Directorio de migraciones no encontrado.\n";
            return false;
        }

        $migrations = $this->getMigrationFiles();
        if (empty($migrations)) {
            echo "No hay migraciones pendientes.\n";
            return true;
        }

        foreach ($migrations as $migration) {
            require_once $migration;
            $className = $this->getMigrationClassName($migration);
            $instance = new $className();

            try {
                if ($instance->up()) {
                    echo "Migración ejecutada: " . basename($migration) . "\n";
                }
            } catch (\Exception $e) {
                echo "Error en la migración " . basename($migration) . ": " . $e->getMessage() . "\n";
                return false;
            }
        }

        return true;
    }

    public function rollback() {
        $migrations = array_reverse($this->getMigrationFiles());
        
        foreach ($migrations as $migration) {
            require_once $migration;
            $className = $this->getMigrationClassName($migration);
            $instance = new $className();

            try {
                if ($instance->down()) {
                    echo "Migración revertida: " . basename($migration) . "\n";
                }
            } catch (\Exception $e) {
                echo "Error al revertir la migración " . basename($migration) . ": " . $e->getMessage() . "\n";
                return false;
            }
        }

        return true;
    }

    public function seed($seeder = null) {
        if (!is_dir($this->seedersPath)) {
            echo "Error: Directorio de seeders no encontrado.\n";
            return false;
        }

        if ($seeder) {
            return $this->runSeeder($seeder);
        }

        $seeders = $this->getSeederFiles();
        foreach ($seeders as $seederFile) {
            $this->runSeeder(basename($seederFile, '.php'));
        }

        return true;
    }

    protected function runSeeder($seeder) {
        $seederFile = $this->seedersPath . $seeder . '.php';
        if (!file_exists($seederFile)) {
            echo "Error: Seeder no encontrado: {$seeder}\n";
            return false;
        }

        require_once $seederFile;
        $className = "Database\\Seeders\\{$seeder}";
        $instance = new $className();

        try {
            $instance->run();
            echo "Seeder ejecutado: {$seeder}\n";
            return true;
        } catch (\Exception $e) {
            echo "Error al ejecutar seeder {$seeder}: " . $e->getMessage() . "\n";
            return false;
        }
    }

    protected function getMigrationFiles() {
        $files = glob($this->migrationsPath . '*.php');
        sort($files); // Ordenar por nombre para mantener el orden de ejecución
        return $files;
    }

    protected function getSeederFiles() {
        return glob($this->seedersPath . '*.php');
    }

    protected function getMigrationClassName($file) {
        $className = basename($file, '.php');
        // Remover el número de orden y convertir a CamelCase
        $className = preg_replace('/^\d+_/', '', $className);
        $className = str_replace('_', ' ', $className);
        $className = ucwords($className);
        $className = str_replace(' ', '', $className);
        return "Database\\Migrations\\{$className}";
    }
}