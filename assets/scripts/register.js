// $(function() {
//     // Crear el elemento main y el formulario
//     var main = $('<main></main>');
//     var form = $('<form action="register.php" method="post"></form>');
//     main.append(form);
//     $('body').append(main);

//     $.get('ruta_al_archivo_php', function(data) {
//         var countryOptions = JSON.parse(data);
//         createInputContainer('country', 'select', 'Pais', validateCountry, countryOptions);
//     });

//     function createInputContainer(id, type, placeholder, validator, options) {
//         var div = $('<div class="input-container"></div>');
//         var button = $('<button class="next-button" disabled>CONTINUAR</button>');
//         if (type === 'select') {
//             var select = $('<select class="field" id="' + id + '" name="' + id + '" required></select>');
//             options.forEach(function(option) {
//                 var optionElement = $('<option></option>');
//                 optionElement.text(option);
//                 select.append(optionElement);
//             });
//             div.append(select);
//         } else {
//             var input = $('<input class="field" type="' + type + '" id="' + id + '" name="' + id + '" placeholder="' + placeholder + '" autocomplete="off" required>');
//             div.append(input);
//         }
//     }

//     function createInputContainer(id, type, placeholder, validator) {
//         var div = $('<div class="input-container"></div>');
//         var input = $('<input class="field" type="' + type + '" id="' + id + '" name="' + id + '" placeholder="' + placeholder + '"autocomplete="off" required>');
//         var button = $('<button class="next-button" disabled>CONTINUAR</button>');

//         // Habilitar el boton cuando el usuario introduzca texto
//         input.on('keyup', function() {
//             button.prop('disabled', !$(this).val());
//         });

//         // Quitar la clase 'invalid' cuando el campo de entrada recibe el foco
//         input.on('focus', function() {
//             $(this).parent('.input-container').removeClass('invalid');
//         });

//         // Validar el campo de entrada cuando se haga clic en el boton
//         button.on('click', function(e) {
//             e.preventDefault();
//             if (validator(input.val())) {
//                 $(this).remove();
//                 input.prop('disabled', true);
//                 // Crear el siguiente div.input-container si el campo de entrada es valido
//                 switch (id) {
//                     case 'userName':
//                         createInputContainer('password', 'password', 'Contraseña', validatePassword);
//                         break;
//                     case 'password':
//                         createInputContainer('confirmPassword', 'password', 'Confirmar contraseña', validateConfirmPassword);
//                         break;
//                     case 'confirmPassword':
//                         createInputContainer('email', 'email', 'E-Mail', validateEmail);
//                         break;
//                     case 'email':
//                         createInputContainer('mobile', 'number', 'Telefono', validateMobile);
//                         break;
//                     case 'mobile':
//                         createInputContainer('country', 'text', 'Pais', validateCountry);
//                         break;
//                     case 'country':
//                         createInputContainer('city', 'text', 'Ciudad', validateCity);
//                         break;
//                     case 'city':
//                         createInputContainer('postalCode', 'number', 'Código Postal', validatePostalCode);
//                         break;
//                     case 'postalCode':
//                         var submitButton = $('<input type="submit" value="REGISTRARSE">');
//                         form.append(submitButton);
//                         break;
//                 }
//             } else {
//                 input.parent('.input-container').addClass('invalid');
//             }
//         });

//         div.append(input);
//         div.append(button);
//         form.append(div);
//     }

//     // Funciones de validacion
//     function validateUserName(userName) {
//         return userName.length > 0;
//     }

//     function validatePassword(password) {
//         return password.length > 8;
//     }

//     function validateConfirmPassword(confirmPassword) {
//         return confirmPassword === $('#password').val();
//     }

//     function validateEmail(email) {
//         var regex = /^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/;
//         return regex.test(email);
//     }

//     function validateMobile(mobile) {
//         return mobile.length === 9;
//     }

//     function validateCountry(country) {
//         return true;
//     }

//     function validateCity(city) {
//         return true;
//     }

//     function validatePostalCode(postalCode) {
//         return postalCode.length === 5;
//     }
// });








// $(function() {
//     // Crear el elemento main y el formulario
//     var main = $('<main></main>');
//     var form = $('<form action="register.php" method="post"></form>');
//     main.append(form);
//     $('body').append(main);

//     $.get('../../register.php', function(data) {
//         var countryOptions = JSON.parse(data);
//         createInputContainer('country', 'select', 'Pais', validateCountry, countryOptions);
//     });

//     function createInputContainer(id, type, placeholder, validator, options) {
//         var div = $('<div class="input-container"></div>');
//         var divButton = $('<div class="continue-div-button" disabled>CONTINUAR</div>');
//         if (type === 'select') {
//             var select = $('<select class="field" id="' + id + '" name="' + id + '" required></select>');
//             options.forEach(function(option) {
//                 var optionElement = $('<option></option>');
//                 optionElement.text(option);
//                 select.append(optionElement);
//             });
//             div.append(select);
//         } else {
//             var input = $('<input class="field" type="' + type + '" id="' + id + '" name="' + id + '" placeholder="' + placeholder + '" autocomplete="off" required>');
//             div.append(input);
//         }
//         div.append(divButton);
//         form.append(div);

//         // Habilitar el boton cuando el usuario introduzca texto
//         if (type !== 'select') {
//             input.on('keyup', function() {
//                 divButton.prop('disabled', !$(this).val());
//             });

//             // Quitar la clase 'invalid' cuando el campo de entrada recibe el foco
//             input.on('focus', function() {
//                 $(this).parent('.input-container').removeClass('invalid');
//             });
//         }

//         // Validar el campo de entrada cuando se haga clic en el boton
//         divButton.on('click', function() {
//             if (validator($(this).prev('.field').val())) {
//                 $(this).parent('.input-container').removeClass('invalid');
//                 $(this).parent('.input-container').next('.input-container').fadeIn();
//             } else {
//                 $(this).parent('.input-container').addClass('invalid');
//             }
//         });
//     }


//     // Funciones de validacion
//     function validateUserName(userName) {
//         return userName.length > 0;
//     }

//     function validatePassword(password) {
//         return password.length > 8;
//     }

//     function validateConfirmPassword(confirmPassword) {
//         return confirmPassword === $('#password').val();
//     }

//     function validateEmail(email) {
//         var regex = /^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/;
//         return regex.test(email);
//     }

//     function validateCountry(country, countryOptions) {
//         return countryOptions.includes(country);
//     }

//     function validateCity(city) {
//         return true;
//     }

//     function validatePostalCode(postalCode) {
//         return postalCode.length === 5;
//     }

//     function validateMobile(mobile) {
//         return mobile.length === 9;
//     }

//     // Aquí puedes llamar a createInputContainer para otros campos
// });