<?php
http_response_code(403);
header('HTTP/1.0 403 Forbidden');
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/assets/images/dos.png" type="image/png">
    <link rel="stylesheet" href="/assets/styles/styles.css">
    <title>Error 403</title>
</head>
<body class="body-error">
    <?php
        include("./templates/header.php");
    ?>
    <h1 class="title">Error 403. Permiso denegado</h1>
    <p class="number">403</p>
    <?php
        include("./templates/footer.php");
    ?>
</body>
</html>