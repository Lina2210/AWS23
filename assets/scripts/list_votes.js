$(function() {
    checkAndNotify('success', 'success');
    checkAndNotify('error', 'error');

    $(".toggle-response").click(function() {
        var h3Element = $(this).siblings(".answer");
        console.log(h3Element);

        if (h3Element.is(':visible')) {
            h3Element.hide();
        } else {
            h3Element.show();
        }
    });
});