<?php
if (isset($_GET["token"])) {
    require_once("./data/dbAccess.php");
    try {
        $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", "$username", "$pw");
    } catch (PDOException $e) {

        echo "Failed to get DB handle: " . $e->getMessage() . "\n";
        exit;
    }
    $query = $pdo->prepare("SELECT * FROM InvitedUser WHERE token = ?");
    $query->bindParam(1, $_GET["token"], PDO::PARAM_STR);
    $query->execute();
    
    // compruebo errores
    $e = $query->errorInfo();
    if ($e[0] != '00000') {
        echo "\nPDO::errorInfo():\n";
        die("Error accedint a dades: " . $e[2]);
    }

    $row = $query->fetch();
    if (!$row) {
        include("./error404.php");
        exit;
    } else {
        // consulta para mostrar el contenido y respuestas de x encuesta. OJO se muestran muchas veces el title y el question
        // SELECT Survey.title, Question.questionText, Answer.answer_text FROM Survey INNER JOIN Question ON Survey.survey_id = 2 AND Question.survey_id = 2 INNER JOIN Answer ON Question.question_id = Answer.question_id;
        $queryInner = $pdo->prepare("
        SELECT Survey.title, Question.questionText, Answer.answer_text FROM Survey 
        INNER JOIN Question ON Survey.survey_id = ? AND Question.survey_id = ?
        INNER JOIN Answer ON Question.question_id = Answer.question_id
        ");
        $queryInner->bindParam(1, $row["survey_id"], PDO::PARAM_INT);
        $queryInner->bindParam(2, $row["survey_id"], PDO::PARAM_INT);
        $queryInner->execute();
        
        // compruebo errores
        $e = $queryInner->errorInfo();
        if ($e[0] != '00000') {
            echo "\nPDO::errorInfo():\n";
            die("Error accedint a dades: " . $e[2]);
        }

        // muestro el formulario que debo mostrar
        echo "<!DOCTYPE html>";
        echo "<html lang='es'>";
        echo "<head>";
        echo "    <meta charset='UTF-8'>";
        echo "    <meta name='viewport' content='width=device-width, initial-scale=1.0'>";
        echo "    <link rel='icon' href='./assets/images/dos.png' type='image/png'>";
        echo "    <link rel='stylesheet' href='./assets/styles/styles.css'>";
        echo "    <script src='https://code.jquery.com/jquery-3.6.4.min.js'></script>";
        echo "    <script src='./assets/scripts/notifications.js'></script>";
        echo "    <script src='./assets/scripts/anonymous_vote.js'></script>";
        echo "    <title>Votar como Invitado</title>";
        echo "</head>";
        echo "<body>";
        include("./templates/header.php");
        // meto los datos necesarios. Piensa en hacer un fetch a solas para el titulo y la primera opcion y luego el while
        $innerRow = $queryInner->fetch();
        echo "    <h1>Título de Encuesta: ".$innerRow["title"]."</h1>";
        echo "    <h2>Pregunta: ".$innerRow["questionText"]."</h2>";
        echo "    <form action='anonymous_vote.php' method='post'>";
        echo "          <label>".$innerRow["answer_text"]."</label>";
        echo "          <input type='radio' name='opcion' value='".$innerRow["answer_text"]."'><br>";

        while ($innerRow = $queryInner->fetch(PDO::FETCH_ASSOC)) {
            echo "      <label>".$innerRow["answer_text"]."</label>";
            echo "      <input type='radio' name='opcion' value='".$innerRow["answer_text"]."'><br>";
        }
        echo "          <input type='submit' value='Guardar Voto'>";
        echo "    </form>";
        include("./templates/header.php");
        echo "</body>";
        echo "</html>";
    }
} 
elseif (isset($_POST["opcion"])) {
    echo "<h1>Has seleccionado la opcion: ".$_POST["opcion"]."</h1>";
} else {
    include("./error404.php");
    exit;
}
?>