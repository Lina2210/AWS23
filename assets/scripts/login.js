$(function() {

    // Comprobar si hay un mensaje de error en el Local Storage
    var success = localStorage.getItem('success');
    if (success) {
        addNotification('success', success);
        localStorage.removeItem('success');
    }
});