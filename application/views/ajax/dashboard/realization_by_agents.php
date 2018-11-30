<?if (!empty($data)) {?>
<table class="table table_small">
    <tr>
        <th>Дистрибьютор</th>
        <th class="right">Кол-во<br>литров</th>
        <th class="right">Кол-во<br>транзакций</th>
        <th class="right">Кол-во<br>клиентов</th>
    </tr>
    <?foreach ($data as $row) {?>
        <tr>
            <td><?=$row['WEB_NAME']?></td>
            <td class="right"><?=number_format($row['SERVICE_AMOUNT'], 2, '.', '&nbsp;')?></td>
            <td class="right"><?=number_format($row['COUNT_TRZ'], 0, '.', '&nbsp;')?></td>
            <td class="right"><?=number_format($row['CL_COUNT'], 0, '.', '&nbsp;')?></td>
        </tr>
    <?}?>
    <tr>
        <td><b>Итого:</b></td>
        <td class="right"><b><?=number_format(array_sum(array_column($data, 'SERVICE_AMOUNT')), 2, '.', '&nbsp;')?></b></td>
        <td class="right"><b><?=number_format(array_sum(array_column($data, 'COUNT_TRZ')), 0, '.', '&nbsp;')?></b></td>
        <td class="right"><b><?=number_format(array_sum(array_column($data, 'CL_COUNT')), 0, '.', '&nbsp;')?></b></td>
    </tr>
</table>
<?} else {?>
    <div class="center">
        <i class="gray">Нет данных</i>
    </div>
<?}?>