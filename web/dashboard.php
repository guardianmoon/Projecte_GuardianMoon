<?php
//solo entran usuarios registrados
session_start();
if (!isset($_SESSION['usuari_nom'])) {
    header("Location: login.php");
    exit();
}
// Conectar a la base de datos
require_once 'conexion.php';
// MODIFICADO: Filtrar alertas por dispositivo del usuario en sesión
$dispositiu_usuario = $_SESSION['dispositiu'] ?? null;
if ($dispositiu_usuario) {
    // Obtener solo las alertas del dispositivo del usuario actual
    $stmt = $conexion->prepare("SELECT * FROM alertes WHERE usuari = ? ORDER BY data_hora DESC");
    $stmt->execute([$dispositiu_usuario]);
    $alertes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Si no hay dispositivo en sesión, mostrar tabla vacía (caso de error)
    $alertes = [];
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Guardian Moon</title>
    <link rel="stylesheet" href="estil/estil.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map { height: 450px; width: 100%; border-radius: 20px; border: 1px solid var(--accent); margin: 20px 0; z-index: 1; }
        .alert-row { animation: flash 2s infinite; color: #ff4444; font-weight: bold; background: rgba(255, 0, 0, 0.1) !important; }
        @keyframes flash { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }
        .no-data { text-align: center; padding: 20px; color: var(--text-dim); }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #333; }
        
        /* Estilos para la sección de dispositivo */
        .device-info {
            background: rgba(0, 242, 255, 0.1);
            border: 1px solid var(--accent);
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .device-name {
            color: var(--accent);
            font-weight: 700;
            font-size: 1.1rem;
        }
        .change-device-btn {
            background: #444;
            color: var(--accent);
            border: 1px solid var(--accent);
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .change-device-btn:hover {
            background: var(--accent);
            color: #000;
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
        }
        .modal-content {
            background: var(--card-bg);
            margin: 10% auto;
            padding: 30px;
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            width: 90%;
            max-width: 400px;
        }
        .modal-content h3 {
            color: var(--accent);
            margin-bottom: 20px;
        }
        .modal-content input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            border-radius: 8px;
            padding: 10px;
            color: var(--text-main);
            width: 100%;
            margin-bottom: 15px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .modal-content input:focus {
            outline: none;
            border-color: var(--accent);
            background: rgba(0, 242, 255, 0.1);
        }
        .modal-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        .modal-buttons button {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600;
        }
        .btn-guardar {
            background: var(--accent);
            color: #000;
        }
        .btn-cancelar {
            background: #444;
            color: var(--text-main);
        }
        /* ===== ESTILOS DEL RELOJ RETRO AZUL ===== */
        .clock-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, rgba(0, 50, 100, 0.3), rgba(0, 100, 150, 0.2));
            border-radius: 20px;
            border: 2px solid var(--accent);
            box-shadow: 0 0 30px rgba(0, 242, 255, 0.4), inset 0 0 20px rgba(0, 242, 255, 0.1);
        }
        .digital-clock {
            font-family: 'Courier New', monospace;
            font-size: 4rem;
            font-weight: 900;
            color: #00f2ff;
            text-shadow: 
                0 0 10px #00f2ff,
                0 0 20px #0099ff,
                0 0 30px #0066ff,
                0 0 40px #0033ff,
                0 0 50px rgba(0, 242, 255, 0.8);
            letter-spacing: 0.1em;
            animation: clockGlow 2s ease-in-out infinite;
            padding: 20px 40px;
            background: rgba(0, 20, 40, 0.6);
            border-radius: 15px;
            border: 2px solid rgba(0, 242, 255, 0.5);
            min-width: 350px;
            text-align: center;
        }
        @keyframes clockGlow {
            0%, 100% {
                text-shadow: 
                    0 0 10px #00f2ff,
                    0 0 20px #0099ff,
                    0 0 30px #0066ff,
                    0 0 40px #0033ff,
                    0 0 50px rgba(0, 242, 255, 0.8);
            }
            50% {
                text-shadow: 
                    0 0 15px #00f2ff,
                    0 0 25px #0099ff,
                    0 0 35px #0066ff,
                    0 0 45px #0033ff,
                    0 0 60px rgba(0, 242, 255, 1);
            }
        }
        .clock-label {
            color: var(--accent);
            font-size: 0.9rem;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
        }
        /* Responsive */
        @media (max-width: 768px) {
            .digital-clock {
                font-size: 2.5rem;
                min-width: 250px;
                padding: 15px 25px;
            }
            .device-info {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            .change-device-btn {
                width: 100%;
            }
            table {
                font-size: 0.85rem;
            }
            th, td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <nav>
        <a href="index.html" class="logo">GUARDIAN MOON</a>
        <ul>
            
            <li><a href="dashboard.php" class="active">Monitoratge</a></li>
            <li><a href="compte.php">Perfil</a></li>
            <li><a href="mi_panel.php" class="active">El meu Panell</a></li>
            <li><a href="api/logout.php" style="color: #ff4444;">Sortir</a></li>
        </ul>
    </nav>
    <div class="container">
        <!-- NUEVO: Reloj Retro Azul en Vivo -->
        <div class="clock-container">
            <div>
                <div class="digital-clock" id="liveClockDisplay">00:00:00</div>
                <div class="clock-label">⏰ Hora en Viu</div>
            </div>
        </div>
        <!-- NUEVO: Sección de dispositivo vinculado -->
        <div class="device-info">
            <div>
                <p style="color: var(--text-dim); margin: 0 0 5px 0; font-size: 0.9rem;">Dispositiu Vinculat:</p>
                <p class="device-name">
                    <i class="fas fa-mobile-alt"></i> <?php echo htmlspecialchars($_SESSION['dispositiu']); ?>
                </p>
            </div>
            <button class="change-device-btn" onclick="abrirModal()">⚙️ Canviar Dispositiu</button>
        </div>
        <div class="card">
            <h2>📍 Geolocalització en Temps Real</h2>
            <div id="map"></div>
        </div>
        <div class="card">
            <h3>📋 Registre d'Alertes Recents</h3>
            <table>
                <thead>
                    <tr>
                        <th>Data/Hora</th>
                        <th>Usuari</th>
                        <th>Coordenades</th>
                        <th>Bateria</th>
                    </tr>
                </thead>
                <tbody id="taula-alertes">
                    <?php if (count($alertes) > 0): ?>
                        <?php foreach ($alertes as $index => $alerta): ?>
                            <tr class="<?php echo $index === 0 ? 'alert-row' : ''; ?>">
                                <td><?php echo htmlspecialchars($alerta['data_hora']); ?></td>
                                <td><?php echo htmlspecialchars($alerta['usuari']); ?></td>
                                <td><?php echo htmlspecialchars($alerta['coordenades']); ?></td>
                                <td><?php echo htmlspecialchars($alerta['bateria']); ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="no-data">No hi ha alertes registrades per a aquest dispositiu.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- NUEVO: Modal para cambiar dispositivo -->
    <div id="modalCambiar" class="modal">
        <div class="modal-content">
            <h3>Canviar Nom del Dispositiu</h3>
            <form id="formCambiarDispositiu" onsubmit="guardarDispositiu(event)">
                <label for="nuevoDispositiu" style="color: var(--text-dim); font-size: 0.9rem;">Nou nom del dispositiu:</label>
                <input 
                    type="text" 
                    id="nuevoDispositiu" 
                    name="nuevoDispositiu" 
                    placeholder="Ex: iPhone_Maria, Samsung_Oussama"
                    value="<?php echo htmlspecialchars($_SESSION['dispositiu']); ?>"
                    required
                >
                <div class="modal-buttons">
                    <button type="button" class="btn-cancelar" onclick="cerrarModal()">Cancelar</button>
                    <button type="submit" class="btn-guardar">Guardar Canvi</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // ===== FUNCIONES DEL RELOJ EN VIVO =====
        function actualizarReloj() {
            const ahora = new Date();
            const horas = String(ahora.getHours()).padStart(2, '0');
            const minutos = String(ahora.getMinutes()).padStart(2, '0');
            const segundos = String(ahora.getSeconds()).padStart(2, '0');
            
            const horaFormato = `${horas}:${minutos}:${segundos}`;
            document.getElementById('liveClockDisplay').textContent = horaFormato;
        }
        // Actualizar el reloj cada 1000ms (1 segundo)
        setInterval(actualizarReloj, 1000);
        
        // Llamada inicial para evitar esperar 1 segundo
        actualizarReloj();
        // ===== FUNCIONES PARA EL MODAL =====
        function abrirModal() {
            document.getElementById('modalCambiar').style.display = 'block';
        }
        function cerrarModal() {
            document.getElementById('modalCambiar').style.display = 'none';
        }
        window.onclick = function(event) {
            const modal = document.getElementById('modalCambiar');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
        function guardarDispositiu(event) {
            event.preventDefault();
            const nuevoDispositiu = document.getElementById('nuevoDispositiu').value.trim();
            if (!nuevoDispositiu) { // CORREGIDO: ahora dice nuevoDispositiu en vez de nuevoDispositivo
                alert('El nom del dispositiu no pot estar buit');
                return;
            }
            // Enviar AJAX al servidor
            fetch('api/actualizar_dispositiu.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'nuevoDispositiu=' + encodeURIComponent(nuevoDispositiu)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'ok') {
                    alert('Dispositiu actualitzat correctament');
                    location.reload(); // Recargar la página
                } else {
                    alert('Error: ' + data.msg);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar el dispositivo');
            });
        }
        
        // ===== INICIALIZACIÓN DEL MAPA CON LEAFLET =====
        const map = L.map('map').setView([41.3851, 2.1734], 13); // Coordenadas por defecto (Barcelona)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        // ===== RECUPERAR COORDENADAS DE PHP Y DIBUJAR MARCADOR =====
        <?php if (!empty($alertes)): 
            $ultima = $alertes[0]['coordenades']; // Coge la coordenada más reciente
        ?>
            const darreraCoordenada = "<?php echo $ultima; ?>";
        <?php else: ?>
            const darreraCoordenada = null;
        <?php endif; ?>

        if (darreraCoordenada) {
            const coords = darreraCoordenada.split(',').map(c => parseFloat(c.trim()));
            
            if (coords.length === 2 && !isNaN(coords[0]) && !isNaN(coords[1])) {
                
                // Centramos el mapa en la nueva posición
                map.setView(coords, 16);

                // Icono personalizado tipo "Punto de pulso" (estilo Guardian Moon)
                const pulseMarker = L.circleMarker(coords, {
                    radius: 10,
                    fillColor: "#00f2ff", // Tu azul accent
                    color: "#00f2ff",
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.7
                }).addTo(map);

                // Segundo círculo exterior para dar efecto de "radar"
                L.circle(coords, {
                    radius: 40,
                    color: '#00f2ff',
                    fillColor: '#00f2ff',
                    fillOpacity: 0.1,
                    weight: 1
                }).addTo(map);

                pulseMarker.bindPopup(`<b>Última alerta detectada</b><br>${darreraCoordenada}`).openPopup();
            }
        }
    </script>
</body>
</html>































































































































































































































































































































































































































































































































































































































































































































