<h1>Список клиентов <?if(Access::allow('clients_client-add')){?><a href="#client_add" class="btn fancy">+ Добавить клиента</a><?}?></h1>
<?
if(!empty($_REQUEST['search'])){?>
    <h3>Результаты поиска</h3>
<?}?>

<div class="ajax_block_clients_out block_loading">

</div>

<?if(Access::allow('clients_client-add')){?>
    <?=$popupClientAdd?>
<?}?>


<script>
    $(function(){
        paginationAjax('/clients/?search=<?=(!empty($_REQUEST['search']) ? strip_tags($_REQUEST['search']) : '')?>', 'ajax_block_clients', renderAjaxPaginationClients, {show_all_btn: true});
    });

    function renderAjaxPaginationClients(data, block)
    {
        for(var i in data){
            var client = data[i];
            var tpl = $('<div class="block client">' +
                '<div class="fr label label_big">ID ' + client.CLIENT_ID + '</div>' +
                '<h2 class="f24 blue"><a href="/clients/client/' + client.CLIENT_ID + '">' + client.CLIENT_NAME + '</a></h2>' +
            '</div>');

            if (client.LONG_NAME) {
                $('<h3>' + client.LONG_NAME + '</h3>').insertAfter(tpl.find('h2'));
            }

            if (client.contracts.length) {
                $('<div class="fr btn" toggle="client' + client.CLIENT_ID + '">' +
                    '<span toggle_block="client' + client.CLIENT_ID + '">Договоры</span>' +
                    '<span toggle_block="client' + client.CLIENT_ID + '" class="dn">Свернуть</span>' +
                '</div>').insertAfter(tpl.find('.fr'));

                $('<div class="table_out dn" toggle_block="client' + client.CLIENT_ID + '">' +
                    '<br>' +
                    '<table class="table table_small" />' +
                '</div>').appendTo(tpl);

                for (var j in client.contracts) {
                    var contract = client.contracts[j];
                    $('<tr>' +
                        '<td><span class="label ' + contract.contract_state_class + '">' + contract.contract_state_name + '</span></td>' +
                        '<td><a href="/clients/client/' + client.CLIENT_ID + '?contract_id=' + contract.CONTRACT_ID + '">' + contract.CONTRACT_NAME + '</td>' +
                        '<td><span class="gray">Счет:</span> ' + contract.balance_formatted + '</td>' +
                        '<td><span class="gray">Карты:</span> ' + contract.ALL_CARDS + '</td>' +
                    '</tr>').appendTo(tpl.find('table'));
                }
            }

            block.append(tpl);
        }
    }
</script>