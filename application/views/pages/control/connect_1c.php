<style>
    .tabs_connect_1c .tbl > div:last-child{
        width: 250px; padding-left: 20px;
    }
    .tabs_connect_1c .jsGrid:not(:empty), .tabs_connect_1c .jsGrid.block_loading{
        margin-top: 30px;
    }
</style>

<h1>Связь с 1с</h1>

<div class="tabs_block tabs_switcher tabs_connect_1c">
    <div class="tabs">
        <span tab="payments" class="tab active">Загрузка платежей</span><?if (Access::allow('view_export_1c_tab')) {?><span tab="export" class="tab">Выгрузка в 1С</span><?}?>
    </div>
    <div class="tabs_content">
        <div tab_content="payments" class="tab_content active">
            <div class="tbl">
                <div>
                    <div class="connect_1c_payments dropzone"></div>
                </div>
                <div>
                    <button disabled onclick="connect1cPayments_addPayments($(this))" class="btn load_connect1c_payments_btn">Загрузить выделенные</button>
                </div>
            </div>

            <div class="jsGrid connect_1c_payments_jsGrid"></div>
        </div>
        <?if (Access::allow('view_export_1c_tab')) {?>
        <div tab_content="export" class="tab_content">
            <?include('connect_1c/export_1c.php')?>
        </div>
        <?}?>
    </div>
</div>

<script>
    $(function(){
        dropzone = new Dropzone('.connect_1c_payments', {
            url: "/control/upload_pays",
            acceptedFiles: '.txt, .json, .xls, .xlsx',
            addedfile: function () {
                $('.load_connect1c_payments_btn').prop('disabled', true);

                var grid = $(".connect_1c_payments_jsGrid");

                if ($('.jsgrid-table', grid).length) {
                    grid.jsGrid("destroy");
                }
                grid.addClass(CLASS_LOADING);
            },
            success: function(file, response)
            {
                if(response.data){
                    connect1cPayments_drawTable(response.data);

                    $('.load_connect1c_payments_btn').prop('disabled', false);
                }
            },
            error : function(file, response) {
                var grid = $(".connect_1c_payments_jsGrid");

                grid.removeClass(CLASS_LOADING);

                message(0, response);
            }
        });
    });

    function connect1cPayments_drawTable(rows)
    {
        var grid = $(".connect_1c_payments_jsGrid");
        grid.removeClass(CLASS_LOADING);
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
                { name: "PAYMENT_STATUS", type: "text", title: 'Статус', width:100},
                { name: "OPERATION_NAME", type: "text", title: 'Действие', width:120},
                { name: "CONTRACT_NAME", type: "text", title: 'Договор', width:120},
                { name: "ORDER_DATE", type: "text", title: 'Дата п/п', width:100},
                { name: "PAYMENT_DATE", type: "text", title: 'Дата оплаты', width:120},
                { name: "SUMPAY", type: "number", title: 'Сумма', width:80},
                { name: "DOC_CURRENCY", type: "text", title: 'Валюта', width:80},
                { name: "ORDER_NUM", type: "number", title: 'Номер п/п', width:100},
                { name: "PURPOSE", type: "text", title: 'Описание', width:'auto'},
                { name: "COMMENT", type: "text", title: 'Комментарий', width:250}
            ]
        });
    }

    function connect1cPayments_toggleSelectedItems(btn)
    {
        var tbl = btn.closest('.jsGrid');

        tbl.find('[type=checkbox].add_element').prop('checked', btn.prop('checked')).trigger('change');
    }

    function connect1cPayments_addPayments(btn)
    {
        btn.prop('disabled', true);

        var tbl = $('.connect_1c_payments_jsGrid');
        var payments = [];
        
        tbl.find('.add_element:checked').each(function () {
            var t = $(this);

            payments.push({
                contract_id: t.data('contract_id'),
                num: t.data('num'),
                date: t.data('date'),
                value: t.data('value'),
                comment: t.data('comment')
            });
        });

        $.post('/clients/contract_payment_add', {'multi': 1, payments: payments}, function (data) {
            if(data.success){
                message(1, 'Платежи успешно добавлены');
                $(".connect_1c_result_block").jsGrid("destroy");
            }else{
                message(0, 'Ошибка добавления платежей');
                btn.prop('disabled', false);
            }
        })
    }
</script>