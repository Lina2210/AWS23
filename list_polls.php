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
      
.index-body .buttonBottom:hover {
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

      $conn = new mysqli($servername, $username, $password, $dbname);

      // Verificar la conexión
      if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
      }

      $user_id_query = "SELECT user_id FROM User WHERE user_name = '$user_name'";
      $user_id_result = $conn->query($user_id_query);



      // Query para obtener los datos de la base de datos
      if ($user_id_result->num_rows > 0) {
      // Obtener el user_id
      $user_row = $user_id_result->fetch_assoc();
      $user_id = $user_row["user_id"];

      // Query para obtener los datos de la base de datos
      $survey_query = "SELECT title, state FROM Survey WHERE user_id = $user_id";
      $survey_result = $conn->query($survey_query);

      // Verificar si hay resultados en la encuesta
        if ($survey_result->num_rows > 0) {
            echo "<table>";
            echo '<tr><th class="name-column">Name</th><th>Estado de Publicación</th><th>Estado de Bloqueo</th></tr>';


            // Mostrar datos en la tabla
            while ($row = $survey_result->fetch_assoc()) {
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

        // Cerrar la conexión
        $conn->close();
    ?>
    <a href="dashboard.php">Atras</a>
    
  </main> 
  <?php
        include("./templates/footer.php");
  ?>
    
</body>
</html>