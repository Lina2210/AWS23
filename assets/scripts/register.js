// (function($) {
//     $.generateInput = function(type, name) {
//         // Construir el HTML del componente
//         var html = '<input type="' + type + '" id="' + name + '" name="' + name + '" required>';
//         html += '<button class="next-button">Siguiente</button>';
    
//         // Devolver el HTML generado como objeto jQuery
//         return $(html);
//     };
// }(jQuery));


// $(function() {
//     $(".next-button:last").click(function() {
//         let newInput = $.generateInput('text', 'userName');
//         $("form").append(newInput);
//     })
// })