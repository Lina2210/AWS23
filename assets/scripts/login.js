$(function() {
    checkAndNotify('success', 'success');
    checkAndNotify('error', 'error');
});

function checkTerms(username, mail, userId) {
    var termsForm = $("#terms-form");
    var checkbox = $('input[type="checkbox"]');
    var acceptButton = $("#accept");
    var declineButton = $("#decline");

    checkbox.change(function() {
        if (checkbox.prop('checked')) {
            acceptButton.prop("disabled", false);

        } else {
            acceptButton.prop("disabled", true);
        }
    });
    
    acceptButton.click(function () {
        var inputElementName = $("<input type='hidden' name='username'>").val(username);
        var inputElementMail = $("<input type='hidden' name='mail'>").val(mail);
        var inputElementUserId = $("<input type='hidden' name='user_id'>").val(userId);

        termsForm.append(inputElementName);
        termsForm.append(inputElementMail);
        termsForm.append(inputElementUserId);

        termsForm.submit();
    });

    declineButton.click(function () {
        localStorage.setItem('error', 'Debes aceptar los términos y condiciones para poder iniciar sesión.');
        window.location.href = 'login.php';
    });
}

function openPopup(popupId) {
    $('#' + popupId).css('display', 'flex');
}

function closePopup(popupId) {
    $('#' + popupId).css('display', 'none');
}
