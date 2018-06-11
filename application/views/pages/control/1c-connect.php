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
        <span tab="payments" class="tab active">Загрузка платежей</span><?if (Access::allow('control_1c-export')) {?><span tab="export" class="tab">Выгрузка в 1С</span><?}?>
    </div>
    <div class="tabs_content tabs_content_no_padding">
        <div tab_content="payments" class="tab_content active">
            <div class="tc_top_line">
                <div class="fr">
                    Формат даты:
                    <select class="select_big" name="date_format">
                        <option value="d.m.Y" selected>дд.мм.гггг</option>
                        <option value="Y-m-d">гггг-мм-дд</option>
                    </select>
                </div>
                <span class="upload_pays_all">Всего строк: <b>0</b></span> &nbsp;&nbsp;&nbsp;
                <span class="upload_pays_old">Из них проведенных: <b>0</b></span> &nbsp;&nbsp;&nbsp;
                <span class="upload_pays_new">К загрузке: <b>0</b></span> &nbsp;&nbsp;&nbsp;
                <span class="upload_pays_error">Ошибки: <b class="red">0</b></span> &nbsp;&nbsp;&nbsp;
            </div>
            <div class="padding__20">
                <small>
                    <i class="gray">- Дата платежа не может быть больше текущей даты</i><br>
                    <i class="gray">- Дата платежа не может быть меньше текущей даты минус 2 месяца</i>
                </small>
                <br><br>
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
            </div>
        </div>
        <?if (Access::allow('control_1c-export')) {?>
        <div tab_content="export" class="tab_content padding__20">
            <? include('1c_connect/1c_export.php') ?>
        </div>
        <?}?>
    </div>
</div>

<script>
    $(function(){
        dropzone = new Dropzone('.connect_1c_payments', {
            url: "/control/upload-pays",
            acceptedFiles: '.txt, .json, .xls, .xlsx, .csv',
            addedfile: function () {
                $('.load_connect1c_payments_btn').prop('disabled', true);

                var grid = $(".connect_1c_payments_jsGrid");

                if ($('.jsgrid-table', grid).length) {
                    grid.jsGrid("destroy");
                }
                grid.empty().addClass(CLASS_LOADING);
            },
            success: function(file, response)
            {
                if(response.data && response.data.rows){
                    connect1cPayments_drawTable(response.data.rows);

                    $('.load_connect1c_payments_btn').prop('disabled', false);
                } else {
                    var grid = $(".connect_1c_payments_jsGrid");
                    grid.removeClass(CLASS_LOADING);
                    grid.html('<div class="center"><i class="gray">Данные отсутствуют</i></div>');
                }

                if(response.data && response.data.summary){
                    $('.upload_pays_all b').text(response.data.summary.all);
                    $('.upload_pays_new b').text(response.data.summary.new);
                    $('.upload_pays_old b').text(response.data.summary.old);
                    $('.upload_pays_error b').text(response.data.summary.error);
                }
            },
            error : function(file, response) {
                var grid = $(".connect_1c_payments_jsGrid");

                grid.removeClass(CLASS_LOADING);

                message(0, response);
            }
        });

        dropzone.on('sending', function (file, xhr, formData) {
            formData.append('date_format', $('[name=date_format]').val());
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

        $.post('/clients/contract-payment-add', {'multi': 1, payments: payments}, function (data) {
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