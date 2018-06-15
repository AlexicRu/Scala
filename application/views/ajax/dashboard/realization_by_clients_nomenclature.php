<?if (!empty($data)) {?>
<table class="table table_small">
    <tr>
        <th>Наименование</th>
        <th><nobr>Кол-во, л.</nobr></th>
        <th>Сумма<br>продажи</th>
    </tr>
    <?foreach ($data as $row) {?>
        <tr>
            <td><?=$row['LONG_DESC']?></td>
            <td class="right"><?=number_format($row['SERVICE_AMOUNT'], 2, '.', '&nbsp;')?></td>
            <td class="right"><?=number_format($row['SUMPRICE_DISCOUNT'], 2, '.', '&nbsp;')?></td>
        </tr>
    <?}?>
</table>
<?} else {?>
    <div class="center">
        <i class="gray">Нет данных</i>
    </div>
<?}?>