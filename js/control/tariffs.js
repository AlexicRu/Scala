var usedConditions = [];

$(function () {

});

function onChangeCondition(t) {
    var conditionId = t.val();
    var block = t.closest('.reference_block');

    block.find('.reference_compare[condition_id]').hide();

    block.find('.reference_compare[condition_id='+ conditionId +']').show();

    var section = t.closest('[uid_section]');
    var uidSection = section.attr('uid_section');

    checkUsedConditions(uidSection);
}

function changeCondition(uid, conditionId, compareId)
{
    var block = $('.reference_block[uid='+ uid +']');
    var conditionSelect = block.find('[name=CONDITION_ID]');

    conditionSelect.val(conditionId);
    conditionSelect.trigger('change');

    var compareSelect = block.find('[name=COMPARE_ID][condition_id='+ conditionId +']');

    compareSelect.val(compareId);
}

function checkUsedConditions(uidSection)
{
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

    var tpl = $('<div class="tsc_item line_inner block_loading"><span class="btn btn_small btn_icon btn_red ts_remove"><i class="icon-cancel"></i></span><div class="line_inner_100">Условие:</div></div>');

    tpl.appendTo(list);

    $.post('/control/get_tariff_reference_tpl', { uid_section: uidSection, used_conditions: usedConditions[uidSection]}, function (data) {
        if(data.success){
            tpl.removeClass('block_loading').append(data.data.html);

            changeCondition(data.data.uid, data.data.condition_id, data.data.compare_id);
        }else{
            message(0, 'Доступные условия закончились');
            tpl.remove();
            $('.btn_add_condition', block).hide();
        }
    });
}