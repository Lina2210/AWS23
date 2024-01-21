<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/assets/images/dos.png" type="image/png">
    <link rel="stylesheet" href="/assets/styles/styles.css">
    <title>Iniciar Sesión</title>
</head>
<body class="login-body">
    <header>
        <img class="logo" src="/assets/images/logo2.png" alt="encuesta2">
        <nav>
            <a class="buttonLeft" href="login.php">INICIAR SESIÓN</a>
            <a class="buttonRight" href="register.php">REGISTRARSE</a>
        </nav> 
    </header>
    <h1>INICIAR SESIÓN</h1>
    <main class="main-content">   
        <form action="" method="post">
            <label for="email">E-Mail</label>
            <input class="field" type="email" name="email">
            <label for="password">Contraseña</label>
            <input class="field" type="password" name="password">
            <input type="submit" value="ENTRAR">
        </form>
    </main>
</body>
</html>