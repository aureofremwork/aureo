<?php

namespace System;

use PDO;
use PDOException;
use Dotenv\Dotenv;

/**
 * Clase Database - Maneja la conexión a la base de datos usando PDO
 * 
 * Esta clase implementa el patrón Singleton para asegurar una única instancia
 * de conexión a la base de datos durante toda la ejecución de la aplicación.
 * Utiliza las variables de entorno definidas en el archivo .env para la configuración.
 */
class Database {
    /**
     * Instancia única de la conexión PDO
     * @var PDO|null
     */
    private static ?PDO $instance = null;

    /**
     * Constructor privado para prevenir la instanciación directa
     */
    private function __construct() {}

    /**
     * Obtiene la instancia única de la conexión a la base de datos
     * 
     * @return PDO La instancia de la conexión
     * @throws PDOException Si hay un error en la conexión
     */
    public static function getConnection(): PDO {
        if (self::$instance === null) {
            // Carga las variables de entorno
            $dotenv = Dotenv::createImmutable(dirname(__DIR__));
            $dotenv->load();

            $host = $_ENV['DB_HOST'];
            $port = $_ENV['DB_PORT'];
            $database = $_ENV['DB_DATABASE'];
            $username = $_ENV['DB_USERNAME'];
            $password = $_ENV['DB_PASSWORD'];

            try {
                // Configura la conexión PDO
                $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
                self::$instance = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]);
            } catch (PDOException $e) {
                // Manejo claro de errores de conexión
                throw new PDOException(
                    "Error de conexión a la base de datos: " . $e->getMessage(),
                    (int)$e->getCode()
                );
            }
        }

        return self::$instance;
    }

    /**
     * Previene la clonación de la instancia
     */
    private function __clone() {}

    /**
     * Previene la deserialización de la instancia
     */
    public function __wakeup() {
        throw new \Exception("No se permite deserializar una instancia de Database");
    }
}