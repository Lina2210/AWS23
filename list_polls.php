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
        <link rel="stylesheet" href="./assets/styles/styles.css?no-cache=<?php echo time(); ?>">
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="./assets/scripts/notifications.js"></script>
        <script src="./assets/scripts/login.js"></script>
        <title>Encuestas — ENCUESTA2</title>
    </head>

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
                            echo '<tr><th class="name-column">Name</th><th>Estado de publicación</th><th>Estado de bloqueo</th><th>Acciones</th></tr>';
                            
                            foreach ($survey_result as $row) {
                                echo "<tr>";
                                echo "<td>" . $row["title"] . "</td>";
                                echo "<td>" . $row["state"] . "</td>";
                                echo "<td>";
                                echo "<form method='POST' action='update_state.php'>";
                                echo "<input type='hidden' name='survey_id' value='".$row["survey_id"]."'>";
                                echo "<select class='aa23' name='new_state'>";
                                if ($row["state"] == "bloqueado") {
                                    echo "<option value='bloqueado' selected>Bloqueada</option>";
                                    echo "<option value='desbloqueado'>Desbloqueada</option>";
                                } else {
                                    echo "<option value='bloqueado'>Bloqueada</option>";
                                    echo "<option value='desbloqueado' selected>Desbloqueada</option>";
                                }
                                echo "</select>";
                                echo "<input type='submit' value='Actualizar'>";
                                echo "</form>";
                                echo "</td>";
                                echo "<td>";

                                echo "<form method='POST' action='/survey_invitation.php'>";
                                echo "<input type='hidden' name='survey_id' value='".$row["survey_id"]."'>";
                                echo "<input type='hidden' name='title' value='".$row["title"]."'>";
                                echo "<input id='invitarButton' type='submit' value='Invitar'>";
                                echo "</form>";
                                
                                echo "<form method='POST' action='/detalles.php'>";
                                echo "<input type='hidden' name='survey_id' value='".$row["survey_id"]."'>";
                                echo "<input type='hidden' name='title' value='".$row["title"]."'>";
                                echo "<input id='detallesButton' type='submit' value='Detalles'>";
                                echo "</form>";

                                echo "</td>";
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
        <?php include("./templates/footer.php"); ?>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Obtener todos los botones "Detalles" con su respectivo formulario
                var detallesForms = document.querySelectorAll('form[action="detalles.php"]');
                
                // Iterar sobre cada formulario
                detallesForms.forEach(function(form) {
                    // Agregar un evento de escucha para el envío del formulario
                    form.addEventListener('submit', function(event) {
                        // Obtener el ID de la encuesta del formulario
                        var surveyId = form.querySelector('input[name="survey_id"]').value;
                        
                        // Redirigir a detalles.php con el ID de la encuesta en la URL
                        window.location.href = 'detalles.php?id=' + surveyId;
                        
                        // Prevenir el comportamiento predeterminado del formulario (envío)
                        event.preventDefault();
                    });
                });
            });
        </script>
    </body>
</html>
