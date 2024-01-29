$(function() {
    // Crear el elemento main y el formulario
    var main = $('<main></main>');
    var form = $('<form action="register.php" method="post"></form>');
    main.append(form);
    $('body').append(main);

    form.on('keydown', '.field', function(event) {
        if (event.which === 13 || event.which === 9) {
            console.log(event.which)
            event.preventDefault(); // Evitar el comportamiento predeterminado de la tecla Enter
    
            var campo = $(this);
    
            // Verificar si el campo actual es el campo "mobile"
            if (campo.attr('id') === 'mobile') {
                var submitButton = $('<input type="submit" id="submit" value="REGISTRARSE">');
                form.append(submitButton);
    
                // Enfocar el botón de envío
                submitButton.focus();
            } else {
                // Enfocar el siguiente campo
                campo.closest('.input-container').next().find('.field').focus();
            }
        }
    });

    // Comprobar si hay un mensaje de error en el Local Storage
    checkAndNotify('error', 'error');


    createInputContainer('userName', 'text', 'Nombre', validateLength);

    function createInputContainer(id, type, placeholder, validator, options=[]) {
        var div = $('<div class="input-container"></div>');
        var input;
        
        if (type === 'select') {
            input = $('<select class="field" id="' + id + '" name="' + id + '" required></select>');

            var initialOption = $('<option selected disabled>País</option>');
            input.append(initialOption);

            options.forEach(function(option) {
                var optionElement = $('<option></option>');
                optionElement.text(option);
                input.append(optionElement);
            });

            if (id === 'country') {
                countrySelect = input; // Guarda una referencia al campo de país
            }

            div.append(input);
            setTimeout(function() {
                input.focus();
            }, 0);
        } else if (type === 'tlfn') {
            var select = $('<select class="field" id="' + id + '-prefix" name="' + id + 'Prefix" required></select>');

            // Get the selected country prefix number
            var selectedCountryName = $('#country').val();
            var selectedCountry = countryOptions.find(function(country) {
                return country.country_name === selectedCountryName;
            });
            if (selectedCountry) var prefix = selectedCountry.phone_prefix;
            if (prefix) {
                var initialOption = $('<option selected> +' + prefix + '</option>');
            } else {
                var initialOption = $('<option selected> -- </option>');
            }

            select.append(initialOption);

            options.forEach(function(option) {
                var optionElement = $('<option></option>');
                optionElement.text('+' + option);
                if (option != prefix) select.append(optionElement);
            });

            input = $('<input class="field" type="number" id="' + id + '" name="' + id + '" placeholder="' + placeholder + '" autocomplete="off" required>');
            console.log(input + " input2")
            div.append(select);
            div.append(input);
            
            setTimeout(function() {
                input.focus();
            }, 0);
        } else {
            input = $('<input class="field" type="' + type + '" id="' + id + '" name="' + id + '" placeholder="' + placeholder + '" autocomplete="off" required>');
            div.append(input);
            setTimeout(function() {
                input.focus();
            }, 0);
        }      
        console.log("input: " + input)
        // Validar el campo de entrada cuando se haga clic en el boton
        input.on('keyup', function(e) {
            if (e.which === 13 || e.which === 9) {
                e.preventDefault();
                if (id === 'country') {
                    
                    var selectedCountry = countrySelect.val();
                    if (validateCountry(selectedCountry, options)) {
                        createInputContainer('city', 'text', 'Ciudad', validateLength);
                    } else {
                        $(this).parent('.input-container').addClass('invalid');
                        $(this).prev('.field').blur();
                    }
                }
                if (validator($(this).val(), options)) {
                    addNotification('success', '¡' + placeholder + ' correcto!');
                    
                    // Crear el siguiente div.input-container si el campo de entrada es valido
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
                            createInputContainer('country', 'select', 'País', validateCountry, countryOptions.map(function (country) {
                                return country.country_name;
                            }));
                            break;
                        case 'city':
                            createInputContainer('postalCode', 'number', 'Código Postal', validatePostalCode);
                            break;
                        case 'postalCode':
                            createInputContainer('mobile', 'tlfn', 'Telefono', validateMobile, countryOptions.map(function (country) {
                                return country.phone_prefix;
                            }));
                            break;
                    }

                } else {
                    $(this).parent('.input-container').addClass('invalid');
                    $(this).prev('.field').blur();
                    addNotification('warning', '¡Campo inválido!');
                }
            }
        });
        
        div.append(input);
        form.append(div);

        setTimeout(function() {
            $('html, body').animate({
                scrollTop: form.children(':last-child').offset().top
            }, 1000); // Desplaza la página al div en 1 segundo
        }, 0);
    }

    // Cuando el campo de username está vacío, borra los campos de abajo 
    $(document).on('input', '#userName', function() {
        if (!$(this).val()) {
            $('#username').val('');
            $('#password').val('');
            $('#password').parent().remove();
            $('#confirmPassword').val('');
            $('#confirmPassword').parent().remove();
            $('#email').val('');
            $('#email').parent().remove(); 
            $('#country').val('');
            $('#country').parent().remove();
            $('#city').val('');
            $('#city').parent().remove();
            $('#postalCode').val('');
            $('#postalCode').parent().remove();
            $('#mobile').val('');
            $('#mobile').parent().remove();
            $('#submit').remove();
        }
    });

    // Cuando el campo de PASSWORD está vacío, borra lo de abajo 
    $(document).on('input', '#password', function() {
        if (!$(this).val()) {
            $('#password').val('');
            $('#confirmPassword').val('');
            $('#confirmPassword').parent().remove();
            $('#email').val('');
            $('#email').parent().remove(); 
            $('#country').val('');
            $('#country').parent().remove();
            $('#city').val('');
            $('#city').parent().remove();
            $('#postalCode').val('');
            $('#postalCode').parent().remove();
            $('#mobile').val('');
            $('#mobile').parent().remove();
            $('#submit').remove();
        }
    });

    // Cuando el campo de CONFIRM PASSWORD está vacío, borra lo de abajo 
    $(document).on('input', '#confirmPassword', function() {
        if (!$(this).val()) {
            $('#confirmPassword').val('');
            $('#email').val('');
            $('#email').parent().remove(); 
            $('#country').val('');
            $('#country').parent().remove();
            $('#city').val('');
            $('#city').parent().remove();
            $('#postalCode').val('');
            $('#postalCode').parent().remove();
            $('#mobile').val('');
            $('#mobile').parent().remove();
            $('#submit').remove();
        }
    });


    // Cuando el campo de email está vacío, borra lo de abajo 
    $(document).on('input', '#email', function() {
        if (!$(this).val()) {
            $('#email').val('');
            $('#country').val('');
            $('#country').parent().remove();
            $('#city').val('');
            $('#city').parent().remove();
            $('#postalCode').val('');
            $('#postalCode').parent().remove();
            $('#mobile').val('');
            $('#mobile').parent().remove();
            $('#submit').remove();
        }
    });

    // Cuando el campo  "pais" cambia, borra lo de abajo 
    $(document).on('change', '#country', function() {
        $('#city').val('');
        $('#city').parent().remove();
        $('#postalCode').val('');
        $('#postalCode').parent().remove();
        $('#mobile').val('');
        $('#mobile').parent().remove();
        $('#submit').remove();
        
    });

    // Cuando el campo "Ciudad" está vacío, borra de abajo 
    $(document).on('input', '#city', function() {
        if (!$(this).val()) {
            $('#city').val('');
            $('#postalCode').val('');
            $('#postalCode').parent().remove();
            $('#mobile').val('');
            $('#mobile').parent().remove();
            $('#submit').remove();
        }
    });

    // Cuando el campo "Codigo Postal" está vacío, borra lo de abajo 
    $(document).on('input', '#postalCode', function() {
        if (!$(this).val()) {
            $('#postalCode').val('');
            $('#mobile').val('');
            $('#mobile').parent().remove();
            $('#submit').remove();
        }
    });


     // Cuando el campo TLF está vacío, borra lo de abajo 
     $(document).on('input', '#mobile', function() {
        if (!$(this).val()) {
            $('#mobile').val('');
            $('#submit').remove();           
        }
    });
 



    // Funciones de validación
    function validateLength(value) {
        return value.trim().length > 0;
    }

    function validatePassword(password){
        // Al menos 8 caracteres
        // Al menos una letra minúscula
        // Al menos una letra mayúscula
        // Al menos un dígito
        // Al menos un carácter especial
        return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()\-_=+\\|[\]{};:'",.<>/?]).{8,}$/.test(password);
    }
    
    function validateConfirmPassword(confirmPassword) {
        return confirmPassword.trim() === $('#password').val();
    }

    function validateEmail(email) {
        var regex = /^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/;
        return regex.test(email.trim());
    }

    function validateCountry(country, countryOptions) {
        console.log(country)
        return countryOptions.includes(String(country).trim());
    }

    function validatePostalCode(postalCode) {
        return /^\d{5}$/.test(postalCode.trim());
    }

    function validateMobile(mobile) {
        return /^\d{7,15}$/.test(mobile.trim());
    }
});