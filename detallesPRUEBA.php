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
        <title>Detalles — encuesta2</title>
    </head>
    <body class="graphics">
        <?php include("./templates/header.php"); ?>
        
        <main>
        <?php
            session_start();
            
            $id_encuesta = $_POST["survey_id"];
            $user_id = $_SESSION["user_id"];
            var_dump($user_id . "id usuario");
            try {
                require_once("./data/dbAccess.php");
                $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", "$username", "$pw");
                        $query_encuesta = 'SELECT * FROM Survey WHERE survey_id = :id_encuesta';
                        $stmt_encuesta = $pdo->prepare($query_encuesta);
                        $stmt_encuesta->bindParam(':id_encuesta', $id_encuesta, PDO::PARAM_INT);
                        $stmt_encuesta->execute();
                        $encuesta = $stmt_encuesta->fetch(PDO::FETCH_ASSOC);
                        $id_encuesta = $_POST['survey_id'];
                        var_dump($id_encuesta . "id encuesta");
                            echo "<h1 id='pollName'>Detalles de {$encuesta['title']}</h1>";
                        
                            // Consulta para obtener las preguntas de la encuesta
                            $query_preguntas = 'SELECT * FROM Question WHERE survey_id = :id_encuesta';
                            $stmt_preguntas = $pdo->prepare($query_preguntas);
                            $stmt_preguntas->bindParam(':id_encuesta', $id_encuesta, PDO::PARAM_INT);
                            $stmt_preguntas->execute();
                            $preguntas = $stmt_preguntas->fetchAll(PDO::FETCH_ASSOC);
                            var_dump($preguntas);
                            // Verificar si hay preguntas asociadas con la encuesta
                            if ($preguntas) {
                                // Iterar sobre cada pregunta y mostrar sus respuestas
                                foreach ($preguntas as $pregunta) {
                                    echo "<h2>{$pregunta['questionText']}</h2>";
                        
                                    // Consulta para obtener las respuestas de la pregunta con sus respectivos votos
                                    $query_respuestas = 'SELECT Answer.answer_text, COUNT(UserVote.user_id) AS vote_count
                                                         FROM Answer
                                                         LEFT JOIN UserVote ON Answer.answer_id = UserVote.answer_id
                                                         WHERE Answer.question_id = :question_id
                                                         GROUP BY Answer.answer_id';
                                    $stmt_respuestas = $pdo->prepare($query_respuestas);
                                    $stmt_respuestas->bindParam(':question_id', $pregunta['question_id'], PDO::PARAM_INT);
                                    $stmt_respuestas->execute();
                                    $respuestas = $stmt_respuestas->fetchAll(PDO::FETCH_ASSOC);
                        
                                    // Crear arrays para los datos del gráfico
                                    $labels = [];
                                    $data = [];
                        
                                    // Llenar los arrays con los datos de las respuestas
                                    foreach ($respuestas as $respuesta) {
                                        $labels[] = $respuesta['answer_text'];
                                        $data[] = $respuesta['vote_count'];
                                    }
                        
                                    // Mostrar el gráfico utilizando Chart.js
                                    echo "<canvas class='chart' data-labels='" . json_encode($labels) . "' data-data='" . json_encode($data) . "'></canvas>";
                                    echo "<ul>";
                                    foreach ($respuestas as $respuesta) {
                                        echo "<li>{$respuesta['answer_text']}</li>";
                                    }
                                    echo "</ul>";
                                }
                            } else {
                                echo "<p>No hay preguntas asociadas con esta encuesta.</p>";
                            }
                        unset($pdo);
                    } catch (PDOException $e) {
                        // Manejar errores de la base de datos
                        echo "Error: " . $e->getMessage();
                    }
            ?>
        </main>
        <?php include("./templates/footer.php"); ?>
    </body>
</html>