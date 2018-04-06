$(function(){
    $('.hamburger a.menu-toggle').on('click', function(){
        $('.wrapper').toggleClass('menu_collapsed');
    });

    $('.hamburger a.clients-toggle').on('click', function(){
        $('.clients-toggle').toggleClass('active');
    });

    $(document).on('click', "[tab]", function(){
        var t = $(this);
        var block = t.closest('.tabs_switcher');
        $(
            ' > div > [tab_content],' +
            ' > div > [tab],' +
            ' > div > .scroll > [tab],' +
            ' > div > .scroll > .ajax_pagination_out > .ajax_pagination > [tab]'
            , block).removeClass('active');
        t.addClass('active');
        $('[tab_content='+ t.attr('tab') +']', block).addClass('active');

        if(t.hasClass('tab_v')) {
            renderScroll(t.closest('.tabs_switcher').find('.scroll'));
        }

        return false;
    });


    var hash = document.location.hash;

    if (hash) {
        var tab = hash.replace('#', '');
        if (tab.length){
            $('.tab[tab="'+ tab +'"]').click();
        }
    }

    $(document).on('click', "[toggle]", function(){
        $("[toggle_block='"+ $(this).attr('toggle') +"']").toggle();
    });

    $(document).on('click', '.filter_toggle', function () {
        var t = $(this);
        t.closest('.filter_outer').find('.filter_block').slideToggle();
        t.toggleClass('active');
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

    $(document).on('change', '.switch_block [type=checkbox].switch', function(){
        var t = $(this);
        var switchBlock = t.closest('.switch_block');

        if(t.prop('checked') == true){
            switchBlock.find('input, select, textarea').prop('disabled', false);
            switchBlock.find('.sb_content').removeClass('sb_disabled');
            switchBlock.find('.checkbox_inner').removeClass('checkbox_disabled');
            switchBlock.find('.radio_inner').removeClass('radio_disabled');
        }else{
            switchBlock.find('input, select, textarea').prop('disabled', true);
            switchBlock.find('.sb_content').addClass('sb_disabled');
            switchBlock.find('.checkbox_inner').addClass('checkbox_disabled');
            switchBlock.find('.radio_inner').addClass('radio_disabled');
        }
    });

    $('.mark_read').on('click', function () {
        $.post('/messages/make-read', {}, function (data) {
            if(data.success){
                message(1, 'Сообщения отмечены прочитанными');
                $('.notices').fadeOut();
                $('.mail span span').remove();
                $('.unread0').removeClass('unread0');
                setTimeout(function () {
                    $('.notices').remove();
                }, 400);
            }else{
                message(0, 'Ошибка');
            }
        });
        return false;
    });
});

function cardLoad(elem, force)
{
    if($(".tabs_cards [tab_content="+ elem.attr('tab') +"]").text() == '' || force == true){
        $(".tabs_cards [tab_content="+ elem.attr('tab') +"]").empty().addClass('block_loading');

        $.post('/clients/card/' + elem.attr('tab') + '/?contract_id=' + $('[name=contracts_list]').val(), {}, function(data){
            $(".tabs_cards [tab_content="+ elem.attr('tab') +"]").html(data).removeClass('block_loading');
        });
    }
}