<?php

try {
    require_once("./data/dbAccess.php");
    $conn = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $pw);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $survey_id = $_POST['survey_id'];
        $new_state = $_POST['new_state'];
    
        // Actualizar el estado en la base de datos
        $update_query = "UPDATE Survey SET state = :new_state WHERE survey_id = :survey_id";
        $update_statement = $conn->prepare($update_query);
        $update_statement->bindParam(':new_state', $new_state);
        $update_statement->bindParam(':survey_id', $survey_id);
        $update_statement->execute();



        
    
        // Redirigir de vuelta a donde estabas
        header("Location: {$_SERVER['HTTP_REFERER']}");
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
    $informacionError = "Error: No se pudo conectar a la base de datos. Hora del error: " . $dataSinCambiar . ". Error realizado por: " . $user_id . ".\n";
    file_put_contents($nombreArchivo, $informacionError, FILE_APPEND);
    echo "Error: " . $e->getMessage();
} finally {
    $conn = null;
}
?>