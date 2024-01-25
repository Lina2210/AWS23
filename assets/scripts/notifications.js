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

    $('#notification-container').scrollTop($('#notification-container')[0].scrollHeight);
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