<?if (!empty($data)) {?>
<table class="table table_small">
    <tr>
        <th>Дистрибьютор</th>
        <th class="right">Всего карт</th>
        <th class="right">Из них активных</th>
    </tr>
    <?foreach ($data as $row) {?>
        <tr>
            <td><?=$row['WEB_NAME']?></td>
            <td class="right"><?=number_format($row['ALL_CARDS'], 0, '.', '&nbsp;')?></td>
            <td class="right"><?=number_format($row['CARDS_IN_WORK'], 0, '.', '&nbsp;')?></td>
        </tr>
    <?}?>
    <tr>
        <td><b>Итого:</b></td>
        <td class="right"><b><?=number_format(array_sum(array_column($data, 'ALL_CARDS')), 0, '.', '&nbsp;')?></b></td>
        <td class="right"><b><?=number_format(array_sum(array_column($data, 'CARDS_IN_WORK')), 0, '.', '&nbsp;')?></b></td>
    </tr>
</table>
<?} else {?>
    <div class="center">
        <i class="gray">Нет данных</i>
    </div>
<?}?>
