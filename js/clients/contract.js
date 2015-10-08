$(function(){

    loadContract();

    $('[name=contracts_list]').on('change', function(){
        loadContract();
    });
});

function loadContract()
{
    $('.ajax_contract_block').empty().addClass('block_loading');
    var contractId = $('[name=contracts_list]').val();

    $.post('/clients/contract/' + contractId, {}, function(data){
        $('.ajax_contract_block').html(data).removeClass('block_loading');
    });
}