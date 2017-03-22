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
    $("input[type=text].combobox").each(function(){
        renderComboBox($(this));
    });


    $(document).on('click', function(e){
        var t = $(e.target);

        if(t.closest('.combobox_multi_wrapper').length == 0){
            $('.combobox_multi_wrapper .combobox_multi_result').hide().html('');
        }
        if(t.closest('.combobox_outer').length == 0){
            $('.combobox_outer .combobox_result').hide().html('');
        }
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

var ajaxComboBoxMulti;
function renderComboBoxMulti(combo)
{
    if(combo.data('rendered')){
        return false;
    }

    combo.data('rendered', true);

    var url = combo.attr('url');

    combo.wrap('<div class="combobox_multi_outer" />');

    var outer = combo.closest('.combobox_multi_outer');

    outer.wrap('<div class="combobox_multi_wrapper" />');

    var wrapper = combo.closest('.combobox_multi_wrapper');

    outer.append('<div class="combobox_multi_result" />');
    wrapper.append('<div class="combobox_multi_selected" />');

    var result = outer.find('.combobox_multi_result');
    var selected = wrapper.find('.combobox_multi_selected');

    wrapper.append('<input type="hidden" name="combobox_multi_value">');

    var hiddenValue = wrapper.find('[name=combobox_multi_value]');

    var preLoad = true;

    combo.on('keyup', function () {
        var t = $(this);
        var val = t.val();

        result.hide().html('');

        if(val.length < 1 && preLoad == false){
            return;
        }

        preLoad= false;

        if(ajaxComboBoxMulti){
            ajaxComboBoxMulti.abort();
        }

        ajaxComboBoxMulti = $.post(url, { search:val }, function(data){
            if(data.success){
                for(var i in data.data){
                    var tpl = $('<div class="combobox_multi_result_item" onclick="selectComboBoxMultiResult($(this))"></div>');
                    tpl.attr('value', data.data[i].value);
                    tpl.text(data.data[i].name);

                    if(selected.find('.combobox_multi_selected_item[value='+ data.data[i].value +']').length){
                        tpl.addClass('combobox_multi_result_item_selected');
                    }

                    tpl.appendTo(result);
                }
                result.show();
            }

            ajaxComboBoxMulti = false;
        });
    }).on('focus', function () {
        if (preLoad || hiddenValue.val() == '') {
            preLoad = true;
            combo.trigger('keyup');
        }
    });
}

function selectComboBoxMultiResult(item)
{
    item.toggleClass('combobox_multi_result_item_selected');

    var value = item.attr('value');

    var wrapper = item.closest('.combobox_multi_wrapper');
    var selected = wrapper.find('.combobox_multi_selected');

    var selectedItem = selected.find('.combobox_multi_selected_item[value='+ value +']');

    if(selectedItem.length){
        uncheckComboBoxMultiItem(selectedItem);
        return;
    }

    renderComboBoxMultiSelectedItem(value, item.text(), wrapper);
}

function renderComboBoxMultiSelectedItem(value, text, wrapper)
{
    var selected = wrapper.find('.combobox_multi_selected');
    var hiddenValue = wrapper.find('[name=combobox_multi_value]');

    var tpl = $('<div class="combobox_multi_selected_item"><span class="combobox_multi_selected_item_name" /><span class="combobox_multi_selected_item_close" onclick="uncheckComboBoxMultiItem($(this))">×</span></div>');

    tpl.find('.combobox_multi_selected_item_name').text(text);
    tpl.attr('value', value);

    selected.append(tpl);

    var values = hiddenValue.val() != '' ? hiddenValue.val().split(',') : [];
    values.push(value);

    hiddenValue.val(values.join(','));
}

function uncheckComboBoxMultiItem(item)
{
    var wrapper = item.closest('.combobox_multi_wrapper');
    var selected = wrapper.find('.combobox_multi_selected');
    var hiddenValue = wrapper.find('[name=combobox_multi_value]');

    item.closest('.combobox_multi_selected_item').remove();

    var values = [];

    selected.find('.combobox_multi_selected_item').each(function () {
        values.push($(this).attr('value'));
    });

    hiddenValue.val(values.join(','));
}

var ajaxComboBox;
function renderComboBox(combo, params)
{
    if(combo.data('rendered')){
        return false;
    }

    combo.data('rendered', true);

    var url = combo.attr('url');

    combo.wrap('<div class="combobox_outer" />');

    var outer = combo.closest('.combobox_outer');

    outer.append('<div class="combobox_result" />');

    var result = outer.find('.combobox_result');

    outer.append('<input type="hidden" name="combobox_value">');

    var hiddenValue = outer.find('[name=combobox_value]');

    var preLoad = true;

    combo.on('keyup', function () {
        if(params && params['depend']){
            var dependCombo = $('[name=' + params['depend'] + ']');
            setComboboxValue(dependCombo, false);
        }

        var t = $(this);
        var val = t.val();

        result.hide().html('');

        if(val.length < 1 && preLoad == false){
            hiddenValue.val('');
            return;
        }

        preLoad= false;

        if(ajaxComboBox){
            ajaxComboBox.abort();
        }

        var postParams = { search:val };

        if(params && params['depend_on']){
            var value = getComboboxValue($('[name="'+ params['depend_on']['field'] + '"]'));

            if(value == ''){
                return;
            }

            postParams[params['depend_on']['name']] = value;
        }

        hiddenValue.val('');

        ajaxComboBox = $.post(url, postParams, function(data){
            if(data.success){
                for(var i in data.data){
                    var tpl = $('<div class="combobox_result_item" onclick="selectComboBoxResult($(this))"></div>');
                    tpl.attr('value', data.data[i].value);
                    tpl.text(data.data[i].name);

                    if(hiddenValue.val() == data.data[i].value){
                        tpl.addClass('combobox_result_item_selected');
                    }

                    tpl.appendTo(result);
                }
                result.show();
            }

            ajaxComboBox = false;
        });
    }).on('focus', function () {
        if (preLoad || hiddenValue.val() == '') {
            preLoad = true;
            combo.trigger('keyup');
        }
    }).on('blur', function () {
        var t = $(this);

        if(t.val() == ''){
            hiddenValue.val('');
        }
    });
}

function selectComboBoxResult(item)
{
    item.toggleClass('combobox_result_item_selected');

    var value = item.attr('value');
    var outer = item.closest('.combobox_outer');
    var combo = outer.find('.combobox');
    var hiddenValue = outer.find('[name=combobox_value]');

    combo.val(item.text());
    hiddenValue.val(value);

    $('.combobox_result', outer).hide().html('');
}

function setComboboxValue(combo, value)
{
    var outer = combo.closest('.combobox_outer');
    var hiddenValue = outer.find('[name=combobox_value]');

    if(!value || value == ''){
        combo.val('');
        hiddenValue.val('');
    }else{
        $.post(combo.attr('url'), {ids: value}, function (data) {
            if (data.success) {
                combo.val(data.data[0].name);
                hiddenValue.val(data.data[0].value);
            }
        });
    }
}

function getComboboxValue(combo)
{
    var outer = combo.closest('.combobox_outer');
    var hiddenValue = outer.find('[name=combobox_value]');

    return hiddenValue.val();
}

function setComboboxMultiValue(combo, value)
{
    var wrapper = combo.closest('.combobox_multi_wrapper');

    var list = value ? value.split(',') : [];

    for(var i in list){
        $.post(combo.attr('url'), { ids:list[i] }, function(data){
            if(data.success){
                for(var i in data.data){
                    renderComboBoxMultiSelectedItem(data.data[0].value, data.data[0].name, wrapper);
                }
            }
        });
    }
}