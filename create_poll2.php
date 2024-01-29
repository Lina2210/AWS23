<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <link rel="stylesheet" href="./assets/styles/styles.css">
    <script src="./assets/scripts/notifications.js"></script>
    <title>Document</title>
</head>
<body class="createPoll-body">


<script>
$(document).ready(function() {
    // Variable para llevar el seguimiento del paso actual
    var paso = 1;

    // Crear campo para el nombre de la encuesta
    function crearCampoNombre() {
        var nombreEncuesta = $('<input id="nombreEncuesta" name="nombreEncuesta" type="text"  class="field" placeholder="Nombre de la encuesta" requered>');
        $('#formulario').append(nombreEncuesta);
        nombreEncuesta.focus();
        nombreEncuesta.keypress(function(event) {
            if (event.which === 13) {
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
            alert('Por favor ingrese un nombre para la encuesta.');
        }
    }

    // Crear campo para la fecha de inicio
    function crearCampoFechaInicio() {
        var label = $('<label for="dateStart">Fecha de apertura</label>')
        var fechaInicio = $('<input id="fechaInicio" name="fechaInicio" class="field" type="date"  requered>');
        $('#formulario').append(label);
        $('#formulario').append(fechaInicio);
        fechaInicio.focus();
        fechaInicio.keypress(function(event) {
            if (event.which === 13) {
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
            alert('La fecha de inicio debe ser posterior o igual a la fecha actual.');
        }
    }

    // Crear campo para la fecha final
    function crearCampoFechaFin() {
        var label = $('<label for="fechaFin">Fecha de apertura</label>')
        var fechaFin = $('<input id="fechaFin" name="fechaFin" class="field" type="date" requered>');
        $('#formulario').append(label);
        $('#formulario').append(fechaFin);
        fechaFin.focus();
        fechaFin.keypress(function(event) {
            if (event.which === 13) {
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
            alert('La fecha de fin debe ser posterior o igual a la fecha de inicio.');
        }
    }

    // Crear campo para la pregunta
    function crearCampoPregunta() {
        var pregunta = $('<textarea id="pregunta" name="pregunta" class="question" cols="30" rows="10" placeholder="Pregunta" required></textarea>');
        $('#formulario').append(pregunta);
        pregunta.focus();
        pregunta.keypress(function(event) {
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
            alert('Por favor ingrese una pregunta.');
        }
    }

    // Crear campos para las respuestas
    function crearCamposRespuestas() {
        for (var i = 1; i <= 2; i++) {
            var respuesta = $('<input id="respuesta' + i +'" type="text" name="respuesta' + i +'" class="field"  placeholder="Respuesta ' + i + '">');
            var cargarImagen = $('<input id="respuesta' + i +'" type="file" class="imagen" accept="image/*">');
            $('#formulario').append('<br>');
            $('#formulario').append(respuesta);
            $('#formulario').append(cargarImagen);
        }
        var enviar = $('<input type="submit" value="Enviar">');
        $('#formulario').append('<br>');
        $('#formulario').append(enviar);
    }

    // Evento para manejar el avance al siguiente paso
    $('#formulario').on('keypress', '.respuesta', function(event) {
        if (event.which === 13) {
            event.preventDefault();
            if (paso === 4) {
                // Se ha completado el formulario, podrías enviar los datos
                alert('Formulario completado. Enviar datos...');
            }
        }
    });

    // Iniciar creación del formulario
    crearCampoNombre();
});
</script>

<form id="formulario"></form>

</body>
</html>
</html>

