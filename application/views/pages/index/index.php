<h1>Dashboard</h1>

<canvas id="canvas"></canvas>

<script>
    var config = {
        type: 'line',
        data: {
            labels: ['<?=implode("','", array_keys($data))?>'],
            datasets: [{
                label: 'Payments',
                /*backgroundColor: window.chartColors.red,
                borderColor: window.chartColors.red,*/
                data: [
                    <?=implode(',', $data)?>
                ],
                fill: true,
            }]
        },
        options: {
            responsive: true,
            tooltips: {
                mode: 'index',
                intersect: false,
            },
            hover: {
                mode: 'nearest',
                intersect: true
            }
        }
    };


    $(function () {
        var ctx = document.getElementById("canvas").getContext('2d');
        new Chart(ctx, config);
    });
</script>