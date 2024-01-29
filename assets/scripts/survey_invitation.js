$(function() {
    checkAndNotify('success', 'success');
    checkAndNotify('error', 'error');

    var sendMailsForm = $("#sendMailsForm");
    var submitButton = $("#checkEmails");

    submitButton.click(function () {
        var textArea = $("#emails");
        var emails = textArea.val();
        if (emails.indexOf('@') !== -1 && emails.indexOf('\n') === -1) {
            addNotification('warning', 'Separa cada email usando intro.');
        } else {
            sendMailsForm.submit();
        }
    });
});



