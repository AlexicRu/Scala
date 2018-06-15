<h1>Реализация по дистрибьюторам</h1>

<div class="as_table as_table__dashboard">
    <div class="col" style="width: 500px">
        <div class="block">
            <b class="f18">Выберите период:</b><br>
            <select name="date_agent_month">
                <?for ($i = 1; $i <= 12; $i++) {?>
                    <option value="<?=$i?>" <?=($i == date('n') ? 'selected' : '')?>><?=Date::monthRu($i)?></option>
                <?}?>
            </select>
            <input type="number" class="input_mini" name="date_agent_year" value="<?=date('Y')?>">

            <span class="btn btn_green btn_small btn_reverse" onclick="buildRealizationsByAgents()">Обновить</span>
        </div>

        <div class="block">
            <h2>Реализация</h2>

            <div class="realization_by_agents"></div>
        </div>

        <div class="block">
            <h2>В разрезе номенклатур</h2>

            <div class="realization_by_agents_nomenclature"></div>
        </div>
    </div>
    <div class="col">
        <div class="block">
            <h2>Реализация за год</h2>

            <div id="realization_by_agents_graph" class="graph"></div>
        </div>

        <div class="block">
            <h2>Средняя скидка по дистрибьюторам</h2>
            <div id="realization_by_agents_avg_discount_graph" class="graph"></div>
        </div>
    </div>
</div>

<script>
    var chart;
    $(function () {
        var datepicker = $('.datepicker');
        renderDatePicker(datepicker);
        datepicker.datepicker("setDate", '<?=date('01.m.Y')?>');

        buildRealizationsByAgents();
        buildRealizationByAgentsGraph();
        buildRealizationByAgentsAvgDiscountGraph();

        AmCharts.addInitHandler( function ( chart ) {
            // set base values
            var categoryWidth = 30;

            // calculate bottom margin based on number of data points
            var chartHeight = categoryWidth * chart.graphs.length / 3;

            // set the value
            chart.div.style.height = parseInt(chartHeight + 500) + 'px';

        }, ['serial'] );
    });

    function getDate()
    {
        var day = '01';
        var month = $('[name=date_agent_month]').val();
        var year = $('[name=date_agent_year]').val();

        if (month < 10) {
            month = '0' + month;
        }

        return day + '.' + month + '.' + year;
    }

    function buildRealizationsByAgents() {
        buildRealizationByAgents();
        buildRealizationByAgentsNomenclature();
    }

    function buildRealizationByAgents()
    {
        var block = $('.realization_by_agents');
        block.empty().addClass(CLASS_LOADING);

        $.post('/dashboard/get-realization-by-agents', {date: getDate()}, function (data) {
            block.removeClass(CLASS_LOADING).html(data)
        })
    }

    function buildRealizationByAgentsNomenclature()
    {
        var block = $('.realization_by_agents_nomenclature');
        block.empty().addClass(CLASS_LOADING);

        $.post('/dashboard/get-realization-by-agents-nomenclature', {date: getDate()}, function (data) {
            block.removeClass(CLASS_LOADING).html(data)
        })
    }

    function buildRealizationByAgentsGraph()
    {
        var graphBlock = $('#realization_by_agents_graph');
        graphBlock.empty().addClass(CLASS_LOADING);

        $.post('/dashboard/get-realization-by-agents-graph', {}, function (response) {
            graphBlock.removeClass(CLASS_LOADING);

            if (response.data.data.length == 0) {
                graphBlock.removeClass('graph').html('<div class="center"><i class="gray">Нет данных</i></div>');
                return;
            }

            var colors = palette('mpn65', response.data.agents.length);

            var graphs = [];

            for(var i in response.data.agents) {
                var graph = {
                    "id":"g" + response.data.agents[i].agent_id,
                    "title": response.data.agents[i].label,
                    "bullet": "round",
                    "bulletSize": 6,
                    "lineColor": "#" + colors.shift(),
                    "lineThickness": 2,
                    "type": "smoothedLine",
                    "valueField": "agent" + response.data.agents[i].agent_id
                };
                graphs.push(graph);
            }

            chart = AmCharts.makeChart("realization_by_agents_graph", {
                "hideCredits":true,
                "type": "serial",
                "theme": "light",
                "autoMarginOffset": 20,
                "dataProvider": response.data.data,
                "numberFormatter": {
                    "precision": -1,
                    "decimalSeparator": ".",
                    "thousandsSeparator": " "
                },
                "valueAxes": [{
                    "axisAlpha": 0,
                    "position": "left"
                }],
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

    function buildRealizationByAgentsAvgDiscountGraph()
    {
        var graphBlock = $('#realization_by_agents_avg_discount_graph');
        graphBlock.empty().addClass(CLASS_LOADING);

        $.post('/dashboard/get-realization-by-agents-avg-discount-graph', {}, function (response) {
            graphBlock.removeClass(CLASS_LOADING);

            if (response.data.data.length == 0) {
                graphBlock.removeClass('graph').html('<div class="center"><i class="gray">Нет данных</i></div>');
                return;
            }

            var colors = palette('mpn65', response.data.agents.length);

            var graphs = [];

            for(var i in response.data.agents) {
                var graph = {
                    "id":"g" + response.data.agents[i].agent_id,
                    "title": response.data.agents[i].label,
                    "bullet": "round",
                    "bulletSize": 6,
                    "lineColor": "#" + colors.shift(),
                    "lineThickness": 2,
                    "type": "smoothedLine",
                    "valueField": "agent" + response.data.agents[i].agent_id
                };
                graphs.push(graph);
            }

            chart = AmCharts.makeChart("realization_by_agents_avg_discount_graph", {
                "hideCredits":true,
                "type": "serial",
                "theme": "light",
                "autoMarginOffset": 20,
                "dataProvider": response.data.data,
                "numberFormatter": {
                    "precision": -1,
                    "decimalSeparator": ".",
                    "thousandsSeparator": " "
                },
                "valueAxes": [{
                    "axisAlpha": 0,
                    "position": "left"
                }],
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
</script>