<?php
// Archivo: config/database.php

class Database {
    private $host = "localhost";
    private $db_name = "sistema_academico_respaldo";
    private $username = "root"; 
    private $password = ""; 
    public $conn;

    // AÑADIMOS 'static' AQUÍ
    public static function getConnection() {
        $conn = null;

        try {
            $dsn = "mysql:host=" . (new self)->host . ";dbname=" . (new self)->db_name . ";charset=utf8mb4";
            
            // Instanciamos PDO de forma estática segura
            $conn = new PDO($dsn, (new self)->username, (new self)->password);
            
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        } catch(PDOException $exception) {
            echo "Error de conexión a la Base de Datos: " . $exception->getMessage();
        }

        return $conn;
    }
}
?>