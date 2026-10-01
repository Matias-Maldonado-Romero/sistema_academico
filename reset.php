<?php
require_once "config/Database.php"; // Verifica que la ruta a tu Database.php sea correcta

try {
    $db = Database::getConnection();
    
    // PHP generará el hash Bcrypt válido en TU servidor
    $passValida = password_hash("Admin2026!", PASSWORD_BCRYPT);

    $stmt = $db->prepare("UPDATE usuarios SET password = :pass WHERE username = 'admin'");
    $stmt->execute([':pass' => $passValida]);

    echo "Contraseña de 'admin' actualizada con éxito.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}