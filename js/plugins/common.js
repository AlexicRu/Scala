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

function message(type, text)
{
    if(type == 0){
        type = 'Ошибка!';
    }
    if(type == 1){
        type = 'Успех!';
    }

    $.jGrowl(text, { header: type });
}

function errorStr(str , error)
{
    if(parseInt(error) != error){
        str = str + '.<br>' + error;
    }

    return str;
}