<?php
    session_start();
    if (!isset($_SESSION["mail"])) {
        include("./error403.php");
        exit;
    }

// Obtener el user_id a partir del user_name (correo electrónico)
$mail = $_SESSION["mail"];
file_put_contents('user_id_result.txt', print_r($mail, true));
try {
    require_once("./data/dbAccess.php");
    $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $pw);
} catch (PDOException $e) {
    echo "Failed to get DB handle: " . $e->getMessage() . "\n";
    exit;
}

    $user_id_query = $pdo->prepare("SELECT user_id FROM User WHERE mail = :mail");
    $user_id_query->bindParam(':mail', $mail);
    $user_id_query->execute();
    $user_id_result = $user_id_query->fetch(PDO::FETCH_ASSOC);

    if (!$user_id_result) {
        // Si no se encuentra el usuario, manejamos el error o redirigimos a una página de error.
        echo "  <script>
                    localStorage.setItem('error', 'No se encontró el usuario en la base de datos.');
                    window.location.href = 'login.php';
                </script>";
        exit;
    }
    $user_id = $user_id_result["user_id"];
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
        <script src="./assets/scripts/dashboard.js"></script>
        <title>Create Poll  —  ENCUESTA2</title>
    </head>

<body class="createPoll-body">
    <?php
    include("./templates/header.php");
    ?>
    <main class="main-content">
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            try {
                require_once("./data/dbAccess.php");
                $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", "$username", "$pw");
            } catch (PDOException $e) {
                $date = date_create(null, timezone_open("Europe/Paris"));
                $tz = date_timezone_get($date);
                $dataSinCambiar = date("d-m-Y");
                $dataReal = str_replace(" ", "-", $dataSinCambiar);
                $carpetaArchivos = "logs/";
                if (!file_exists($carpetaArchivos)) {  mkdir($carpetaArchivos, 0777, true); }
                $nombreArchivo = $carpetaArchivos . "errorLog-" . $dataReal . ".txt";
                $informacionError = "Error: No se pudo acceder a los datos. Hora del error: " . $dataSinCambiar . ". Error realizado por: " . $user_id . ".\n";
                file_put_contents($nombreArchivo, $informacionError, FILE_APPEND);
                echo "  <script>
                                localStorage.setItem('error', 'Failed to get DB handle: " . $e->getMessage() . ");
                                window.location.href = 'login.php';
                            </script>";
                exit;
            }
            file_put_contents('user_id_result.txt', print_r($user_id, true));
            $namePoll = $_POST["nombreEncuesta"];
            $dateStart = $_POST["fechaInicio"];
            $dateFinish = $_POST["fechaFin"];
            $questions = $_POST["pregunta"];
            $answers = $_POST["respuesta"];

            if ($namePoll && $dateStart && $dateFinish && $questions && $answers) {
                $querySurvey = $pdo->prepare("INSERT INTO Survey (title, user_id, start_date, end_date, state, creation) VALUES (?, ?, ?, ?, 'Activo', NOW())");
                $querySurvey->bindParam(1, $namePoll, PDO::PARAM_STR);
                $querySurvey->bindParam(2, $user_id, PDO::PARAM_INT);
                $querySurvey->bindParam(3, $dateStart, PDO::PARAM_STR);
                $querySurvey->bindParam(4, $dateFinish, PDO::PARAM_STR);
                $querySurvey->execute();
                $surveyId = $pdo->lastInsertId();

            foreach ($questions as $questionIndex => $question) {
                file_put_contents('debug_log.txt', "Pregunta index: $questionIndex\n", FILE_APPEND);
                $queryQuestion = $pdo->prepare("INSERT INTO Question (questionText, survey_id) VALUES (?, ?)");
                $queryQuestion->bindParam(1, $question, PDO::PARAM_STR);
                $queryQuestion->bindParam(2, $surveyId, PDO::PARAM_INT);
                $queryQuestion->execute();
                $questionId = $pdo->lastInsertId();

                if (!empty($_FILES['imagenPregunta']['name'][$questionIndex])) {
                    
                    $directorioDestino = './uploads/';
                    $fileType = $_FILES['imagenPregunta']['type'];
                    $fileName = $_FILES['imagenPregunta']['name'];
                    file_put_contents('debug_log.txt', "File name: $fileName\n", FILE_APPEND);
                    $nombreImagenUnico = generateUniqueName($fileName);

                    file_put_contents('debug_log.txt', "Nombre imagen unico: $nombreImagenUnico\n", FILE_APPEND);
                    $newFileName = $nombreImagenUnico;
                    $targetPath = $directorioDestino . $newFileName;
                    file_put_contents('debug_log.txt', "ruta: $targetPath\n", FILE_APPEND);

                    // Mover la imagen del directorio temporal al directorio de destino
                    if (move_uploaded_file($_FILES['imagenPregunta']['tmp_name'], $targetPath)) {
                        //ALERT La imagen se ha guardado exitosamente en el servidor
                        // Puedes almacenar la ruta de la imagen en la base de datos si lo deseas
                        $queryInsertImagen = $pdo->prepare("UPDATE Question SET image = ? WHERE question_id = ?");
                        $queryInsertImagen->bindParam(1, $targetPath, PDO::PARAM_STR);
                        $queryInsertImagen->bindParam(2, $questionId, PDO::PARAM_STR);
                        $queryInsertImagen->execute();

                    } else {
                        file_put_contents('debug_log.txt', "Error al mover la imagen\n", FILE_APPEND);
                        
                    }
                }
                // else{
                //     file_put_contents('debug_log.txt', "El input imagen está vacio", FILE_APPEND);
                // }

                }
                foreach ($answers as $index => $respuestaPregunta) {
                    foreach ($respuestaPregunta as $answerIndex => $answer){
                        $queryAnswer = $pdo->prepare("INSERT INTO Answer (question_id, answer_text) VALUES (?, ?)");
                        $queryAnswer->bindParam(1, $questionId, PDO::PARAM_INT);
                        $queryAnswer->bindParam(2, $answer, PDO::PARAM_STR);
                        $queryAnswer->execute();
                        $answerId = $pdo->lastInsertId();

                      
                            // Verifica si la imagen está presente y se ha cargado correctamente
                            if (!empty($_FILES['imagenRespuesta']['name'][$index][$answerIndex])) {
                                $directorioDestino = './uploads/';
                                $fileType = $_FILES['imagenRespuesta']['type'][$index];
                                $fileName = $_FILES['imagenRespuesta']['name'][$index];
                                file_put_contents('debug_log.txt', "File name: $fileName\n", FILE_APPEND);
                                $nombreImagenUnico = generateUniqueName($fileName);

                                file_put_contents('debug_log.txt', "Nombre imagen unico: $nombreImagenUnico\n", FILE_APPEND);
                                $newFileName = $nombreImagenUnico;
                                $targetPath = $directorioDestino . $newFileName;
                                file_put_contents('debug_log.txt', "ruta: $targetPath\n", FILE_APPEND);

                                if (move_uploaded_file($_FILES['imagenRespuesta']['tmp_name'][$index], $targetPath)) {
                                    $queryInsertImagen = $pdo->prepare("UPDATE Answer SET image = ? WHERE answer_id = ?");
                                    $queryInsertImagen->bindParam(1, $targetPath, PDO::PARAM_STR);
                                    $queryInsertImagen->bindParam(2, $answerId, PDO::PARAM_STR);
                                    $queryInsertImagen->execute();

                                } else {
                                    file_put_contents('debug_log.txt', "Error al mover la imagen\n", FILE_APPEND);
                                }
                            }else{
                                file_put_contents('debug_log.txt', "El input imagen está vacio", FILE_APPEND);
                            }
                        
                    }

                }
            }

            $date = date_create(null, timezone_open("Europe/Paris"));
            $tz = date_timezone_get($date);
            $dataSinCambiar = date("d-m-Y");
            $dataReal = str_replace(" ", "-", $dataSinCambiar);
            $carpetaArchivos = "logs/";
            if (!file_exists($carpetaArchivos)) {  mkdir($carpetaArchivos, 0777, true); }
            $nombreArchivo = $carpetaArchivos . "errorLog-" . $dataReal . ".txt";
            $informacionError = "Acción: " . $user_id . " ha creado una encuesta. Hora de la acción: " . $dataSinCambiar . ".\n";
            file_put_contents($nombreArchivo, $informacionError, FILE_APPEND);
            echo "  <script>
                            localStorage.setItem('success', 'Encuest creada correctamente.');
                            window.location.href = 'list_polls.php';
                        </script>";
            exit;
        } else {
            echo "  <script>addNotification('warning', 'Error: Faltan datos en el formulario.');</script>";
        }
        $uploadDirectory = 'uploads/'; // Directorio donde se guardarán las imágenes

        // Función para generar un nombre único para el archivo
        function generateUniqueName($originalName) {
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            $uniqueName = uniqid() . '.' . $extension;
            return $uniqueName;
        }
    
    ?>
    <main class="main-content">
        <form id="formulario" method="post" enctype="multipart/form-data"></form>
    </main>
    

    <ul id="notification-container"></ul>
    <?php
        include("./templates/footer.php");
    ?>
    <script src="./assets/scripts/create_poll.js"></script>
</body>

</html>

