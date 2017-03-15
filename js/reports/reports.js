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
        var name = t.attr('field');

        var field = $('[name='+ name +']', block);
        var value = false;

        if(field.attr('depend_on')){
            return;
        }

        if(field.attr('type') == 'checkbox'){
            value = field.is(':checked') ? 1 : 0;
        }else if(field.hasClass('combobox_multi')){
            //todo
        }else if(field.hasClass('combobox')){
            var dependField = field.attr('depend');

            if(dependField){
                field = $('[name='+ dependField +']', block);
            }

            value = getComboboxValue(field);
        }else{
            value = field.val();
        }

        params.additional.push({name: name, value: value, weight: field.attr('weight')});
    });

    window.open('/reports/generate/?' + $.param(params));

    isGenerating = false;

    return true;
}