$(function() {
    // Crear el elemento main y el formulario
    var main = $('<main></main>');
    var form = $('<form action="register.php" method="post"></form>');
    main.append(form);
    $('body').append(main);

    

    createInputContainer('userName', 'text', 'Nombre', validateLength);

    function createInputContainer(id, type, placeholder, validator, options=[]) {
        var div = $('<div class="input-container"></div>');
        var button = $('<button class="continue-button" disabled>CONTINUAR</button>');

        if (type === 'select') {
            var select = $('<select class="field" id="' + id + '" name="' + id + '" required></select>');

            var initialOption = $('<option selected disabled>País</option>');
            select.append(initialOption);

            options.forEach(function(option) {
                var optionElement = $('<option></option>');
                optionElement.text(option);
                select.append(optionElement);
            });

            // Habilitar el botón "CONTINUAR" solo cuando se selecciona una opción diferente a "País"
            select.on('change', function() {
                button.prop('disabled', $(this).val() === 'País');
            });

            div.append(select);
            setTimeout(function() {
                select.focus();
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

            var input = $('<input class="field" type="number" id="' + id + '" name="' + id + '" placeholder="' + placeholder + '" autocomplete="off" required>');

            div.append(select);
            div.append(input);
            setTimeout(function() {
                input.focus();
            }, 0);
        } else {
            var input = $('<input class="field" type="' + type + '" id="' + id + '" name="' + id + '" placeholder="' + placeholder + '" autocomplete="off" required>');
            div.append(input);
            setTimeout(function() {
                input.focus();
            }, 0);
        }

        

        // Habilitar el boton cuando el usuario introduzca texto
        if (type !== 'select') {
            input.on('keyup', function() {
                button.prop('disabled', !$(this).val());
            });

            // Quitar la clase 'invalid' cuando el campo de entrada recibe el foco
            input.on('focus', function() {
                $(this).parent('.input-container').removeClass('invalid');
            });
        }

        // Validar el campo de entrada cuando se haga clic en el boton
        button.on('click', function(e) {
            e.preventDefault();

            if (id === 'country') {
                var selectedCountry = $(this).prev('.field').val();
                if (validateCountry(selectedCountry, options)) {
                    createInputContainer('city', 'text', 'Ciudad', validateLength);
                } else {
                    $(this).parent('.input-container').addClass('invalid');
                    $(this).prev('.field').blur();
                }
            }
            if (validator($(this).prev('.field').val(), options)) {
                addNotification('success', '¡' + placeholder + ' correcto!');
                $(this).remove();
                $(this).prev('.field').prop('disabled', true);
                // Crear el siguiente div.input-container si el campo de entrada es valido
                switch (id) {
                    case 'userName':
                        createInputContainer('password', 'password', 'Contraseña', validateLength);
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
                    case 'mobile':
                        var submitButton = $('<input type="submit" value="REGISTRARSE">');
                        form.append(submitButton);
                        break;
                }

            } else {
                $(this).parent('.input-container').addClass('invalid');
                $(this).prev('.field').blur();
                addNotification('warning', '¡Campo inválido!');
            }
        });
        div.append(button);
        form.append(div);

        setTimeout(function() {
            $('html, body').animate({
                scrollTop: form.children(':last-child').offset().top
            }, 1000); // Desplaza la página al div en 1 segundo
        }, 0);
    }


    // Funciones de validación
    function validateLength(value) {
        return value.trim().length > 0;
    }

    function validateConfirmPassword(confirmPassword) {
        return confirmPassword.trim() === $('#password').val();
    }

    function validateEmail(email) {
        var regex = /^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/;
        return regex.test(email.trim());
    }

    function validateCountry(country, countryOptions) {
        return countryOptions.includes(country.trim());
    }

    function validatePostalCode(postalCode) {
        return /^\d{5}$/.test(postalCode.trim());
    }

    function validateMobile(mobile) {
        return /^\d{7,15}$/.test(mobile.trim());
    }
});