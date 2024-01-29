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

    var notificationContainer = $('#notification-container');
    console.log(notificationContainer);
    notificationContainer.append(notification);

    if (notificationContainer[0].scrollHeight !== undefined) {
        // Verifica si el scrollHeight está definido, lo que significa que la barra de desplazamiento existe
        notificationContainer.scrollTop(notificationContainer[0].scrollHeight);
    }
}

function removeNotification(type, message) {
    $('#notification-container').find('li').each(function() {
        var notification = $(this);
        if (notification.hasClass(type) && notification.text().includes(message)) {
            notification.remove();
        }
    });
}

function checkAndNotify(key, type) {
    var message = localStorage.getItem(key);
    if (message) {
        addNotification(type, message);
        localStorage.removeItem(key);
    }
}