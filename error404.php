<?php
    http_response_code(404);
?><!DOCTYPE html>
<html lang="es">
    <head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="./assets/images/dos.png" type="image/png">
        <link rel="stylesheet" href="./assets/styles/styles.css?no-cache=<?php echo time(); ?>">
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="./assets/scripts/notifications.js"></script>
        <script src="./assets/scripts/login.js"></script>
        <title>Error 404  —  ENCUESTA2</title>
    </head>
    <body class="body-error">
        <?php
            include("./templates/header.php");
        ?>
        <h1 class="title">Error 404. Página no encontrada</h1>
        <p class="number">404</p>
        <?php
            include("./templates/footer.php");
        ?>
    </body>
</html>