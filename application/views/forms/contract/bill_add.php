<style>
    .form_client_add_bill .table_form{
        width: 760px;
    }
    .form_client_add_bill_product{
        position: relative;
    }
</style>

<div class="form_client_add_bill">
    <table class="table_form">
        <tr>
            <td colspan="3">
                <div class="form_client_add_bill_products"></div>
            </td>
        </tr>
        <tr>
            <td class="gray right" width="100">Сумма:</td>
            <td>
                <input type="text" name="client_add_bill_summ">
            </td>
            <td rowspan="2" class="right" style="vertical-align: bottom;">
                <?if(Access::allow('client_add_bill_add_product')){?>
                <div style="margin-bottom: 10px;">
                    <span class="btn btn_green" onclick="renderProduct()">+ Добавить товар</span>
                </div>
                <?}?>
                <span class="btn btn_reverse btn_client_add_bill_go" onclick="addBill()">Выставить счет</span>
                <span class="btn btn_red fancy_close">Отмена</span>
            </td>
        </tr>
        <tr>
            <td class="gray right">НДС:</td>
            <td>
                <input type="text" name="client_add_bill_nds" disabled>
            </td>
        </tr>
    </table>
    <?if(Access::allow('client_add_bill_add_product')){?>
    <i class="gray">Для выставления счета на сумму добавление товаров не требуется</i>
    <?}?>
</div>

<script>
    $(function(){
        $('[name=client_add_bill_summ]').on('keyup', function () {
            recalcNDS();
        });
    });

    function addBill() {
        var error = false;
        var params = {
            contract_id:    $('[name=contracts_list]').val(),
            sum:            0,
            products:       []
        };

        if ($('.form_client_add_bill_product').length == 0) {
            params.sum += $('[name=client_add_bill_summ]').val();
        } else {
            $('.form_client_add_bill_product').each(function () {
                var t = $(this);
                var product = {
                    service: getComboboxValue($('[name^=add_bill_product_service_]', t)),
                    cnt: $('[name^=add_bill_product_cnt_]', t).val(),
                    price: $('[name^=add_bill_product_price_]', t).val(),
                };

                if (product.service == '' || product.cnt == '' || product.price == '') {
                    message(0, 'Заполните товары корректно');
                    error = true;
                    return false;
                }

                params.products.push(product);
                params.sum += $('[name^=add_bill_product_summ_]', t).val();
            });
        }

        if (error) {
            return false;
        }

        if(params.sum == '' || params.sum <= 0){
            message(0, 'Введите корректную сумму');
            return false;
        }

        window.location.href = '/clients/add_bill?' + $.param(params);
        $.fancybox.close();
    }

    function calcRowSumm(item)
    {
         var row = item.closest('.form_client_add_bill_product');
         var cnt = $('[name^=add_bill_product_cnt_]', row);
         var cntVal = cnt.val();
         var price = $('[name^=add_bill_product_price_]', row);
         var priceVal = price.val();
         var summ = $('[name^=add_bill_product_summ_]', row);

         if (isNaN(cntVal) || cntVal < 0) {
             cnt.val(0);
             summ.val(0);
             cntVal = 0;
         }

        if (isNaN(priceVal) || priceVal < 0) {
            price.val(0);
            summ.val(0);
            priceVal = 0;
        }

        if ((parseInt(priceVal * 10000) / 10000) != priceVal) {
            priceVal = parseInt(priceVal * 10000) / 10000;
            price.val(priceVal);
        }

        if ((parseInt(cntVal * 100000) / 100000) != cntVal) {
            cntVal = parseInt(cntVal * 100000) / 100000;
            cnt.val(cntVal);
        }

        summ.val(parseInt(priceVal * cntVal * 10000) / 10000);

        recalcNDS();
    }
    
    function renderProduct()
    {
        var block = $('.form_client_add_bill_products');

        $.post('/clients/add_bill_product_template', {}, function (data) {
            block.append(data);
            $('[name=client_add_bill_summ]').prop('disabled', true);
            recalcNDS();
        });

    }

    function addBillDeleteRow(btn)
    {
        if(!confirm('Удаляем?')) {
            return;
        }

        var fieldset = btn.parent();

        fieldset.remove();

        if ($('.form_client_add_bill_product').length == 0){
            $('[name=client_add_bill_summ]').prop('disabled', false);
            recalcNDS();
        }
    }

    /**
     * 4) НДС считается как "Общая сумма" * 18 / 118 и округляется до 2х знаков
     */
    function recalcNDS()
    {
        var ndsInput = $('[name=client_add_bill_nds]');
        var nds = 0;

        if ($('.form_client_add_bill_product').length == 0) {
            nds = parseFloat($('[name=client_add_bill_summ]').val()) * 18 / 118;
        } else {
            $('[name^=add_bill_product_summ_]').each(function () {
                var t = $(this);
                var val = parseFloat(t.val());
                if (val > 0 && !isNaN(val)) {
                    nds = nds + val;
                }
            });

            nds = nds * 18 / 118;
        }

        ndsInput.val(parseInt(nds*100) / 100);
    }
</script>