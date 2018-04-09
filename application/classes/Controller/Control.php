<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Control extends Controller_Common {

	public function before()
	{
		parent::before();

		$this->title[] = 'Управление';
	}

	public function action_index()
	{
        $this->redirect('/control/managers');
	}

    /**
     * получаем список манегеров
     */
	public function action_managers()
    {
        $this->title[] = 'Менеджеры';

        $filter = $this->request->query('filter') ?: ['only_managers' => 1];

        $user = Auth::instance()->get_user();

        $params = [
            'agent_id' => $user['AGENT_ID'],
            'not_admin' => true
        ];

        $params = array_merge($params, $filter);

        $managers = Model_Manager::getManagersList($params);

        $popupManagerAdd = Form::popup('Добавление менеджера', 'manager/add');

        $this->tpl
            ->bind('managers', $managers)
            ->bind('popupManagerAdd', $popupManagerAdd)
            ->bind('filter', $filter)
        ;
    }

    /**
     * а вот тут уже аяксово получаем инфу по конкретному менеджеру
     */
    public function action_manager()
    {
        $managerId = $this->request->param('id');

        $manager = Model_Manager::getManager($managerId);

        if(empty($manager)){
            $this->html('<div class="error_block">Ошибка</div>');
        }

        $managerSettingsForm = View::factory('forms/manager/settings');

        $managerSettingsForm
            ->set('manager', $manager)
            ->set('width', 100)
            ->set('reload', 0)
            ->set('changeRole', 1)
        ;

        $popupManagerAddClients = Form::popup('Добавление клиентов', 'manager/add_clients');
        $popupManagerAddReports = Form::popup('Доступные для добавления отчеты', 'manager/add_reports');

        $html = View::factory('ajax/control/manager')
            ->bind('managerId', $managerId)
            ->bind('manager', $manager)
            ->bind('managerSettingsForm', $managerSettingsForm)
            ->bind('popupManagerAddClients', $popupManagerAddClients)
            ->bind('popupManagerAddReports', $popupManagerAddReports)
        ;

        $this->html($html);
    }

    /**
     * страницы групп точек
     */
    public function action_dotsGroups()
    {
        $this->title[] = 'Группы точек';

        $filter = $this->request->query('filter');

        $dotsGroups = Model_Dot::getGroups($filter);

        $popupAddDotsGroup = Form::popup('Добавление группы точек', 'control/add_dots_group');
        $popupAddDots = Form::popup('Добавление точек', 'control/add_dots');

        $popupEditDotsGroup = Form::popup('Редактирование группы точек', 'control/edit_dots_group');

        $this->tpl
            ->bind('dotsGroups', $dotsGroups)
            ->bind('filter', $filter)
            ->bind('popupEditDotsGroup', $popupEditDotsGroup)
            ->bind('popupAddDots', $popupAddDots)
            ->bind('popupAddDotsGroup', $popupAddDotsGroup)
        ;
    }

    /**
     * оболочка для постраничной загрузки списка точек группы
     */
    public function action_groupDots()
    {
        $groupId = $this->request->param('id');

        $group = Model_Dot::getGroup($groupId);

        $user = User::current();

        $canEdit = true;
        if($group['GROUP_TYPE'] == Model_Dot::GROUP_TYPE_SUPPLIER && !in_array($user['role'], Access::$adminRoles)){
            $canEdit = false;
        }

        $html = View::factory('ajax/control/dots_in_group')
            ->bind('groupId', $groupId)
            ->bind('canEdit', $canEdit)
        ;

        $this->html($html);
    }

    /**
     * получаем список точек по группе
     */
    public function action_loadGroupDots()
    {
        $params = [
            'group_id'	    => $this->request->post('group_id') ?: $this->request->query('group_id'),
            'offset' 		=> $this->request->post('offset'),
            'pagination'    => $this->toXls ? false : true
        ];

        $result = Model_Dot::getGroupDots($params);

        if ($this->toXls){
            $this->showXls('group_dots', $result, [
                'PROJECT_NAME'  => 'Шаблон ТО',
                'ID_EMITENT'    => 'Эмитент',
                'ID_TO'         => 'Номер ТО',
                'POS_NAME'      => 'Название',
                'OWNER'         => 'Владелец',
                'POS_ADDRESS'   => 'Адрес'
            ], true);
        } else {
            list($dots, $more) = $result;
        }

        if(empty($dots)){
            $this->jsonResult(false);
        }

        $this->jsonResult(true, ['items' => $dots, 'more' => $more]);
    }

    /**
     * удаляем группы точек
     */
    public function action_delGroupDots()
    {
        $groupsIds = $this->request->post('groups');

        $deleted = $notDeleted = [];

        if(is_array($groupsIds)) {
            $user = User::current();

            $groups = Model_Dot::getGroups(['ids' => $groupsIds]);

            foreach($groupsIds as $groupId) {

                $canEdit = true;
                foreach($groups as $group){
                    if($group['GROUP_ID'] == $groupId && $group['GROUP_TYPE'] == Model_Dot::GROUP_TYPE_SUPPLIER && !in_array($user['role'], Access::$adminRoles)){
                        $canEdit = false;
                        break;
                    }
                }

                list($dots, $more) = Model_Dot::getGroupDots(['group_id' => $groupId]);

                if(empty($dots) && $canEdit) {
                    $deleted[$groupId] = Oracle::CODE_SUCCESS == Model_Contract::editDotsGroup(['group_id' => $groupId], Model_Contract::DOTS_GROUP_ACTION_DEL);
                }else{
                    $notDeleted[$groupId] = true;
                }
            }
        }

        $this->jsonResult(true, ['deleted' => $deleted, 'not_deleted' => $notDeleted]);
    }

    /**
     * удаление точек из группы
     */
    public function action_delDots()
    {
        $groupId = $this->request->post('group_id');
        $dots = $this->request->post('dots');

        $result = Model_Dot::editDotsToGroup($groupId, $dots, Model_Dot::ACTION_DEL);

        $this->jsonResult($result);
    }

    /**
     * добавляем группу фирм
     */
    public function action_addFirmsGroup()
    {
        $params = $this->request->post('params');

        $result = Model_Firm::addFirmsGroup($params);

        if(empty($result)){
            $this->jsonResult(false);
        }

        $this->jsonResult(true);
    }

    /**
     * добавляем группу точек
     */
    public function action_addDotsGroup()
    {
        $params = $this->request->post('params');

        $result = Model_Contract::addDotsGroup($params);

        if(!empty($result)){
            $this->jsonResult(false);
        }

        $this->jsonResult(true);
    }

    /**
     * редактирование группы точек
     */
    public function action_editDotsGroup()
    {
        $params = $this->request->post('params');

        $result = Model_Contract::editDotsGroup($params);

        if(!empty($result)){
            $this->jsonResult(false);
        }

        $this->jsonResult(true);
    }

    /**
     * показываем таб списка точек, сами точки будут аяксом постранично грузиться
     */
    public function action_showDots()
    {
        $postfix = $this->request->post('postfix') ?: '';
        $showCheckbox = $this->request->post('show_checkbox') ?: '';
        $groupId = $this->request->post('group_id') ?: '';

        $html = View::factory('ajax/control/dots')
            ->bind('postfix', $postfix)
            ->bind('showCheckbox', $showCheckbox)
            ->bind('groupId', $groupId)
        ;

        $this->html($html);
    }

    /**
     * аяксовая постраничная загрузка точек
     */
    public function action_loadDots()
    {
        $params = [
            'POS_ID'        => $this->request->post('POS_ID'),
            'ID_EMITENT'    => $this->request->post('ID_EMITENT'),
            'ID_TO'         => $this->request->post('ID_TO'),
            'POS_NAME'      => $this->request->post('POS_NAME'),
            'OWNER'         => $this->request->post('OWNER'),
            'POS_ADDRESS'   => $this->request->post('POS_ADDRESS'),
            'PROJECT_NAME'  => $this->request->post('PROJECT_NAME'),
            'group_id' 		=> $this->request->post('group_id'),
            'offset' 		=> $this->request->post('offset'),
            'pagination'    => true
        ];

        if ($this->toXls) {
            unset($params['pagination']);

            $params['POS_ID'] = explode(',', $this->request->query('pos_id'));
        }

        $result = Model_Dot::getDots($params);

        if ($this->toXls){
            $this->showXls('dots', $result, [
                'PROJECT_NAME'  => 'Шаблон ТО',
                'ID_EMITENT'    => 'Эмитент',
                'ID_TO'         => 'Номер ТО',
                'POS_NAME'      => 'Название',
                'OWNER'         => 'Владелец',
                'POS_ADDRESS'   => 'Адрес'
            ], true);
        } else {
            list($dots, $more) = $result;
        }

        if(empty($dots)){
            $this->jsonResult(false);
        }

        $this->jsonResult(true, ['items' => $dots, 'more' => $more]);
    }

    /**
     * добавляем точки к конкретной группе
     */
    public function action_addDotsToGroup()
    {
        $posIds = $this->request->post('pos_ids');
        $groupId = $this->request->post('group_id');

        $result = Model_Dot::editDotsToGroup($groupId, $posIds);

        $this->jsonResult((bool) $result);
    }

    /**
     * страница тарифов
     */
    public function action_tariffs()
    {
        $this->scripts[] = Common::getAssetsLink() . 'js/control/tariffs.js';
        $this->scripts[] = '/assets/plugins/jquery.mask.js';

        $filter = $this->request->query('filter') ?: ['only_managers' => 1];

        $tariffs = Model_Tariff::getAvailableTariffs($filter);

        $this->tpl
            ->bind('tariffs', $tariffs)
            ->bind('filter', $filter)
        ;
    }

    /**
     * загрудаем выбранный тариф
     */
    public function action_loadTariff()
    {
        $tariffId = $this->request->param('id');

        if($tariffId == -1){
            $this->html(Model_Tariff::buildTemplate([], []));
        }

        $lastVersion = $this->request->post('version');

        $tariff = Model_Tariff::getAvailableTariffs(['tariff_id' => $tariffId]);
        if(!empty($tariff)){
            $tariff = reset($tariff);
        }

        $tariffSettings = Model_Tariff::getTariffSettings($tariffId, $lastVersion);

        $this->html(Model_Tariff::buildTemplate($tariff, $tariffSettings));
    }

    /**
     * грузим свеженький шаблон шаблон условий
     */
    public function action_getTariffReferenceTpl()
    {
        $usedConditions = $this->request->post('used_conditions');
        $uidSection = $this->request->post('uid_section');
        $index = $this->request->post('index');

        $reference = Model_Tariff::getReference();

        foreach($reference as $referenceBlock){
            $referenceItem = reset($referenceBlock);

            if(empty($usedConditions) || !in_array($referenceItem['CONDITION_ID'], $usedConditions)){
                $conditionId = $referenceItem['CONDITION_ID'];
                $compareId = $referenceItem['COMPARE_ID'];
                break;
            }
        }

        if(empty($conditionId)){
            $this->jsonResult(false);
        }

        $uid = $uidSection.'_'.$index;

        $html = strval(Model_Tariff::buildReference($uid, $reference));

        $this->jsonResult(true, [
            'html' => $html,
            'condition_id' => $conditionId,
            'compare_id' => $compareId,
            'uid' => $uid
        ]);
    }

    /**
     * подгружаем пустой шаблон секции
     */
    public function action_getTariffSectionTpl()
    {
        $uidSection = $this->request->post('uid_section');
        $sectionNum = $this->request->post('section_num');

        $section = ['SECTION_NUM' => $sectionNum];

        $html = strval(Model_Tariff::buildSection($uidSection, $section));

        $this->jsonResult(true, [
            'html' => $html,
        ]);
    }

    /**
     * сохраняем тариф
     */
    public function action_editTariff()
    {
        $params = $this->request->post('params');
        $tariffId = $this->request->post('tariff_id');

        $res = Model_Tariff::edit($params, $tariffId);

        if(empty($res)){
            $this->jsonResult(false);
        }
        $this->jsonResult(true);
    }

    /**
     * страница загрузки транзакций
     */
    public function action_1cConnect()
    {
        $this->title[] = 'Связь с 1с';

        $this->_initDropZone();
        $this->_initJsGrid();
    }

    /**
     * считываем файл с платежами
     */
    public function action_uploadPays()
    {
        $file = Upload::uploadFile('pays');

        if(empty($file)){
            $this->jsonResult(false);
        }

        list($data, $mimeType) = Upload::readFile($_SERVER["DOCUMENT_ROOT"].$file['file']);

        if(empty($data)){
            $this->jsonResult(false);
        }

        $rows = (new Model_Transaction_Parser())->parse($data, $mimeType);

        $this->jsonResult(true, $rows);
    }

    /**
     * страница работыс группами фирм
     */
    public function action_firmsGroups()
    {
        $this->title[] = 'Группы фирм';

        $filter = $this->request->query('filter');

        $firmsGroups = Model_Firm::getFirmsGroups($filter);

        $popupAddFirms = Form::popup('Добавление фирм', 'control/add_firms');
        $popupAddFirmsGroup = Form::popup('Добавление группы фирм', 'control/add_firms_group');
        $popupEditFirmsGroup = Form::popup('Редактирование группы фирм', 'control/edit_firms_group');

        $this->tpl
            ->bind('firmsGroups', $firmsGroups)
            ->bind('filter', $filter)
            ->bind('popupAddFirms', $popupAddFirms)
            ->bind('popupAddFirmsGroup', $popupAddFirmsGroup)
            ->bind('popupEditFirmsGroup', $popupEditFirmsGroup)
        ;
    }

    /**
     * страница работыс группами карт
     */
    public function action_cardsGroups()
    {
        $this->title[] = 'Группы карт';

        $filter = $this->request->query('filter');

        $cardsGroups = Model_Card::getGroups($filter);

        $popupAddCards = Form::popup('Добавление карт', 'control/add_cards');
        $popupAddCardsGroup = Form::popup('Добавление группы карт', 'control/add_cards_group');
        $popupEditCardsGroup = Form::popup('Редактирование группы точек', 'control/edit_cards_group');

        $this->tpl
            ->bind('cardsGroups', $cardsGroups)
            ->bind('filter', $filter)
            ->bind('popupAddCards', $popupAddCards)
            ->bind('popupAddCardsGroup', $popupAddCardsGroup)
            ->bind('popupEditCardsGroup', $popupEditCardsGroup)
        ;
    }

    /**
     * добавление группы карт
     */
    public function action_addCardsGroup()
    {
        $params = $this->request->post('params');

        $result = Model_Card::addCardsGroup($params);

        if(!empty($result)){
            $this->jsonResult(false);
        }

        $this->jsonResult(true);
    }

    /**
     * грузим список фирм по группе
     */
    public function action_loadGroupFirms()
    {
        //если это есть значит уже грузим данные а не страницу
        $offset = $this->request->post('offset');

        if(is_null($offset) && !$this->toXls){

            $groupId = $this->request->param('id');

            $user = User::current();

            $canEdit = true;
            if (!in_array($user['role'], Access::$adminRoles)) {
                $canEdit = false;
            }

            $html = View::factory('ajax/control/firms_in_group')
                ->bind('groupId', $groupId)
                ->bind('canEdit', $canEdit);

            $this->html($html);
        }else{
            $params = [
                'group_id'      => $this->request->post('group_id'),
                'offset'        => $offset,
                'pagination'    => $this->toXls ? false : true
            ];

            $result = Model_Card::getGroupCards($params);

            if ($this->toXls){
                $this->showXls('group_firms', $result, [
                    'CLIENT_ID'         => 'CLIENT ID',
                    'HOLDER'            => 'Владелец',
                    'DESCRIPTION_RU'    => 'Описание'
                ]);
            } else {
                list($items, $more) = $result;
            }

            if (empty($items)) {
                $this->jsonResult(false);
            }

            $this->jsonResult(true, ['items' => $items, 'more' => $more]);
        }
    }

    /**
     * грузим список карт по группе
     */
    public function action_loadGroupCards()
    {
        //если это есть значит уже грузим данные а не страницу
        $offset = $this->request->post('offset');

        if(is_null($offset) && !$this->toXls){

            $groupId = $this->request->param('id');

            $user = User::current();

            $canEdit = true;
            /*if (in_array($user['role'], Access::$adminRoles)) {
                $canEdit = true;
            }*/

            $html = View::factory('ajax/control/cards_in_group')
                ->bind('groupId', $groupId)
                ->bind('canEdit', $canEdit);

            $this->html($html);
        }else{
            $params = [
                'group_id'      => $this->request->post('group_id') ?: $this->request->query('group_id'),
                'offset'        => $offset,
                'pagination'    => $this->toXls ? false : true
            ];

            $result = Model_Card::getGroupCards($params);

            if ($this->toXls){
                $this->showXls('group_cards', $result, [
                    'GROUP_ID'			=> 'ID Группы',
                    'GROUP_NAME'		=> 'Наименование',
                    'CARD_ID'           => 'Номер карты',
                    'HOLDER'            => 'Владелец',
                    'DESCRIPTION_RU'    => 'Описание'
                ]);
            } else {
                list($items, $more) = $result;
            }

            if (empty($items)) {
                $this->jsonResult(false);
            }

            $this->jsonResult(true, ['items' => $items, 'more' => $more]);
        }
    }

    /**
     * редактирование группы карт
     */
    public function action_editCardsGroup()
    {
        $params = $this->request->post('params');

        $result = Model_Card::editCardsGroup($params);

        if(!empty($result)){
            $this->jsonResult(false);
        }

        $this->jsonResult(true);
    }

    /**
     * показываем карты, сами карты будут аяксом постранично грузиться
     */
    public function action_showCards()
    {
        $postfix = $this->request->post('postfix') ?: '';
        $showCheckbox = $this->request->post('show_checkbox') ?: '';
        $groupId = $this->request->post('group_id') ?: '';

        $html = View::factory('ajax/control/cards')
            ->bind('postfix', $postfix)
            ->bind('showCheckbox', $showCheckbox)
            ->bind('groupId', $groupId)
        ;

        $this->html($html);
    }

    /**
     * аяксовая постраничная загрузка точек
     */
    public function action_loadCards()
    {
        $params = [
            'CARD_ID'           => $this->request->post('CARD_ID'),
            'HOLDER'            => $this->request->post('HOLDER'),
            'DESCRIPTION_RU'    => $this->request->post('DESCRIPTION_RU'),
            'group_id' 		    => $this->request->post('group_id'),
            'offset' 		    => $this->request->post('offset'),
            'pagination'        => true
        ];

        list($dots, $more) = Model_Card::getAvailableGroupCards($params);

        if(empty($dots)){
            $this->jsonResult(false);
        }

        $this->jsonResult(true, ['items' => $dots, 'more' => $more]);
    }

    /**
     * добавляем точки к конкретной группе
     */
    public function action_addCardsToGroup()
    {
        $cardsIds = $this->request->post('cards_ids');
        $groupId = $this->request->post('group_id');

        $result = Model_Card::addCardsToGroup($groupId, $cardsIds);

        if(!empty($result)){
            $this->jsonResult(false);
        }

        $this->jsonResult(true);
    }

    /**
     * удаление группы
     */
    public function action_delCardsGroup()
    {
        $groups = (array)$this->request->post('groups');

        $result = [];

        foreach ($groups as $group) {
            $result[] = [
                'group_id' => $group,
                'deleted' => Model_Card::editCardsGroup(['group_id' => $group], Model_Card::CARDS_GROUP_ACTION_DEL)
            ];
        }

        $this->jsonResult($result);
    }

    /**
     * удаление карт из группы
     */
    public function action_delCardsFromGroup()
    {
        $cardsNumbers = $this->request->post('cards_numbers');
        $groupId = $this->request->post('group_id');

        $result = Model_Card::delCardsFromGroup($groupId, $cardsNumbers);

        $this->jsonResult($result);
    }

    /**
     * рендерим блок формы
     */
    public function action_clientContractForm()
    {
        $iteration = $this->request->query('iteration') ?: 1;

        $html = View::factory('ajax/control/connect_1c/client_contract_form')
            ->bind('iteration', $iteration)
        ;

        $this->html($html);
    }

    /**
     * экспорт данный для 1с
     */
    public function action_1cExport()
    {
        $get = $this->request->query();

        if (!empty($get['contracts'])) {
            $contracts = [];

            foreach ($get['contracts'] as $item) {
                $contracts = array_merge($contracts, (array)$item);
            }

            $get['contracts'] = $contracts;
        }

        $user = User::current();

        $report = Report_1c_Common::factory($user['AGENT_ID']);

        $data = $report->getDataForExport($get);

        $this->showXml($report->generateXmlForExport($data));
    }
}
