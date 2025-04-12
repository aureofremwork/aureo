<?php
namespace Database\Migrations;

use System\Migration;

/**
 * Migración para crear las tablas de roles y permisos
 */
class CreateRolesAndPermissionsTables extends Migration {
    /**
     * Ejecuta la migración
     */
    public function up(): void {
        // Crear tabla de roles
        $stmt = $this->db->prepare("
            CREATE TABLE roles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(50) NOT NULL UNIQUE,
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        $stmt->execute();

        // Crear tabla de permisos
        $stmt = $this->db->prepare("
            CREATE TABLE permissions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL UNIQUE,
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        $stmt->execute();

        // Crear tabla pivote roles_permissions
        $stmt = $this->db->prepare("
            CREATE TABLE roles_permissions (
                role_id INT,
                permission_id INT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (role_id, permission_id),
                FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
                FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
            )
        ");
        $stmt->execute();

        // Agregar columna role_id a la tabla users
        $stmt = $this->db->prepare("
            ALTER TABLE users
            ADD COLUMN role_id INT,
            ADD FOREIGN KEY (role_id) REFERENCES roles(id)
        ");
        $stmt->execute();

        // Insertar roles básicos
        $stmt = $this->db->prepare("
            INSERT INTO roles (name, description) VALUES
            ('admin', 'Administrador del sistema con acceso total'),
            ('user', 'Usuario regular con acceso limitado')
        ");
        $stmt->execute();

        // Insertar permisos básicos
        $stmt = $this->db->prepare("
            INSERT INTO permissions (name, description) VALUES
            ('users.view', 'Ver usuarios'),
            ('users.create', 'Crear usuarios'),
            ('users.edit', 'Editar usuarios'),
            ('users.delete', 'Eliminar usuarios'),
            ('roles.manage', 'Gestionar roles y permisos')
        ");
        $stmt->execute();

        // Asignar todos los permisos al rol admin
        $stmt = $this->db->prepare("
            INSERT INTO roles_permissions (role_id, permission_id)
            SELECT 1, id FROM permissions
        ");
        $stmt->execute();

        // Asignar permisos básicos al rol user
        $stmt = $this->db->prepare("
            INSERT INTO roles_permissions (role_id, permission_id)
            SELECT 2, id FROM permissions WHERE name IN ('users.view')
        ");
        $stmt->execute();
    }

    /**
     * Revierte la migración
     */
    public function down(): void {
        // Eliminar la columna role_id de users
        $stmt = $this->db->prepare("ALTER TABLE users DROP FOREIGN KEY users_ibfk_1");
        $stmt->execute();
        $stmt = $this->db->prepare("ALTER TABLE users DROP COLUMN role_id");
        $stmt->execute();

        // Eliminar las tablas en orden inverso
        $stmt = $this->db->prepare("DROP TABLE roles_permissions");
        $stmt->execute();
        $stmt = $this->db->prepare("DROP TABLE permissions");
        $stmt->execute();
        $stmt = $this->db->prepare("DROP TABLE roles");
        $stmt->execute();
    }
}