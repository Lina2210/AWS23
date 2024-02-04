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
        require_once("./data/dbAccess.php");
        try {
            $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", "$username", "$pw");
        } catch (PDOException $e) {

            echo "Failed to get DB handle: " . $e->getMessage() . "\n";
            exit;
        }
        // SELECT 
        $query = $pdo->prepare("SELECT survey_id FROM UserSurveyAccess WHERE user_id = ?");
        $query->bindParam(1, $_SESSION["user_id"], PDO::PARAM_INT);
        $query->execute();
        
        // compruebo errores
        $e = $query->errorInfo();
        if ($e[0] != '00000') {
            echo "\nPDO::errorInfo():\n";
            die("Error accedint a dades: " . $e[2]);
        }

        if ($query->rowCount() > 0) {
            
            echo "<ul>";
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                echo "<li></li>";
            }
            echo "</ul>";
        } else {
            echo "<script> $(function() {addNotification('warning', 'No se encontraron votos para este usuario.')});</script>";
        }

        include("./templates/footer.php");
    ?>
    <ul id="notification-container"></ul>
</body>
</html>