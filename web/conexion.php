<?php
// Credenciales de la base de datos
$host = 'mariadb.guardianmoon.cat';
$dbname = 'guardianmoon';
$username = 'admin';
$password = 'Rio-Alto-724'; // <-- ¡No olvides poner tu contraseña!

try {
    // Creamos la conexión PDO
    $conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Le decimos que nos avise si hay errores internos de MySQL
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Si falla la conexión, mostramos el error
    die("Error crítico al conectar con la base de datos: " . $e->getMessage());
}
?>
