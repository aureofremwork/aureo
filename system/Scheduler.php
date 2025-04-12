<?php
namespace System;

/**
 * Sistema de tareas programadas
 * 
 * Permite definir y ejecutar tareas programadas de forma flexible y extensible.
 * Las tareas se pueden programar para ejecutarse en intervalos específicos.
 */
class Scheduler {
    private array $tasks = [];
    private array $frequencies = [
        'everyMinute' => '* * * * *',
        'hourly' => '0 * * * *',
        'daily' => '0 0 * * *',
        'weekly' => '0 0 * * 0',
        'monthly' => '0 0 1 * *',
        'yearly' => '0 0 1 1 *'
    ];

    /**
     * Registra una nueva tarea programada
     * 
     * @param string $command Comando a ejecutar
     * @param array $args Argumentos del comando
     * @return Task
     */
    public function command(string $command, array $args = []): Task {
        $task = new Task($command, $args);
        $this->tasks[] = $task;
        return $task;
    }

    /**
     * Ejecuta todas las tareas programadas que correspondan
     */
    public function run(): void {
        $now = time();
        foreach ($this->tasks as $task) {
            if ($this->isDue($task, $now)) {
                $this->execute($task);
            }
        }
    }

    /**
     * Verifica si una tarea debe ejecutarse
     */
    private function isDue(Task $task, int $now): bool {
        $expression = $task->getExpression();
        if (empty($expression)) {
            return false;
        }

        $parts = explode(' ', $expression);
        if (count($parts) !== 5) {
            return false;
        }

        $date = getdate($now);
        return $this->matchesCron($parts[0], $date['minutes'])
            && $this->matchesCron($parts[1], $date['hours'])
            && $this->matchesCron($parts[2], $date['mday'])
            && $this->matchesCron($parts[3], $date['mon'])
            && $this->matchesCron($parts[4], $date['wday']);
    }

    /**
     * Verifica si un valor coincide con una expresión cron
     */
    private function matchesCron(string $expr, int $value): bool {
        if ($expr === '*') {
            return true;
        }

        if (str_contains($expr, ',')) {
            $values = explode(',', $expr);
            return in_array($value, $values);
        }

        if (str_contains($expr, '/')) {
            [$_, $step] = explode('/', $expr);
            return $value % (int)$step === 0;
        }

        if (str_contains($expr, '-')) {
            [$start, $end] = explode('-', $expr);
            return $value >= (int)$start && $value <= (int)$end;
        }

        return (int)$expr === $value;
    }

    /**
     * Ejecuta una tarea programada
     */
    private function execute(Task $task): void {
        $command = $task->getCommand();
        $args = $task->getArguments();

        // Aquí se implementaría la lógica para ejecutar el comando
        // Por ejemplo, usando proc_open() o system()
        $cmd = escapeshellcmd($command);
        foreach ($args as $arg) {
            $cmd .= ' ' . escapeshellarg($arg);
        }

        if (PHP_OS_FAMILY === 'Windows') {
            pclose(popen('start /B ' . $cmd, 'r'));
        } else {
            exec($cmd . ' > /dev/null 2>&1 &');
        }
    }
}

/**
 * Clase auxiliar para definir tareas programadas
 */
class Task {
    private string $command;
    private array $args;
    private string $expression = '';

    public function __construct(string $command, array $args = []) {
        $this->command = $command;
        $this->args = $args;
    }

    public function cron(string $expression): self {
        $this->expression = $expression;
        return $this;
    }

    public function everyMinute(): self {
        return $this->cron('* * * * *');
    }

    public function hourly(): self {
        return $this->cron('0 * * * *');
    }

    public function daily(): self {
        return $this->cron('0 0 * * *');
    }

    public function weekly(): self {
        return $this->cron('0 0 * * 0');
    }

    public function monthly(): self {
        return $this->cron('0 0 1 * *');
    }

    public function yearly(): self {
        return $this->cron('0 0 1 1 *');
    }

    public function getExpression(): string {
        return $this->expression;
    }

    public function getCommand(): string {
        return $this->command;
    }

    public function getArguments(): array {
        return $this->args;
    }
}