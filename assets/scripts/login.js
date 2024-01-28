$(function() {
    checkAndNotify('success', 'success');
    checkAndNotify('error', 'error');
});

function checkTerms(username, mail) {
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

        termsForm.append(inputElementName);
        termsForm.append(inputElementMail);

        termsForm.submit();
    });

    declineButton.click(function () {
        localStorage.setItem('error', 'Debes aceptar los términos y condiciones para poder iniciar sesión.');
        window.location.href = 'login.php';
    });
}