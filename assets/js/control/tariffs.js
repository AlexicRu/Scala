var usedConditions = [];

$(function () {
    $(document).on('click', '.ts_remove', function () {
        if(!confirm('Удаляем?')) {
            return;
        }

        var t = $(this);
        var fieldset = t.parent();
        var uidSection;
        var btnAddCondition;

        if(fieldset.hasClass('tsc_item')){
            uidSection = t.closest('[uid_section]');
            btnAddCondition = uidSection.find('.btn_add_condition');
        }else{
            if(fieldset.closest('.section_wrapper')) {
                fieldset = fieldset.closest('.section_wrapper');
            }
        }

        fieldset.remove();

        if(uidSection) {
            btnAddCondition.show();
            checkUsedConditions(uidSection.attr('uid_section'));
        }
    });
});

function onChangeCondition(t) {
    var conditionId = t.val();
    var block = t.closest('.reference_block');

    block.find('.reference_compare[condition_id], .web_form_element').hide();

    block.find('.reference_compare[condition_id='+ conditionId +'], .web_form_element[condition_id='+ conditionId +']').show();

    var section = t.closest('[uid_section]');
    var uidSection = section.attr('uid_section');

    checkUsedConditions(uidSection);
}

function changeCondition(uid, conditionId, compareId, conditionValue, elem)
{
    var block;
    if(elem != undefined) {
        block = $('.reference_block[uid=' + uid + ']', elem);
    } else {
        block = $('.reference_block[uid=' + uid + ']');
    }
    var conditionSelect = block.find('[name=CONDITION_ID]');

    conditionSelect.val(conditionId);
    conditionSelect.trigger('change');

    var compareSelect = block.find('[name=COMPARE_ID][condition_id='+ conditionId +']');
    compareSelect.val(compareId);

    if(conditionValue) {
        var webForm = block.find('.web_form_element[condition_id=' + conditionId + ']');

        setFormFieldValue(webForm.find('.form_field'), conditionValue);
    }
}

function checkUsedConditions(uidSection)
{
    return true;
    var usedConditionsInSection = [];
    var section = $('[uid_section='+ uidSection +']');

    section.find('[name=CONDITION_ID]').each(function () {
        var t = $(this);
        var val = parseInt(t.val());

        usedConditionsInSection.push(val);
    });

    section.find('[name=CONDITION_ID] option:not(:selected)').each(function () {
        var t = $(this);
        var val = parseInt(t.attr('value'));

        if(usedConditionsInSection && usedConditionsInSection.indexOf(val) != -1){
            t.prop('disabled', true);
        } else {
            t.prop('disabled', false);
        }
    });

    usedConditions[uidSection] = usedConditionsInSection;
}

function addSectionCondition(t)
{
    var block = t.closest('[uid_section]');
    var uidSection = block.attr('uid_section');
    var list = block.find('.ts_conditions');

    var tpl = $('<div class="tsc_item line_inner block_loading"><span class="btn btn_small btn_icon btn_red ts_remove"><i class="icon-cancel"></i></span></div>');

    tpl.appendTo(list);

    $.post('/control/get-tariff-reference-tpl', { uid_section: uidSection, used_conditions: usedConditions[uidSection], index: $('.tsc_item', block).length}, function (data) {
        if(data.success){
            tpl.removeClass('block_loading').append(data.data.html);

            changeCondition(data.data.uid, data.data.condition_id, data.data.compare_id, false, tpl);
        }else{
            message(0, 'Доступные условия закончились');
            tpl.remove();
            $('.btn_add_condition', block).hide();
        }
    });
}

function changeParam(uid, discType, discParam)
{
    var block = $('.params_block[uid='+ uid +']');
    var typeSelect = block.find('[name=DISC_TYPE]');

    if(discType) {
        typeSelect.val(discType);

        block.find('[name=DISC_PARAM][disc_type='+ discType +']').val(discParam);
    }
    typeSelect.trigger('change');
}

function onChangeParam(t) {
    var distType = t.val();
    var block = t.closest('.params_block');

    block.find('.disc_param_select[disc_type]').hide();

    block.find('.disc_param_select[disc_type='+ distType +']').show();
}

function addSection(t)
{
    var block = t.closest('.tariffs_block');
    var list = block.find('.t_sections_list');
    var tariffId = block.find('[name=tarif_id]').val();
    var sectionNum = parseInt(block.find('fieldset[section_num]:last').attr('section_num')) + 1;
    if(isNaN(sectionNum)){
        sectionNum = 1;
    }

    var uidSection = tariffId + '_' + sectionNum;

    var tpl = $('<div />');

    tpl.appendTo(list);

    $.post('/control/get-tariff-section-tpl', { uid_section: uidSection, section_num: sectionNum}, function (data) {
        if(data.success){
            tpl.removeClass('block_loading').replaceWith(data.data.html);
        }else{
            message(0, 'Доступные условия закончились');
        }
    });
}

function sectionMove(way, btn)
{
    var section = btn.closest('.section_wrapper');
    var swap;

    if(way == 'up') {
        swap = section.prev();
        section.insertBefore(swap);
    }else{
        swap = section.next();
        section.insertAfter(swap);
    }
}

function deleteTariff(btn)
{
    if (!confirm('Удаляем?')) {
        return false;
    }

    var wrapper = btn.closest('.tariff_wrapper');

    var tariffId = wrapper.find('[name=tarif_id]').val();

    $.post('/control/delete-tariff', {tariff_id: tariffId}, function (data) {
        if (data.success) {
            message(1, 'Тариф удален');
            window.location.reload();
        } else {
            message(0, 'Ошибка удаления тарифа');
        }
    });
}

function saveTariff(btn)
{
    var wrapper = btn.closest('.tariff_wrapper');

    var tariffId = wrapper.find('[name=tarif_id]').val();
    var params = {
        name: wrapper.find('[name=tarif_name]').val(),
        sections: []
    };

    if(params.name == ''){
        message(0, 'Заполните название тарифа');
        return;
    }

    var breakOut = false;

    wrapper.find('.section_wrapper').each(function () {
        var t = $(this);

        var section = {
            conditions:[],
            params:{
                DISC_TYPE: t.find('[name=DISC_TYPE]').val(),
                DISC_PARAM: t.find('[name=DISC_PARAM]:visible').val(),
                DISC_VALUE: t.find('[name=DISC_VALUE]').val(),
                CLOSE_CALCULATION: t.find('[name=CLOSE_CALCULATION]').is(':checked') ? 1 : 0
            }
        };

        if(section.params.DISC_VALUE == ''){
            message(0, 'Заполните значение параметров секции тарифа');
            alarm(t);
            breakOut = true;
            return;
        }

        var conditions = t.find('.tsc_item');

        if(conditions.length == 0){
            message(0, 'Добавьте условия в секцию тарифа');
            alarm(t);
            breakOut = true;
            return;
        }

        conditions.each(function () {
            var _t = $(this);

            var conditionValue = '';

            var formField = _t.find('.form_field:visible');

            if(formField.find('.combobox_multi').length){
                conditionValue = formField.find('[name=combobox_multi_value]').val();
            }else if(formField.find('.combobox').length){
                conditionValue = formField.find('[name=combobox_value]').val();
            }else{
                conditionValue = formField.find('.custom_field').val();
            }

            if(conditionValue == ''){
                message(0, 'Добавьте значение условия секции тарифа');
                alarm(_t);
                breakOut = true;
                return;
            }

            var condition = {
                CONDITION_ID: _t.find('[name=CONDITION_ID]').val(),
                COMPARE_ID: _t.find('[name=COMPARE_ID]:visible').val(),
                CONDITION_VALUE: conditionValue
            };

            section.conditions.push(condition);
        });

        if(breakOut){
            return false;
        }

        params.sections.push(section);
    });

    if(breakOut){
        return false;
    }

    $.post('/control/edit-tariff', {tariff_id: tariffId, params: params}, function (data) {
        if(data.success){
            message(1, 'Тариф успешно сохранен');
        }else{
            message(0, 'Ошибка сохранения тарифа');
        }
    });
}


function loadTariff(tariff, force, version)
{
    var block = $('.tariffs_block[tab_content='+ tariff +']');

    if(block.text() == '' || force == true){
        block.empty().addClass('block_loading');

        if (!version) {
            version = $('[tab='+ tariff +']').attr('version');
        }

        $.post('/control/load-tariff/' + tariff, { version: version }, function(data){
            block.html(data).removeClass('block_loading');
        });
    }
}

function loadTariffVersion(btn)
{
    var wrapper = btn.closest('.tariff_wrapper');
    var select = wrapper.find('[name=tariff_versions]');
    var version = select.val();
    var tariff = wrapper.find('[name=tarif_id]').val();

    loadTariff(tariff, true, version);
}