<h1>Настройки агента</h1>

<div class="tabs_block tabs_switcher">
    <div class="tabs">
        <span tab="main" class="tab active"><i class="icon-gear"></i> Основные</span>
    </div>
    <div class="tabs_content tabs_content_no_padding">
        <div tab_content="main" class="tab_content active">
            <div id="vue_agent_info">
                <div class="tc_top_line">
                    <span>
                        [<?=$agent['AGENT_ID']?>]
                        <?if (Access::allow('root')) {?>
                            <span toggle_block="agentBlock" v-html="checkEmpty(agent.title.WEB_NAME)"></span>
                            <span toggle_block="agentBlock" class="dn">
                                <input type="text" class="input_grand input_big" v-model="agent.title.WEB_NAME" placeholder="Имя">
                            </span>
                        <?} else {?>
                            <span v-html="checkEmpty(agent.title.WEB_NAME)"></span>
                        <?}?>
                    </span>
                    
                    <div class="fr">
                        <span class="btn" toggle_block="agentBlock" toggle="agentBlock"><i class="icon-pen"></i> Редактировать</span>
                        <span class="btn btn_red btn_reverse dn" toggle_block="agentBlock" toggle="agentBlock" v-on:click="cancelForm()"><i class="icon-cancel"></i> Закрыть</span>
                    </div>
                </div>

                <div class="padding__20">
                    <table>
                        <tr>
                            <td class="gray right" width="300">WEB имя:</td>
                            <td>
                                <?if (Access::allow('root')) {?>
                                    <span toggle_block="agentBlock" v-html="checkEmpty(agent.title.FULL_NAME)"></span>
                                    <span toggle_block="agentBlock" class="dn">
                                        <input class="input_grand" type="text" v-model="agent.title.FULL_NAME">
                                    </span>
                                <?} else {?>
                                    <span v-html="checkEmpty(agent.title.FULL_NAME)"></span>
                                <?}?>
                            </td>
                        </tr>
                        <tr>
                            <td class="gray right">Статус:</td>
                            <td>
                                <?if (Access::allow('root')) {?>
                                    <span toggle_block="agentBlock" v-html="statusFormatted"></span>
                                    <span toggle_block="agentBlock" class="dn">
                                        <select type="text" v-model="agent.title.STATE_ID">
                                            <?foreach (Model_Agent::$agentStatuses as $status => $statusName) {?>
                                                <option value="<?=$status?>"><?=$statusName?></option>
                                            <?}?>
                                        </select>
                                    </span>
                                <?} else {?>
                                    <span v-html="statusFormatted"></span>
                                <?}?>
                            </td>
                        </tr>
                        <tr>
                            <td class="gray right">Номер договора на доступ к Личному кабинету:</td>
                            <td>
                                <?if (Access::allow('root')) {?>
                                    <span toggle_block="agentBlock" v-html="checkEmpty(agent.title.GP_CONTRACT)"></span>
                                    <span toggle_block="agentBlock" class="dn"><input class="input_grand" type="text" v-model="agent.title.GP_CONTRACT"></span>
                                <?} else {?>
                                    <span v-html="checkEmpty(agent.title.GP_CONTRACT)"></span>
                                <?}?>
                            </td>
                        </tr>
                        <tr>
                            <td class="gray right">Дата договора на доступ к Личному кабинету:</td>
                            <td>
                                <?if (Access::allow('root')) {?>
                                    <span toggle_block="agentBlock" v-html="checkEmpty(agent.title.GP_CONTRACT_DATE)"></span>
                                    <span toggle_block="agentBlock" class="dn"><input class="input_grand" type="text" v-model="agent.title.GP_CONTRACT_DATE"></span>
                                <?} else {?>
                                    <span v-html="checkEmpty(agent.title.GP_CONTRACT_DATE)"></span>
                                <?}?>
                            </td>
                        </tr>
                        <?if (Access::allow('root')) {?>
                        <tr toggle_block="agentBlock" class="dn">
                            <td></td>
                            <td>
                                <button class="btn btn_green btn_reverse" onclick="saveAgentTitleInfo()"><i class="icon-ok"></i> Сохранить</button>
                            </td>
                        </tr>
                        <?}?>
                    </table>

                    <br>
                    <b class="f18">Информация о компании:</b>
                    <br>

                    <table>
                        <tr>
                            <td class="gray right" width="300">ИНН агента:</td>
                            <td>
                                <span toggle_block="agentBlock" v-html="checkEmpty(agent.info.AGENT_INN)"></span>
                                <span toggle_block="agentBlock" class="dn"><input class="input_grand" type="text" v-model="agent.info.AGENT_INN"></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="gray right">КПП агента:</td>
                            <td>
                                <span toggle_block="agentBlock" v-html="checkEmpty(agent.info.AGENT_KPP)"></span>
                                <span toggle_block="agentBlock" class="dn"><input class="input_grand" type="text" v-model="agent.info.AGENT_KPP"></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="gray right">Юридический адрес:</td>
                            <td>
                                <span toggle_block="agentBlock" v-html="checkEmpty(agent.info.AGENT_Y_ADDRESS)"></span>
                                <span toggle_block="agentBlock" class="dn"><input class="input_grand" type="text" v-model="agent.info.AGENT_Y_ADDRESS"></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="gray right">Почтовый адрес:</td>
                            <td>
                                <span toggle_block="agentBlock" v-html="checkEmpty(agent.info.AGENT_P_ADDRESS)"></span>
                                <span toggle_block="agentBlock" class="dn"><input class="input_grand" type="text" v-model="agent.info.AGENT_P_ADDRESS"></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="gray right">Фактический адрес:</td>
                            <td>
                                <span toggle_block="agentBlock" v-html="checkEmpty(agent.info.AGENT_F_ADDRESS)"></span>
                                <span toggle_block="agentBlock" class="dn"><input class="input_grand" type="text" v-model="agent.info.AGENT_F_ADDRESS"></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="gray right">Email:</td>
                            <td>
                                <span toggle_block="agentBlock" v-html="checkEmpty(emailFormatted)"></span>
                                <span toggle_block="agentBlock" class="dn"><input class="input_grand" type="text" v-model="agent.info.AGENT_EMAIL"></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="gray right">Телефон:</td>
                            <td>
                                <span toggle_block="agentBlock" v-html="checkEmpty(agent.info.AGENT_PHONE)"></span>
                                <span toggle_block="agentBlock" class="dn"><input class="input_grand" type="text" name="phone" v-model="agent.info.AGENT_PHONE"></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="gray right">Город:</td>
                            <td>
                                <span toggle_block="agentBlock" v-html="checkEmpty(agent.info.AGENT_CITY)"></span>
                                <span toggle_block="agentBlock" class="dn"><input class="input_grand" type="text" v-model="agent.info.AGENT_CITY"></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="gray right">Должность подписанта в именительном падеже:</td>
                            <td>
                                <span toggle_block="agentBlock" v-html="checkEmpty(agent.info.AGENT_SIGNER_POST_1)"></span>
                                <span toggle_block="agentBlock" class="dn"><input class="input_grand" type="text" v-model="agent.info.AGENT_SIGNER_POST_1"></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="gray right">Имя подписанта в именительном падеже:</td>
                            <td>
                                <span toggle_block="agentBlock" v-html="checkEmpty(agent.info.AGENT_SIGNER_NAME_1)"></span>
                                <span toggle_block="agentBlock" class="dn"><input class="input_grand" type="text" v-model="agent.info.AGENT_SIGNER_NAME_1"></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="gray right">Должность подписанта в родительном падеже:</td>
                            <td>
                                <span toggle_block="agentBlock" v-html="checkEmpty(agent.info.AGENT_SIGNER_POST_2)"></span>
                                <span toggle_block="agentBlock" class="dn"><input class="input_grand" type="text" v-model="agent.info.AGENT_SIGNER_POST_2"></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="gray right">Имя подписанта в родительном падеже:</td>
                            <td>
                                <span toggle_block="agentBlock" v-html="checkEmpty(agent.info.AGENT_SIGNER_NAME_2)"></span>
                                <span toggle_block="agentBlock" class="dn"><input class="input_grand" type="text" v-model="agent.info.AGENT_SIGNER_NAME_2"></span>
                            </td>
                        </tr>
                        <tr toggle_block="agentBlock" class="dn">
                            <td></td>
                            <td>
                                <button class="btn btn_green btn_reverse" onclick="saveAgentInfoInfo()"><i class="icon-ok"></i> Сохранить</button>
                            </td>
                        </tr>
                    </table>

                    <br>
                    <b class="f18">Сервисные настройки:</b>
                    <br>

                    <table>
                        <tr>
                            <td class="gray right" width="300">Email для отправки оповещений:</td>
                            <td>
                                <?if (Access::allow('root')) {?>
                                    <span toggle_block="agentBlock" v-html="checkEmpty(agent.service.SEND_FROM)"></span>
                                    <span toggle_block="agentBlock" class="dn"><input class="input_grand" type="text" v-model="agent.service.SEND_FROM"></span>
                                <?} else {?>
                                    <span v-html="checkEmpty(agent.service.SEND_FROM)"></span>
                                <?}?>
                            </td>
                        </tr>
                        <tr>
                            <td class="gray right">Префикс к выставлению счета:</td>
                            <td>
                                <?if (Access::allow('root')) {?>
                                    <span toggle_block="agentBlock" v-html="checkEmpty(agent.service.BILL_PREFIX)"></span>
                                    <span toggle_block="agentBlock" class="dn"><input class="input_grand" type="text" v-model="agent.service.BILL_PREFIX"></span>
                                <?} else {?>
                                    <span v-html="checkEmpty(agent.service.BILL_PREFIX)"></span>
                                <?}?>
                            </td>
                        </tr>
                        <tr>
                            <td class="gray right">Номенклатура счета:</td>
                            <td>
                                <?if (Access::allow('root')) {?>
                                    <span toggle_block="agentBlock" v-html="checkEmpty(agent.service.DEFAULT_GOOD_NAME)"></span>
                                    <span toggle_block="agentBlock" class="dn"><input class="input_grand" type="text" v-model="agent.service.DEFAULT_GOOD_NAME"></span>
                                <?} else {?>
                                    <span v-html="checkEmpty(agent.service.DEFAULT_GOOD_NAME)"></span>
                                <?}?>
                            </td>
                        </tr>
                        <tr>
                            <td class="gray right">Ссылка к ЛК для клиентов:</td>
                            <td>
                                <?if (Access::allow('root')) {?>
                                    <span toggle_block="agentBlock" v-html="checkEmpty(agent.service.OFFICE_LINK)"></span>
                                    <span toggle_block="agentBlock" class="dn"><input class="input_grand" type="text" v-model="agent.service.OFFICE_LINK"></span>
                                <?} else {?>
                                    <span v-html="checkEmpty(agent.service.OFFICE_LINK)"></span>
                                <?}?>
                            </td>
                        </tr>
                        <tr>
                            <td class="gray right">SMS рассылка:</td>
                            <td>
                                <?if (Access::allow('root')) {?>
                                    <span toggle_block="agentBlock" v-html="smsSubscriptionFormatted"></span>
                                    <span toggle_block="agentBlock" class="dn"><input type="checkbox" v-model="agent.service.SENDER_SMS"></span>
                                <?} else {?>
                                    <span v-html="smsSubscriptionFormatted"></span>
                                <?}?>
                            </td>
                        </tr>
                        <?if (Access::allow('root')) {?>
                        <tr toggle_block="agentBlock" class="dn">
                            <td></td>
                            <td>
                                <button class="btn btn_green btn_reverse" onclick="saveAgentServiceInfo()"><i class="icon-ok"></i> Сохранить</button>
                            </td>
                        </tr>
                        <?}?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var agentId = '<?=$agent['AGENT_ID']?>';
    var vueAgentInfo = new Vue({
        el: '#vue_agent_info',
        data: {
            agent: {
                title : {
                    AGENT_ID:               '<?=$agent['AGENT_ID']?>',
                    FULL_NAME:              '<?=$agent['FULL_NAME']?>',
                    WEB_NAME:               '<?=$agent['WEB_NAME']?>',
                    STATE_ID:               '<?=$agent['STATE_ID']?>',
                    GP_CONTRACT:            '<?=$agent['GP_CONTRACT']?>',
                    GP_CONTRACT_DATE:       '<?=$agent['GP_CONTRACT_DATE']?>'
                },
                info : {
                    AGENT_INN:              '<?=$agent['AGENT_INN']?>',
                    AGENT_KPP:              '<?=$agent['AGENT_KPP']?>',
                    AGENT_Y_ADDRESS:        '<?=$agent['AGENT_Y_ADDRESS']?>',
                    AGENT_P_ADDRESS:        '<?=$agent['AGENT_P_ADDRESS']?>',
                    AGENT_F_ADDRESS:        '<?=$agent['AGENT_F_ADDRESS']?>',
                    AGENT_EMAIL:            '<?=$agent['AGENT_EMAIL']?>',
                    AGENT_PHONE:            '<?=$agent['AGENT_PHONE']?>',
                    AGENT_CITY:             '<?=$agent['AGENT_CITY']?>',
                    AGENT_SIGNER_POST_1:    '<?=$agent['AGENT_SIGNER_POST_1']?>',
                    AGENT_SIGNER_NAME_1:    '<?=$agent['AGENT_SIGNER_NAME_1']?>',
                    AGENT_SIGNER_POST_2:    '<?=$agent['AGENT_SIGNER_POST_2']?>',
                    AGENT_SIGNER_NAME_2:    '<?=$agent['AGENT_SIGNER_NAME_2']?>'
                },
                service : {
                    SEND_FROM:              '<?=$agent['SEND_FROM']?>',
                    BILL_PREFIX:            '<?=$agent['BILL_PREFIX']?>',
                    DEFAULT_GOOD_NAME:      '<?=$agent['DEFAULT_GOOD_NAME']?>',
                    OFFICE_LINK:            '<?=$agent['OFFICE_LINK']?>',
                    SENDER_SMS:             '<?=$agent['SENDER_SMS']?>'
                }
            }
        },
        computed: {
            smsSubscriptionFormatted: function () {
                var enable = this.agent.service.SENDER_SMS;

                return '<input type="checkbox" '+ (enable ? 'checked' : '') +' disabled>';
            },
            emailFormatted: function () {
                var email = this.agent.info.AGENT_EMAIL;

                return email ? '<a href="mailto:' + email + '">' + email + '</a>' : '';
            },
            statusFormatted: function () {
                var status = this.agent.title.STATUS_ID;

                return status == <?=Model_Agent::AGENT_STATUS_ACTIVE?> ? '<span class="label label_success">В работе</span>' : '<span class="label label_error">Заблокирован</span>';
            }
        },
        methods: {
            checkEmpty: function (val) {
                return val ? val : '<i class="gray">Не заполнено</i>';
            },
            cancelForm: function () {
                this.agent = JSON.parse(JSON.stringify(this._cache));
            },
            cacheForm: function () {
                this._cache = JSON.parse(JSON.stringify(this.agent));
            }
        },
        mounted: function () {
            this.cacheForm();
            renderPhoneInput($('[name=phone]'));
        }
    });

    <?if (Access::allow('root')) {?>
    function saveAgentServiceInfo()
    {
        var params = vueRawData(vueAgentInfo.agent.service);

        $.post('/administration/agent-edit/' + agentId, { params:params, part: '<?=Model_Agent::AGENT_PART_SERVICE?>' }, function(data){
            if(data.success){
                message(1, 'Сервисный блок настроек агента обновлен');
            }else{
                message(0, 'Сохранение не удалось');
            }

            vueAgentInfo.cacheForm();
        });
    }

    function saveAgentTitleInfo()
    {
        var params = vueRawData(vueAgentInfo.agent.title);

        $.post('/administration/agent-edit/' + agentId, { params:params, part: '<?=Model_Agent::AGENT_PART_TITLE?>' }, function(data){
            if(data.success){
                message(1, 'Титульный блок настроек агента обновлен');
            }else{
                message(0, 'Сохранение не удалось');
            }

            vueAgentInfo.cacheForm();
        });
    }
    <?}?>

    function saveAgentInfoInfo()
    {
        var params = vueRawData(vueAgentInfo.agent.info);

        $.post('/administration/agent-edit/' + agentId, { params:params, part: '<?=Model_Agent::AGENT_PART_INFO?>' }, function(data){
            if(data.success){
                message(1, 'Информационный блок настроек агента обновлен');
            }else{
                message(0, 'Сохранение не удалось');
            }

            vueAgentInfo.cacheForm();
        });
    }
</script>