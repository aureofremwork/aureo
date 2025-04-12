<?php

namespace System;

abstract class Seeder {
    protected $db;
    protected $table;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    abstract public function run();

    protected function insert($data) {
        if (empty($data)) {
            return false;
        }

        // Si es un solo registro, convertirlo en array
        if (!is_array(reset($data))) {
            $data = [$data];
        }

        foreach ($data as $row) {
            $columns = array_keys($row);
            $values = array_values($row);
            $placeholders = array_fill(0, count($values), '?');

            $sql = sprintf(
                "INSERT INTO %s (%s) VALUES (%s)",
                $this->table,
                implode(", ", $columns),
                implode(", ", $placeholders)
            );

            $stmt = $this->db->prepare($sql);
            $stmt->execute($values);
        }

        return true;
    }

    protected function truncate() {
        $sql = "TRUNCATE TABLE {$this->table}";
        return $this->db->exec($sql);
    }
}