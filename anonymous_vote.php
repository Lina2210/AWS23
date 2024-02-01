<?php
if (isset($_GET["token"]) && $_GET["token"] != 'ko') {
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
        // SELECT Survey.title, Question.questionText, Question.image AS question_image, Answer.answer_text, Answer.answer_id, Answer.image AS answer_image FROM Survey INNER JOIN Question ON Survey.survey_id = 1 AND Question.survey_id = 1 INNER JOIN Answer ON Question.question_id = Answer.question_id;
        $queryInner = $pdo->prepare("
        SELECT Survey.title, Question.questionText, Question.image AS question_image, Answer.answer_text, Answer.answer_id, Answer.image AS answer_image FROM Survey 
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
        if ($innerRow["question_image"] != NULL) { echo "<img src='".$innerRow["question_image"]."'></img>"; }
        echo "    <form action='anonymous_vote.php' method='post'>";
        echo "          <input type='hidden' name='token' value='".$_GET["token"]."'>";
        echo "          <input type='hidden' name='email' value='".$row["email"]."'>";
        echo "          <input type='hidden' name='survey_id' value='".$row["survey_id"]."'>";
        if ($innerRow["answer_image"] != NULL) { echo "<img src='".$innerRow["answer_image"]."'></img>"; }
        echo "          <label>".$innerRow["answer_text"]."</label>";
        echo "          <input type='radio' name='opcion' value='".$innerRow["answer_id"]."'><br>";

        while ($innerRow = $queryInner->fetch(PDO::FETCH_ASSOC)) {
            if ($innerRow["answer_image"] != NULL) { echo "<img src='".$innerRow["answer_image"]."'></img>"; }
            echo "      <label>".$innerRow["answer_text"]."</label>";
            echo "      <input type='radio' name='opcion' value='".$innerRow["answer_id"]."'><br>";
        }
        echo "          <input type='submit' value='Guardar Voto'>";
        echo "    </form>";
        include("./templates/footer.php");
        echo "</body>";
        echo "</html>";
    }
} 
elseif (isset($_POST["opcion"]) && isset($_POST["token"]) && isset($_POST["email"]) && isset($_POST["survey_id"])) {
    // borrar el token de invited user para no poder volver a votar y crear ese usuario en la tabla User para que cuando se registre que coja eso y se lo guarde.
    require_once("./data/dbAccess.php");
    try {
        $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", "$username", "$pw");
        $queryUpdate = $pdo->prepare("UPDATE InvitedUser SET token = 'ko' WHERE token = ?");
        $queryUpdate->bindParam(1, $_POST["token"], PDO::PARAM_STR);
        $queryUpdate->execute();

        $e = $queryUpdate->errorInfo();
        if ($e[0] != '00000') {
            echo "\nPDO::errorInfo():\n";
            die("Error accedint a dades: " . $e[2]);
        }
    } catch (PDOException $e) {
        echo "Failed to get DB handle: " . $e->getMessage() . "\n";
        exit;
    }
    
    // seguir aqui lo de crear un usuario anonimo. Ten en cuenta que has cambiado el .sql de User y le has añadido un nuevo campo
    $queryInsert = $pdo->prepare("INSERT INTO User (user_name, mail, password, tlfn, country_id, city, postal_code, email_token, terms_of_use, invited_user) 
    VALUES ('anonimo', ?, 'password1', 0, 1, 'Ciudad1', 12345, 'ok', 0, 1)");
    $queryInsert->bindParam(1, $_POST["email"], PDO::PARAM_STR);
    $queryInsert->execute();

    $e = $queryInsert->errorInfo();
    if ($e[0] != '00000') {
        echo "\nPDO::errorInfo():\n";
        die("Error accedint a dades: " . $e[2]);
    }

    // seleccionar el id de usuario autogenerado y asociarlo con su respuesta
    $querySelect = $pdo->prepare("SELECT user_id FROM User WHERE mail = ?");
    $querySelect->bindParam(1, $_POST["email"], PDO::PARAM_STR);
    $querySelect->execute();

    $e = $querySelect->errorInfo();
    if ($e[0] != '00000') {
        echo "\nPDO::errorInfo():\n";
        die("Error accedint a dades: " . $e[2]);
    }

    $selectRow = $querySelect->fetch();

    if(!$selectRow) {
        echo "NO SE HA PODIDO SELECIONAR EL USER_ID DEL USUARIO ANONIMO";
        exit;   
    }

    $queryInsertAnswer = $pdo->prepare("INSERT INTO UserVote (user_id, answer_id) VALUES (?, ?)");
    $queryInsertAnswer->bindParam(1, $selectRow["user_id"], PDO::PARAM_INT);
    $queryInsertAnswer->bindParam(2, intval($_POST["opcion"]), PDO::PARAM_INT);
    $queryInsertAnswer->execute();

    $e = $queryInsertAnswer->errorInfo();
    if ($e[0] != '00000') {
        echo "\nPDO::errorInfo():\n";
        die("Error accedint a dades: " . $e[2]);
    }

    // muestro feedback al usuario anonimo
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
    echo "<h1>Tu voto ha sido guardado.</h1>";
    echo "<h2>Registrate para ver los resultados:</h2>";
    echo "<a href='/register.php'>Registrarse</a>";
    include("./templates/footer.php");
    echo "</body>";
    echo "</html>";

} else {
    include("./error404.php");
    exit;
}
?>