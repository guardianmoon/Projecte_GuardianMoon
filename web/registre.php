
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Registre | Guardian Moon</title>
    <link rel="stylesheet" href="estil/estil.css">
</head>
<body>
    <nav>
        <a href="index.html" class="logo">GUARDIAN MOON</a>
        <ul>
            <li><a href="index.html">Inici</a></li>
            <li><a href="https://www.guardianmoon.cat/shop">Botiga</a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="registre.php" class="active">Registre</a></li>
        </ul>
    </nav>
    <div class="container" style="padding-top: 100px;">
    <div class="card" style="max-width: 500px; margin: 50px auto;">
        <h2 style="text-align:center; margin-bottom: 30px;">Crea el teu compte</h2>
        <form action="api/processar_registre.php" method="POST">
            <label for="nombre">Nom Complet</label>
            <input type="text" id="nombre" name="nombre" placeholder="Ex: Oussama ..." required>
            
            <label for="dispositiu">Nom del teu Dispositiu</label>
            <input type="text" id="dispositiu" name="dispositiu" placeholder="Ex: iPhone_Maria, Samsung_Oussama" required>
            <small style="color: var(--text-dim); font-size: 0.85rem;">
                ⓘ Aquest nom ha de coincidir exactament amb el que envia la teva aplicació mòbil.
            </small>
            
            <label for="email">Correu Electrònic</label>
            <input type="email" id="email" name="email" placeholder="correu@exemple.com" required>
            
            <label for="password">Contrasenya</label>
            <input type="password" id="password" name="password" placeholder="********" required>
            
            <label for="telefono">Telèfon</label>
            <input type="text" id="telefono" name="telefono" placeholder="600000000">
            
            <label for="direccion">Adreça</label>
            <textarea id="direccion" name="direccion" placeholder="Carrer, Número, Ciutat..."></textarea>
            
            <button type="submit" class="cta-button">Registrar-me</button>
        </form>
        <p style="text-align:center; margin-top:20px;">
            Ja tens compte? <a href="login.php">Inicia sessió</a>
        </p>
    </div>
</div>
</body>
</html>





















