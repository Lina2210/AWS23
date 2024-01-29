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
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="./assets/scripts/notifications.js"></script>
        <script src="./assets/scripts/list_polls.js"></script>
        <title>Encuestas  —  ENCUESTA2</title>

    <body class="listPolls-body">
        <?php include("./templates/header.php"); ?>
        <main class="main-content">

        <?php
        $mail = $_SESSION["mail"];

        try {
            require_once("./data/dbAccess.php");
            $conn = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $pw);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $user_id_query = "SELECT user_id FROM User WHERE mail = :mail";
            $user_id_statement = $conn->prepare($user_id_query);
            $user_id_statement->bindParam(':mail', $mail);
            $user_id_statement->execute();
            $user_id_result = $user_id_statement->fetch(PDO::FETCH_ASSOC);

            if ($user_id_result) {
                $user_id = $user_id_result["user_id"];

                $survey_query = "SELECT title, state, survey_id FROM Survey WHERE user_id = :user_id";
                $survey_statement = $conn->prepare($survey_query);
                $survey_statement->bindParam(':user_id', $user_id);
                $survey_statement->execute();
                $survey_result = $survey_statement->fetchAll(PDO::FETCH_ASSOC);

                        if ($survey_result) {
                            echo "<table>";
                            echo '<tr><th class="name-column">Name</th><th>Estado de publicación</th><th>Estado de bloqueo</th></tr>';

                    foreach ($survey_result as $row) {
                        echo "<tr>";
                        echo "<td>" . $row["title"] . "</td>";
                        echo "<td>" . $row["state"] . "</td>";
                        echo "<td>Desbloqueada</td>"; // Campo adicional con valor por defecto
                        echo "<form method='POST' action='/survey_invitation.php'>";
                        echo "  <input type='hidden' name='survey_id' value='".$row["survey_id"]."'>";
                        echo "  <input type='hidden' name='title' value='".$row["title"]."'>";
                        echo "  <input type='submit' value='Invitar'>";
                        echo "</form>";
                        echo "</tr>";
                    }

                    echo "</table>";
                } else {
                    echo "<script> $(function() {addNotification('warning', 'No se encontraron encuestas para este usuario.')});</script>";
                }
            } else {
                $date = date_create(null, timezone_open("Europe/Paris"));
                $tz = date_timezone_get($date);
                $dataSinCambiar = date("d-m-Y");
                $dataReal = str_replace(" ", "-", $dataSinCambiar);
                $carpetaArchivos = "logs/";
                if (!file_exists($carpetaArchivos)) {  mkdir($carpetaArchivos, 0777, true); }
                $nombreArchivo = $carpetaArchivos . "errorLog-" . $dataReal . ".txt";
                $informacionError = "Error: No se encontró el usuario en la base de datos. Hora del error: " . $dataSinCambiar . ". Error realizado por: " . $user_id . ".\n";
                file_put_contents($nombreArchivo, $informacionError, FILE_APPEND);
                echo "  <script>
                            localStorage.setItem('error', 'No se encontró el usuario en la base de datos.');
                            window.location.href = 'login.php';
                        </script>";
                exit;
            }
        } catch (PDOException $e) {
            $date = date_create(null, timezone_open("Europe/Paris"));
            $tz = date_timezone_get($date);
            $dataSinCambiar = date("d-m-Y");
            $dataReal = str_replace(" ", "-", $dataSinCambiar);
            $carpetaArchivos = "logs/";
            if (!file_exists($carpetaArchivos)) {  mkdir($carpetaArchivos, 0777, true); }
            $nombreArchivo = $carpetaArchivos . "errorLog-" . $dataReal . ".txt";
            $informacionError = "Error: No sé qué es. Hora del error: " . $dataSinCambiar . ". Error realizado por: " . $user_id . ".\n";
            file_put_contents($nombreArchivo, $informacionError, FILE_APPEND);
            echo "Error: " . $e->getMessage();
        } finally {
            $conn = null;
        }
        ?>
        <a href="dashboard.php">Atrás</a>

    </main>
    <ul id="notification-container"></ul>
    <?php
    include("./templates/footer.php");
    ?>

</body>

</html>