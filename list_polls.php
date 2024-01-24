<?php
    session_start();
    if (!isset($_SESSION["user_name"])) {
        include("./error403.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/styles/styles.css">
    <title>List polls</title>
    <style>

      .listPolls-body {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }

      .listPolls-body .main-content{
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        margin: 50px 0 100px 0;
        
      }

      .listPolls-body .main-content table {
        border-collapse: collapse;
        width: 50%;
        margin-bottom: 20px; 
      }

      .listPolls-body .main-content th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
      }

      .listPolls-body .main-content th.name-column {
        width: 50%; 
      }

      .listPolls-body .main-content th {
        background-color: #f2f2f2;
      }

      .listPolls-body .main-content a {
        width: fit-content;
        margin-left: auto;
        margin-right: auto;
        margin-bottom: 100px;
        text-decoration: none;
        color: #C1E8FF;
        border: 1px solid #C1E8FF;
        border-radius: 10px;
        padding-left: 40px;
        padding-right: 40px;
        padding-top: 15px;
        padding-bottom: 15px;
        font-size: 20px;
        transition: background-color 0.5s;
      }

      .listPolls-body .main-content a:hover {
          background-color: #C1E8FF;
          color: #052659;
      }
  </style>

<body class="listPolls-body">
  <?php
      include("./templates/header.php");
  ?>
  <main class="main-content">
    
    <?php
      $user_name = $_SESSION["user_name"];

      $servername = "localhost";
      $username = "encuesta2";
      $password = "naranjasVerdes";
      $dbname = "encuesta2";

      try {
          $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
          $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

          $user_id_query = "SELECT user_id FROM User WHERE user_name = :user_name";
          $user_id_statement = $conn->prepare($user_id_query);
          $user_id_statement->bindParam(':user_name', $user_name);
          $user_id_statement->execute();
          $user_id_result = $user_id_statement->fetch(PDO::FETCH_ASSOC);

          if ($user_id_result) {
              $user_id = $user_id_result["user_id"];

              $survey_query = "SELECT title, state FROM Survey WHERE user_id = :user_id";
              $survey_statement = $conn->prepare($survey_query);
              $survey_statement->bindParam(':user_id', $user_id);
              $survey_statement->execute();
              $survey_result = $survey_statement->fetchAll(PDO::FETCH_ASSOC);

              if ($survey_result) {
                  echo "<table>";
                  echo '<tr><th class="name-column">Name</th><th>Estado de Publicación</th><th>Estado de Bloqueo</th></tr>';

                  foreach ($survey_result as $row) {
                      echo "<tr>";
                      echo "<td>".$row["title"]."</td>";
                      echo "<td>".$row["state"]."</td>";
                      echo "<td>Desbloqueada</td>"; // Campo adicional con valor por defecto
                      echo "</tr>";
                  }

                  echo "</table>";
              } else {
                  echo "No se encontraron encuestas para este usuario.";
              }
          } else {
              echo "No se encontró el usuario en la base de datos.";
          }
      } catch (PDOException $e) {
          echo "Error: " . $e->getMessage();
      } finally {
          $conn = null;
      }
    ?>
    <a href="dashboard.php">Atras</a>
    
  </main> 
  <?php
        include("./templates/footer.php");
  ?>
    
</body>
</html>