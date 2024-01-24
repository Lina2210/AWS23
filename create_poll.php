<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <link rel="stylesheet" href="./assets/styles/styles.css">
    <title>Crear Encuesta</title>
    <style>
        .createPoll-body {
            margin: 0;
            padding: 0;
            background-color: #052659;
            font-family: "Saans", Arial, sans-serif;    
        }

        .createPoll-body h1 {
            margin: 0;
            position: absolute;
            left: 80px;
            top: 55px;
            color: #C1E8FF;
        }

        .createPoll-body .main-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        
        .createPoll-body .main-content form {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: left;
            margin-top: 50px;
        }

        .createPoll-body .main-content form label {
            color: #C1E8FF;
            font-size: 20px;
            margin-left: 20px;
            margin-bottom: 10px;
        }

        .createPoll-body .main-content form input.field{
            font-weight: bold;
            height: 50px;
            width: 450px;
            background-color: #053377;
            border: 1px solid #07429b;
            border-radius: 50px;
            margin-bottom: 30px;
            transition: background-color 0.5s;
            padding-left: 20px;
        }

        .createPoll-body .main-content form textarea {
            font-weight: bold;
            width: 450px;
            height: auto;
            background-color: #053377;
            border: 1px solid #07429b;
            border-radius: 50px;
            margin-bottom: 30px;
            transition: background-color 0.5s;
            padding-left: 20px;
        }

        .createPoll-body .main-content form textarea::placeholder {
            color: #fff;
            padding-top: 20px;
        }

        .createPoll-body .main-content form input.field::placeholder {
            color: #fff;
        }

        .createPoll-body .main-content form input[type="date"]::-webkit-datetime-edit-text,
        .createPoll-body .main-content form input[type="date"]::-webkit-datetime-edit-year-field,
        .createPoll-body .main-content form input[type="date"]::-webkit-datetime-edit-month-field,
        .createPoll-body .main-content form input[type="date"]::-webkit-datetime-edit-day-field {
            color: #fff; 
        }

        .createPoll-body .main-content .question,
        .createPoll-body .moreAnswer {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .createPoll-body .main-content .question button {
            background-color: transparent;
            width: fit-content;
            margin: 20px auto 10px auto;
            text-decoration: none;
            color: #C1E8FF;
            border: 1px solid #C1E8FF;
            border-radius: 10px;
            padding: 15px 40px;
            font-size: 20px;
            transition: background-color 0.5s;
        }

        .createPoll-body .main-content .question button:hover{
            background-color: #C1E8FF;
            color: #052659;
        }

        .createPoll-body .main-content #addQuestion{
            background-color: transparent;
            width: 100%;
            margin: 20px auto 10px auto;
            text-decoration: none;
            color: #C1E8FF;
            border: 1px solid #C1E8FF;
            border-radius: 10px;
            padding: 15px 40px;
            font-size: 20px;
            transition: background-color 0.5s;
        }

        .createPoll-body .main-content #addQuestion:hover {
            background-color: #C1E8FF;
            color: #052659;
        }

        .createPoll-body .main-content .buttonAddQuestion {
            display: flex;
            justify-content: flex-start;
        }

        .createPoll-body .main-content #send {
            background-color: black;
            width: fit-content;
            margin: 20px auto 10px auto;
            text-decoration: none;
            color: #fff;
            border: 1px solid #C1E8FF;
            border-radius: 50px;
            padding: 15px 40px;
            font-size: 20px;
            transition: background-color 0.5s;
        }

        .createPoll-body .main-content #send:hover {
            background-color: white;
            color: black;
        }

        
    </style>
</head>
<body class="createPoll-body">
    <h1>Crear Encuesta</h1>
    <?php
        include("./templates/header.php");
    ?>
    <main class="main-content">
        <form action="" method="post">
            <input type="text" class="field" name="namePoll" placeholder="Nombre de la encuesta" required>
            <label for="dateStart">Fecha de apertura</label>
            <input type="date" class="field" name="dateStart" required>
            <label for="dateFinish">Fecha de cierre</label>
            <input type="date" class="field" name="dateFinish" required>
            <div id="questions">
                <div class="question">
                    <textarea name="question1" class="question1" cols="30" rows="10" placeholder="Pregunta1" required></textarea>
                    <input type="text" class="field" name="answer1" placeholder="Respuesta" required>
                    <input type="text" class="field" name="answer2" placeholder="Respuesta" required>
                    <div class="moreAnswer"></div>
                    
                    <button type="button" class="addAnswer">Agregar respuesta</button>
                    <button type="button" class="deleteAnswer" disabled>Eliminar respuesta</button>
                    
                </div>
            </div>
            <div id="moreQuestion"></div>
            
            <button type="button" id="addQuestion">Agregar pregunta</button>
            <input type="submit" id="send" value="Guardar">
            
            
    </main>
    <script>
        $(document).ready(function() {
                       
            // Delegación de eventos para el botón "Agregar Respuesta"
            $("#questions").on("click", ".addAnswer", function() {
                var newAnswer = '<input type="text" class="field" name="answer" placeholder="Respuesta" required>';
                $(this).siblings(".moreAnswer").append(newAnswer);
                updateDeleteAnswerButton($(this).siblings(".moreAnswer"));
                
            });

            $("#questions").on("click", ".deleteAnswer", function() {
                var moreAnswerContainer = $(this).siblings(".moreAnswer");
                moreAnswerContainer.children("input:last").remove();
                updateDeleteAnswerButton(moreAnswerContainer);
            });

         
            $("#addQuestion").on("click", function() {
                var countQuestion = $(".question").length + 1;
                var newQuestion = $("#questions .question:first").clone();
                newQuestion.find("textarea").attr("name", "question" + countQuestion).attr("id", "question" + countQuestion).attr("placeholder", "Pregunta" + countQuestion).val("");
                newQuestion.find(".moreAnswer").empty();
                countQuestion++;

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
