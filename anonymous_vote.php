<?php
if (isset($_GET["token"])) {

    /* 
    CREATE TABLE `InvitedUser` (
        `email` varchar(255) NOT NULL,
        `token` varchar(255) NOT NULL,
        `survey_id` int NOT NULL
    );
    */

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
    $e = $deleteQuery->errorInfo();
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
        echo "    <form action='anonymous_vote.php' method='post'>";
        echo "        <input>";
        echo "    </form>";
        include("./templates/header.php");
        echo "</body>";
        echo "</html>";
    }

} else {
    include("./error404.php");
    exit;
}
?>