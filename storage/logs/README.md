# Sistema de Logging de Áureo Framework

Este directorio contiene los archivos de registro (logs) del framework. El archivo principal es `framework.log`, que almacena todos los registros del sistema.

## Niveles de Log

El sistema soporta tres niveles de logging:

- **ERROR**: Para errores críticos y excepciones que afectan el funcionamiento de la aplicación
- **INFO**: Para información general sobre el funcionamiento del sistema
- **DEBUG**: Para información detallada útil durante el desarrollo y depuración

## Formato de Log

Cada entrada en el log sigue este formato:

```
[YYYY-MM-DD HH:mm:ss] [NIVEL] Mensaje {Contexto JSON opcional}
```

## Uso

```php
$logger = System\Logger::getInstance();

// Ejemplos de uso
$logger->error('Error en la conexión a la base de datos', ['error' => 'Connection refused']);
$logger->info('Usuario autenticado correctamente', ['user_id' => 1]);
$logger->debug('Valores de variables', ['data' => $someData]);
```