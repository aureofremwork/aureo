<?php

namespace System;

use PDO;

abstract class Migration {
    protected PDO $db;
    protected $table;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    abstract public function up();
    abstract public function down();

    protected function createTable($columns) {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table} (";
        $columnDefinitions = [];

        foreach ($columns as $name => $definition) {
            $columnDefinitions[] = "{$name} {$definition}";
        }

        $sql .= implode(", ", $columnDefinitions);
        $sql .= ")";

        return $this->db->query($sql);
    }

    protected function dropTable() {
        $sql = "DROP TABLE IF EXISTS {$this->table}";
        return $this->db->query($sql);
    }

    protected function addColumn($name, $definition) {
        $sql = "ALTER TABLE {$this->table} ADD COLUMN {$name} {$definition}";
        return $this->db->query($sql);
    }

    protected function dropColumn($name) {
        $sql = "ALTER TABLE {$this->table} DROP COLUMN {$name}";
        return $this->db->query($sql);
    }

    protected function addIndex($name, $columns) {
        $columnList = is_array($columns) ? implode(", ", $columns) : $columns;
        $sql = "CREATE INDEX {$name} ON {$this->table} ({$columnList})";
        return $this->db->query($sql);
    }

    protected function dropIndex($name) {
        $sql = "DROP INDEX {$name} ON {$this->table}";
        return $this->db->query($sql);
    }
}