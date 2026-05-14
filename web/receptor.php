<?php
// Conectamos a la base de datos
require_once 'conexion.php';

// Recibimos los datos que envía MacroDroid (pueden venir por GET o POST)
$usuari = isset($_REQUEST['usuari']) ? $_REQUEST['usuari'] : 'Dispositiu Desconegut';
$coordenades = isset($_REQUEST['coordenades']) ? $_REQUEST['coordenades'] : '0.0000, 0.0000';
$bateria = isset($_REQUEST['bateria']) ? (int)$_REQUEST['bateria'] : 0;

try {
    // Preparamos la orden para guardar en MySQL (evitando inyecciones SQL)
    $stmt = $conexion->prepare("INSERT INTO alertes (data_hora, usuari, coordenades, bateria) VALUES (NOW(), ?, ?, ?)");
    
    // Ejecutamos la orden con los datos que nos han llegado
    $stmt->execute([$usuari, $coordenades, $bateria]);
    
    echo "¡Alerta guardada correctamente en la base de datos!";
} catch (PDOException $e) {
    echo "Error al guardar: " . $e->getMessage();
}
?>
