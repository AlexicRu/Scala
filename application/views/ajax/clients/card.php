<div class="fr">
    <button class="btn btn_red">Заблокировать</button> &nbsp;
    <button class="btn"><i class="icon-pen"></i> Редактировать</button>
</div>

<b class="f18">Обороты за текущий период:</b><br>
152 л. / 5 122 &#x20bd;<br><br>

<b class="f18">Последняя заправка:</b>
<div class="line_inner">
    <span class="gray">06.01.2015</span> &nbsp;&nbsp;&nbsp; <b>АЗС Роснефть №15</b> <div class="fr">Бензин АИ-95 10л. / 372 &#x20bd;</div>
</div>
<br>

<?if(!empty($oilRestrictions)){?>
    <b class="f18">Ограничения по топливу:</b>
    <table>
        <?foreach($oilRestrictions as $restrictions){
            $restrict = reset($restrictions);
            ?>
            <tr>
                <td class="gray right">
                    <?foreach($restrictions as $restriction){?>
                        <?=$restriction['DESCRIPTION']?>:<br>
                    <?}?>
                </td>
                <td class="line_inner">
                    <?=$restrict['LIMIT_VALUE']?>
                    <?=($restrict['LIMIT_PARAM'] == 1 ? 'л.' : '&#x20bd;')?>
                    <?
                        switch($restrict['LIMIT_TYPE']) {
                            case 1:
                                echo 'в сутки';
                                break;
                            case 2:
                                echo 'в неделю';
                                break;
                            case 3:
                                echo 'в месяц';
                                break;
                            case 4:
                                echo 'единовременно';
                                break;
                        }
                    ?>
                </td>
            </tr>
        <?}?>
    </table>
    <br>
<?}?>

<b class="f18">История операций:</b>
<div class="line_inner">
    <span class="gray">06.01.2015</span> &nbsp;&nbsp;&nbsp; Иванов И.И. <div class="fr">Статус - заблокирована</div>
</div>
<div class="line_inner">
    <span class="gray">06.01.2015</span> &nbsp;&nbsp;&nbsp; Петров А.Г. <div class="fr">Изменен лимит по карте</div>
</div>
