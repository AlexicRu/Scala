<h1>Список клиентов <?if(Access::allow('clients_client_add')){?><a href="#client_add" class="btn fancy">+ Добавить клиента</a><?}?></h1>
<?
if(!empty($_REQUEST['search'])){?>
    <h3>Результаты поиска</h3>
<?}

if(!empty($clients)){

    foreach($clients as $client){
        ?>
        <div class="block">
            <div class="fr label label_big">ID <?=$client['CLIENT_ID']?></div>

            <?if(!empty($client['contracts'])){?>
                <div class="fr btn" toggle="client<?=$client['CLIENT_ID']?>">
                    <span toggle_block="client<?=$client['CLIENT_ID']?>">Договора</span>
                    <span toggle_block="client<?=$client['CLIENT_ID']?>" class="dn">Свернуть</span>
                </div>
            <?}?>

            <h2><a href="/clients/client/<?=$client['CLIENT_ID']?>"><?=$client['CLIENT_NAME']?></a></h2>
            <?if(!empty($client['LONG_NAME'])){?>
                <h3><?=$client['LONG_NAME']?></h3>
            <?}?>

            <?if(!empty($client['contracts'])){?>
                <div class="table_out dn" toggle_block="client<?=$client['CLIENT_ID']?>">
                    <br>
                    <table class="table">
                        <?foreach($client['contracts'] as $contract){?>
                            <tr>
                                <td><span class="label <?=Status::$statusContractClasses[$contract['CONTRACT_STATE']]?>"><?=Status::$statusContractNames[$contract['CONTRACT_STATE']]?></span></td>
                                <td><?=$contract['CONTRACT_NAME']?></td>
                                <td><span class="gray">Счет:</span> <?=number_format($contract['BALANCE'], 2, ',', ' ')?> <?=Text::RUR?></td>
                                <td><span class="gray">Карты:</span> <?=$contract['ALL_CARDS']?></td>
                            </tr>
                        <?}?>
                    </table>
                </div>
            <?}?>
        </div>
    <?}
}else{?>
    <div class="block">Клиенты не найдены</div>
<?}?>

<?if(Access::allow('clients_client_add')){?>
    <?=$popupClientAdd?>
<?}?>
