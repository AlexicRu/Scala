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

        $popupManagerAdd = Common::popupForm('Добавление менеджера', 'manager/add');

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

        $managerSettingsForm = View::factory('/forms/manager/settings');

        $managerSettingsForm
            ->set('manager', $manager)
            ->set('width', 100)
            ->set('reload', 0)
            ->set('changeRole', 1)
        ;

        $popupManagerAddClients = Common::popupForm('Добавление клиентов', 'manager/add_clients');

        $html = View::factory('/ajax/control/manager')
            ->bind('managerId', $managerId)
            ->bind('manager', $manager)
            ->bind('managerSettingsForm', $managerSettingsForm)
            ->bind('popupManagerAddClients', $popupManagerAddClients)
        ;

        $this->html($html);
    }

    /**
     * страницы групп точек
     */
    public function action_dots()
    {
        $this->title[] = 'Группы точек';

        $filter = $this->request->query('filter');

        $dotsGroups = Model_Dot::getGroups($filter);

        $popupAddDotsGroup = Common::popupForm('Добавление группы точек', 'control/add_dots_group');
        $popupAddDot = Common::popupForm('Добавление точки', 'control/add_dot');

        $popupEditDotsGroup = Common::popupForm('Редактирование группы точек', 'control/edit_dots_group');

        $this->tpl
            ->bind('dotsGroups', $dotsGroups)
            ->bind('filter', $filter)
            ->bind('popupEditDotsGroup', $popupEditDotsGroup)
            ->bind('popupAddDot', $popupAddDot)
            ->bind('popupAddDotsGroup', $popupAddDotsGroup)
        ;
    }

    /**
     * оболочка для постраничной загрузки списка точек
     */
    public function action_group_dots()
    {
        $groupId = $this->request->param('id');

        $html = View::factory('/ajax/control/dots')
            ->bind('groupId', $groupId)
        ;

        $this->html($html);
    }

    /**
     * получаем список точек по группе
     */
    public function action_load_group_dots()
    {
        $params = [
            'group_id'	    => $this->request->post('group_id'),
            'offset' 		=> $this->request->post('offset'),
            'pagination'    => true
        ];

        list($dots, $more) = Model_Dot::getGroupDots($params);

        if(empty($dots)){
            $this->jsonResult(false);
        }

        $this->jsonResult(true, ['items' => $dots, 'more' => $more]);
    }
}
