<?if (empty($reports)) {?>
    <div class="line_inner">Отчеты не найдены</div>
<?} else {?>

    <?foreach ($reports as $report) {?>
        <div class="line_inner" manager_id="<?=$managerId?>" report_id="<?=$report['REPORT_ID']?>">
            <span class="label <?=Model_Report::$reportGlobalTypesNames[$report['REPORT_TYPE_ID']]['label']?>">
                <?=Model_Report::$reportGlobalTypesNames[$report['REPORT_TYPE_ID']]['name']?>
            </span>
            &nbsp;&nbsp;&nbsp; <b><?=$report['WEB_NAME']?></b>
            <div class="fr">
                <a href="#" class="red del" onclick="delManagersReport($(this))">Удалить <i class="icon-cancel"></i></a>
            </div>
        </div>
    <?}?>
<?}?>