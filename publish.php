<?php
    session_start();
    if (!isset($_SESSION["mail"])) {
        include("./error403.php");
        exit;
    }

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Document</title>
    <style>

        div{
            display: flex;
            flex-direction: row;
            
            align-items: center;
        }
        p, i{
            margin-left: 10px;
            margin-right: 10px;
        }

    </style>
</head>
<body onload="submitForm()">

    <?php
        $pSurvey = ""; 
        $pResult = "";
        $id_encuesta = $_POST['survey_id'];
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
                $informacionError = "Error: No se pudo conectar a la base de datos. Hora del error: " . $dataSinCambiar . ". Error realizado por: " . $email . ".\n";
                file_put_contents($nombreArchivo, $informacionError, FILE_APPEND);
                echo "Failed to get DB handle: " . $e->getMessage() . "\n";
                exit;
            }

            if ($_SERVER["REQUEST_METHOD"] == "POST"){ 
                $query = $pdo->prepare("SELECT publication_survey, publication_results from Survey where survey_id = :id_encuesta");
                $query->bindParam('_id_encuesta', $id_encuesta, PDO::PARAM_STR);
                $query->execute();
                $queryResult = $query->fetchAll(PDO::FETCH_ASSOC);

                
                foreach ($queryResult as $dato):
                    $pSurvey = $dato['publication_survey'];
                    $pResult = $dato['publication_results'];
                    
                endforeach;
            }
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST['opcionesEncuesta'])) {
                    $estadoEncuesta = $_POST['opcionesEncuesta'];
                    $queryInsertpEncuesta = $pdo->prepare("UPDATE Survey SET publication_survey = ? WHERE survey_id = :id_encuesta");
                    $queryInsertpEncuesta->bindParam(1, $estadoEncuesta, PDO::PARAM_STR);
                    $queryInsertpEncuesta->bindParam(2, $id_encuesta, PDO::PARAM_INT);
                    $queryInsertpEncuesta->execute();
                }
            
            
                if (isset($_POST['opcionesResultado'])) {
                    $estadoResultado = $_POST['opcionesResultado'];
                    $queryInsertpResultado = $pdo->prepare("UPDATE Survey SET publication_results = ? WHERE survey_id = :id_encuesta");
                    $queryInsertpResultado->bindParam(1, $estadoResultado, PDO::PARAM_STR);
                    $queryInsertpResultado->bindParam(2, $id_encuesta, PDO::PARAM_INT);
                    $queryInsertpResultado->execute();
                }
            }
        }
    ?> 
        <div>
            <h3>Estado de publicacion de la encuesta: </h3>
            
            <?php if ($pSurvey === 'oculto'): ?>
                <p>Oculto</p>
                <i class="fas fa-eye-slash" style="color: #63E6BE;"></i>
            <?php elseif ($pSurvey === 'publico'): ?>
                <p>Público</p>
                <i class="fas fa-users" style="color: #63E6BE;"></i>
            <?php elseif ($pSurvey === 'privado'): ?>
                <p>Privado</p>
                <i class="fas fa-user" style="color: #63E6BE;"></i>
            <?php endif; ?>
        </div>
    
        <form method="post" id="formEncuesta" >
            <label for="opcionesEncuesta">Modificar el estado de publicacion de la encuesta</label>
            <select name="opcionesEncuesta" id="opcionesEncuesta">
                <option value="">Selecciona una opción</option>
                <option value="oculto">Oculto</option>
                <option value="publico">Público</option>
                <option value="privado">Privado</option>
            </select>
            <input type="submit" value="Guardar">
        </form>
   
        <div>
            <h3>Estado de publicacion de los resultados: </h3>
            <?php if ($pResult === 'oculto'): ?>
                <p>Oculto</p>
                <i class="fas fa-eye-slash" style="color: #63E6BE;"></i>
            <?php elseif ($pResult === 'publico'): ?>
                <p>Público</p>
                <i class="fas fa-users" style="color: #63E6BE;"></i>
            <?php elseif ($pResult === 'privado'): ?>
                <p>Privado</p>
                <i class="fas fa-user" style="color: #63E6BE;"></i>
            <?php endif; ?>
        </div>
    

        <form method="post" id="formResultado">
            <label for="opcionesResultado">Modificar el estado de publicacion de los resultados</label>
            <select name="opcionesResultado" id="opcionesResultado">
                <option value="">Selecciona una opción</option>
                <option value="oculto">Oculto</option>
                <option value="publico">Público</option>
                <option value="privado">Privado</option>
            </select>
            <input type="submit" value="Guardar">
        </form>

</body>
</html>