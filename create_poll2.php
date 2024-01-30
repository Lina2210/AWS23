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
    var respuestaCount = 0; 

    $('#formulario').submit(function(event) {
        event.preventDefault();
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
            case 5:
                validarRespuesta();
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
            alert('Por favor ingrese un nombre para la encuesta.');
        }
    }

    // Crear campo para la fecha de inicio
    function crearCampoFechaInicio() {
        var label = $('<label for="dateStart">Fecha de apertura</label>');
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
            alert('La fecha de inicio debe ser posterior o igual a la fecha actual.');
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
            alert('La fecha de fin debe ser posterior o igual a la fecha de inicio.');
        }
    }

    // Crear campo para la pregunta
    function crearCampoPregunta() {
        var pregunta = $('<textarea id="pregunta" name="pregunta" class="question" cols="30" rows="10" placeholder="Pregunta" required></textarea>');
        $('#formulario').append(pregunta);
        pregunta.focus();
        pregunta.keydown(function(event) {
            if (event.which === 13 || event.which === 9) {
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
        respuestaCount++; 
        var respuesta = $('<input id="respuesta' + respuestaCount +'" type="text" name="respuesta' + respuestaCount +'" class="field respuesta"  placeholder="Respuesta ' + respuestaCount + '" required>');
        var cargarImagen = $('<input name="imagen' + respuestaCount +'" type="file" class="imagen" accept="image/*" placeholder="Imagen respuesta ' + respuestaCount + '">');
        $('#formulario').append(respuesta);
        $('#formulario').append(cargarImagen);
        respuesta.focus();
        

        respuesta.keydown(function(event) {
            if (event.which === 13 || event.which === 9) {
                event.preventDefault();
                if (respuestaCount <= 1){
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

    function agregarBotonAgregarRespuesta() {
        if ($('.agregar-respuesta').length === 0) {
            var agregarRespuesta = $('<button class="agregar-respuesta">Agregar Respuesta</button>');
            $('#formulario').append(agregarRespuesta);
            
        }
    }

    function agregarSubmit(){
        if ($('#enviar').length === 0) {
            var enviar = $('<input id="enviar" type="submit" value="Enviar">'); 
                $('#formulario').append(enviar); 
        }
    }

    
    $('#formulario').on('click', '.agregar-respuesta', function() { 
        crearCamposRespuestas();
        $('#enviar').remove();
        
    });

    
        
    $('#formulario').on('submit', function(event) {
        var respuestaActualValue = $('.respuesta').last().val().trim();
        if (respuestaActualValue === '') {
            event.preventDefault();
            alert('Por favor ingrese la respuesta.');
        }
    }); 

    crearCampoNombre();


});

    
</script>

<form id="formulario"></form>

</body>

</html>

