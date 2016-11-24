<h1>Отчеты</h1>

<?if(empty($reports)){?>
    <div class="error_block">Нет доступных отчетов</div>
<?}else{?>
    <div class="tabs_vertical_block tabs_switcher tabs_reports">
        <div class="tabs_v">
            <?foreach($reports as $report){?>
                <div class="tab_v" tab="<?=$report['REPORT_ID']?>">
                    <div>
                        <a href="#"><span class="icon-dailes f20"></span> <?=$report['WEB_NAME']?></a>
                    </div>
                </div>
            <?}?>
        </div>
        <div class="tabs_v_content">
            <?foreach($reports as $report){?>
                <div class="tab_v_content" tab_content="<?=$report['REPORT_ID']?>"></div>
            <?}?>
        </div>
    </div>
<?}?>

<script>
    $(function(){
        $(".tabs_reports [tab]").on('click', function(){
            var t = $(this);

            loadReports(t);
        });

        var clicked = false;
        $(".tabs_reports [tab]").each(function(){
            if(clicked){
                return;
            }
            var t = $(this);

            if(!t.attr('style')){
                clicked = true;
                t.click();
            }
        });
    });

    function loadReports(tab, force)
    {
        var tabsBlock = $(".tabs_reports");
        var reportId = tab.attr('tab');
        var tabContent = $("[tab_content="+ reportId +"]", tabsBlock);

        if(tabContent.text() == '' || force == true){
            tabContent.empty().parent().addClass('block_loading');

            $.post('/reports/load_report_template/' + reportId, {}, function(data){
                tabContent.html(data).parent().removeClass('block_loading');
            });
        }
    }
</script>

