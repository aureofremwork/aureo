<?php
namespace System;

/**
 * Sistema de caché basado en archivos
 * 
 * Proporciona una interfaz simple para almacenar y recuperar datos en caché
 * utilizando el sistema de archivos como almacenamiento.
 */
class Cache {
    private string $cachePath;

    public function __construct() {
        $this->cachePath = dirname(__DIR__) . '/storage/cache/';
        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0777, true);
        }
    }

    /**
     * Almacena un valor en la caché
     * 
     * @param string $key Clave única para identificar el valor
     * @param mixed $value Valor a almacenar
     * @param int $ttl Tiempo de vida en segundos (0 = sin expiración)
     * @return bool true si se almacenó correctamente
     */
    public function put(string $key, mixed $value, int $ttl = 0): bool {
        $filename = $this->getFilename($key);
        $data = [
            'value' => $value,
            'expiration' => $ttl > 0 ? time() + $ttl : 0
        ];

        return file_put_contents($filename, serialize($data)) !== false;
    }

    /**
     * Obtiene un valor de la caché
     * 
     * @param string $key Clave del valor a recuperar
     * @param mixed $default Valor por defecto si no existe la clave
     * @return mixed El valor almacenado o el valor por defecto
     */
    public function get(string $key, mixed $default = null): mixed {
        if (!$this->has($key)) {
            return $default;
        }

        $filename = $this->getFilename($key);
        $data = unserialize(file_get_contents($filename));

        return $data['value'];
    }

    /**
     * Verifica si una clave existe en la caché y no ha expirado
     * 
     * @param string $key Clave a verificar
     * @return bool true si existe y no ha expirado
     */
    public function has(string $key): bool {
        $filename = $this->getFilename($key);
        
        if (!file_exists($filename)) {
            return false;
        }

        $data = unserialize(file_get_contents($filename));
        if ($data['expiration'] > 0 && $data['expiration'] < time()) {
            $this->forget($key);
            return false;
        }

        return true;
    }

    /**
     * Elimina una clave de la caché
     * 
     * @param string $key Clave a eliminar
     * @return bool true si se eliminó correctamente
     */
    public function forget(string $key): bool {
        $filename = $this->getFilename($key);
        if (file_exists($filename)) {
            return unlink($filename);
        }
        return true;
    }

    /**
     * Genera el nombre de archivo para una clave
     */
    private function getFilename(string $key): string {
        return $this->cachePath . md5($key) . '.cache';
    }
}