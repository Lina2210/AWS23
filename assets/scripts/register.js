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



$(function() {
    // Crear el elemento main y el formulario
    var main = $('<main></main>');
    var form = $('<form action="register.php" method="post"></form>');
    main.append(form);
    $('body').append(main);

    // Crear el primer div.input-container
    createInputContainer('userName', 'text', 'Nombre', validateUserName);

    function createInputContainer(id, type, placeholder, validator) {
        var div = $('<div class="input-container"></div>');
        var input = $('<input class="field" type="' + type + '" id="' + id + '" name="' + id + '" placeholder="' + placeholder + '" required>');
        var button = $('<button class="next-button" disabled>CONTINUAR</button>');

        // Habilitar el botón cuando el usuario introduzca texto
        input.on('keyup', function() {
            button.prop('disabled', !$(this).val());
        });

        // Validar el campo de entrada cuando se haga clic en el botón
        button.on('click', function(e) {
            e.preventDefault();
            if (validator(input.val())) {
                // Crear el siguiente div.input-container si el campo de entrada es válido
                switch (id) {
                    case 'userName':
                        createInputContainer('password', 'password', 'Contraseña', validatePassword);
                        break;
                    case 'password':
                        createInputContainer('confirmPassword', 'password', 'Confirmar contraseña', validateConfirmPassword);
                        break;
                    case 'confirmPassword':
                        createInputContainer('email', 'email', 'E-Mail', validateEmail);
                        break;
                    case 'email':
                        createInputContainer('mobile', 'number', 'Telefono', validateMobile);
                        break;
                    case 'mobile':
                        createInputContainer('country', 'text', 'Pais', validateCountry);
                        break;
                    case 'country':
                        createInputContainer('city', 'text', 'Ciudad', validateCity);
                        break;
                    case 'city':
                        createInputContainer('postalCode', 'number', 'Código Postal', validatePostalCode);
                        break;
                    case 'postalCode':
                        var submitButton = $('<input type="submit" value="REGISTRARSE">');
                        form.append(submitButton);
                        break;
                }
            }
        });

        div.append(input);
        div.append(button);
        form.append(div);
    }

    // Funciones de validación
    function validateUserName(userName) {
        return userName.length > 0;
    }

    function validatePassword(password) {
        return password.length > 8;
    }

    function validateConfirmPassword(confirmPassword) {
        return confirmPassword === $('#password').val();
    }

    function validateEmail(email) {
        var regex = /^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/;
        return regex.test(email);
    }

    function validateMobile(mobile) {
        return mobile.length === 9;
    }

    function validateCountry(country) {
        return true;
    }

    function validateCity(city) {
        return true;
    }

    function validatePostalCode(postalCode) {
        return postalCode.length === 5;
    }
});