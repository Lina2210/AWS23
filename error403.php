<?php
    http_response_code(403);
    header('HTTP/1.0 403 Forbidden');
?><!DOCTYPE html>
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
        <link rel="icon" href="/assets/images/dos.png" type="image/png">
        <link rel="stylesheet" href="/assets/styles/styles.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
        <title>Error 403  —  ENCUESTA2</title>
    </head>
    <body class="body-error">
        <?php include("./templates/header.php"); ?>
        <h1 class="title">Error 403. Permiso denegado</h1>
        <p class="number">403</p>
        <?php include("./templates/footer.php"); ?>
    </body>
</html>