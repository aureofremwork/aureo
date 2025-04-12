<?php
namespace System\Console;

use System\Cache;

/**
 * Comando para generar componentes del framework
 */
class MakeCommand {
    private string $basePath;

    public function __construct() {
        $this->basePath = dirname(dirname(__DIR__));
    }

    /**
     * Genera un nuevo controlador
     */
    public function controller(string $name): void {
        $path = $this->basePath . '/app/Controllers/' . $name . '.php';
        $namespace = 'App\\Controllers';

        if (str_contains($name, '/')) {
            $parts = explode('/', $name);
            $name = array_pop($parts);
            $namespace .= '\\' . implode('\\', $parts);
            $path = $this->basePath . '/app/Controllers/' . implode('/', $parts) . '/' . $name . '.php';
        }

        $content = "<?php\nnamespace {$namespace};\n\nclass {$name} {\n    public function index() {\n        // Implementa tu lógica aquí\n    }\n}\n";

        $this->createFile($path, $content);
        echo "Controlador {$name} creado exitosamente\n";
    }

    /**
     * Genera un nuevo modelo
     */
    public function model(string $name): void {
        $path = $this->basePath . '/app/Models/' . $name . '.php';
        $content = "<?php\nnamespace App\\Models;\n\nuse System\\Database;\n\nclass {$name} {\n    protected \$db;\n    protected \$table;\n\n    public function __construct() {\n        \$this->db = new Database();\n        \$this->table = strtolower('{$name}s');\n    }\n\n    public function all() {\n        return \$this->db->query(\"SELECT * FROM \" . \$this->table)->fetchAll();\n    }\n\n    public function find(\$id) {\n        return \$this->db->query(\"SELECT * FROM \" . \$this->table . \" WHERE id = ?\", [\$id])->fetch();\n    }\n}\n";

        $this->createFile($path, $content);
        echo "Modelo {$name} creado exitosamente\n";
    }

    /**
     * Genera una nueva migración
     */
    public function migration(string $name): void {
        $timestamp = date('YmdHis');
        $filename = $timestamp . '_' . strtolower($name) . '.php';
        $path = $this->basePath . '/database/migrations/' . $filename;

        $content = "<?php\nuse System\\Migration;\n\nclass {$name} extends Migration {\n    public function up() {\n        \$sql = \"\";\n        return \$this->db->query(\$sql);\n    }\n\n    public function down() {\n        \$sql = \"\";\n        return \$this->db->query(\$sql);\n    }\n}\n";

        $this->createFile($path, $content);
        echo "Migración {$name} creada exitosamente\n";
    }

    /**
     * Limpia la caché de la aplicación
     */
    public function clearCache(): void {
        $cache = new Cache();
        $cacheDir = $this->basePath . '/storage/cache/';
        
        if (is_dir($cacheDir)) {
            $files = glob($cacheDir . '*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }

        echo "Caché limpiada exitosamente\n";
    }

    /**
     * Ejecuta un seeder específico
     */
    public function seed(string $name): void {
        $path = $this->basePath . '/database/seeders/' . $name . '.php';
        if (!file_exists($path)) {
            echo "Error: Seeder {$name} no encontrado\n";
            return;
        }

        require_once $path;
        $seeder = new $name();
        $seeder->run();

        echo "Seeder {$name} ejecutado exitosamente\n";
    }

    /**
     * Crea un archivo si no existe
     */
    private function createFile(string $path, string $content): void {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        if (!file_exists($path)) {
            file_put_contents($path, $content);
        } else {
            echo "Error: El archivo ya existe\n";
        }
    }

    /**
     * Obtiene la descripción del comando
     */
    public function getDescription(): string {
        return 'Genera componentes del framework';
    }

    /**
     * Obtiene el nombre del comando
     */
    public function getName(): string {
        return 'make';
    }
}