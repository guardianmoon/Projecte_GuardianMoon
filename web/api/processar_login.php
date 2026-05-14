<?php
session_start();
require_once '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Busquem l'usuari per email
        $stmt = $conexion->prepare("SELECT * FROM clientes WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Verifiquem si existeix i si la contrasenya coincideix amb el hash
        if ($user && password_verify($password, $user['password'])) {
            // Guardem dades a la sessió
            $_SESSION['usuari_id'] = $user['id'];
            $_SESSION['usuari_nom'] = $user['nombre'];
            $_SESSION['usuari_email'] = $user['email'];
            $_SESSION['dispositiu'] = $user['dispositiu']; 
            header("Location: ../mi_panel.php");
            exit();
        } else {
            // Error si les dades no coincideixen
            echo "<script>alert('Email o contrasenya incorrectes'); window.location.href='../login.php';</script>";
        }
    } catch (PDOException $e) {
        echo "Error en el sistema: " . $e->getMessage();
    }
}
?>
