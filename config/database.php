<?php
/*
 * Esta clase se encarga de establecer la conexión con la
 * base de datos utilizando PDO. Así evitamos repetir código.
 */

class Database {

    private $host = "127.0.0.1";
    private $db_name = "Parcial3_IngWeb"; // Nombre de la BD del parcial
    private $username = "root";
    private $password = "";
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            // Conexión usando PDO
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password);

            // Activar errores como excepciones
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            die("Error de Conexión: " . $e->getMessage());
        }

        return $this->conn;
    }
}
