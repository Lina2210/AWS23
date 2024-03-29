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
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="./assets/images/dos.png" type="image/png">
        <link rel="stylesheet" href="./assets/styles/styles.css?no-cache=<?php echo time(); ?>">
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="./assets/scripts/notifications.js"></script>
        <script src="./assets/scripts/login.js"></script>
        <title>Panel de control  —  ENCUESTA2</title>
    </head>

    <body class="dashboard-body">
        <?php include("./templates/header.php"); ?>
        
        <h1 class="title">PANEL DE CONTROL</h1>
        
        <nav class="dashboard-nav">
            <a href="create_poll.php">CREAR ENCUESTA</a>
            <a href="list_votes.php">VER VOTACIONES</a>
            <a href="spec31_change_pass.php">CAMBIAR CONTRASEÑA</a>
            <a href="list_polls.php">VER ENCUESTAS</a>
        </nav>
        
        <ul id="notification-container"></ul>
        
        <?php include("./templates/footer.php"); ?>
    </body>
</html>