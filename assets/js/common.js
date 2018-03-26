var CLASS_LOADING = 'block_loading';

if (typeof Dropzone == 'function') {
    Dropzone.autoDiscover = false;
}

$(function() {
    $('.message_close').on('click', function () {
        var t = $(this);
        var message = t.closest('.message');

        message.fadeOut();
        setTimeout(function () {
            message.remove();
        }, 500);
    });

    $(document).ajaxSuccess(function( event, xhr, settings) {
        if (xhr.responseJSON && xhr.responseJSON.messages && xhr.responseJSON.messages.length) {
            for (var i in xhr.responseJSON.messages) {
                message(xhr.responseJSON.messages[i].type, xhr.responseJSON.messages[i].text);
            }
        }
    });
});

function message(type, text, sticky, onClose)
{
    var header = '';

    if(type == 0 || type == 'error'){
        header = 'Ошибка!';
    }
    if(type == 1 || type == 'success'){
        header = 'Успех!';
    }
    if(type == 2 || type == 'info'){
        header = 'Внимание!';
    }

    if (!sticky) {
        sticky = false;
    }

    var params = { header: header , theme: 'jgrowl-glopro', group: 'jgrowl-group-' + type, sticky: sticky };

    if (typeof onClose == 'function') {
        params.close = onClose;
    }

    $.jGrowl(text, params);
}

function alarm(block) {
    block.addClass('alarm');
    block.addClass('alarm_show');
    setTimeout(function () {
        block.removeClass('alarm_show');
        setTimeout(function () {
            block.removeClass('alarm');
        }, 500);
    }, 2000)
}

function errorStr(str , error)
{
    if(parseInt(error) != error){
        str = str + '.<br>' + error;
    }

    return str;
}

function initWYSIWYG(elem)
{
    elem.trumbowyg({
        autogrow: true,
        lang: 'ru',
        btnsDef: {
            // Customizables dropdowns
            image: {
                dropdown: ['insertImage', 'upload', 'noembed'],
                ico: 'insertImage'
            }
        },
        btns: [
            ['viewHTML'],
            ['undo', 'redo'],
            ['formatting'],
            'btnGrp-design',
            ['superscript', 'subscript'],
            ['link'],
            ['image'],
            'btnGrp-justify',
            'btnGrp-lists',
            ['foreColor', 'backColor'],
            ['horizontalRule'],
            ['removeformat'],
            ['fullscreen']
        ],
        plugins: {
            // Add imagur parameters to upload plugin
            upload: {
                serverPath: 'https://api.imgur.com/3/image',
                fileFieldName: 'image',
                headers: {
                    'Authorization': 'Client-ID 9e57cb1c4791cea'
                },
                urlPropertyName: 'data.link'
            }
        }
    });
}

function renderDatePicker(elem)
{
    if(elem.data('rendered')){
        return false;
    }

    var options = {
        dateFormat: "dd.mm.yy",
        buttonImage: "/assets/img/icon_calendar.png",
        showOn: "button",
        buttonImageOnly: true,
        changeMonth:true,
        changeYear:true,
        yearRange: "2000:2099"
    };

    if(elem.attr('maxDate') == 1){
        options.maxDate = new Date();
    }

    elem.wrap('<span class="datepicker_out" />');

    if(elem.hasClass('input_big')){
        elem.closest('.datepicker_out').addClass('datepicker_big');
    }

    elem.data('rendered', true).datepicker(options);
}

function renderScroll(elem)
{
    setTimeout(function () {
        var block = elem.closest('.tabs_vertical_block');
        var preScrollHeight = block.find('.before_scroll').size() ? block.find('.before_scroll').height() : 0;

        var height = block.find('.tab_v_content.active').outerHeight() - preScrollHeight;

        elem.css('height', height);
    }, 500);
}

function paginationAjaxRefresh(name)
{
    var outer = $('.' + name + '_out');
    var block = $('.' + name);
    outer.data('offset', 0);
    block.empty();

    outer.find('.ajax_block_more .ajax_block_load').click();
}

function paginationAjax(url, name, callback, params)
{
    var outer = $('.' + name + '_out');
    var block = $('<div class="' + name + '" />');
    var btnBlock = $('<div class="ajax_block_more" />');
    var more = $('<button class="btn btn_small ajax_block_load">Загрузить еще...</button>');
    var all = $('<button class="btn btn_small">Загрузить все</button>');

    outer.addClass('ajax_pagination_out');
    block.addClass('ajax_pagination');

    more.appendTo(btnBlock);

    if (params && params.show_all_btn) {
        all.appendTo(btnBlock);
    }

    outer.append(block);
    outer.append(btnBlock);
    outer.data('offset', 0);

    _paginationAjaxLoad(url, outer, block, callback, params);

    more.on('click', function(){
        _paginationAjaxLoad(url, outer, block, callback, params);
    });

    all.on('click', function(){
        params.show_all = true;
        _paginationAjaxLoad(url, outer, block, callback, params);
    });
}

function _paginationAjaxLoad(url, outer, block, callback, params)
{
    if(!params){
        params = {};
    }
    outer.find('.ajax_block_more').fadeOut();
    params.offset = outer.data('offset');

    var onError = false;
    if (params.onError != undefined && typeof params.onError === 'function') {
        onError = params.onError;
        params.onError = false;
    }

    $.post(url, params, function(data){
        if(data.success){
            callback(data.data.items, block, params);

            outer.data('offset', parseInt(outer.data('offset')) + data.data.items.length);

            if(data.data.more){
                //ALL
                if (params.show_all) {
                    _paginationAjaxLoad(url, outer, block, callback, params);
                } else {
                    outer.find('.ajax_block_more').fadeIn();
                }
            }
        }else{
            if (onError) {
                onError(block, params);
            } else {
                outer.find('.ajax_block_more').fadeIn().html('<span class="gray">Данные отсутствуют</span>');
            }
        }
        block.closest('.block_loading').removeClass('block_loading');
    });
}

function setFormFieldValue(field, value)
{
    if (value == '') {
        return;
    }

    var type = field.attr('field');
    var isCombobox = field.find('.combobox').length;
    var isComboboxMulti = field.find('.combobox_multi').length;
    var isCheckbox = field.find('[type=checkbox]').length;

    switch (type) {
        case 'period':
            break;
        default:
            if(isComboboxMulti){
                setComboboxMultiValue(field.find('.combobox_multi'), value);
            }else if(isCombobox){
                setComboboxValue(field.find('.combobox'), value);
            }else if(isCheckbox){
                if(value){
                    field.prop('checked', true);
                }else{
                    field.prop('checked', false);
                }
            }else{
                field.find('.custom_field').val(value);
            }
    }
}

function checkAllRows(t, name)
{
    var block = t.closest('.check_all_block');

    var checked = t.prop('checked');

    block.find('[name='+ name +']').each(function () {
        var _t = $(this);
        if(checked){
            if(!_t.prop('checked')){
                _t.prop('checked', true).trigger('change');
            }
        }else{
            if(_t.prop('checked')){
                _t.prop('checked', false).trigger('change');
            }
        }
    });
}

/**
 * collect one form from one class forms
 *
 * @param form
 * @param className
 * @returns {boolean}
 */
function collectForms(form, className)
{
    var forms = $('form.' + className).not(form);

    var strings = [];

    forms.each(function () {
       var t = $(this);

       strings.push(t.serialize());
    });

    form.append('<input type="hidden" name="other_data">');

    form.find('[name=other_data]').val(strings.join('&'));

    return true;
}


function deleteRow(btn)
{
    if(!confirm('Удаляем?')) {
        return;
    }

    var fieldset = btn.parent();

    fieldset.remove();
}

function checkBtnLoading(btn)
{
    if (btn.find('.icon-loading').length == 1) {
        return true;
    }
    return false;
}

function toggleBtnLoading(btn)
{
    if (checkBtnLoading(btn)) {
        btn.find('.icon-loading').remove();

        var icon = btn.data('icon');

        if (icon) {
            btn.prepend('<i class="'+ icon +'" />');
        }

    } else {
        var icon = btn.find('i');

        if (icon.length == 1) {
            btn.data('icon', icon.attr('class'));
        }

        icon.remove();

        btn.prepend('<i class="icon-loading" />');
    }
}

function number_format( number, decimals, dec_point, thousands_sep ) {	// Format a number with grouped thousands
    //
    // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +	 bugfix by: Michael White (http://crestidg.com)

    var i, j, kw, kd, km;

    var belowZero = '';

    if(number < 0){
        belowZero = '-';
        number *= -1;
    }

    // input sanitation & defaults
    if( isNaN(decimals = Math.abs(decimals)) ){
        decimals = 2;
    }
    if( dec_point == undefined ){
        dec_point = ",";
    }
    if( thousands_sep == undefined ){
        thousands_sep = ".";
    }

    i = parseInt(number = (+number || 0).toFixed(decimals)) + "";

    if( (j = i.length) > 3 ){
        j = j % 3;
    } else{
        j = 0;
    }

    km = (j ? i.substr(0, j) + thousands_sep : "");
    kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
    //kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).slice(2) : "");
    kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "");


    return belowZero + km + kw + kd;
}

/**
 * достаем сырые данные из vue
 *
 * @param data
 * @returns {any}
 */
function vueRawData(data)
{
    return JSON.parse(JSON.stringify(data));
}

function renderTootip()
{
    $('.tooltip').tooltipster({
        theme: 'tooltipster-shadow'
    });
}

var submitFormInAction = false;

/**
 * функция предварительной обработки перед сабмитом формы
 *
 * @param btn
 * @param callback
 * @returns {boolean}
 */
function submitForm(btn, callback)
{
    if (submitFormInAction) {
        return false;
    }

    submitFormInAction = true;

    callback(btn);
}

/**
 * отмена блокировки отправки
 */
function endSubmitForm()
{
    submitFormInAction = false;
}

function collectFoundIds(block)
{
    var list = $('.selected_items_list', block);

    var ids = [];

    $('.sil_item').each(function () {
        ids.push($(this).attr('item_id'));
    });

    return ids;
}

function checkFoundItem(check)
{
    var block = check.closest('.items_list_autocomplete_block');
    var row = check.closest('.item_found_row');
    var list = $('.selected_items_list', block);

    if(check.is(':checked')) {
        //add
        var tpl = $('<div class="sil_item"><span class="sili_close" onclick="uncheckFoundItem($(this))">&times;</span></div>');

        tpl.attr('item_id', row.attr('item_id')).prepend(row.data('item_name'));

        tpl.appendTo(list);
    } else {
        //remove
        list.find('[item_id='+ row.attr('item_id') +']').remove();
    }
}

function uncheckFoundItem(close)
{
    var block = close.closest('.items_list_autocomplete_block');
    var list = $('.selected_items_list', block);

    var item = close.closest('.sil_item');

    var foundRow = $('.item_found_row[item_id='+ item.attr('item_id') +']');

    if (foundRow.length) {
        $('[type=checkbox]', foundRow).click();
    } else {
        list.find('[item_id='+ item.attr('item_id') +']').remove();
    }
}