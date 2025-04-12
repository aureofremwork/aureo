<?php

namespace System;

class Logger {
    private static $instance = null;
    private $logFile;
    private $logLevels = [
        'ERROR' => 0,
        'INFO'  => 1,
        'DEBUG' => 2
    ];

    private function __construct() {
        $logDir = dirname(__DIR__) . '/storage/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        $this->logFile = $logDir . '/framework.log';
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function log($level, $message, array $context = []) {
        $timestamp = date('Y-m-d H:i:s');
        $formattedMessage = "[$timestamp] [$level] $message";
        
        if (!empty($context)) {
            $formattedMessage .= ' ' . json_encode($context);
        }
        
        $formattedMessage .= PHP_EOL;
        
        file_put_contents($this->logFile, $formattedMessage, FILE_APPEND);
    }

    public function error($message, array $context = []) {
        $this->log('ERROR', $message, $context);
    }

    public function info($message, array $context = []) {
        $this->log('INFO', $message, $context);
    }

    public function debug($message, array $context = []) {
        $this->log('DEBUG', $message, $context);
    }

    public function getLogFile() {
        return $this->logFile;
    }
}