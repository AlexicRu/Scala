$(function(){
    $(".client_edit_btn").on('click', function(){
        var block = $(".edit_client_block");

        var params = {
            NAME:       $('[name=NAME]').val(),
            LONG_NAME:  $('[name=LONG_NAME]').val(),
            Y_ADDRESS:  $('[name=Y_ADDRESS]').val(),
            F_ADDRESS:  $('[name=F_ADDRESS]').val(),
            P_ADDRESS:  $('[name=P_ADDRESS]').val(),
            COMMENTS:   $('[name=COMMENTS]').val(),
            PHONE:      $('[name=PHONE]').val(),
            EMAIL:      $('[name=EMAIL]').val(),
            INN:        $('[name=INN]').val(),
            KPP:        $('[name=KPP]').val(),
            OGRN:       $('[name=OGRN]').val(),
            OKPO:       $('[name=OKPO]').val()
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
                message(0, 'Удаление не удалось.' + (data.messages.length ? '<br>' + data.messages.join('<br>') : ''))
            }
        });
    }
}