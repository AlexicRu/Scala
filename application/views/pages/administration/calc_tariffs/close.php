<div class="jsGrid references_sources_jsGrid"></div>

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

    function referenceSourcesTubeNameChange(btn, tubeId)
    {
        var block = btn.closest('.reference_sources_tube_name_block');
        var name = block.find('input').val();

        if (name == '') {
            message(0, 'Заполните наименование');
            return false;
        }

        $.post('/references/tube_name_edit', {tube_id:tubeId, name:name}, function (data) {
            if (data.success) {
                message(1, 'Наименование изменено');
                block.find('.reference_sources_tube_name').text(name);
                block.find('[toggle]').click();
            } else {
                message(0, data.data);
            }
        });
    }

    var isAjax = false;
    function referenceSourcesCardListLoadStart(btn, tubeId)
    {
        if (isAjax) {
            return false;
        }
        isAjax = true;

        btn.text('Идет загрузка...');

        $.post('/references/card_list_load', {tube_id: tubeId}, function (data) {
            if (data.success) {

            } else {
                message(0, data.data);
            }

            isAjax = false;
        });
    }

    function connect1cPayments_toggleSelectedItems(btn)
    {
        var tbl = btn.closest('.jsGrid');

        tbl.find('[type=checkbox].add_element').prop('checked', btn.prop('checked')).trigger('change');
    }

    function connect1cPayments_drawTable(rows)
    {
        var grid = $(".references_sources_jsGrid");

        grid.jsGrid({
            width: '100%',
            sorting: true,

            onRefreshed: function () {
                $('[type=checkbox]').each(function () {
                    renderCheckbox($(this));
                });
            },

            data: rows,

            fields: [
                {
                    headerTemplate: function() {
                        return $("<input>").attr("type", "checkbox").prop('checked', true)
                            .on("change", function () {
                                connect1cPayments_toggleSelectedItems($(this));
                            });
                    },
                    itemTemplate: function(_, item) {
                        if(item.CAN_ADD == 1) {
                            return $("<input class='add_element'>").attr("type", "checkbox")
                                .prop("checked", true)
                                .data("contract_id", item.CONTRACT_ID)
                                .data("num", item.ORDER_NUM)
                                .data("date", item.ORDER_DATE)
                                .data("value", item.SUMPAY * (item.OPERATION == 50 ? 1 : -1))
                                .data("comment", item.COMMENT)
                                ;
                        }else{
                            return '';
                        }
                    },
                    sorting: false,
                    align: 'center',
                    width: 40
                },
                { name: "CURRENT_STATE", type: "text", title: 'Наименование источника', width:200},
                { name: "SYS_TEMPLATE", type: "text", title: 'Поставщик - договор', width:'auto'},
                { name: "TUBE_NAME", type: "text", title: 'Закрытый период', width:200},
                { name: "CARDS_BLOCK", type: "text", title: 'Текущий статус', width:200}
            ]
        });
    }
</script>