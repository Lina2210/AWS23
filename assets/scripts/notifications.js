function addNotification(type, message) {
    var notification = $('<li>')
        .addClass('notification ' + type)
        .text(message);

    var closeBtn = $('<span>')
        .addClass('close-btn')
        .text('x')
        .on('click', function() {
            notification.remove();
        });

    notification.append(closeBtn);
    $('#notification-container').append(notification);
}

function removeNotification(type, message) {
    $('#notification-container').find('li').each(function() {
        var notification = $(this);
        if (notification.hasClass(type) && notification.text().includes(message)) {
            notification.remove();
        }
    });
}

// Ejemplo de uso
addNotification('success', '¡Usuario creado con éxito!');
addNotification('error', 'Error al crear el usuario.');