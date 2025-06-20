<?php

use Tukuchi\Core\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Ejecutar migración
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(150) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            status VARCHAR(20) DEFAULT 'active',
            created_at TIMESTAMP NULL DEFAULT NULL,
            updated_at TIMESTAMP NULL DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $this->database->query($sql);
    }

    /**
     * Revertir migración
     */
    public function down()
    {
        $sql = "DROP TABLE IF EXISTS users";
        $this->database->query($sql);
    }
}