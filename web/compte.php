<?php
session_start();
require_once 'conexion.php';
// Verificar que l'usuari estigui autentificat
if (!isset($_SESSION['usuari_nom'])) { 
    header("Location: login.php"); 
    exit(); 
}
// Obtenir dades de l'usuari de la BD
$usuari_id = $_SESSION['usuari_id'];
$stmt = $conexion->prepare("SELECT nombre, email, telefono, direccion, dispositiu FROM clientes WHERE id = ?");
$stmt->execute([$usuari_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>El meu Compte | Guardian Moon</title>
    <link rel="stylesheet" href="estil/estil.css">
</head>
<body>
    <nav>
        <a href="index.html" class="logo">GUARDIAN MOON</a>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="compte.php" class="active">Perfil</a></li>
            <li><a href="api/logout.php" style="color: #ff4444;">Sortir</a></li>
        </ul>
    </nav>
    <div class="container">
        <div class="grid">
            <div class="card">
                <h3>🔐 Dades Personals</h3>
                <p style="color:var(--text-dim)">Nom: <span style="color:white"><?php echo htmlspecialchars($usuario['nombre']); ?></span></p>
                <p style="color:var(--text-dim)">Email: <span style="color:white"><?php echo htmlspecialchars($usuario['email']); ?></span></p>
                <p style="color:var(--text-dim)">Telèfon: <span style="color:white"><?php echo htmlspecialchars($usuario['telefono'] ?? 'No especificat'); ?></span></p>
                <p style="color:var(--text-dim)">Adreça: <span style="color:white"><?php echo htmlspecialchars($usuario['direccion'] ?? 'No especificada'); ?></span></p>
                <p style="color:var(--text-dim)">Estat: <span class="status-tag" style="background: #00ff00; color: #000; padding: 4px 8px; border-radius: 4px;">✓ Protecció Activa</span></p>
            </div>
            <div class="card">
                <h3>📱 Dispositiu Vinculat</h3>
                <p style="color:var(--text-dim)">ID: <span style="color:var(--accent)"><?php echo htmlspecialchars($usuario['dispositiu']); ?></span></p>
                <p style="color:var(--text-dim)">Bateria: <span style="color:var(--accent)">92%</span></p>
                <a href="dashboard.php" class="cta-button" style="width: auto; display: inline-block; margin-top: 15px;">
                    Veure Monitoratge
                </a>
            </div>
        </div>
    </div>
</body>
</html>












































