<?
$toggle = 'agreement'.$agreement['AGREEMENT_ID'];
?>
<div class="agreement__outer" agreement_id="<?=$agreement['AGREEMENT_ID']?>">
    <div class="tc_top_line">
        [<?=$agreement['AGREEMENT_ID']?>]
        <span toggle_block="<?=$toggle?>">
            <?=$agreement['AGREEMENT_NAME']?> от <?=$agreement['WEB_DATE_BEGIN']?> <?if($agreement['WEB_DATE_END'] != '31.12.2099'){?>до <?=$agreement['WEB_DATE_END']?><?}?> &nbsp;
        </span>
        <span toggle_block="<?=$toggle?>" class="dn gray">
            <input type="text" name="AGREEMENT_NAME" value="<?=$agreement['AGREEMENT_NAME']?>" class="input_big input_medium" placeholder="Название">
            от
            <input type="text" name="DATE_BEGIN" value="<?=$agreement['WEB_DATE_BEGIN']?>" class="input_big input_medium datepicker" readonly>
            до
            <input type="text" name="DATE_END" value="<?=$agreement['WEB_DATE_END']?>" class="input_big input_medium datepicker" readonly>
        </span>

        <?if(Access::allow('suppliers_agreement-edit')){?>
            <div class="fr" toggle_block="<?=$toggle?>"><button class="btn" toggle="<?=$toggle?>"><i class="icon-pen"></i> Редактировать</button></div>
            <div class="fr dn" toggle_block="<?=$toggle?>">
                <button class="btn btn_green btn_reverse" onclick="agreementSave($(this))"><i class="icon-ok"></i> Сохранить</button>
                <button class="btn btn_red" toggle="<?=$toggle?>"><i class="icon-cancel"></i> Отменить</button>
            </div>
        <?}?>
    </div>
    <div class="padding__20">
        <table>
            <tr>
                <td class="gray right" width="150">Расчет скидки:</td>
                <td>
                    <div toggle_block="<?=$toggle?>">
                        <?if($agreement['DISCOUNT_TYPE'] == Model_Supplier_Agreement::DISCOUNT_TYPE_FROM_LOAD){?>
                            Из данных загрузки
                        <?}else{
                            foreach ($tariffs as $tariff) {
                                if ($tariff['TARIF_ID'] == $agreement['TARIF_ID']) {
                                    ?>По тарифу - <b><?=$tariff['TARIF_NAME']?></b><?
                                }
                            }
                        }?>
                    </div>

                    <div toggle_block="<?=$toggle?>" class="dn">
                        <div class="agreement__discount-type">
                            <label>
                                <input type="radio" name="DISCOUNT_TYPE" value="<?=Model_Supplier_Agreement::DISCOUNT_TYPE_FROM_LOAD?>" <?=($agreement['DISCOUNT_TYPE'] == Model_Supplier_Agreement::DISCOUNT_TYPE_FROM_LOAD ? 'checked' : '')?> onchange="checkAgreementDiscountType($(this))">
                                Из данных загрузки
                            </label>
                        </div>
                        <div class="agreement__discount-type">
                            <label>
                                <input type="radio" name="DISCOUNT_TYPE" value="<?=Model_Supplier_Agreement::DISCOUNT_TYPE_FROM_TARIFF?>" <?=($agreement['DISCOUNT_TYPE'] == Model_Supplier_Agreement::DISCOUNT_TYPE_FROM_TARIFF ? 'checked' : '')?> onchange="checkAgreementDiscountType($(this))">
                                По тарифу
                            </label>
                            <?=Common::buildFormField('contract_tariffs', 'TARIF_ID', $agreement['TARIF_ID'])?>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>

<script>
    var DISCOUNT_TYPE_FROM_LOAD = <?=Model_Supplier_Agreement::DISCOUNT_TYPE_FROM_LOAD?>;

    $(function () {
        $("input[type=radio]").each(function(){
            renderRadio($(this));
        });
    });
</script>