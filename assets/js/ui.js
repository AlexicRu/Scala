var SHOW_ALL_VALUE          = -1;
var SHOW_ALL_NAME           = '-- Все --';
var SHOW_NOT_FOUNT_VALUE    = 0;
var SHOW_NOT_FOUND_NAME     = 'Не найдено';

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
    /*$("input[type=text].combobox").each(function(){
        renderComboBox($(this));
    });*/


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
    if(
        check.data('rendered') ||
        check.closest('.jsgrid-filter-row').length
    ){
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
function renderComboBoxMulti(combo, params)
{
    if (params && params != '') {

        for (var i in params) {
            combo.data(i, params[i]);
        }
    }

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
    wrapper.prepend('<div class="combobox_multi_selected" />');

    var result = outer.find('.combobox_multi_result');
    var selected = wrapper.find('.combobox_multi_selected');

    wrapper.append('<input type="hidden" name="combobox_multi_value">');

    var hiddenValue = wrapper.find('[name=combobox_multi_value]');

    combo.on('keyup', function () {
        if(params && params['depend_to']){
            var dependWrapper = outer.parents('.with_depend:last');
            var dependCombo = $('[name=' + params['depend_to'] + ']', dependWrapper);

            if (dependCombo.hasClass('combobox_multi')) {
                resetComboboxMultiValue(dependCombo);
            } else {
                setComboboxValue(dependCombo, false);
            }
        }

        var t = $(this);
        var val = t.val();

        result.hide().html('');

        if(ajaxComboBoxMulti){
            ajaxComboBoxMulti.abort();
        }

        var postParams = {
            params: params,
            search: val
        };

        if(params && params['depend_on']){
            var dependWrapper = outer.parents('.with_depend:last');
            var value = getComboboxValue($('[name="'+ params['depend_on']['name'] + '"]', dependWrapper), true);

            if(value == ''){
                return;
            }

            postParams[params['depend_on']['param']] = value;
        }

        t.addClass('loading');

        ajaxComboBoxMulti = $.post(url, postParams, function(data){
            if(data.success){
                if(params && params.show_all){
                    data.data.unshift({
                        name: SHOW_ALL_NAME,
                        value: SHOW_ALL_VALUE
                    });
                }
            }

            if (data.data && data.data.length == 0) {
                data.data.unshift({
                    name: SHOW_NOT_FOUND_NAME,
                    value: SHOW_NOT_FOUNT_VALUE,
                    disabled: true
                });
            }

            for(var i in data.data){
                var tpl = $('<div class="combobox_multi_result_item"></div>');
                tpl.attr('value', data.data[i].value);
                tpl.text(data.data[i].name);
                if(data.data[i].disabled) {
                    tpl.attr('disabled', true);
                } else {
                    tpl.attr('onclick', 'selectComboBoxMultiResult($(this))');
                }

                if(selected.find('.combobox_multi_selected_item[value='+ data.data[i].value +']').length){
                    tpl.addClass('combobox_multi_result_item_selected');
                }

                tpl.appendTo(result);
            }

            result.show();

            t.removeClass('loading');

            ajaxComboBoxMulti = false;
        });
    }).on('focus', function () {
        combo.trigger('keyup');
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

    if (value == SHOW_ALL_VALUE) {
        //если выбрали все, то все остальное выключаем
        selected.find('.combobox_multi_selected_item[value!="'+ SHOW_ALL_VALUE +'"]').each(function () {
            var t = $(this);
            wrapper.find('.combobox_multi_result_item_selected[value='+ t.attr('value') +']').removeClass('combobox_multi_result_item_selected');
            uncheckComboBoxMultiItem(t);
        });
    } else {
        //если выбрали что-то отличное от все, то выключаемв все
        var itemAll = selected.find('.combobox_multi_selected_item[value='+ SHOW_ALL_VALUE +']');
        if (itemAll.length) {
            wrapper.find('.combobox_multi_result_item_selected[value='+ SHOW_ALL_VALUE +']').removeClass('combobox_multi_result_item_selected');
            uncheckComboBoxMultiItem(itemAll);
        }
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

    checkRenderTo(wrapper.find('.combobox_multi'), {value:value, text:text});
}

function uncheckComboBoxMultiItem(item)
{
    var wrapper = item.closest('.combobox_multi_wrapper');
    var selected = wrapper.find('.combobox_multi_selected');
    var selectedItem = item.closest('.combobox_multi_selected_item');
    var hiddenValue = wrapper.find('[name=combobox_multi_value]');

    checkRenderTo(wrapper.find('.combobox_multi'), {value:selectedItem.attr('value')}, true);

    selectedItem.remove();

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

    if (params && params != '') {
        for (var i in params) {
            combo.data(i, params[i]);
        }
    }

    combo.data('rendered', true);

    var url = combo.attr('url');

    combo.wrap('<div class="combobox_outer" />');

    var outer = combo.closest('.combobox_outer');

    outer.append('<div class="combobox_result" />');

    var result = outer.find('.combobox_result');

    outer.append('<input type="hidden" name="combobox_value">');

    var hiddenValue = outer.find('[name=combobox_value]');

    combo.on('keyup', function () {
        if(params && params['depend_to']){
            var dependWrapper = outer.parents('.with_depend:last');
            var dependCombo = $('[name=' + params['depend_to'] + ']', dependWrapper);

            if (dependCombo.hasClass('combobox_multi')) {
                resetComboboxMultiValue(dependCombo);
            } else {
                setComboboxValue(dependCombo, false);
            }
        }

        var t = $(this);
        var val = t.val();

        result.hide().html('');

        if(ajaxComboBox){
            ajaxComboBox.abort();
        }

        var postParams = { search:val };

        if(params && params['depend_on']){
            var dependWrapper = outer.parents('.with_depend:last');
            var value = getComboboxValue($('[name="'+ params['depend_on']['name'] + '"]', dependWrapper), true);

            if(value == ''){
                return;
            }

            postParams[params['depend_on']['param']] = value;
        }

        hiddenValue.val('');
        checkRenderTo(combo, {}, true);

        t.addClass('loading');

        ajaxComboBox = $.post(url, postParams, function(data){
            if(data.success){
                if(params && params.show_all){
                    data.data.unshift({
                        name: SHOW_ALL_NAME,
                        value: SHOW_ALL_VALUE
                    });
                }
            }

            if (data.data && data.data.length == 0) {
                data.data.unshift({
                    name: SHOW_NOT_FOUND_NAME,
                    value: SHOW_NOT_FOUNT_VALUE,
                    disabled: true
                });
            }

            for(var i in data.data){
                var tpl = $('<div class="combobox_result_item"></div>');
                tpl.attr('value', data.data[i].value);
                tpl.text(data.data[i].name);
                if(data.data[i].disabled) {
                    tpl.attr('disabled', true);
                } else {
                    tpl.attr('onclick', 'selectComboBoxResult($(this))');
                }

                if(hiddenValue.val() == data.data[i].value){
                    tpl.addClass('combobox_result_item_selected');
                }

                tpl.appendTo(result);
            }

            result.show();

            t.removeClass('loading');

            ajaxComboBox = false;
        });
    }).on('focus', function () {
        combo.trigger('keyup');
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
        if (value == SHOW_ALL_VALUE && combo.data('show_all')){
            combo.val(SHOW_ALL_NAME);
            hiddenValue.val(SHOW_ALL_VALUE);

            checkRenderTo(combo, {value:SHOW_ALL_VALUE, text:SHOW_ALL_NAME});
        } else {

            $.post(combo.attr('url'), {ids: value}, function (data) {
                if (data.success) {
                    combo.val(data.data[0].name);
                    hiddenValue.val(data.data[0].value);

                    checkRenderTo(combo, {value:data.data[0].value, text:data.data[0].name});
                }
            });
        }
    }
}

function getComboboxValue(combo, skipDepend)
{
    if(combo.attr('depend_to') && skipDepend != true){
        var outerDepend = combo.parents('.with_depend:last');
        var dependField = combo.attr('depend_to');
        combo = $('[name='+ dependField +']', outerDepend);

        if(combo.hasClass('combobox_multi')){
            return getComboboxMultiValue(combo);
        }
    }

    var outer = combo.closest('.combobox_outer');
    var hiddenValue = outer.find('[name=combobox_value]');

    return hiddenValue.val();
}

function getComboboxMultiValue(combo)
{
    var wrapper = combo.closest('.combobox_multi_wrapper');
    var hiddenField = wrapper.find('[name=combobox_multi_value]');
    var hiddenValue = hiddenField.val();

    if (hiddenValue == ''){
        return [];
    }

    var hiddenArray = hiddenValue.split(',');

    if (hiddenArray.indexOf(SHOW_ALL_VALUE.toString()) != -1) {
        return [SHOW_ALL_VALUE];
    }

    return hiddenArray;
}

function resetComboboxMultiValue(combo)
{
    var wrapper = combo.closest('.combobox_multi_wrapper');
    var selected = wrapper.find('.combobox_multi_selected');
    var hiddenValue = wrapper.find('[name=combobox_multi_value]');

    selected.empty();

    hiddenValue.val('');
}

function setComboboxMultiValue(combo, value)
{
    var wrapper = combo.closest('.combobox_multi_wrapper');

    var list = value ? value.split(',') : [];

    for(var i in list){
        if (parseInt(list[i]) == SHOW_ALL_VALUE && combo.data('show_all')){
            renderComboBoxMultiSelectedItem(SHOW_ALL_VALUE, SHOW_ALL_NAME, wrapper);
            continue;
        }

        $.post(combo.attr('url'), { ids:list[i] }, function(data){
            if(data.success){
                for(var j in data.data){
                    renderComboBoxMultiSelectedItem(data.data[j].value, data.data[j].name, wrapper);
                }
            }
        });
    }
}

/**
 * дополнительный рендер результата в какой-то блок
 */
function checkRenderTo(combo, item, isRemove) {
    var renderTo = combo.data('render_value_to');

    if (!renderTo) {
        return;
    }

    var block = $(renderTo);

    if (combo.hasClass('combobox_multi')) {
        //combobox_multi
        if (isRemove) {
            block.find('[value='+ item.value +']').remove();
        } else {
            var tpl = $('<div class="combobox_multi_selected_item" />');

            tpl.attr('value', item.value).text(item.text);

            tpl.appendTo(block);
        }
    } else if (combo.hasClass('combobox')) {
        //combobox
        if (isRemove) {
            block.find('.combobox_multi_selected_item').remove();
        } else {
            var tpl = $('<div class="combobox_multi_selected_item" />');

            tpl.attr('value', item.value).text(item.text);

            tpl.appendTo(block);
        }
    }
}