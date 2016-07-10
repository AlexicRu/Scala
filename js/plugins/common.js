$(function() {
    $('.message_close').on('click', function () {
        var t = $(this);
        var message = t.closest('.message');

        message.fadeOut();
        setTimeout(function () {
            message.remove();
        }, 500);
    });
});