<?php
// INVITACIONS: enviament d'invitacions per llista d'emails. Els destinataris reben un link per a votar en una enquesta concreta. 
// Els emails s'envien en diferit en un procés del CRON, en paquets de 5 emails cada 5 min per no ser detectats com a Spam.
if (!isset($_POST["survey_id"])) {
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
    <title>Invitar a participantes</title>
</head>
<body>
    <?php include("./templates/header.php"); ?>


    <?php include("./templates/footer.php"); ?>
</body>
</html>