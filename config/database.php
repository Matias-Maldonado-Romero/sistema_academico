<?php
// Archivo: config/database.php

class Database {
    // Credenciales por defecto de XAMPP/WAMP
    private $host = "localhost";
    private $db_name = "sistema_academico";
    private $username = "root"; 
    private $password = ""; 
    public $conn;

    // Función para obtener la conexión
    public function getConnection() {
        $this->conn = null;

        try {
            // DSN (Data Source Name): define el tipo de BD, host y nombre
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            
            // Instanciamos PDO
            $this->conn = new PDO($dsn, $this->username, $this->password);
            
            // CONFIGURACIONES DE SEGURIDAD Y MANEJO DE ERRORES:
            
            // 1. Que PDO lance "Excepciones" si hay un error en SQL. 
            // Esto evita que los errores revelen información sensible y nos ayuda a debuggear.
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // 2. Que los datos nos lleguen siempre como un Array Asociativo (ej: $fila['nombre'])
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        } catch(PDOException $exception) {
            // Si la conexión falla, capturamos el error aquí
            echo "Error de conexión a la Base de Datos: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>