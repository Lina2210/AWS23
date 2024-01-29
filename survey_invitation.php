<?php
// INVITACIONS: enviament d'invitacions per llista d'emails. Els destinataris reben un link per a votar en una enquesta concreta. 
// Els emails s'envien en diferit en un procés del CRON, en paquets de 5 emails cada 5 min per no ser detectats com a Spam.
if (isset($_POST["emails"]) && isset($_POST["survey_id"])) {
    // COMPROBAR QUE FUNCIONA EN PROXMOX
    try {
        require_once("./data/dbAccess.php");
        $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", "$username", "$pw");

        $emailsArray = explode("\n", $_POST["emails"]);

        foreach ($emailsArray as $email) {
            $query = $pdo->prepare("INSERT INTO SendEmailTo (email, survey_id) VALUES (?, ?)");
            $query->bindParam(1, $email, PDO::PARAM_STR);
            $query->bindParam(2, $_POST["survey_id"], PDO::PARAM_STR);
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
if (!isset($_POST["survey_id"]) && !isset($_POST["title"])) {
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
    <script src="./assets/scripts/survey_invitation.js"></script>
    <title>Invitar a participantes</title>
</head>
<body>
    <?php include("./templates/header.php"); ?>

    <h1>Invitar participantes a la encuesta "<?php echo $_POST["title"]; ?>"</h1>
    <p>Por favor separa los correos con saltos de linea (pulsando "Intro")</p>
    <form id="sendMailsForm" action="survey_invitation.php" method="post">
        <label for="emails">Lista de emails invitados a la encuesta:</label>
        <textarea id="emails" name="emails" rows="4" cols="50" required></textarea>
        <input type="hidden" value="<?php $_POST["survey_id"] ?>">
        <button id="checkEmails" type="button">Enviar Invitaciones</button>
    </form>

    <ul id="notification-container"></ul>
    <?php include("./templates/footer.php"); ?>
</body>
</html>