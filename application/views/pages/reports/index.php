<script src="/js/reports/reports.js"></script>

<h1>Отчеты</h1>

<?if(empty($reports)){?>
    <div class="error_block">Нет доступных отчетов</div>
<?}else{?>
    <div class="tabs_vertical_block tabs_switcher tabs_reports">
        <div class="tabs_v">
            <?foreach($reports as $reportGroupId => $reportsList){?>
                <div class="tab_v" tab="<?=$reportGroupId?>">
                    <div>
                        <a href="#"><span class="icon-dailes f20"></span> <?=Model_Report::$reportGroups[$reportGroupId]['name']?></a>
                    </div>
                </div>
            <?}?>
        </div>
        <div class="tabs_v_content">
            <?foreach($reports as $reportGroupId => $reportsList){?>
                <div class="tab_v_content" tab_content="<?=$reportGroupId?>">
                    <table>
                        <tr>
                            <td class="gray right" width="150">Отчет:</td>
                            <td>
                                <select class="report_select select_big select_long">
                                    <?foreach($reportsList as $report){?>
                                        <option value="<?=$report['REPORT_ID']?>"><?=$report['WEB_NAME']?></option>
                                    <?}?>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <?foreach($reportsList as $report){?>
                        <div class="report_template_block" report="<?=$report['REPORT_ID']?>"></div>
                    <?}?>
                </div>
            <?}?>
        </div>
    </div>
<?}?>

<script>
    $(function(){
        $(".tabs_reports .report_select").on('change', function(){
            var t = $(this);
            var reportId = t.val();

            loadReport(reportId);
        });

        $(".tabs_reports [tab]").on('click', function(){
            var t = $(this);
            var tab = t.attr('tab');

            $('.tab_v_content[tab_content='+ tab +'] .report_select').trigger('change');
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

    function loadReport(reportId, force)
    {
        var tabsBlock = $(".tabs_reports");

        var block = $('.report_template_block[report='+ reportId +']');

        $('.report_template_block').hide();

        block.show();

        if(block.text() == '' || force == true){
            block.empty().addClass('block_loading');

            $.post('/reports/load_report_template/' + reportId, {}, function(data){
                block.html(data).removeClass('block_loading');
            });
        }
    }
</script>

