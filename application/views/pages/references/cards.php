<h1>Список карт</h1>

<pre>
    <?print_r($cardsList)?>
</pre>

<div class="jsGrid references_cards_jsGrid"></div>

<script>
    $(function(){
        var rows = [];
        var row;
        var tpl;

        <?if (!empty($tubesList)) {
        foreach($tubesList as $tube) {?>
        tpl = '<div class="reference_sources_tube_name_block">' +
            '<span toggle_block="referenceSourcesTube<?=$tube['TUBE_ID']?>">' +
            '<span toggle="referenceSourcesTube<?=$tube['TUBE_ID']?>" class="btn btn_small btn_icon"><i class="icon-pen"></i></span> ' +
            '<span class="reference_sources_tube_name"><?=$tube['TUBE_NAME']?></span>'+
            '</span>' +
            '<span toggle_block="referenceSourcesTube<?=$tube['TUBE_ID']?>" style="display:none;">' +
            '<span onclick="referenceSourcesTubeNameChange($(this), <?=$tube['TUBE_ID']?>)" class="btn btn_small btn_icon btn_green"><i class="icon-ok"></i></span>'+
            '<input type="text" value="<?=$tube['TUBE_NAME']?>" class="input_mini">'+
            '</span>' +
            '</div>';

        row = {
            'TUBE_ID'           : '<?=$tube['TUBE_ID']?>',
            'CURRENT_STATE'     : '<?=$tube['CURRENT_STATE']?>',
            'SYS_TEMPLATE'      : '<?=$tube['SYS_TEMPLATE']?>',
            'TUBE_NAME'         : tpl,
            'CARDS_BLOCK'       : '<?=$tube['CARDS_BLOCK']?>',
            'CARDS_CHANGE_LIMIT': '<?=$tube['CARDS_CHANGE_LIMIT']?>',
        };

        <?if ($tube['CARDS_LIST_RECIEVE'] === "0") {?>
        row.CARDS_LIST_RECIEVE = '<button class="btn btn_small" <?=(($tube['STATE_ID'] != 1 || $tube['IS_OWNER'] != 1)? 'disabled' : '')?> onclick="referenceSourcesCardListLoadStart($(this),<?=$tube['TUBE_ID']?>)">Загрузить</button>';
        <?} else if ($tube['CARDS_LIST_RECIEVE'] === "1") {?>
        row.CARDS_LIST_RECIEVE = 'Идет загрузка...';
        <?} else {?>
        row.CARDS_LIST_RECIEVE = 'Не подключено';
        <?}?>

        <?if ($tube['TUBE_ORDER_REPORT'] === "0") {?>
        row.TUBE_ORDER_REPORT = '<button class="btn btn_small" <?=($tube['STATE_ID'] != 1 ? 'disabled' : '')?>>Загрузить</button>';
        <?} else if ($tube['TUBE_ORDER_REPORT'] === "1") {?>
        row.TUBE_ORDER_REPORT = 'Идет загрузка...';
        <?} else {?>
        row.TUBE_ORDER_REPORT = 'Не подключено';
        <?}?>

        rows.push(row);
        <?}
        }?>

        connect1cPayments_drawTable(rows);
    });

    function connect1cPayments_drawTable(rows)
    {
        var grid = $(".references_cards_jsGrid");

        grid.jsGrid({
            width: '100%',
            sorting: true,

            data: rows,

            fields: [
                { name: "TUBE_ID", type: "text", title: 'ID', width:100},
                { name: "CURRENT_STATE", type: "text", title: 'Статус', width:100},
                { name: "SYS_TEMPLATE", type: "text", title: 'Шаблон', width:150},
                { name: "TUBE_NAME", type: "text", title: 'Наименование', width:150},
                { name: "CARDS_BLOCK", type: "text", title: 'Блокировка карт', width:200},
                { name: "CARDS_CHANGE_LIMIT", type: "text", title: 'Изменение лимитов', width:200},
                { name: "CARDS_LIST_RECIEVE", type: "text", title: 'Получение списка карт', width:200},
//                { name: "TUBE_ORDER_REPORT", type: "text", title: 'Загрузка транзакций за период', width:'auto'}
            ]
        });
    }
</script>