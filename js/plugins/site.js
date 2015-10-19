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
});

function renderDatePicker(elem)
{
    elem.wrap('<span class="datepicker_out" />');
    elem.datepicker({
        dateFormat: "dd.mm.yy",
        buttonImage: "/img/icon_calendar.png",
        showOn: "button",
        buttonImageOnly: true
    });
}