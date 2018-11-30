var isGenerating = false;

/**
 * собраем данные и генерим отчет
 *
 * @param btn
 * @returns {boolean}
 */
function generateReport(btn)
{
    if(isGenerating){
        return false;
    }
    isGenerating = true;

    var block = btn.closest('.report_template_block');
    var reportId = block.attr('report');

    var params = {
        'build':        1,
        'report_id':    reportId,
        'period_start': $('[name=period_start]', block).val(),
        'period_end':   $('[name=period_end]', block).val(),
        'format':       $('[format].active', block).attr('format'),
        'additional':   []
    };

    if(!params.format){
        isGenerating = false;
        message(0, 'Выберите формат отчета');
        return false;
    }

    $('.report_additional_params .form_field', block).each(function () {
        var t = $(this);
        var field = $('.custom_field', t);
        var name = field.attr('name');
        var value = false;

        if(field.attr('depend_to')){
            return;
        }

        if(field.attr('type') == 'checkbox'){
            value = field.is(':checked') ? 1 : 0;
        }else if(field.hasClass('combobox_multi')){
            value = getComboboxMultiValue(field);
            if (value.length == 0) {
                value = [-1];
            }
        }else if(field.hasClass('combobox')){

            value = getComboboxValue(field);

            if (!value) {
                value = -1;
            }
        }else{
            value = field.val();
        }

        params.additional.push({name: name, value: value, weight: field.attr('weight')});
    });

    if ($('[name=fix_contract]').length) {
        params.contract_id = $('[name=contracts_list]').val();
    }

    window.open('/reports/generate/?' + $.param(params));

    isGenerating = false;

    return true;
}