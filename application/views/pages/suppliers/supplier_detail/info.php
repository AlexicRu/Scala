<div id="vue_supplier_info">

    <div class="supplier-detail__name">
        <h2 class="supplier-detail__id">ID: <?=$supplier['ID']?></h2>

        <div class="supplier-detail__avatar" v-bind:class="avatar_class">
            <div toggle_block="edit_supplier" class="supplier-detail__avatar-pic" v-bind:style="avatar_style"></div>
            <div toggle_block="edit_supplier" class="dn">
                <div class="dropzone supplier-detail__avatar-dropzone"></div>
            </div>
        </div>

        <h2 toggle_block="edit_supplier">{{ supplier.SUPPLIER_NAME }}</h2>
        <h2 toggle_block="edit_supplier" class="dn"><input type="text" class="input_big input_grand" v-model="supplier.SUPPLIER_NAME"></h2>

        <p toggle_block="edit_supplier">{{ supplier.LONG_NAME }}</p>
        <p toggle_block="edit_supplier" class="dn"><input type="text" class="input_grand" v-model="supplier.LONG_NAME"></p>
    </div>

    <div toggle_block="supplier_info" class="dn">
        <div class="col">
            <table>
                <tr>
                    <td class="gray right" width="170">Юридический адрес:</td>
                    <td width="370">
                        <span toggle_block="edit_supplier" v-html="checkEmpty(supplier.Y_ADDRESS)"></span>
                        <span toggle_block="edit_supplier" class="dn"><nobr><input type="text" v-model="supplier.Y_ADDRESS"></nobr></span>
                    </td>
                </tr>
                <tr>
                    <td class="gray right">Фактический адрес:</td>
                    <td>
                        <span toggle_block="edit_supplier" v-html="checkEmpty(supplier.F_ADDRESS)"></span>
                        <span toggle_block="edit_supplier" class="dn"><input type="text" v-model="supplier.F_ADDRESS"></span>
                    </td>
                </tr>
                <tr>
                    <td class="gray right">Почтовый адрес:</td>
                    <td>
                        <span toggle_block="edit_supplier" v-html="checkEmpty(supplier.P_ADDRESS)"></span>
                        <span toggle_block="edit_supplier" class="dn"><input type="text" v-model="supplier.P_ADDRESS"></span>
                    </td>
                </tr>
                <tr>
                    <td class="gray right">Контактное лицо:</td>
                    <td>
                        <span toggle_block="edit_supplier" v-html="checkEmpty(supplier.CONTACT_PERSON)"></span>
                        <span toggle_block="edit_supplier" class="dn"><input type="text" v-model="supplier.CONTACT_PERSON"></span>
                    </td>
                </tr>
                <tr>
                    <td class="gray right" valign="top">Комментарий:</td>
                    <td>
                        <span toggle_block="edit_supplier" v-html="checkEmpty(comments_formatted)"></span>
                        <span toggle_block="edit_supplier" class="dn"><textarea v-model="supplier.COMMENTS"></textarea></span>
                    </td>
                </tr>
            </table>
        </div>
        <div class="col">
            <table>
                <tr>
                    <td class="gray right" width="170">Телефон:</td>
                    <td width="200">
                        <span toggle_block="edit_supplier" v-html="checkEmpty(supplier.PHONE)"></span>
                        <span toggle_block="edit_supplier" class="dn"><nobr><input type="text" v-model="supplier.PHONE"></nobr></span>
                    </td>
                </tr>
                <tr>
                    <td class="gray right">E-mail:</td>
                    <td>
                        <span toggle_block="edit_supplier" v-html="checkEmpty(email_formatted)"></span>
                        <span toggle_block="edit_supplier" class="dn"><nobr><input type="text" v-model="supplier.EMAIL"></nobr></span>
                    </td>
                </tr>
                <tr>
                    <td class="gray right">ИНН:</td>
                    <td>
                        <span toggle_block="edit_supplier" v-html="checkEmpty(supplier.INN)"></span>
                        <span toggle_block="edit_supplier" class="dn"><nobr><input type="text" v-model="supplier.INN"></nobr></span>
                    </td>
                </tr>
                <tr>
                    <td class="gray right">КПП:</td>
                    <td>
                        <span toggle_block="edit_supplier" v-html="checkEmpty(supplier.KPP)"></span>
                        <span toggle_block="edit_supplier" class="dn"><nobr><input type="text" v-model="supplier.KPP"></nobr></span>
                    </td>
                </tr>
                <tr>
                    <td class="gray right">ОГРН:</td>
                    <td>
                        <span toggle_block="edit_supplier" v-html="checkEmpty(supplier.OGRN)"></span>
                        <span toggle_block="edit_supplier" class="dn"><input type="text" v-model="supplier.OGRN"></span>
                    </td>
                </tr>
                <tr>
                    <td class="gray right">ОКПО:</td>
                    <td>
                        <span toggle_block="edit_supplier" v-html="checkEmpty(supplier.OKPO)"></span>
                        <span toggle_block="edit_supplier" class="dn"><input type="text" v-model="supplier.OKPO"></span>
                    </td>
                </tr>
                <tr>
                    <td class="gray right">ОКОНХ:</td>
                    <td>
                        <span toggle_block="edit_supplier" v-html="checkEmpty(supplier.OKONH)"></span>
                        <span toggle_block="edit_supplier" class="dn"><input type="text" v-model="supplier.OKONH"></span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="more_info dn" toggle_block="supplier_info">
        <a href="#" class="btn btn_gray btn_min_width" toggle="supplier_info">Скрыть информацию о поставщике</a> &nbsp;

        <?if(Access::allow('suppliers_supplier-edit')){?>
        <button class="btn" toggle="edit_supplier" toggle_block="edit_supplier"><i class="icon-pen"></i> Редактировать</button> &nbsp;

        <button class="btn btn_green dn btn_reverse" toggle_block="edit_supplier" onclick="saveSupplierInfo()"><i class="icon-ok"></i> Сохранить</button>
        <button class="btn btn_red dn" toggle="edit_supplier" toggle_block="edit_supplier" v-on:click="cancelForm()"><i class="icon-cancel"></i> Отмена</button>
        <?}?>
    </div>
    <div class="more_info" toggle_block="supplier_info">
        <a href="#" class="btn btn_gray btn_min_width" toggle="supplier_info">Информация о поставщике</a>
    </div>

</div>

<script>
    var vueSupplierInfo = new Vue({
        el: '#vue_supplier_info',
        data: {
            supplier: {
                ICON_PATH: '<?=$supplier['ICON_PATH']?>',
                SUPPLIER_NAME: '<?=$supplier['SUPPLIER_NAME']?>',
                LONG_NAME: '<?=$supplier['LONG_NAME']?>',
                Y_ADDRESS: '<?=$supplier['Y_ADDRESS']?>',
                F_ADDRESS: '<?=$supplier['F_ADDRESS']?>',
                P_ADDRESS: '<?=$supplier['P_ADDRESS']?>',
                CONTACT_PERSON: '<?=$supplier['CONTACT_PERSON']?>',
                COMMENTS: '<?=$supplier['COMMENTS']?>',
                PHONE: '<?=$supplier['PHONE']?>',
                EMAIL: '<?=$supplier['EMAIL']?>',
                INN: '<?=$supplier['INN']?>',
                KPP: '<?=$supplier['KPP']?>',
                OGRN: '<?=$supplier['OGRN']?>',
                OKPO: '<?=$supplier['OKPO']?>',
                OKONH: '<?=$supplier['OKONH']?>',
            }
        },
        computed: {
            email_formatted: function () {
                var email = this.supplier.EMAIL;

                return email ? '<a href="mailto:' + email + '">' + email + '</a>' : '';
            },
            comments_formatted: function () {
                return this.supplier.COMMENTS.replace(/\n/g, "<br>");
            },
            avatar_class: function () {
                return this.supplier.ICON_PATH ? '' : 'supplier-detail__avatar-empty';
            },
            avatar_style: function () {
                var icon = this.supplier.ICON_PATH;
                return icon ? {backgroundImage: 'url('+ icon +')'} : '';
            }
        },
        methods: {
            checkEmpty: function (val) {
                return val ? val : '<i class="gray">Не заполнено</i>';
            },
            cancelForm: function () {
                Object.assign(this.supplier, this._cache);
            },
            cacheForm: function () {
                this._cache = Object.assign({}, this.supplier);
            }
        },
        mounted: function () {
            this.cacheForm();
        }
    })
</script>