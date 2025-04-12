<?php
namespace System;

/**
 * Sistema de gestión de archivos
 * 
 * Proporciona una interfaz segura para subir, mover, borrar y listar archivos
 * con validaciones de tipo, tamaño y extensiones permitidas.
 */
class FileManager {
    private string $uploadPath;
    private array $allowedTypes;
    private int $maxSize;
    private array $dangerousExtensions;

    public function __construct() {
        $this->uploadPath = dirname(__DIR__) . '/storage/uploads/';
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0777, true);
        }

        // Tipos MIME permitidos por defecto
        $this->allowedTypes = [
            'image/jpeg', 'image/png', 'image/gif',
            'application/pdf', 'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain', 'text/csv'
        ];

        // Tamaño máximo por defecto (5MB)
        $this->maxSize = 5 * 1024 * 1024;

        // Extensiones potencialmente peligrosas
        $this->dangerousExtensions = [
            'php', 'php3', 'php4', 'php5', 'phtml',
            'exe', 'bat', 'cmd', 'sh', 'js',
            'pif', 'application', 'gadget', 'msi', 'msp', 'com',
            'scr', 'hta', 'cpl', 'msc', 'jar', 'vb', 'vbs', 'vbe',
            'jse', 'ws', 'wsf', 'wsc', 'wsh', 'ps1', 'ps1xml', 'ps2',
            'ps2xml', 'psc1', 'psc2', 'msh', 'msh1', 'msh2', 'mshxml',
            'msh1xml', 'msh2xml', 'scf', 'lnk', 'inf', 'reg'
        ];
    }

    /**
     * Sube un archivo al servidor
     * 
     * @param array $file Archivo del formulario ($_FILES)
     * @param string $directory Subdirectorio opcional dentro de uploads
     * @return array Información del archivo subido o errores
     */
    public function upload(array $file, string $directory = ''): array {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['error' => 'No se ha subido ningún archivo'];
        }

        // Validaciones básicas
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['error' => 'Error al subir el archivo: ' . $this->getUploadError($file['error'])];
        }

        if ($file['size'] > $this->maxSize) {
            return ['error' => 'El archivo excede el tamaño máximo permitido'];
        }

        $mimeType = mime_content_type($file['tmp_name']);
        if (!in_array($mimeType, $this->allowedTypes)) {
            return ['error' => 'Tipo de archivo no permitido'];
        }

        // Validación de extensión
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (in_array($extension, $this->dangerousExtensions)) {
            return ['error' => 'Extensión de archivo no permitida por razones de seguridad'];
        }

        // Preparar directorio de destino
        $uploadDir = $this->uploadPath . trim($directory, '/');
        if (!empty($directory) && !is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Generar nombre único
        $filename = $this->generateUniqueFilename($file['name'], $uploadDir);
        $destination = $uploadDir . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return [
                'success' => true,
                'filename' => $filename,
                'path' => $destination,
                'size' => $file['size'],
                'type' => $mimeType
            ];
        }

        return ['error' => 'Error al mover el archivo'];
    }

    /**
     * Mueve un archivo a una nueva ubicación
     */
    public function move(string $source, string $destination): bool {
        if (!file_exists($source)) {
            return false;
        }

        $destDir = dirname($destination);
        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, true);
        }

        return rename($source, $destination);
    }

    /**
     * Elimina un archivo
     */
    public function delete(string $path): bool {
        if (file_exists($path) && is_file($path)) {
            return unlink($path);
        }
        return false;
    }

    /**
     * Lista archivos en un directorio
     * 
     * @param string $directory Subdirectorio dentro de uploads
     * @return array Lista de archivos con sus detalles
     */
    public function list(string $directory = ''): array {
        $path = $this->uploadPath . trim($directory, '/');
        if (!is_dir($path)) {
            return [];
        }

        $files = [];
        $items = scandir($path);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;

            $fullPath = $path . '/' . $item;
            if (is_file($fullPath)) {
                $files[] = [
                    'name' => $item,
                    'path' => $fullPath,
                    'size' => filesize($fullPath),
                    'type' => mime_content_type($fullPath),
                    'modified' => filemtime($fullPath)
                ];
            }
        }

        return $files;
    }

    /**
     * Establece los tipos MIME permitidos
     */
    public function setAllowedTypes(array $types): void {
        $this->allowedTypes = $types;
    }

    /**
     * Establece el tamaño máximo permitido en bytes
     */
    public function setMaxSize(int $size): void {
        $this->maxSize = $size;
    }

    /**
     * Genera un nombre de archivo único
     */
    private function generateUniqueFilename(string $originalName, string $directory): string {
        $info = pathinfo($originalName);
        $ext = $info['extension'];
        $filename = $info['filename'];

        $newFilename = $filename;
        $counter = 1;

        while (file_exists($directory . '/' . $newFilename . '.' . $ext)) {
            $newFilename = $filename . '_' . $counter++;
        }

        return $newFilename . '.' . $ext;
    }

    /**
     * Obtiene el mensaje de error de subida
     */
    private function getUploadError(int $code): string {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo permitido por PHP',
            UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño máximo permitido por el formulario',
            UPLOAD_ERR_PARTIAL => 'El archivo se subió parcialmente',
            UPLOAD_ERR_NO_FILE => 'No se subió ningún archivo',
            UPLOAD_ERR_NO_TMP_DIR => 'Falta la carpeta temporal',
            UPLOAD_ERR_CANT_WRITE => 'Error al escribir el archivo',
            UPLOAD_ERR_EXTENSION => 'Una extensión de PHP detuvo la subida',
            default => 'Error desconocido'
        };
    }
}