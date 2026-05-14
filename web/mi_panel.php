<?php
session_start();
require_once 'conexion.php';
// Verificar sesión del usuario
if (!isset($_SESSION['usuari_id'])) {
    header("Location: login.php");
    exit();
}
$usuari_id = $_SESSION['usuari_id'];
// Consulta para obtener los datos personales del usuario
$sql = "SELECT nombre, email, telefono, direccion FROM clientes WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->execute([$usuari_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
// Se asume que en la sesión se almacena el nombre del dispositivo (por ejemplo, $_SESSION['dispositiu'])
$dispositiu = $_SESSION['dispositiu'] ?? null;
// Consulta para obtener la última alerta (para batería y datos de conexión)
$ultima_alerta = null;
if ($dispositiu) {
    $sql_alerta = "SELECT bateria, data_hora, coordenades FROM alertes 
                   WHERE usuari = ?
                   ORDER BY data_hora DESC 
                   LIMIT 1";
    $stmt_alerta = $conexion->prepare($sql_alerta);
    $stmt_alerta->execute([$dispositiu]);
    $ultima_alerta = $stmt_alerta->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Panel - Guardian Moon</title>
    <link rel="stylesheet" href="estil/estil.css">
    <!-- Estilos de Leaflet para el minimapa -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        /* Estilos adicionales para el mi_panel */
        .container {
            max-width: 960px;
            margin: 100px auto 20px;
            padding: 0 20px;
        }
        .section {
            margin: 20px 0;
            padding: 20px;
            border: 1px solid #444;
            border-radius: 8px;
            background: rgba(0, 0, 0, 0.5);
            color: #fff;
        }
        h2 {
            margin-bottom: 15px;
            color: var(--accent);
        }
        .modificar-btn {
            background: #28a745;
            color: #000;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 10px;
        }
        .modificar-btn:hover {
            background: #34c759;
        }
        #updateForm {
            display: none;
            margin-top: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        /* Indicador de batería */
        .battery-indicator {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            background: rgba(0, 242, 255, 0.1);
            border-radius: 8px;
            border: 1px solid #00f;
        }
        .battery-bar {
            width: 60px;
            height: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            overflow: hidden;
            border: 1px solid #ccc;
        }
        .battery-fill {
            height: 100%;
            background: linear-gradient(90deg, #00ff00, #ffff00, #ff4444);
            transition: width 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            color: #000;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <nav>
        <a href="index.html" class="logo">GUARDIAN MOON</a>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="compte.php">Perfil</a></li>
            <li><a href="api/logout.php" style="color: #ff4444;">Salir</a></li>
        </ul>
    </nav>
    
    <div class="container">
        <!-- Sección de Datos Personales -->
        <div class="section" id="datosPersonales">
            <h2>Mis Datos Personales</h2>
            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['nombre']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
            <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($usuario['telefono']); ?></p>
            <p><strong>Dirección:</strong> <?php echo htmlspecialchars($usuario['direccion']); ?></p>
            <button class="modificar-btn" onclick="toggleUpdateForm()">Modificar Datos</button>
            <!-- Formulario para actualizar datos personales -->
            <form id="updateForm" method="POST" action="api/actualizar_datos.php">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="text" name="telefono" id="telefono" value="<?php echo htmlspecialchars($usuario['telefono']); ?>">
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección:</label>
                    <textarea name="direccion" id="direccion"><?php echo htmlspecialchars($usuario['direccion']); ?></textarea>
                </div>
                <button type="submit" class="modificar-btn">Guardar Cambios</button>
            </form>
        </div>
        
        <!-- Sección de Estado de Batería en Tiempo Real -->
        <div class="section" id="estadoBateria">
            <h2>Estado de la Batería</h2>
            <div class="battery-indicator">
                <span>Batería:</span>
                <div class="battery-bar">
                    <div id="batteryFill" class="battery-fill" style="width: <?php echo ($ultima_alerta) ? (int)$ultima_alerta['bateria'] : 0; ?>%;">
                        <?php echo ($ultima_alerta) ? (int)$ultima_alerta['bateria'] . "%" : "0%"; ?>
                    </div>
                </div>
            </div>
            <?php if($ultima_alerta): ?>
                <p>Última actualización: <?php echo date("d/m/Y H:i", strtotime($ultima_alerta['data_hora'])); ?></p>
            <?php else: ?>
                <p>No hay datos de batería disponibles.</p>
            <?php endif; ?>
        </div>
        
        <!-- Sección de Última Conexión/Dispositivo con Minimapa -->
        <div class="section" id="ultimaConexion">
            <h2>Última Conexión/Dispositivo</h2>
            <?php if($ultima_alerta && !empty($ultima_alerta['coordenades'])): 
                $coords = explode(',', $ultima_alerta['coordenades']);
                $lat = isset($coords[0]) ? trim($coords[0]) : 0;
                $lon = isset($coords[1]) ? trim($coords[1]) : 0;
            ?>
                <p>Fecha: <?php echo date("d/m/Y H:i", strtotime($ultima_alerta['data_hora'])); ?></p>
                <div id="minimap" style="width: 100%; height: 200px;"></div>
            <?php else: ?>
                <p>No hay datos de última conexión disponibles.</p>
            <?php endif; ?>
        </div>
    </div>
    <!-- Incluir Leaflet JS para el minimapa -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Función para alternar la visibilidad del formulario de actualización de datos
        function toggleUpdateForm(){
            var form = document.getElementById('updateForm');
            if(form.style.display === "none" || form.style.display === ""){
                form.style.display = "block";
            } else {
                form.style.display = "none";
            }
        }
        // Función para actualizar el estado de la batería en tiempo real
        function actualizarBateria(){
            fetch('api/obtener_bateria.php')
            .then(response => response.json())
            .then(data => {
                if(data.bateria !== undefined){
                    const batteryFill = document.getElementById('batteryFill');
                    batteryFill.style.width = data.bateria + '%';
                    batteryFill.textContent = data.bateria + '%';
                    // Se pueden agregar clases para cambiar el color según el nivel de carga
                }
            })
            .catch(error => console.error('Error al obtener la batería:', error));
        }
        // Actualizar la batería cada 30 segundos
        setInterval(actualizarBateria, 30000);
        // Llamada inicial
        actualizarBateria();
        // Inicializar el minimapa si existen coordenadas de la última conexión
        <?php if($ultima_alerta && !empty($ultima_alerta['coordenades'])): ?>
            var map = L.map('minimap').setView([<?php echo $lat; ?>, <?php echo $lon; ?>], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
            L.marker([<?php echo $lat; ?>, <?php echo $lon; ?>]).addTo(map)
                .bindPopup('Última conexión')
                .openPopup();
        <?php endif; ?>
    </script>
</body>
</html>


















































































































































