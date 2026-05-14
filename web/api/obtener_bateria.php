<?php
session_start();
require_once '../conexion.php';
header('Content-Type: application/json');
if (!isset($_SESSION['dispositiu'])) {
    echo json_encode(['error' => 'No device linked']);
    exit();
}
$dispositiu = $_SESSION['dispositiu'];
try {
    $sql = "SELECT bateria FROM alertes 
            WHERE usuari = ? 
            ORDER BY data_hora DESC 
            LIMIT 1";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$dispositiu]);
    $alerta = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($alerta) {
        echo json_encode(['bateria' => (int)$alerta['bateria']]);
    } else {
        echo json_encode(['bateria' => 0, 'message' => 'No data available']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>