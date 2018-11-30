<div>
    <div class="fr">
        <span class="btn btn_green btn_reverse" onclick="gridToXls()"><i class="icon-exel1"></i> Выгрузить в Excel</span>
    </div>
    <h1>Список карт</h1>
</div>

<div class="jsGrid references_cards_jsGrid"></div>

<script>
    var db = {
        loadData: function(filter) {
            return $.grep(this.rows, function(row) {
                return (!filter.CARD_ID || row.CARD_ID.toLowerCase().indexOf(filter.CARD_ID.toLowerCase()) > -1)
                    && (!filter.SOURCE_NAME || row.SOURCE_NAME.toLowerCase().indexOf(filter.SOURCE_NAME.toLowerCase()) > -1)
                    && (!filter.SOURCE_STATE || row.SOURCE_STATE == filter.SOURCE_STATE)
                    && (!filter.ISSUE_STATE || row.ISSUE_STATE == filter.ISSUE_STATE)
                    && (!filter.status || row.status == filter.status)
                ;
            });
        },
        rows: [],
        statuses: [
            {'name': ''},
            {'name': 'В работе'},
            {'name': 'Не в работе'}
        ]
    };

    <?if (!empty($cardsList)) {
        foreach($cardsList as $card) {?>
            db.rows.push({
                'CARD_ID'       : '<?=$card['CARD_ID']?>',
                'SOURCE_NAME'   : '<?=$card['SOURCE_NAME']?>',
                'SOURCE_STATE'  : '<?=$card['SOURCE_STATE']?>',
                'ISSUE_STATE'   : '<?=$card['ISSUE_STATE']?>',
                'status'        : <?=($card['ISSUE_ID'] == 0 ? 'true' : 'false')?>,
            });
        <?}
    }?>

    window.db = db;

    var grid = $(".references_cards_jsGrid");

    grid.jsGrid({
        width: '100%',
        sorting: true,
        filtering: true,
        paging: true,
        pageSize: 15,

        controller:db,

        fields: [
            { name: "CARD_ID", type: "text", title: 'Номер карты', width:200},
            { name: "SOURCE_NAME", type: "text", title: 'Имя источника', width:'auto'},
            { name: "SOURCE_STATE", type: "select", title: 'Статус в источнике', width:200, items: db.statuses, valueField: "name", textField: "name" },
            { name: "ISSUE_STATE", type: "text", title: 'Статус выдачи', width:250 },
            { name: "status", type: "checkbox", title: 'Не выдано', width:80}
        ],
        onRefreshed: function(args) {
            $('.jsgrid-grid-body [type=checkbox]').each(function () {
                renderCheckbox($(this));
            });
        }
    });

    grid.jsGrid("search");
    
    function gridToXls()
    {
        var csv = grid.jsGrid("exportData");

        var form = $('<form method="post" action="/index/as-xls" style="display: none" />');
        var textarea = $('<textarea name="csv" />');
        textarea.val(csv);
        textarea.appendTo(form);
        form.appendTo('body');
        form.submit();
        form.remove();
    }
</script>