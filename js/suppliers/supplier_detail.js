var supplierLogo = '';
var supplierId = false;
var contractId = false;

$(function () {
    contractId = $('[name=suppliers_contracts_list]').val();
    supplierId = $('[name=supplier_id]').val();

    dropzone = new Dropzone('.supplier-detail__avatar-dropzone', {
        url: "/index/upload_file?component=supplier",
        autoProcessQueue: false,
        addRemoveLinks: true,
        maxFiles: 1,
        success: function(file, response)
        {
            if(response.success){
                supplierLogo = response.data.file;
            }
        },
        queuecomplete: function ()
        {
            _saveSupplierInfo();
        }
    });

    $('[name=suppliers_contracts_list]').on('change', function () {
        contractId = $(this).val();
    });
});

function saveSupplierInfo()
{
    if(dropzone.getQueuedFiles().length){
        dropzone.processQueue();
    }else{
        _saveSupplierInfo();
    }
}

function _saveSupplierInfo()
{
    var block = $(".supplier-detail__info");

    var params = {
        NAME:           $('[name=NAME]', block).val(),
        LONG_NAME:      $('[name=LONG_NAME]', block).val(),
        Y_ADDRESS:      $('[name=Y_ADDRESS]', block).val(),
        F_ADDRESS:      $('[name=F_ADDRESS]', block).val(),
        P_ADDRESS:      $('[name=P_ADDRESS]', block).val(),
        COMMENTS:       $('[name=COMMENTS]', block).val(),
        PHONE:          $('[name=PHONE]', block).val(),
        EMAIL:          $('[name=EMAIL]', block).val(),
        INN:            $('[name=INN]', block).val(),
        KPP:            $('[name=KPP]', block).val(),
        OGRN:           $('[name=OGRN]', block).val(),
        OKPO:           $('[name=OKPO]', block).val(),
        OKONH:          $('[name=OKONH]', block).val(),
        CONTACT_PERSON: $('[name=CONTACT_PERSON]', block).val(),
        ICON_PATH:      supplierLogo
    };

    /*if(
        params.NAME == '' ||
        params.Y_ADDRESS == '' ||
        params.PHONE == '' ||
        params.EMAIL == '' ||
        params.INN == ''
    ){
        message(0, 'Заполните обязательные поля');
        return false;
    }*/

    $.post('/suppliers/supplier_edit/' + supplierId, { params:params }, function(data){
        if(data.success){
            message(1, 'Поставщик обновлен');

            $.each( params, function( key, value ) {
                var uid = $('[name='+ key +']', block).closest('[uid]');

                switch (key) {
                    case 'EMAIL':
                        $("[uid=" + uid.attr('uid') + "]", block).not(uid).html("<a href='mailto:"+value+"'>"+ value +"</a>");
                        break;
                    case 'COMMENTS':
                        $("[uid=" + uid.attr('uid') + "]", block).not(uid).html(value.replace(/\n/g, "<br>"));
                        break;
                    case 'ICON_PATH':
                        if (value) {
                            $('.supplier-detail__avatar').removeClass('supplier-detail__avatar-empty');
                            $('.supplier-detail__avatar-pic').css({'background-image': 'url('+ value +')'});
                        }
                        break;
                    default:
                        $("[uid=" + uid.attr('uid') + "]", block).not(uid).text(value);
                }
            });

            $("[toggle='edit_supplier']:first", block).click();

        }else{
            message(0, 'Сохранение не удалось');
        }

        dropzone.removeAllFiles();
        supplierLogo = '';
    });
}

function loadSupplierContract(tab)
{
    if (!tab) {
        tab = 'contract';
    }

    var block = $('.supplier-contract');

    block.empty().addClass(CLASS_LOADING);

    $.post('/suppliers/contract/' + contractId, {tab: tab}, function (data) {
        block.html(data).removeClass(CLASS_LOADING);

        $('.datepicker', block).each(function () {
            renderDatePicker($(this));
        });
    });
}

function editSupplierContract()
{
    var block = $('.supplier-contract__contract');

    var params = {
        CONTRACT_NAME:          $('[name=CONTRACT_NAME]', block).val(),
        DATE_BEGIN:             $('[name=DATE_BEGIN]', block).val(),
        DATE_END:               $('[name=DATE_END]', block).val(),
        CONTRACT_STATE:         $('[name=CONTRACT_STATE]', block).val(),
        DATA_SOURCE:            $('[name=DATA_SOURCE]:checked', block).val(),
        TUBE_ID:                $('[name=TUBE_ID]', block).val(),
        CONTRACT_SERVICES:      getComboboxMultiValue($('[name=CONTRACT_SERVICES]', block)),
        CONTRACT_POS_GROUPS:    getComboboxMultiValue($('[name=CONTRACT_POS_GROUPS]', block)),
    };

    if (params.CONTRACT_NAME == '' || params.CONTRACT_SERVICES.length == 0 || params.CONTRACT_POS_GROUPS.length == 0) {
        message(0, 'Заполните все поля');
        return;
    }

    $.post('/suppliers/contract_edit/' + contractId, {params:params}, function (data) {
        if (data.success) {
            message(1, 'Контракт успешно обновлен');

            loadSupplierContract();
        } else {
            message(0, data.data ? data.data : 'Ошибка добавления контракта');
        }
    });
}

function checkSupplierContractDataSource()
{
    var block = $('.supplier-contract__contract');

    var dataSource = $('[name=DATA_SOURCE]:checked').val();

    if (dataSource == DATA_SOURCE_INSIDE) {
        $('[name=TUBE_ID]').prop('disabled', true);
    } else {
        $('[name=TUBE_ID]').prop('disabled', false);
    }
}

function checkAgreementDiscountType(radio)
{
    var block = radio.closest('.agreement__outer');

    var discountType = $('[name=DISCOUNT_TYPE]:checked').val();

    if (discountType == DISCOUNT_TYPE_FROM_LOAD) {
        $('[name=TARIF_ID]').prop('disabled', true);
    } else {
        $('[name=TARIF_ID]').prop('disabled', false);
    }
}

function loadAgreement(elem)
{
    if($(".tabs_agreements [tab_content="+ elem.attr('tab') +"]").text() == ''){
        $(".tabs_agreements [tab_content="+ elem.attr('tab') +"]").empty().addClass(CLASS_LOADING);

        $.post('/suppliers/agreement/' + elem.attr('tab') + '/?contract_id=' + contractId, {}, function(data){
            $(".tabs_agreements [tab_content="+ elem.attr('tab') +"]").html(data).removeClass(CLASS_LOADING);
        });
    }
}

function agreementSave(btn)
{
    var block = btn.closest('.agreement__outer');
    var agreementId = block.attr('agreement_id');
}