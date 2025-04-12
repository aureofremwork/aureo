<?php
namespace System;

/**
 * Sistema de internacionalización (i18n)
 * 
 * Proporciona soporte para múltiples idiomas en la aplicación
 * utilizando archivos de traducción en formato PHP.
 */
class I18n {
    private static ?I18n $instance = null;
    private string $locale;
    private array $messages = [];
    private string $langPath;

    private function __construct() {
        $this->langPath = dirname(__DIR__) . '/resources/lang/';
        $this->locale = $_ENV['APP_LOCALE'] ?? 'es';
        $this->loadMessages();
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Obtiene una traducción
     * 
     * @param string $key Clave de traducción (puede usar punto para acceder a niveles anidados)
     * @param array $replace Variables a reemplazar en el mensaje
     * @param string|null $locale Idioma específico (opcional)
     * @return string
     */
    public function get(string $key, array $replace = [], ?string $locale = null): string {
        $locale = $locale ?? $this->locale;
        $message = $this->getMessage($key, $locale);

        if ($message === null) {
            return $key;
        }

        if (!empty($replace)) {
            foreach ($replace as $key => $value) {
                $message = str_replace(':' . $key, $value, $message);
            }
        }

        return $message;
    }

    /**
     * Cambia el idioma actual
     */
    public function setLocale(string $locale): void {
        $this->locale = $locale;
        $this->loadMessages();
    }

    /**
     * Obtiene el idioma actual
     */
    public function getLocale(): string {
        return $this->locale;
    }

    /**
     * Carga los mensajes del idioma actual
     */
    private function loadMessages(): void {
        $path = $this->langPath . $this->locale;
        if (!is_dir($path)) {
            return;
        }

        $this->messages = [];
        foreach (glob($path . '/*.php') as $file) {
            $group = basename($file, '.php');
            $messages = require $file;
            if (is_array($messages)) {
                $this->messages[$group] = $messages;
            }
        }
    }

    /**
     * Obtiene un mensaje de traducción
     */
    private function getMessage(string $key, string $locale): ?string {
        $parts = explode('.', $key);
        if (count($parts) < 2) {
            return null;
        }

        $group = array_shift($parts);
        $messages = $this->messages[$group] ?? [];

        foreach ($parts as $part) {
            if (!isset($messages[$part])) {
                return null;
            }
            $messages = $messages[$part];
        }

        return is_string($messages) ? $messages : null;
    }
}

/**
 * Función global de ayuda para traducciones
 * 
 * @param string $key Clave de traducción
 * @param array $replace Variables a reemplazar
 * @param string|null $locale Idioma específico
 * @return string
 */
function __(string $key, array $replace = [], ?string $locale = null): string {
    return I18n::getInstance()->get($key, $replace, $locale);
}