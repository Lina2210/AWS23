$(function() {
    checkAndNotify('success', 'success');
    checkAndNotify('error', 'error');

    $(".toggle-response").click(function() {
        var h3Element = $(this).closest("h3");

        if (h3Element.hasAttribute('hidden')) {
            h3Element.show();
        } else {
            h3Element.hide();
        }
    });
});