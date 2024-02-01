<?php
// INVITACIONS: enviament d'invitacions per llista d'emails. Els destinataris reben un link per a votar en una enquesta concreta. 
// Els emails s'envien en diferit en un procés del CRON, en paquets de 5 emails cada 5 min per no ser detectats com a Spam.
if (isset($_POST["emails"]) && isset($_POST["survey_id"])) {
    try {
        require_once("./data/dbAccess.php");
        $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", "$username", "$pw");

        // metemos los mails y el id de las encuesta en la tabla SendEmailTo
        $emailsArray = explode("\n", $_POST["emails"]);
        $survey_id = intval($_POST["survey_id"]);

        foreach ($emailsArray as $email) {
            $emailok = str_replace("\n", "", $email);
            $query = $pdo->prepare("INSERT INTO SendEmailTo (email, survey_id) VALUES (?, ?)");
            $query->bindParam(1, trim($emailok), PDO::PARAM_STR);
            $query->bindParam(2, $survey_id, PDO::PARAM_INT);
            $query->execute();

            //comprovo errors:
            $e = $query->errorInfo();
            if ($e[0] != '00000') {
                echo "\nPDO::errorInfo():\n";
                echo "  <script>
                            localStorage.setItem('error', 'PDO::errorInfo()');
                            window.location.href = 'list_polls.php';
                        </script>";
                die("Error accedint a dades: " . $e[2]);
            }
        }
    } catch (PDOException $e) {
        echo "Failed to get DB handle: " . $e->getMessage() . "\n";
        exit;
    }

    echo "  <script>
                localStorage.setItem('success', 'Tus invitaciones han sido enviadas con éxito.');
                window.location.href = 'dashboard.php';
            </script>";
}
elseif (!isset($_POST["survey_id"]) && !isset($_POST["title"])) {
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
        <title>Invitar a participantes</title>
    </head>
    <body>
        <?php include("./templates/header.php"); ?>

    <h1>Invitar participantes a la encuesta "<?php echo $_POST["title"]; ?>"</h1>
    <p>Por favor separa los correos con saltos de linea (pulsando "Intro")</p>
    <form id="sendMailsForm" action="/survey_invitation.php" method="post">
        <label for="emails">Lista de emails invitados a la encuesta:</label>
        <textarea id="emails" name="emails" rows="4" cols="50" required></textarea>
        <input type="hidden" name="survey_id" value="<?php echo $_POST["survey_id"]; ?>">
        <button id="checkEmails" type="button">Enviar Invitaciones</button>
    </form>

        <ul id="notification-container"></ul>
        <?php include("./templates/footer.php"); ?>
    </body>
</html>