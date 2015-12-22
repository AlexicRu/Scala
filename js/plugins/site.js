$(function(){
    $('.hamburger a').on('click', function(){
        $('.wrapper').toggleClass('menu_collapsed');
    });

    $(document).on('click', "[tab]", function(){
        var t = $(this);
        var block = t.closest('.tabs_switcher');
        $('[tab_content], [tab]', block).removeClass('active');
        t.addClass('active');
        $('[tab_content='+ t.attr('tab') +']', block).addClass('active');
        return false;
    });

    $(document).on('click', "[toggle]", function(){
        $("[toggle_block='"+ $(this).attr('toggle') +"']").toggle();
    });

    $('.datepicker').each(function(){
        renderDatePicker($(this));
    });

    $(document).on('click', ".btn_toggle", function(){
        var btn = $(this);
        btn.parent().find('.btn').removeClass('active');
        btn.addClass('active');
    });

    $('.fancy').fancybox({
        padding: [0,0,0,0]
    });

    $(document).on('click', '.fancy_close', function(){
        $.fancybox.close();
    });
});

function renderDatePicker(elem)
{
    var options = {
        dateFormat: "dd.mm.yy",
        buttonImage: "/img/icon_calendar.png",
        showOn: "button",
        buttonImageOnly: true,
        changeMonth:true,
        changeYear:true
    };

    if(elem.attr('maxDate') == 1){
        options.maxDate = new Date();
    }

    elem.wrap('<span class="datepicker_out" />');

    elem.datepicker(options);
}

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

function renderScroll(elem, height)
{
    setTimeout(function(){
        if(height == undefined) {
            elem.css('height', elem.parent().height());
        }else{
            if(height < 0){
                height = elem.parent().height() + height;
            }
            elem.css('height', height);
        }

        elem.show();
    }, 500);
}

function cardLoad(elem, force)
{
    if($(".tabs_cards [tab_content="+ elem.attr('tab') +"]").text() == '' || force == true){
        $(".tabs_cards [tab_content="+ elem.attr('tab') +"]").empty().addClass('block_loading');

        $.post('/clients/card/' + elem.attr('tab'), {}, function(data){
            $(".tabs_cards [tab_content="+ elem.attr('tab') +"]").html(data).removeClass('block_loading');
        });
    }
}

function paginationAjax(url, name, callback)
{
    var outer = $('.' + name + '_out');
    var block = $('<div class="' + name + '" />');
    var more = $('<div class="ajax_block_more"><button class="btn btn_small">Загрузить еще...</button></div>');

    outer.append(block);
    outer.append(more);
    outer.data('offset', 0);

    _paginationAjaxLoad(url, outer, block, callback);
    more.on('click', function(){
        _paginationAjaxLoad(url, outer, block, callback);
    });
}
function _paginationAjaxLoad(url, outer, block, callback)
{
    outer.find('.ajax_block_more').fadeOut();

    $.post(url, {offset:outer.data('offset')}, function(data){
        if(data.success){
            callback(data.data.items, block);
            if(data.data.more){
                outer.find('.ajax_block_more').fadeIn();
            }
            outer.data('offset', parseInt(outer.data('offset')) + data.data.items.length);
        }else{
            outer.find('.ajax_block_more').fadeIn().html('<span class="gray">Данные отсутствуют</span>');
        }
    });
}