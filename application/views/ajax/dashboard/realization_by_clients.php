<table>
    <tr>
        <td class="gray right">Количество клиентов:</td>
        <td><?=$data['CNT_CLIENTS']?></td>
    </tr>
    <tr>
        <td class="gray right">Количество договоров:</td>
        <td><?=$data['CNT_CONTRACTS']?></td>
    </tr>
    <tr>
        <td class="gray right">Объем выборки за период:</td>
        <td><?=number_format($data['SERVICE_AMOUNT'], 2, '.', '&nbsp;')?> л.</td>
    </tr>
    <tr>
        <td class="gray right">Продажа за период:</td>
        <td><?=number_format($data['SUMPRICE_DISCOUNT'], 2, '.', '&nbsp;')?> <?=Text::RUR?></td>
    </tr>
</table>