<?php
session_start();
if (!isset($_SESSION["mail"])) {
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
    <link rel="stylesheet" href="./assets/styles/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="./assets/scripts/notifications.js"></script>
    <script src="./assets/scripts/list_votes.js"></script>
    <title>Mis Votos</title>
</head>
<body>
    <?php
        include("./templates/header.php");
        // consulta mysql pdo para mostrar los votos y si no hay votos que de un warning de que este usuario no tiene votos
        include("./templates/footer.php");
    ?>
</body>
</html>