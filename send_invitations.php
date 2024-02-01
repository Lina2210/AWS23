<?php
$hostname = "localhost";
$dbname = "encuesta2";
$username = "encuesta2";
$pw = "naranjasV3rdes#";
try {
    $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", "$username", "$pw");
} catch (PDOException $e) {

    echo "Failed to get DB handle: " . $e->getMessage() . "\n";
    exit;
}

$query = $pdo->prepare("SELECT * FROM SendEmailTo LIMIT 5");
$query->execute();

//comprovo errors:
$e = $query->errorInfo();
if ($e[0] != '00000') {
    echo "\nPDO::errorInfo():\n";
    die("Error accedint a dades: " . $e[2]);
}

// Insertamos los usuarios que han sido invitados, borramos el mail de SendEmailTo y enviamos un mail de invitacion a cada una de las direcciones email
$insertQuery = $pdo->prepare("INSERT INTO InvitedUser (email, token, survey_id) VALUES (?, ?, ?)");
$deleteQuery = $pdo->prepare("DELETE FROM SendEmailTo WHERE email = ? AND survey_id = ?");

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    // insert
    $token = bin2hex(random_bytes(32 / 2));
    $link = "https://aws23.ieti.site/anonymous_vote.php?token=".$token;
    $insertQuery->bindParam(1, $row["email"], PDO::PARAM_STR);
    $insertQuery->bindParam(2, $token, PDO::PARAM_STR);
    $insertQuery->bindParam(3, $row["survey_id"], PDO::PARAM_INT);
    $insertQuery->execute();
    $e = $insertQuery->errorInfo();
    if ($e[0] != '00000') {
        echo "\nPDO::errorInfo():\n";
        die("Error accedint a dades: " . $e[2]);
    }

    //delete
    $deleteQuery->bindParam(1, $row["email"], PDO::PARAM_STR);
    $deleteQuery->bindParam(2, $row["survey_id"], PDO::PARAM_INT);
    $deleteQuery->execute();
    $e = $deleteQuery->errorInfo();
    if ($e[0] != '00000') {
        echo "\nPDO::errorInfo():\n";
        die("Error accedint a dades: " . $e[2]);
    }

    // send email
    mail($row["email"], "Has sido invitado a participar en una encuesta de encuesta2", "No respondas a este mensaje. Link de la encuesta: ".$link);
}
?>