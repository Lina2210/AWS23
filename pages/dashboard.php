<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/assets/images/dos.png" type="image/png">
    <link rel="stylesheet" href="/assets/styles/styles.css">
    <title>Panel De Control</title>
</head>
<body>
    <?php
        session_start();
        echo "<h1>Bienvenido ".$_SESSION['user_name'].", has entrado en el panel de control (dashboard)</h1>";
    ?>
    
</body>
</html>
