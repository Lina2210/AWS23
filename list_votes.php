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
    <script src="https://kit.fontawesome.com/eaace330d4.js" crossorigin="anonymous"></script>
    <title>Mis Votos</title>
</head>
<body>
    <?php
        include("./templates/header.php");
        require_once("./data/dbAccess.php");
        try {
            $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", "$username", "$pw");
        } catch (PDOException $e) {

            echo "Failed to get DB handle: " . $e->getMessage() . "\n";
            exit;
        }
        if (isset($_POST["pass"])) {
            // consulta mysql pdo para mostrar los votos y si no hay votos que de un warning de que este usuario no tiene votos
            // SELECT
            // $query = $pdo->prepare("SELECT * FROM UserVote WHERE user_id = ?");
            // SELECT UserSurveyAccess.survey_id, Answer.answer_id FROM UserSurveyAccess INNER JOIN Question ON Question.survey_id = UserSurveyAccess.survey_id INNER JOIN Answer ON Question.question_id = Answer.question_id WHERE UserSurveyAccess.user_id = 2;
            // HACER AQUI EL SELECT DE LA SPEC 30 ENCRIPTACION VOTOS!!!!
            // QUIZAS DEBES PEDIR LA CONTRASEÑA DE NUEVO AL USUARIO AL ACCEDER A LIST VOTES

            /*
            CREATE TABLE `UserVote` (
                `user_id` int NOT NULL,
                `survey_id`int NOT NULL,
                `answer_id` int NOT NULL
            );
            select AES_ENCRYPT("text","mykey");
            select cast(AES_DECRYPT(0x51C4607C0A37C8B875DE31682E4E6212,"mykey") as char);
            SELECT * FROM UserVote WHERE user_id = AES_ENCRYPT(6, "Mata123_");
            */

            $query = $pdo->prepare("SELECT * FROM UserVote WHERE user_id = AES_ENCRYPT(?, ?)");
            $query->bindParam(1, $_SESSION["user_id"], PDO::PARAM_INT);
            $query->bindParam(2, $_POST["pass"], PDO::PARAM_STR);
            $query->execute();
            
            // compruebo errores
            $e = $query->errorInfo();
            if ($e[0] != '00000') {
                echo "\nPDO::errorInfo():\n";
                die("Error accedint a dades: " . $e[2]);
            }

            if ($query->rowCount() > 0) {
                // SELECT Survey.title, Question.questionText, Answer.answer_text FROM Survey INNER JOIN Question ON Survey.survey_id = 1 AND Question.survey_id = 1 INNER JOIN Answer ON Answer.answer_id = 1;
                $querySelect = $pdo->prepare("
                SELECT Survey.title, Question.questionText, Answer.answer_text FROM Survey 
                INNER JOIN Question ON Survey.survey_id = ? AND Question.survey_id = ? INNER JOIN Answer ON Answer.answer_id = ?;
                ");

                echo "<h1>Estos son tus votos: </h1>";
                echo "<ul>";
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $querySelect->bindParam(1, $row["survey_id"], PDO::PARAM_INT);
                    $querySelect->bindParam(2, $row["survey_id"], PDO::PARAM_INT);
                    $querySelect->bindParam(3, $row["answer_id"], PDO::PARAM_INT);
                    $querySelect->execute();
                    
                    // compruebo errores
                    $e = $querySelect->errorInfo();
                    if ($e[0] != '00000') {
                        echo "\nPDO::errorInfo():\n";
                        die("Error accedint a dades: " . $e[2]);
                    }

                    $selectRow = $querySelect->fetch();

                    if ($selectRow) {
                        // imprimo las encuestas
                        echo "<li>";
                        echo "  <h1>Título: ".$selectRow["title"]."</h1>";
                        echo "  <h2>Pregunta: ".$selectRow["questionText"]."</h2>";
                        echo "  <h3>Respuesta: </h3><h3 class='answer' hidden>".$selectRow["answer_text"]."</h3><i class='fa-solid fa-eye toggle-response' style='color: #63E6BE;'></i>";
                        echo "</li>";
                    }   
                }
                echo "</ul>";
            } else {
                echo "<script> $(function() {addNotification('warning', 'No se encontraron votos para este usuario.')});</script>";
            }
        } else {
            echo "<form action='list_votes.php' method='post'>";
            echo "  <label for='pass'>Valida tu password</label>";
            echo "  <input type='password' name='pass'>";
            echo "  <input type='submit' value='Verificar'>";
            echo "</form>";
            echo "<p>En encuesta2 nos tomamos en serio la seguridad. Valida tu password para poder ver tus votaciones.</p>";
        }
        include("./templates/footer.php");
    ?>
    <ul id="notification-container"></ul>
</body>
</html>