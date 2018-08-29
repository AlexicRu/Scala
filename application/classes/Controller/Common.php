<?php defined('SYSPATH') or die('No direct script access.');
 
abstract class Controller_Common extends Controller_Template {

    public $template = 'layout';
    public $title = [];
    public $errors = [];
    public $scripts = [];
    public $scriptsRaw = [];
    public $styles = [];

    /**
     * @var View
     */
    public $tpl = '';
    public $toXls = false;

    public function before()
    {
        View::set_global('js', []);
        View::set_global('css', []);

        $controller = Text::camelCaseToDashed($this->request->controller());
        $action = Text::camelCaseToDashed($this->request->action());

        $withoutAuth = Kohana::$config->load('access')['without_auth'];

        if(!User::loggedIn()){
            if(!in_array($controller . '_' . $action, $withoutAuth) && $_SERVER['REQUEST_URI'] != '/'){
                $this->redirect('/');
            }
            $this->template = 'not_auth';
        }else{
            //подключаем меню
            $menu = Kohana::$config->load('menu');
            $content = View::factory('_includes/menu')
                ->bind('menu', $menu);

            View::set_global('menu', $content);
        }

        parent::before();

        $allow = Access::allow(strtolower($controller.'_'.$action), true);

        if ($this->request->query('to_xls')) {
            $this->toXls = true;
        }

        //если не аяксовый запрос
        if(!$this->request->is_ajax() && !$this->toXls && !in_array($action, ['get-json'])) {
            //проверяем кастомный дизайн
            $this->_checkCustomDesign();

            if($allow == false){
                throw new HTTP_Exception_403();
            }

            //рендерим шаблон страницы
            try {
                $this->tpl = View::factory('pages/' . $controller . '/' . $action);
            } catch (Exception $e) {
                throw new HTTP_Exception_404();
            }

            //прикрепляем файлы стилей и скриптов
            $this->_appendFilesBefore();

            //выполняем различные функции, которые необходимо выполнить до загрузки страницы
            $this->_actionsBefore();
        }

        //если все таки аякс
        if($allow == false){
            echo '<script>alert("У вас недостаточно прав доступа");</script>';
            die;
        }

        $this->_collectAdditionalDataForForms();
    }

    /**
     * прописываем глобальные конфиги
     *
     * @throws Kohana_Exception
     */
    public function after()
    {
        $this->_appendFiles();

        if(!$this->request->is_ajax()) {
            $this->_appendFilesAfter();
        }

        View::set_global('user', Auth_Oracle::instance()->get_user());

        $config = Kohana::$config->load('main')->as_array();
        foreach ($config as $k => $v) {
            View::set_global($k, $v);
        }

        View::set_global('title', implode(" :: ",$this->title));
        View::set_global('errors', $this->errors);

        if(Auth::instance()->logged_in()) {
            $notices = Model_Message::getList(['status' => Model_Message::MESSAGE_STATUS_NOTREAD]);

            $notices = Model_Message::clearBBCodes($notices);

            View::set_global('notices', $notices);

            if(!$this->request->is_ajax()) {
                $this->_checkGlobalMessages();
            }
        }

        $this->template->content = $this->tpl;

        parent::after();
    }

    public function html($data){
        echo $data;
        exit;
    }

    public function showFile($file)
    {
        $path = $_SERVER['DOCUMENT_ROOT'];
        $directory = DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR;

        if (!file_exists($path . $directory . $file)) {
            throw new HTTP_Exception_404();
        }

        header("X-Accel-Redirect: " . $directory . $file);
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file));
        die;
    }

    /**
     * show xml
     * @param $xml
     */
    public function showXml($xml)
    {
        header('Content-type: text/xml');
        header('Content-Disposition: attachment; filename="export.xml"');

        echo $xml;
        exit;
    }

    public function json($data){
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function jsonResult($result, $data = [])
    {
        self::json(['success' => $result, 'data' => $data, 'messages' => Messages::get()]);
    }

    public function isPost()
    {
        return HTTP_Request::POST == $this->request->method();
    }

    /**
     * проверка необходимости применить кастомный дизайн
     *
     * @throws Kohana_Exception
     */
    private function _checkCustomDesign()
    {
        //опредеяем кастомный диазйн
        $design = Kohana::$config->load('design')->as_array();

        $url = str_replace(['.', '-'], '', !empty($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '');

        if(isset($design['url'][$url])){
            $customView = $design['url'][$url]['class'];
            $this->title[] = $design['url'][$url]['title'];
        }

        $user = User::current();

        if (!empty($design['user']['a_' . $user['AGENT_ID']])) {
            if (
                empty($design['user']['a_' . $user['MANAGER_ID']]['url']) ||
                $design['user']['a_' . $user['MANAGER_ID']]['url'] == $url
            ) {
                $customView = $design['user']['a_' . $user['AGENT_ID']]['class'];
                $this->title[] = $design['user']['a_' . $user['AGENT_ID']]['title'];
            }
        }

        if (!empty($design['user']['u_' . $user['MANAGER_ID']])) {
            if (
                empty($design['user']['u_' . $user['MANAGER_ID']]['url']) ||
                $design['user']['u_' . $user['MANAGER_ID']]['url'] == $url
            ) {
                $customView = $design['user']['u_' . $user['MANAGER_ID']]['class'];
                $this->title[] = $design['user']['u_' . $user['MANAGER_ID']]['title'];
            }
        }

        //если не смогли определить дизайн под конкретный урл, то грузим дефолтовый
        if(empty($customView)){
            $customView = $design['default']['class'];
            $this->title[] = $design['default']['title'];
        }

        View::set_global('customView', $customView);
    }

    /**
     * подключаем файлы стией и скриптов
     *
     * @throws Kohana_Exception
     */
    private function _appendFilesBefore()
    {
        if(Auth::instance()->logged_in()) {
            $this->template->styles = [
                '/assets/plugins/jGrowl/jGrowl.css',
                '/assets/plugins/fancy/jquery.fancybox.css',
            ];
            $this->template->scripts = [
                '/assets/plugins/jquery.2.1.3.min.js',
                '/assets/plugins/jquery-ui.1.11.2.min.js',
                '/assets/plugins/jGrowl/jGrowl.js',
                '/assets/plugins/fancy/jquery.fancybox.js',
                '/assets/plugins/jquery.maskMoney.min.js',
            ];
        }else{
            $this->template->styles = [];
            $this->template->scripts = [
                //'https://www.google.com/recaptcha/api.js',
                '/assets/plugins/jquery.2.1.3.min.js',
            ];
        }
        $this->template->scriptsRaw = [];
    }

    private function _appendFiles()
    {
        $styles = array_merge(
            $this->styles,
            (array)(new View())->css
        );

        $scripts = array_merge(
            $this->scripts,
            (array)(new View())->js
        );

        foreach($styles as $style){
            $this->template->styles[] =  $style;
        }
        foreach($scripts as $script){
            $this->template->scripts[] =  $script;
        }
        foreach($this->scriptsRaw as $script){
            $this->template->scriptsRaw[] =  $script;
        }
    }

    private function _appendFilesAfter()
    {
        if(Auth::instance()->logged_in()) {
            $this->_initTooltipster();

            $this->template->styles[] = Common::getAssetsLink() . 'css/ui.css';
            $this->template->styles[] = Common::getAssetsLink() . 'css/style.css';
            $this->template->styles[] = Common::getAssetsLink() . 'css/design.css';

            $this->template->scripts[] = Common::getAssetsLink() . 'js/ui.js';
            $this->template->scripts[] = Common::getAssetsLink() . 'js/common.js';
            $this->template->scripts[] = Common::getAssetsLink() . 'js/site.js';
        }else{
            $this->template->styles[] = Common::getAssetsLink() . 'css/style.css';
            $this->template->styles[] = Common::getAssetsLink() . 'css/design.css';
        }
    }

    /**
     * подключаем скрипты и стили редактора
     */
    protected function _initWYSIWYG()
    {
        $this->template->styles[] = '/assets/plugins/trumbowyg/ui/trumbowyg.min.css';
        $this->template->styles[] = '/assets/plugins/trumbowyg/plugins/colors/ui/trumbowyg.colors.min.css';
        $this->template->scripts[] = '/assets/plugins/trumbowyg/trumbowyg.min.js';
        $this->template->scripts[] = '/assets/plugins/trumbowyg/plugins/colors/trumbowyg.colors.min.js';
        $this->template->scripts[] = '/assets/plugins/trumbowyg/plugins/noembed/trumbowyg.noembed.min.js';
        $this->template->scripts[] = '/assets/plugins/trumbowyg/plugins/upload/trumbowyg.upload.min.js';
    }

    /**
     * подключаем скрипты и стили аяксовой загрузки картинок
     */
    protected function _initDropZone()
    {
        $this->template->styles[] = '/assets/plugins/dropzone/dropzone.css';
        $this->template->scripts[] = '/assets/plugins/dropzone/dropzone.5.3.0.js';
    }

    /**
     * подключаем скрипты и стили JsGrid
     */
    protected function _initJsGrid()
    {
        $this->template->styles[] = '/assets/plugins/jsgrid/jsgrid.min.css';
        $this->template->styles[] = '/assets/plugins/jsgrid/jsgrid-theme.min.css';
        $this->template->scripts[] = '/assets/plugins/jsgrid/jsgrid.js';
    }

    /**
     * подключаем скрипты и стили EnjoyHint
     */
    protected function _initEnjoyHint()
    {
        $this->template->styles[] = '/assets/plugins/enjoyhint/enjoyhint.css';
        $this->template->scripts[] = '/assets/plugins/enjoyhint/enjoyhint.min.js';
        $this->template->scripts[] = '/assets/js/scenarios.js';

        $webtours = Kohana::$config->load('access')['webtours'];

        $user = User::current();

        $script = [];

        foreach ($webtours as $key => $webtour) {
            if (in_array($user['ROLE_ID'], $webtour['roles'])) {
                $script[$key] = $webtour['scenario'];
            }
        }

        $this->template->scriptsRaw[] = 'var scenarios = ' . json_encode($script) . ' ;';

        if (!isset($user['tours'])) {
            $user['tours'] = User::getWebTours();

            Auth::instance()->saveSession($user);
        }
    }

    /**
     * подключаем VueJs
     */
    protected function _initVueJs()
    {
        $this->template->scripts[] = 'https://cdn.jsdelivr.net/npm/vue';
    }

    /**
     * подключаем ChartJs
     */
    protected function _initChart()
    {
        $this->template->scripts[] = '/assets/plugins/palette.js';
        $this->template->scripts[] = '//www.amcharts.com/lib/3/amcharts.js';
        $this->template->scripts[] = '//www.amcharts.com/lib/3/serial.js';
        $this->template->scripts[] = '//www.amcharts.com/lib/3/pie.js';
        $this->template->scripts[] = '//www.amcharts.com/lib/3/themes/light.js';
    }

    /**
     * подключаем Tooltipster
     */
    protected function _initTooltipster()
    {
        $this->template->styles[] = '/assets/plugins/tooltipster/css/tooltipster.bundle.min.css';
        $this->template->styles[] = '/assets/plugins/tooltipster/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-shadow.min.css';
        $this->template->scripts[] = '/assets/plugins/tooltipster/js/tooltipster.bundle.min.js';
    }

    /**
     * подключаем флаги
     */
    protected function _initPhoneInputWithFlags()
    {
        $this->template->styles[] = '/assets/plugins/intl-tel-input/css/intlTelInput.min.css';
        $this->template->scripts[] = '/assets/plugins/intl-tel-input/js/intlTelInput.min.js';
    }

    /**
     * через js можно собрать данные с разных форм, и если так, то их надо раскидать по нормальным полям
     */
    private function _collectAdditionalDataForForms()
    {
        $additionalFormDataPOST = $this->request->post('other_data');
        $additionalFormDataGET = $this->request->query('other_data');

        if(!empty($additionalFormDataPOST)){
            $params = [];
            parse_str(urldecode($additionalFormDataPOST), $params);

            foreach($params as $key => $value){
                $data = $this->request->post($key) ?: [];

                $this->request->post($key, array_merge($data, $value));
            }
        }

        if(!empty($additionalFormDataGET)){
            $params = [];
            parse_str(urldecode($additionalFormDataGET), $params);

            foreach($params as $key => $value){
                $data = $this->request->query($key) ?: [];

                $this->request->query($key, array_merge($data, $value));
            }
        }
    }

    /**
     * показываем как XLS
     *
     * @param $rows
     * @param $headers
     * @param $filterRows
     */
    public function showXls($filename, $rows, $headers = [], $filterRows = false)
    {
        $preRows = [];
        if ($filterRows) {
            foreach ($rows as $row) {

                $preRow = [];

                foreach ($headers as $key => $value) {
                    $preRow[$key] = isset($row[$key]) ? $row[$key] : '';
                }

                $preRows[] = $preRow;
            }

            $rows = $preRows;
        }

        $PHPToExcel = new PHPToExcel();
        $PHPToExcel->display($filename . '_'.date('Ymd'), $rows, $headers);
    }

    /**
     * проверяем глобальные сообщения
     */
    private function _checkGlobalMessages()
    {
        $globalMessages = Model_Message::getList([
            'note_type' => Model_Message::MESSAGE_TYPE_GLOBAL,
            'status' => Model_Message::MESSAGE_STATUS_NOTREAD
        ]);

        if (!empty($globalMessages)) {
            $globalMessages = Model_Message::parseBBCodes($globalMessages, false);

            $popupGlobalMessages = Form::popup('ВАЖНО!', 'common/global_messages', [
                'globalMessages' => $globalMessages
            ]);

            View::set_global('popupGlobalMessages', $popupGlobalMessages);

            Model_Message::makeRead(['note_type' => Model_Message::MESSAGE_TYPE_GLOBAL]);
        }
    }

    /**
     * выполняем функции перед загрузкой страницы
     */
    private function _actionsBefore()
    {
        //проверка флага установки прочитанности сообщения
        $noteGuid = $this->request->query('read');

        if (!empty($noteGuid)) {
            Model_Message::makeRead([
                'note_guid' => $noteGuid,
                'note_type' => Model_Message::MESSAGE_TYPE_COMMON
            ]);
        }
    }
} // End Common