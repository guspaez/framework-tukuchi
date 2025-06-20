<?php

use Tukuchi\Core\Migration;

class AddRoleToUsersTable extends Migration
{
    /**
     * Ejecutar migración
     */
    public function up()
    {
        $sql = "ALTER TABLE users ADD COLUMN role VARCHAR(20) DEFAULT 'user' AFTER status";
        $this->database->query($sql);
        
        // Crear índice para la columna role
        $sql = "CREATE INDEX idx_users_role ON users(role)";
        $this->database->query($sql);
    }

    /**
     * Revertir migración
     */
    public function down()
    {
        // Eliminar índice
        $sql = "DROP INDEX idx_users_role ON users";
        $this->database->query($sql);
        
        // Eliminar columna
        $sql = "ALTER TABLE users DROP COLUMN role";
        $this->database->query($sql);
    }
}