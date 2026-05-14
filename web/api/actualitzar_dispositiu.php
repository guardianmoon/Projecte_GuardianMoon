<?php
session_start();
require_once '../conexion.php';
if (!isset($_SESSION['usuari_id'])) {
    echo json_encode(['status' => 'error', 'msg' => 'No autorizado']);
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevoDispositiu'])) {
    $nuevoDispositiu = trim($_POST['nuevoDispositiu']);
    $usuari_id = $_SESSION['usuari_id'];
    if (empty($nuevoDispositiu)) {
        echo json_encode(['status' => 'error', 'msg' => 'El nombre del dispositivo no puede estar vacío']);
        exit();
    }
    try {
        // Actualizar la columna dispositiu en la tabla clientes
        $stmt = $conexion->prepare("UPDATE clientes SET dispositiu = ? WHERE id = ?");
        $stmt->execute([$nuevoDispositiu, $usuari_id]);
        // Actualizar también la sesión
        $_SESSION['dispositiu'] = $nuevoDispositiu;
        echo json_encode(['status' => 'ok', 'msg' => 'Dispositivo actualizado correctamente']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'msg' => 'Solicitud inválida']);
}
?>