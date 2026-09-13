<?php
// Archivo: index.php (Temporal para prueba)
require_once 'config/database.php';

$db = new Database();
$conexion = $db->getConnection();

if($conexion){
    echo "¡Conexión exitosa a PDO usando MVC!";
}
?>