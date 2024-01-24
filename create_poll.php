<?php
    session_start();
    if (!isset($_SESSION["mail"])) {
        include("./error403.php");
        exit;
    }

    // Obtener el user_id a partir del user_name (correo electrónico)
    $mail = $_SESSION["mail"];
    try {
        $hostname = "localhost";
        $dbname = "encuesta2";
        $username = "encuesta2";
        $pw = "naranjasVerdes";
        $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $pw);
    } catch (PDOException $e) {
        echo "Failed to get DB handle: " . $e->getMessage() . "\n";
        exit;
    }

    $user_id_query = $pdo->prepare("SELECT user_id FROM User WHERE mail = :mail");
    $user_id_query->bindParam(':mail', $mail);
    $user_id_query->execute();
    $user_id_result = $user_id_query->fetch(PDO::FETCH_ASSOC);

    if (!$user_id_result) {
        // Si no se encuentra el usuario, puedes manejar el error o redirigir a una página de error.
        echo "No se encontró el usuario en la base de datos.";
        exit;
    }

    $user_id = $user_id_result["user_id"];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <link rel="stylesheet" href="./assets/styles/styles.css">
    <title>Crear Encuesta</title>
</head>
<body class="createPoll-body">
    <h1>Crear Encuesta</h1>
    <?php
        include("./templates/header.php");
    ?>
    <main class="main-content">
        <?php 
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                try {
                    $hostname = "localhost";
                    $dbname = "encuesta2";
                    $username = "encuesta2";
                    $pw = "naranjasVerdes";
                    $pdo = new PDO ("mysql:host=$hostname;dbname=$dbname","$username","$pw");
                  } catch (PDOException $e) {
        
                    echo "Failed to get DB handle: " . $e->getMessage() . "\n";
                    exit;
                }
                
                $namePoll = $_POST["namePoll"];
                $dateStart = $_POST["dateStart"];
                $dateFinish = $_POST["dateFinish"];
                $questions = $_POST["questions"];
                $answers = $_POST["answers"];
                
                if ($namePoll && $dateStart && $dateFinish && $questions && $answers) {
                    $querySurvey = $pdo -> prepare("INSERT INTO Survey (title, user_id, start_date, end_date, state, creation) 
                    VALUES (?, ?, ?, ?, 'Activo', NOW())");
                    $querySurvey->bindParam(1, $namePoll, PDO::PARAM_STR);
                    $querySurvey->bindParam(2, $userId, PDO::PARAM_INT); 
                    $querySurvey->bindParam(3, $dateStart, PDO::PARAM_STR);
                    $querySurvey->bindParam(4, $dateFinish, PDO::PARAM_STR);
                    $querySurvey->execute();
                    $surveyId = $pdo->lastInsertId();
                    
                    foreach ($questions as $questionIndex => $question) {
                        file_put_contents('debug_log.txt', "Pregunta index: $questionIndex\n", FILE_APPEND);
                        $queryQuestion = $pdo -> prepare("INSERT INTO Question (questionText, survey_id) VALUES (?, ?)");
                        $queryQuestion->bindParam(1, $question, PDO::PARAM_STR);
                        $queryQuestion->bindParam(2, $surveyId, PDO::PARAM_INT);
                        $queryQuestion->execute();
                        $questionId = $pdo->lastInsertId();
                        
                        foreach ($answers[$questionIndex] as $answer) {
                            $queryAnswer = $pdo -> prepare("INSERT INTO Answer (question_id, answer_text) VALUES (?, ?)");
                            $queryAnswer->bindParam(1, $questionId, PDO::PARAM_INT);
                            $queryAnswer->bindParam(2, $answer, PDO::PARAM_STR);
                            $queryAnswer->execute();
                        }
                    }
                } else {
                    
                    echo "Error: Faltan datos en el formulario.";
                }
            }
        ?>
        <form action="" method="post">
            <input type="text" class="field" name="namePoll" placeholder="Nombre de la encuesta" required>
            <label for="dateStart">Fecha de apertura</label>
            <input type="date" class="field" name="dateStart" required>
            <label for="dateFinish">Fecha de cierre</label>
            <input type="date" class="field" name="dateFinish" required>
            <div id="errorContainer"></div>
            <div id="questions">
                <div class="question">
                    <textarea name="questions[0]" class="question" cols="30" rows="10" placeholder="Pregunta" required></textarea>
                    <input type="text" class="field" name="answers[0][]" placeholder="Respuesta" required>
                    <input type="text" class="field" name="answers[0][]" placeholder="Respuesta" required>
                    <div class="moreAnswer"></div>
                    
                    <button type="button" class="addAnswer">Agregar respuesta</button>
                    <button type="button" class="deleteAnswer" disabled>Eliminar respuesta</button>
                    
                </div>
            </div>
            <div id="moreQuestion"></div>
            
            <button type="button" id="addQuestion">Agregar pregunta</button>
            <input type="submit" id="send" value="Guardar">
        </form>
            
    </main>
    <script>
        $(document).ready(function() {
                       
            //date configuration
            var currentDate = new Date();
            var formattedCurrentDate = currentDate.getFullYear() + '-' + 0 +(currentDate.getMonth() + 1) + '-' + currentDate.getDate();
            

            //Disable days before current day in dateStart
            $('input[name="dateStart"]').attr('min', formattedCurrentDate);

            //Disable days before the current day in dateFinish until dateStart is filled
            $('input[name="dateFinish"]').prop('disabled', true);

            var errorContainer = $('#errorContainer');

            $('input[name="dateStart"]').on('change', function() {
                
                var initDate = $(this).val();
                var parts = initDate.split("-");
                var startDate = parts[2] + '-' + parts[1] + '-' + parts[0];
                console.log(startDate)
                console.log(initDate);
                if (isValidDate(startDate) && isDateValid(startDate)) {
                    $('input[name="dateFinish"]').prop('disabled', false);
                    //Disable days before selected date in dateStart in dateFinish
                    $('input[name="dateFinish"]').attr('min', initDate);
                    errorContainer.text('');
                } else {
                    $('input[name="dateFinish"]').prop('disabled', true);
                    errorContainer.text('La fecha de inicio debe ser válida y no puede ser anterior a la fecha actual.');
                }
            });

            

            // validate format
            function isValidDate(dateString) {
                var regex = /^\d{2}-\d{2}-\d{4}$/;
                return regex.test(dateString);
            }

            //validate current date
            function isDateValid(dateString) {
                var parts = dateString.split("-");
                var dateObject = new Date(parts[2], parts[1] - 1, parts[0]);
                return dateObject >= currentDate;
            }      

            // Delegación de eventos para el botón "Agregar Respuesta"
            $("#questions").on("click", ".addAnswer", function() {
                var questionContainer = $(this).closest(".question");
                var answerContainer = questionContainer.find(".moreAnswer");
                var questionIndex = $(".question").index(questionContainer);
                var newAnswer = '<input type="text" class="field" name="answers[' + questionIndex + '][]" placeholder="Respuesta" required>';
                answerContainer.append(newAnswer);
                updateDeleteAnswerButton(answerContainer);
                
            });

            $("#questions").on("click", ".deleteAnswer", function() {
                var questionContainer = $(this).closest(".question");
                var answerContainer = questionContainer.find(".moreAnswer");
                answerContainer.children("input:last").remove();
                updateDeleteAnswerButton(answerContainer);
            });

         
            $("#addQuestion").on("click", function() {
                var countQuestion = $(".question").length;
                var newQuestion = $(".question:first").clone();
                newQuestion.find("textarea").attr("name", "questions[" + countQuestion + "]").attr("id", "question" + countQuestion).attr("placeholder", "Pregunta").val("");
                newQuestion.find("input").attr("name", "answers[" + countQuestion + "][]")
                newQuestion.find(".moreAnswer").empty();
                countQuestion;
                

                $("#questions").append(newQuestion);
                initializeDeleteAnswerButton();
            });

            function initializeDeleteAnswerButton() {
                $(".question").each(function() {
                    var numAnswers = $(this).find(".moreAnswer input").length;
                    $(this).find(".deleteAnswer").prop("disabled", numAnswers <= 0);
                });
            }

            function updateDeleteAnswerButton(container) {
                var numAnswers = container.children("input").length;
                container.siblings(".deleteAnswer").prop("disabled", numAnswers <= 0);
            }
        });
    </script>
    <?php
        include("./templates/footer.php");
    ?>
</body>
</html>
