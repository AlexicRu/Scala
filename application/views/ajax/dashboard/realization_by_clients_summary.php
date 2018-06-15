<table>
    <tr>
        <td class="gray right">Количество литров:</td>
        <td class="right"><?=number_format($data['SERVICE_AMOUNT'], 2, '.', '&nbsp;')?></td>
    </tr>
    <tr>
        <td class="gray right">Реализация:</td>
        <td class="right"><?=number_format($data['SUMPRICE_DISCOUNT'], 2, '.', '&nbsp;')?></td>
    </tr>
    <?if (Access::allow('view_full_dashboard_clients_summary')) {?>
    <tr>
        <td class="gray right">Стоимость на АЗС:</td>
        <td class="right"><?=number_format($data['SERVICE_SUMPRICE'], 2, '.', '&nbsp;')?></td>
    </tr>
    <tr>
        <td class="gray right">Покупка от поставщика:</td>
        <td class="right"><?=number_format($data['SUMPRICE_BUY'], 2, '.', '&nbsp;')?></td>
    </tr>
    <tr>
        <td class="gray right">Маржинальность:</td>
        <td class="right"><?=number_format($data['MARGINALITY'], 2, '.', '&nbsp;')?></td>
    </tr>
    <tr>
        <td class="gray right">Средний дисконт:</td>
        <td class="right"><?=number_format($data['AVG_DISCOUNT'], 5, '.', '&nbsp;')?></td>
    </tr>
    <?}?>
</table>