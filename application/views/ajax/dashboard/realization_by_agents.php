<?if (!empty($data)) {?>
<table class="table table_small">
    <tr>
        <th>Дистрибьютор</th>
        <th>Количество<br>литров</th>
        <!--        <th>Стоимость по столбу</th>-->
        <!--        <th>Стоимость продажи клиентам</th>-->
        <th>Количество<br>транзакций</th>
    </tr>
    <?foreach ($data as $row) {?>
        <tr>
            <td><?=$row['WEB_NAME']?></td>
            <td class="right"><?=number_format($row['SERVICE_AMOUNT'], 2, '.', '&nbsp;')?></td>
            <!--            <td>--><?//=number_format($row['SERVICE_SUMPRICE'], 2, '.', ' ')?><!--</td>-->
            <!--            <td>--><?//=number_format($row['SUMPRICE_DISCOUNT'], 2, '.', ' ')?><!--</td>-->
            <td class="right"><?=number_format($row['COUNT_TRZ'], 0, '.', '&nbsp;')?></td>
        </tr>
    <?}?>
    <tr>
        <td><b>Итого:</b></td>
        <td class="right"><b><?=number_format(array_sum(array_column($data, 'SERVICE_AMOUNT')), 2, '.', '&nbsp;')?></b></td>
        <td class="right"><b><?=number_format(array_sum(array_column($data, 'COUNT_TRZ')), 0, '.', '&nbsp;')?></b></td>
    </tr>
</table>
<?} else {?>
    <div class="center">
        <i class="gray">Нет данных</i>
    </div>
<?}?>