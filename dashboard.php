<?php
    session_start();
    if (!isset($_SESSION["user_name"])) {
        include("./error403.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="robots" content="noindex, nofollow">
        <meta name="keywords" content="encuesta2, votación en línea, votación, encuestas, elecciones, privacidad, seguridad">
        <meta name="description" content="Plataforma de votación en línea comprometida con la privacidad y seguridad de los usuarios. Regístrate ahora y participa en encuestas y elecciones de manera segura.">
        <meta property="og:title" content="encuesta2">
        <meta property="og:description" content="Plataforma de votación en línea comprometida con la privacidad y seguridad de los usuarios. Regístrate ahora y participa en encuestas y elecciones de manera segura.">
        <meta property="og:image" content="assets/images/logo2.png">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="author" content="Isaac Furió, Lina Ramírez, Eric Escrich i Claudia Moyano">
        <link rel="icon" href="./assets/images/dos.png" type="image/png">
        <link rel="stylesheet" href="./assets/styles/styles.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
        <script src="./assets/scripts/notifications.js"></script>
        <script src="./assets/scripts/dashboard.js"></script>
        <title>Panel de control  —  ENCUESTA2</title>
    </head>

    <body class="dashboard-body">
        <?php include("./templates/header.php"); ?>
        
        <h1 class="title">PANEL DE CONTROL</h1>
        
        <nav class="dashboard-nav">
            <a href="create_poll.php">CREAR ENCUESTA</a>
            <a href="list_polls.php">VER ENCUESTAS</a>
        </nav>
        
        <ul id="notification-container"></ul>
        
        <?php include("./templates/footer.php"); ?>
    </body>
</html>