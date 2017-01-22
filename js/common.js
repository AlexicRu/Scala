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
        buttonImage: "/img/icon_calendar.png",
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

function paginationAjax(url, name, callback, params)
{
    var outer = $('.' + name + '_out');
    var block = $('<div class="' + name + '" />');
    var more = $('<div class="ajax_block_more"><button class="btn btn_small">Загрузить еще...</button></div>');

    outer.append(block);
    outer.append(more);
    outer.data('offset', 0);

    _paginationAjaxLoad(url, outer, block, callback, params);
    more.on('click', function(){
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

    $.post(url, params, function(data){
        if(data.success){
            callback(data.data.items, block, params);
            if(data.data.more){
                outer.find('.ajax_block_more').fadeIn();
            }
            outer.data('offset', parseInt(outer.data('offset')) + data.data.items.length);
        }else{
            outer.find('.ajax_block_more').fadeIn().html('<span class="gray">Данные отсутствуют</span>');
        }
        block.closest('.block_loading').removeClass('block_loading');
    });
}

function setFormFieldValue(field, value) {
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
                field.val(value);
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