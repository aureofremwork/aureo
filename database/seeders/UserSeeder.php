<?php

namespace Database\Seeders;

use System\Seeder;

class UserSeeder extends Seeder {
    protected $table = 'users';

    public function run() {
        // Limpiar la tabla antes de insertar nuevos datos
        $this->truncate();

        // Insertar usuarios de ejemplo
        $this->insert([
            [
                'name' => 'Administrador',
                'email' => 'admin@aureo.local',
                'password' => password_hash('admin123', PASSWORD_DEFAULT)
            ],
            [
                'name' => 'Usuario Demo',
                'email' => 'demo@aureo.local',
                'password' => password_hash('demo123', PASSWORD_DEFAULT)
            ],
            [
                'name' => 'Juan Pérez',
                'email' => 'juan@aureo.local',
                'password' => password_hash('juan123', PASSWORD_DEFAULT)
            ]
        ]);
    }
}