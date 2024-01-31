<?php
session_start();
if (!isset($_SESSION["mail"])) {
    include("./error403.php");
    exit;
}

// Obtener el user_id a partir del user_name (correo electrónico)
$mail = $_SESSION["mail"];
file_put_contents('user_id_result.txt', print_r($mail, true));
try {
    $hostname = "localhost";
    $dbname = "encuesta2";
    $username = "encuesta2";
    $pw = "naranjasV3rdes#";
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
    echo "  <script>
                    localStorage.setItem('error', 'No se encontró el usuario en la base de datos.');
                    window.location.href = 'login.php';
                </script>";
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
    <script src="./assets/scripts/notifications.js"></script>
    <script src="./assets/scripts/create_poll.js"></script>
    <title>Crear Encuesta</title>
</head>

<body class="createPoll-body">
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
            $pw = "naranjasV3rdes#";
            $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", "$username", "$pw");
        } catch (PDOException $e) {

            echo "  <script>
                            localStorage.setItem('error', 'Failed to get DB handle: " . $e->getMessage() . ");
                            window.location.href = 'login.php';
                        </script>";
            exit;
        }
        file_put_contents('user_id_result.txt', print_r($user_id, true));
        
        $namePoll = $_POST["nombreEncuesta"];
        $dateStart = $_POST["fechaInicio"];
        $dateFinish = $_POST["fechaFin"];
        $questions = $_POST["pregunta"];
        $answers = $_POST["respuesta"];
        

        
        if ($namePoll && $dateStart && $dateFinish && $questions && $answers) {
            $querySurvey = $pdo->prepare("INSERT INTO Survey (title, user_id, start_date, end_date, state, creation) 
                VALUES (?, ?, ?, ?, 'Activo', NOW())");
            $querySurvey->bindParam(1, $namePoll, PDO::PARAM_STR);
            $querySurvey->bindParam(2, $user_id, PDO::PARAM_INT);
            $querySurvey->bindParam(3, $dateStart, PDO::PARAM_STR);
            $querySurvey->bindParam(4, $dateFinish, PDO::PARAM_STR);
            $querySurvey->execute();
            $surveyId = $pdo->lastInsertId();

            foreach ($questions as $questionIndex => $question) {
                file_put_contents('debug_log.txt', "Pregunta index: $questionIndex\n", FILE_APPEND);
                $queryQuestion = $pdo->prepare("INSERT INTO Question (questionText, survey_id) VALUES (?, ?)");
                $queryQuestion->bindParam(1, $question, PDO::PARAM_STR);
                $queryQuestion->bindParam(2, $surveyId, PDO::PARAM_INT);
                $queryQuestion->execute();
                $questionId = $pdo->lastInsertId();

                if (!empty($_FILES['imagenPregunta']['name'][$questionIndex])) {
                    
                    $directorioDestino = './uploads/';
                    $fileType = $_FILES['imagenPregunta']['type'];
                    $fileName = $_FILES['imagenPregunta']['name'];
                    file_put_contents('debug_log.txt', "File name: $fileName\n", FILE_APPEND);
                    $nombreImagenUnico = generateUniqueName($fileName);

                    file_put_contents('debug_log.txt', "Nombre imagen unico: $nombreImagenUnico\n", FILE_APPEND);
                    $newFileName = $nombreImagenUnico;
                    $targetPath = $directorioDestino . $newFileName;
                    file_put_contents('debug_log.txt', "ruta: $targetPath\n", FILE_APPEND);

                    // Mover la imagen del directorio temporal al directorio de destino
                    if (move_uploaded_file($_FILES['imagenPregunta']['tmp_name'], $targetPath)) {
                        //ALERT La imagen se ha guardado exitosamente en el servidor
                        // Puedes almacenar la ruta de la imagen en la base de datos si lo deseas
                        $queryInsertImagen = $pdo->prepare("UPDATE Question SET image = ? WHERE question_id = ?");
                        $queryInsertImagen->bindParam(1, $targetPath, PDO::PARAM_STR);
                        $queryInsertImagen->bindParam(2, $questionId, PDO::PARAM_STR);
                        $queryInsertImagen->execute();

                    } else {
                        file_put_contents('debug_log.txt', "Error al mover la imagen\n", FILE_APPEND);
                        
                    }
                }
                // else{
                //     file_put_contents('debug_log.txt', "El input imagen está vacio", FILE_APPEND);
                // }

                }
                foreach ($answers as $index => $respuestaPregunta) {
                    foreach ($respuestaPregunta as $answerIndex => $answer){
                        $queryAnswer = $pdo->prepare("INSERT INTO Answer (question_id, answer_text) VALUES (?, ?)");
                        $queryAnswer->bindParam(1, $questionId, PDO::PARAM_INT);
                        $queryAnswer->bindParam(2, $answer, PDO::PARAM_STR);
                        $queryAnswer->execute();
                        $answerId = $pdo->lastInsertId();

                      
                            // Verifica si la imagen está presente y se ha cargado correctamente
                            if (!empty($_FILES['imagenRespuesta']['name'][$index][$answerIndex])) {
                                $directorioDestino = './uploads/';
                                $fileType = $_FILES['imagenRespuesta']['type'][$index];
                                $fileName = $_FILES['imagenRespuesta']['name'][$index];
                                file_put_contents('debug_log.txt', "File name: $fileName\n", FILE_APPEND);
                                $nombreImagenUnico = generateUniqueName($fileName);

                                file_put_contents('debug_log.txt', "Nombre imagen unico: $nombreImagenUnico\n", FILE_APPEND);
                                $newFileName = $nombreImagenUnico;
                                $targetPath = $directorioDestino . $newFileName;
                                file_put_contents('debug_log.txt', "ruta: $targetPath\n", FILE_APPEND);

                                if (move_uploaded_file($_FILES['imagenRespuesta']['tmp_name'][$index], $targetPath)) {
                                    $queryInsertImagen = $pdo->prepare("UPDATE Answer SET image = ? WHERE answer_id = ?");
                                    $queryInsertImagen->bindParam(1, $targetPath, PDO::PARAM_STR);
                                    $queryInsertImagen->bindParam(2, $answerId, PDO::PARAM_STR);
                                    $queryInsertImagen->execute();

                                } else {
                                    file_put_contents('debug_log.txt', "Error al mover la imagen\n", FILE_APPEND);
                                }
                            }else{
                                file_put_contents('debug_log.txt', "El input imagen está vacio", FILE_APPEND);
                            }

                        
                    }




                }
            }
            echo "  <script>
                            localStorage.setItem('success', 'Encuest creada correctamente.');
                            window.location.href = 'list_polls.php';
                        </script>";
            exit;
        } else {
            echo "  <script>addNotification('warning', 'Error: Faltan datos en el formulario.');</script>";
        }
        $uploadDirectory = 'uploads/'; // Directorio donde se guardarán las imágenes

        // Función para generar un nombre único para el archivo
        function generateUniqueName($originalName) {
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            $uniqueName = uniqid() . '.' . $extension;
            return $uniqueName;
        }
    
    ?>
    <main class="main-content">
        <form id="formulario" method="post" enctype="multipart/form-data"></form>
    </main>
    <script>
    $(document).ready(function() {
        // Variable para llevar el seguimiento del paso actual
        var paso = 1;
        var respuestaCount = 0; 

        $('#formulario').submit(function(event) {
            //event.preventDefault();
            switch (paso) {
                case 1:
                    validarNombreEncuesta();
                    break;
                case 2:
                    validarFechaInicio();
                    break;
                case 3:
                    validarFechaFin();
                    break;
                case 4:
                    validarPregunta();
                    break; 
                
            }
        });

        // Crear campo para el nombre de la encuesta
        function crearCampoNombre() {
            var nombreEncuesta = $('<input id="nombreEncuesta" name="nombreEncuesta" type="text"  class="field" placeholder="Nombre de la encuesta" required>');
            $('#formulario').append(nombreEncuesta);
            nombreEncuesta.focus();
            nombreEncuesta.keydown(function(event) {
                if (event.which === 13 || event.which === 9) {
                    event.preventDefault();
                    validarNombreEncuesta();
                }
            });
        }

        // Validar el nombre de la encuesta
        function validarNombreEncuesta() {
            var nombre = $('#nombreEncuesta').val().trim();
            if (nombre !== '') {
                crearCampoFechaInicio();
                paso++;
            } else {
                addNotification('warning', 'Error: Por favor ingrese un nombre para la encuesta.');
            }
        }

        // Crear campo para la fecha de inicio
        function crearCampoFechaInicio() {
            var label = $('<label for="fechaInicio">Fecha de apertura</label>');
            var fechaInicio = $('<input id="fechaInicio" name="fechaInicio" class="field" type="date"  required>');
            $('#formulario').append(label);
            $('#formulario').append(fechaInicio);
            fechaInicio.focus();
            fechaInicio.keydown(function(event) {
                if (event.which === 13 || event.which === 9) {
                    event.preventDefault();
                    validarFechaInicio();
                }
            });
        }

        // Validar la fecha de inicio
        function validarFechaInicio() {
            var fechaInicio = new Date($('#fechaInicio').val());
            var fechaActual = new Date();
            if (fechaInicio >= fechaActual) {
                crearCampoFechaFin();
                paso++;
            } else {
                addNotification('warning', 'La fecha de inicio debe ser posterior o igual a la fecha actual.');
            }
        }

        // Crear campo para la fecha final
        function crearCampoFechaFin() {
            var label = $('<label for="fechaFin">Fecha de finalización</label>');
            var fechaFin = $('<input id="fechaFin" name="fechaFin" class="field" type="date" required>');
            $('#formulario').append(label);
            $('#formulario').append(fechaFin);
            fechaFin.focus();
            fechaFin.keydown(function(event) {
                if (event.which === 13 || event.which === 9) {
                    event.preventDefault();
                    validarFechaFin();
                }
            });
        }

        // Validar la fecha final
        function validarFechaFin() {
            var fechaInicio = new Date($('#fechaInicio').val());
            var fechaFin = new Date($('#fechaFin').val());
            if (fechaFin >= fechaInicio) {
                crearCampoPregunta();
                paso++;
            } else {
                addNotification('warning', 'La fecha de finalización debe ser posterior o igual a la fecha de inicio.');
            }
        }

        // Crear campo para la pregunta
        function crearCampoPregunta() {
            var div = $('<div class="divQuestion"></div>');
            var pregunta = $('<textarea id="pregunta" name="pregunta[0]" class="question" cols="30" rows="10" placeholder="Pregunta" required></textarea>');
            var cargarImagen = $('<input name="imagenPregunta" type="file" class="imagen">');
            div.append(pregunta);
            div.append(cargarImagen);
            $('#formulario').append(div);
            pregunta.focus();
            pregunta.keydown(function(event) {
                if (event.which === 13) {
                    event.preventDefault();
                    validarPregunta();
                }
            });
        }

        // Validar la pregunta
        function validarPregunta() {
            var pregunta = $('#pregunta').val().trim();
            if (pregunta !== '') {
                crearCamposRespuestas();
                paso++;
            } else {
                addNotification('warning', 'Por favor ingrese una pregunta.');
            }
        }

        // Función para eliminar respuestas adicionales
        $('#formulario').on('click', '.eliminarRespuesta', function() {
            $('.imagen:last').remove(); // Elimina el input de imagen anterior
            $('.respuesta:last').remove(); // Elimina el input de respuesta anterior
            
            if ($('.respuesta').length <= 2) { // Verifica si hay exactamente dos respuestas
                $('.eliminarRespuesta').remove(); // Elimina el botón de eliminar respuesta si hay exactamente dos respuestas
                $('#enviar').remove(); // Elimina el botón de enviar
            }
            
        });

        // Crear campos para las respuestas
        function crearCamposRespuestas() {
            respuestaCount++; 
            var div = $('<div class="divRespuesta"></div>');
            var respuesta = $('<input id="respuesta' + respuestaCount +'" class="field respuesta" type="text" name="respuesta[' + respuestaCount + '][]" class="field respuesta"  placeholder="Respuesta" required>');
            var cargarImagen = $('<input name="imagenRespuesta[' + respuestaCount +']" type="file" class="imagen" accept="image/*" placeholder="Imagen respuesta ' + respuestaCount + '">');
            div.append(respuesta);
            div.append(cargarImagen);
            $('#formulario').append(div);
            respuesta.focus();
            

            respuesta.keydown(function(event) {
                if (event.which === 13 ) {
                    event.preventDefault();
                    var countDivRespuesta = $('.divRespuesta').length;
                    if (countDivRespuesta <= 1){
                        crearCamposRespuestas();
                    }else {
                        var respuestaActualValue = $(this).val().trim(); 
                        if (respuestaActualValue !== '') {
                            agregarBotonAgregarRespuesta();
                            agregarSubmit();                            
                        }
                    }

                
                }
            })
                
        }

        function crearCamposRespuestasExtras() {
            respuestaCount++; 
            var div = $('<div class="divRespuesta"></div>');
            var respuesta = $('<input id="respuesta' + respuestaCount +'" class="field respuesta" type="text" name="respuesta[' + respuestaCount + '][]" class="field respuesta"  placeholder="Respuesta" required>');
            var cargarImagen = $('<input name="imagenRespuesta[' + respuestaCount +']" type="file" class="imagen" accept="image/*" placeholder="Imagen respuesta ' + respuestaCount + '">');
            div.append(respuesta);
            div.append(cargarImagen);
            $('.agregarRespuesta').before(div);
            
            
            respuesta.focus();
            

            respuesta.keydown(function(event) {
                if (event.which === 13) {
                    event.preventDefault();
                        agregarSubmit();                             
                            
                }
            });
            agregarBotonEliminarRespuesta();
                
        }

        function agregarBotonAgregarRespuesta() {
            if ($('.agregarRespuesta').length === 0) {
                var agregarRespuesta = $('<button class="agregarRespuesta">Agregar Respuesta</button>');
                $('#formulario').append(agregarRespuesta);
                
            }
        }

        function agregarSubmit(){
            if ($('#enviar').length === 0) {
                var enviar = $('<input id="enviar" type="submit" value="Enviar">'); 
                    $('#formulario').append(enviar); 
            }
        }

        
        $('#formulario').on('click', '.agregarRespuesta', function() { 
            crearCamposRespuestasExtras();
            $('#enviar').remove();
            
        });

        function agregarBotonEliminarRespuesta() {
            if ($('.eliminarRespuesta').length === 0) {
                var eliminarRespuesta = $('<button class="eliminarRespuesta">Eliminar respuesta</button>');
                $('#formulario').append(eliminarRespuesta);
            }
        }

        
            
        $('#formulario').on('submit', function(event) {
            var respuestaActualValue = $('.respuesta').last().val().trim();
            if (respuestaActualValue === '') {
                event.preventDefault();
                addNotification('warning', 'Por favor ingrese la respuesta.');
            }
        }); 

        $(document).on('input', '#nombreEncuesta', function() {
            if (!$(this).val()) {
                $('#nombreEncuesta').val('');
                $('label').remove();
                $('#fechaInicio').remove();
                $('#fechaFin').remove();
                $('.divQuestion').remove(); 
                $('.divRespuesta').remove();
                $('.agregarRespuesta').remove();
                $('.eliminarRespuesta').remove();
                $('#enviar').remove();
                paso = 1
            }
        });

        $(document).on('change', '#fechaInicio', function() {
            if (!$(this).val()) {
                
                $('label[for="fechaFin"]').remove();
                $('#fechaFin').remove();
                $('.divQuestion').remove(); 
                $('.divRespuesta').remove();
                $('.agregarRespuesta').remove();
                $('.eliminarRespuesta').remove();
                $('#enviar').remove();
                paso = 2
            }
        });

        $(document).on('change', '#fechaFin', function() {
            if (!$(this).val()) {
                $('.divQuestion').remove(); 
                $('.divRespuesta').remove();
                $('.agregarRespuesta').remove();
                $('.eliminarRespuesta').remove();
                $('#enviar').remove();
                paso = 3
            }
        });

        $(document).on('input', '#pregunta', function() {
            if (!$(this).val()) {
                $('#pregunta').val(''); 
                $('.divRespuesta').remove();
                $('.agregarRespuesta').remove();
                $('.eliminarRespuesta').remove();
                $('#enviar').remove();
                paso = 4
            }
        });

        $(document).on('input', '.respuesta', function() {
        if (!$(this).val()) {
            $(this).parent().nextAll('.divRespuesta').remove();
            $(this).parent().nextAll('.agregarRespuesta').remove();
            $(this).parent().nextAll('.eliminarRespuesta').remove();
            $('#enviar').remove();
            paso = 5;
        }
        
    });

        crearCampoNombre();


    });

        
    </script>

    <ul id="notification-container"></ul>
    <?php
        include("./templates/footer.php");
    ?>

</body>

</html>

