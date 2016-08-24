$(function(){
    renderElements();
});

function renderElements()
{
    $("input[type=checkbox]:not(.switch)").each(function(){
        renderCheckbox($(this));
    });
    $("input[type=checkbox].switch").each(function(){
        renderSwitch($(this));
    });
    $("input[type=radio]").each(function(){
        renderRadio($(this));
    });
}

function renderCheckbox(check)
{
    if(check.data('rendered')){
        return false;
    }

    check.hide();

    check.wrap('<span class="checkbox_outer" />');

    var outer = check.closest('.checkbox_outer');

    outer.append('<span class="checkbox_inner" />');

    var inner = outer.find('.checkbox_inner');
    inner.append('<span class="icon-ok" />');

    if(check.is(':checked')){
        inner.addClass('checkbox_checked');
    }

    if(check.is(':disabled')){
        inner.addClass('checkbox_disabled');
    }

    inner.on('click', function(){
        if(inner.hasClass('checkbox_disabled') || inner.closest('label').size()){
            return;
        }

        if(!check.is(':checked')){
            check.prop('checked', true);
        }else{
            check.prop('checked', false);
        }
        check.trigger('change');
    });

    check.on('change', function(){
        if(check.is(':checked')){
            inner.addClass('checkbox_checked');
        }else{
            inner.removeClass('checkbox_checked');
        }
    }).data('rendered', true);
}

function renderRadio(radio)
{
    if(radio.data('rendered')){
        return false;
    }

    if(radio.closest('.radio_line').size()){
        return;
    }

    radio.hide();

    radio.wrap('<span class="radio_outer" />');

    var outer = radio.closest('.radio_outer');

    outer.append('<span class="radio_inner" />');

    var inner = outer.find('.radio_inner');
    inner.append('<span />');

    if(radio.is(':checked')){
        inner.addClass('radio_checked');
    }

    if(radio.is(':disabled')){
        inner.addClass('radio_disabled');
    }

    inner.on('click', function(){
        if(inner.hasClass('radio_disabled') || inner.hasClass('.radio_checked') || inner.closest('label').size()){
            return;
        }

        if(inner.closest('label').size() == 0){
            radio.prop('checked', true);
            radio.trigger('change');
        }
    });

    radio.on('change', function(){
        if(radio.is(':checked')){
            $('input[type=radio][name='+ radio.attr('name') +']').closest('.radio_outer').find('.radio_inner').removeClass('radio_checked');
            inner.addClass('radio_checked');
        }else{
            inner.removeClass('radio_checked');
        }

    }).data('rendered', true);
}

function renderSwitch(check)
{
    if(check.data('rendered')){
        return false;
    }

    check.hide();

    check.wrap('<span class="switch_outer" />');

    var outer = check.closest('.switch_outer');

    outer.append('<span class="switch_inner" />');

    var inner = outer.find('.switch_inner');
    inner.append('<span />');

    if(check.hasClass('switch_big')){
        inner.addClass('switch_big');
    }

    if(check.is(':checked')){
        inner.addClass('switch_checked');
    }

    if(check.is(':disabled')){
        inner.addClass('switch_disabled');
    }

    inner.on('click', function(){
        if(inner.hasClass('switch_disabled') || inner.closest('label').size()){
            return;
        }

        if(!check.is(':checked')){
            check.prop('checked', true);
        }else{
            check.prop('checked', false);
        }
        check.trigger('change');
    });

    check.on('change', function(){
        if(check.is(':checked')){
            inner.addClass('switch_checked');
        }else{
            inner.removeClass('switch_checked');
        }
    }).data('rendered', true);
}