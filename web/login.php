<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Login | Guardian Moon</title>
    <link rel="stylesheet" href="estil/estil.css">
</head>
<body>
    <nav>
        <a href="index.html" class="logo">GUARDIAN MOON</a>
        <ul>
            <li><a href="index.html">Inici</a></li>
            <li><a href="https://www.guardianmoon.cat/shop">Botiga</a></li>
            <li><a href="login.php" class="active">Login</a></li>
            <li><a href="registre.php">Registre</a></li>
            <li><a href="dashboard.php">Dashboard</a></li>
        </ul>
    </nav>
    <div class="container" style="padding-top: 100px;">
    <div class="card" style="max-width: 400px; margin: 80px auto;">
        <h2 style="text-align:center; margin-bottom: 30px;">Benvingut/da</h2>
        <form action="api/processar_login.php" method="POST">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="El teu correu" required>
            
            <label for="password">Contrasenya</label>
            <input type="password" id="password" name="password" placeholder="La teva clau" required>
            
            <button type="submit" class="cta-button">Entrar al meu panell</button>
        </form>
        <p style="text-align:center; margin-top:20px;">
            Ets nou? <a href="registre.php">Crea un compte</a>
        </p>
    </div>
</div>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
</body>
</html>
