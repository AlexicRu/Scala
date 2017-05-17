var supplierLogo = false;
var supplierId = false;

$(function () {
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

    if(
        params.NAME == '' ||
        params.Y_ADDRESS == '' ||
        params.PHONE == '' ||
        params.EMAIL == '' ||
        params.INN == ''
    ){
        message(0, 'Заполните обязательные поля');
        return false;
    }

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
        supplierLogo = false;
    });
}

function loadSupplierContract(tab)
{
    var contractId = $('[name=suppliers_contracts_list]').val();

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

    message(0, 'Рано');
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