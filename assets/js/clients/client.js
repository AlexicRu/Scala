$(function(){
    $(".client_edit_btn").on('click', function(){
        var block = $(".edit_client_block");

        var params = {
            NAME:                   $('[name=NAME]', block).val(),
            LONG_NAME:              $('[name=LONG_NAME]', block).val(),
            Y_ADDRESS:              $('[name=Y_ADDRESS]', block).val(),
            F_ADDRESS:              $('[name=F_ADDRESS]', block).val(),
            P_ADDRESS:              $('[name=P_ADDRESS]', block).val(),
            COMMENTS:               $('[name=COMMENTS]', block).val(),
            PHONE:                  $('[name=PHONE]', block).val(),
            EMAIL:                  $('[name=EMAIL]', block).val(),
            INN:                    $('[name=INN]', block).val(),
            KPP:                    $('[name=KPP]', block).val(),
            OGRN:                   $('[name=OGRN]', block).val(),
            OKPO:                   $('[name=OKPO]', block).val(),
            P_BANK:                 $('[name=P_BANK]', block).val(),
            P_BANK_BIK:             $('[name=P_BANK_BIK]', block).val(),
            P_BANK_CORR_ACCOUNT:    $('[name=P_BANK_CORR_ACCOUNT]', block).val(),
            P_BANK_ACCOUNT:         $('[name=P_BANK_ACCOUNT]', block).val(),
            P_BANK_ADDRESS:         $('[name=P_BANK_ADDRESS]', block).val(),
            P_CEO:                  $('[name=P_CEO]', block).val(),
            P_CEO_SHORT:            $('[name=P_CEO_SHORT]', block).val(),
            P_ACCOUNTANT:           $('[name=P_ACCOUNTANT]', block).val(),
            P_ACCOUNTANT_SHORT:     $('[name=P_ACCOUNTANT_SHORT]', block).val(),
        };

        if(
            params.NAME == '' ||
            params.Y_ADDRESS == '' ||
            params.PHONE == ''
        ){
            message(0, 'Заполните обязательные поля');
            return false;
        }

        $.post('/clients/client-edit/' + clientId, { params:params }, function(data){
            if(data.success){
                message(1, 'Клиент обновлен');

                $.each( params, function( key, value ) {
                    var uid = $('[name='+ key +']').closest('[uid]');

                    if(key == 'EMAIL'){
                        $("[uid=" + uid.attr('uid') + "]").not(uid).html("<a href='mailto:"+value+"'>"+ value +"</a>");
                    } else if(key == 'COMMENTS') {
                        $("[uid=" + uid.attr('uid') + "]").not(uid).html(value.replace(/\n/g, "<br>"));
                    } else {
                        $("[uid=" + uid.attr('uid') + "]").not(uid).text(value);
                    }
                });

                $("[toggle='edit_client']:first").click();

            }else{
                message(0, 'Сохранение не удалось');
            }
        });
    });

    loadContract('contract');

    $('[name=contracts_list]').on('change', function(){
        loadContract('contract');
    });

    $(document).on('click', '[ajax_tab]', function(){
        var t = $(this);
        if(t.hasClass('active')){
            return false;
        }
        loadContract(t.attr('ajax_tab'));
    });
});

function loadContract(tab, query, params)
{
    $.fancybox.close();
    $('.ajax_contract_block').empty().addClass('block_loading');
    var contractId = $('[name=contracts_list]').val();

    $.post('/clients/contract/' + contractId, {tab:tab, query:query, params:params}, function(data){
        $('.ajax_contract_block').html(data).removeClass('block_loading');
        $('.ajax_contract_block .datepicker').each(function () {
            renderDatePicker($(this));
        });
    });
}

/**
 * Удаление клиента
 *
 * @param btn
 */
function clientDelete(btn) {
    if (confirm('Вы уверены, что хотите удалить клиента?')) {
        $.post('/clients/client-delete', {client_id: clientId}, function (data) {
            if (data.success) {
                window.location.href = '/clients';
            } else {
                message(0, 'Удаление не удалось')
            }
        });
    }
}