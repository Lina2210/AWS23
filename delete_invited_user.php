<?php
if (isset($_POST["opcion"]) && isset($_POST["token"]) && isset($_POST["email"]) && isset($_POST["survey_id"])) {
    // borrar el token de invited user para no poder volver a votar y crear ese usuario en la tabla User para que cuando se registre que coja eso y se lo guarde.
    require_once("./data/dbAccess.php");
    try {
        $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", "$username", "$pw");
        $queryUpdate = $pdo->prepare("UPDATE InvitedUser SET token = ? WHERE token = ?");
        $queryUpdate->bindParam(1, 'ko', PDO::PARAM_STR);
        $queryUpdate->bindParam(2, $_POST["token"], PDO::PARAM_STR);
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