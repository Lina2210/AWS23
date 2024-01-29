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
    <link rel="stylesheet" href="./assets/styles/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="./assets/scripts/notifications.js"></script>
    <script src="./assets/scripts/list_polls.js"></script>
    <title>List polls</title>

<body class="listPolls-body">
    <?php
    include("./templates/header.php");
    ?>
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
                    echo '<tr><th class="name-column">Name</th><th>Estado de Publicación</th><th>Estado de Bloqueo</th></tr>';

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
                echo "  <script>
                            localStorage.setItem('error', 'No se encontró el usuario en la base de datos.');
                            window.location.href = 'login.php';
                        </script>";
                exit;
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        } finally {
            $conn = null;
        }
        ?>
        <a href="dashboard.php">Atras</a>

    </main>
    <ul id="notification-container"></ul>
    <?php
    include("./templates/footer.php");
    ?>

</body>

</html>