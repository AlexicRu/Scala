<h1>Реализация по клиентам</h1>

<div class="as_table as_table__dashboard">
    <div class="col" style="width: 500px">
        <div class="block">
            <b class="f18">Выберите период:</b><br>
            <select name="date_client_month">
                <?for ($i = 1; $i <= 12; $i++) {?>
                    <option value="<?=$i?>" <?=($i == date('n') ? 'selected' : '')?>><?=Date::monthRu($i)?></option>
                <?}?>
            </select>
            <input type="number" class="input_mini" name="date_client_year" value="<?=date('Y')?>">

            <span class="btn btn_green btn_small btn_reverse" onclick="buildRealizationsByClient()">Обновить</span>
        </div>

        <div class="block">
            <h2>Реализация</h2>

            <div class="realization_by_clients"></div>
        </div>

        <div class="block">
            <h2>В разрезе номенклатур (литры)</h2>

            <div id="realization_by_clients_nomenclature_graph" class="graph"></div>
<!--            <div class="realization_by_clients_nomenclature"></div>-->
        </div>

    </div>
    <div class="col">
        <div class="block">
            <h2>Реализация за год</h2>

            <div id="realization_by_clients_graph" class="graph"></div>
        </div>

        <div class="as_table as_table__dashboard">
            <div class="col">
                <div class="block">
                    <h2>Оплаты</h2>

                    <div class="realization_by_clients_payments"></div>
                </div>
            </div>
            <?if (Access::allow('dashboard_get-realization-by-clients-summary')) {?>
            <div class="col">
                <div class="block">
                    <h2>Сводная таблица</h2>

                    <div class="realization_by_clients_summary"></div>
                </div>
            </div>
            <?}?>
        </div>
    </div>
</div>

<script>
    $(function () {
        buildRealizationsByClient();
        buildRealizationByClientsGraph();

        AmCharts.addInitHandler( function ( chart ) {
            // set base values
            var categoryWidth = 30;
            var chartHeight;

            // calculate bottom margin based on number of data points
            if (chart.type == 'serial') {
                chartHeight = categoryWidth * chart.graphs.length / 3;
            } else {
                chartHeight = categoryWidth * chart.dataProvider.length / 2;
            }

            // set the value
            chart.div.style.height = parseInt(chartHeight + 500) + 'px';

        }, ['serial', 'pie'] );
    });

    function buildRealizationsByClient() {
        buildRealizationByClients();
        //buildRealizationByClientsNomenclature();
        buildRealizationByClientsNomenclatureGraph();
        buildRealizationByClientsPayments();
        <?if (Access::allow('dashboard_get-realization-by-clients-summary')) {?>
        buildRealizationByClientsSummary();
        <?}?>
    }

    function getDate()
    {
        var day = '01';
        var month = $('[name=date_client_month]').val();
        var year = $('[name=date_client_year]').val();

        if (month < 10) {
            month = '0' + month;
        }

        return day + '.' + month + '.' + year;
    }

    function buildRealizationByClients()
    {
        var block = $('.realization_by_clients');
        block.empty().addClass(CLASS_LOADING);

        $.post('/dashboard/get-realization-by-clients', {date: getDate()}, function (data) {
            block.removeClass(CLASS_LOADING).html(data)
        })
    }

    function buildRealizationByClientsNomenclature()
    {
        var block = $('.realization_by_clients_nomenclature');
        block.empty().addClass(CLASS_LOADING);

        $.post('/dashboard/get-realization-by-clients-nomenclature', {date: getDate()}, function (data) {
            block.removeClass(CLASS_LOADING).html(data)
        })
    }

    function buildRealizationByClientsPayments()
    {
        var block = $('.realization_by_clients_payments');
        block.empty().addClass(CLASS_LOADING);

        $.post('/dashboard/get-realization-by-clients-payments', {date: getDate()}, function (data) {
            block.removeClass(CLASS_LOADING).html(data)
        })
    }

    <?if (Access::allow('dashboard_get-realization-by-clients-summary')) {?>
    function buildRealizationByClientsSummary()
    {
        var block = $('.realization_by_clients_summary');
        block.empty().addClass(CLASS_LOADING);

        $.post('/dashboard/get-realization-by-clients-summary', {date: getDate()}, function (data) {
            block.removeClass(CLASS_LOADING).html(data)
        })
    }
    <?}?>

    function buildRealizationByClientsGraph()
    {
        var graphBlock = $('#realization_by_clients_graph');
        graphBlock.empty().addClass(CLASS_LOADING);

        $.post('/dashboard/get-realization-by-clients-graph', {}, function (response) {
            graphBlock.removeClass(CLASS_LOADING);

            if (response.data.length == 0) {
                graphBlock.removeClass('graph').html('<div class="center"><i class="gray">Нет данных</i></div>');
                return;
            } else {
                graphBlock.addClass('graph');
            }

            var graphs = [];
            var valueAxes = [];
            var names = [{
                'label': 'Литры',
                'value': 'LITRES',
                'position': 'left',
            }, {
                'label': 'Рубли',
                'value': 'SALE',
                'position': 'right',
            }];
            var colors = palette('mpn65', names.length);

            for(var i in names) {
                var color = '#' + colors.shift();
                graphs.push({
                    "id":"g" + names[i].label,
                    "title": names[i].label,
                    "bullet": "round",
                    "bulletSize": 6,
                    "lineColor": color,
                    "lineThickness": 2,
                    "type": "smoothedLine",
                    "valueField": names[i].value,
                    "valueAxis": 'v' + names[i].value
                });

                valueAxes.push({
                    "id":"v" + names[i].value,
                    "axisColor": color,
                    "axisThickness": 2,
                    "axisAlpha": 1,
                    "position": names[i].position
                })
            }

            var chart = AmCharts.makeChart("realization_by_clients_graph", {
                "hideCredits":true,
                "type": "serial",
                "theme": "light",
                "autoMarginOffset": 20,
                "dataProvider": response.data,
                "valueAxes": valueAxes,
                "numberFormatter": {
                    "precision": -1,
                    "decimalSeparator": ".",
                    "thousandsSeparator": " "
                },
                "legend": {
                    "align": "center",
                    "equalWidths": true,
                    "periodValueText": "[[value]]",
                    "valueAlign": "left",
                    "valueText": "[[value]]",
                    "valueWidth": 100
                },
                "graphs": graphs,
                "valueScrollbar":{
                    "enabled": true,
                    "scrollbarHeight":10
                },
                "chartScrollbar": {
                    "enabled": true,
                    "scrollbarHeight":10
                },
                "chartCursor": {
                    "categoryBalloonDateFormat": "MM.YYYY",
                    "cursorAlpha": 0,
                    "valueLineEnabled":true,
                    "valueLineBalloonEnabled":true,
                    "valueLineAlpha":0.5,
                    "fullWidth":true
                },
                "dataDateFormat": "MM.YYYY",
                "categoryField": "date"
            });
        })
    }

    function buildRealizationByClientsNomenclatureGraph()
    {
        var graphBlock = $('#realization_by_clients_nomenclature_graph');
        graphBlock.empty().addClass(CLASS_LOADING);

        $.post('/dashboard/get-realization-by-clients-nomenclature-graph', {date: getDate()}, function (response) {
            graphBlock.removeClass(CLASS_LOADING);

            if (response.data.length == 0) {
                graphBlock.removeClass('graph').html('<div class="center"><i class="gray">Нет данных</i></div>');
                return;
            } else {
                graphBlock.addClass('graph');
            }

            var chart = AmCharts.makeChart( "realization_by_clients_nomenclature_graph", {
                "hideCredits":true,
                "type": "pie",
                "theme": "light",
                "dataProvider": response.data,
                "valueField": "SERVICE_AMOUNT",
                "titleField": "LONG_DESC",
                "balloonText": "[[title]]<br><span style='font-size:14px'>литры: <b>[[value]]</b></span>",
                "innerRadius": "30%",
                "labelText": "",
                "legend":{
                    "align": 'center',
                    "valueWidth": 100
                },
                "numberFormatter": {
                    "precision": -1,
                    "decimalSeparator": ".",
                    "thousandsSeparator": " "
                },
                "marginBottom": 10,
                "marginTop": 5,
                "pullOutRadius": 0
            } );
        });
    }
</script>