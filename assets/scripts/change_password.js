$(function() {
    checkAndNotify('success', 'success');
    checkAndNotify('error', 'error');
});



function openPopup(popupId) {
    $('#' + popupId).css('display', 'flex');
}

function closePopup(popupId) {
    $('#' + popupId).css('display', 'none');
}

function submitFormWithDelay() {      
    // Abre el siguiente popup
    $('#popupSendEmail').css('display', 'flex');
    
    // Agrega un retraso de 4 segundos antes de enviar el formulario
    setTimeout(function() {
        $('form').submit(); // Envía el formulario usando jQuery
    }, 4000); 
        

}        
    
    