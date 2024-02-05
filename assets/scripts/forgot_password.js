
$('#passwordForm').submit(function(e) {
    e.preventDefault(); // Evitar envío del formulario por defecto
    
    var password = $('#password').val();
    var confirmPassword = $('#confirmPassword').val();

    if (password !== confirmPassword) {
        addNotification('warning', '¡Las contraseñas no coinciden!');
        return;
    }

    if (!validatePassword(password)) {
        addNotification('warning', '¡La contraseña debe contener: 8 caracteres mínimo, 1 minúscula, 1 mayúscula, 1 dígito, 1 carácter especial!');
        return;
    }

    // Aquí puedes enviar el formulario si todas las validaciones pasan
    $(this).unbind('submit').submit();
});

function validatePassword(password) {
    // Al menos 8 caracteres
    // Al menos una letra minúscula
    // Al menos una letra mayúscula
    // Al menos un dígito
    // Al menos un carácter especial
    return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()\-_=+\\|[\]{};:'",.<>/?]).{8,}$/.test(password);
}

