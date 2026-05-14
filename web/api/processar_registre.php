



<?php
require_once '../conexion.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $dispositiu = $_POST['dispositiu']; //registre dispositiu
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    try {
        // ✅ MODIFICADO: Agregada columna dispositiu
        $sql = "INSERT INTO clientes (nombre, dispositiu, email, password, telefono, direccion) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$nombre, $dispositiu, $email, $password, $telefono, $direccion]);
        // Iniciem sessió i anem al panell
        session_start();
        $_SESSION['usuari_nom'] = $nombre;
        $_SESSION['usuari_id'] = $conexion->lastInsertId(); //  Guardar ID
        $_SESSION['dispositiu'] = $dispositiu; // NUEVO: Guardar dispositiu en sesión
        
        header("Location: ../mi_panel.php");
        exit();
    } catch (PDOException $e) {
        echo "Error al registre: " . $e->getMessage();
    }
}
?>











