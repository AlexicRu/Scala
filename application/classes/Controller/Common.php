<?php defined('SYSPATH') or die('No direct script access.');
 
abstract class Controller_Common extends Controller_Template {

    public $template = 'layout';
    public $title = [];
    public $errors = [];
    public $scripts = [];
    public $styles = [];
    public $tpl = '';
    public $toXls = false;

    public function before()
    {
        $controller = $this->request->controller();
        $action = $this->request->action();

        if(!Auth::instance()->logged_in()/* && !in_array($action, ['get_json'])*/){
            if(!in_array($action, ['login', 'logout']) && $_SERVER['REQUEST_URI'] != '/'){
                $this->redirect('/');
            }
            $this->template = 'not_auth';
        }else{
            if($controller == 'Index' && $action == 'index') {
                $this->redirect('/clients');
            }

            //подключаем меню
            $menu = Kohana::$config->load('menu');
            $content = View::factory('includes/menu')
                ->bind('menu', $menu);

            View::set_global('menu', $content);
        }

        parent::before();

        $allow = Access::allow(strtolower($controller.'_'.$action), true);

        if ($this->request->query('to_xls')) {
            $this->toXls = true;
        }

        //если не аяксовый запрос
        if(!$this->request->is_ajax() && !$this->toXls && !in_array($action, ['get_json'])) {
            if($allow == false){
                throw new HTTP_Exception_403();
            }

            //рендерим шаблон страницы
            if (!in_array($controller, ['Index'])) {
                try {
                    $this->tpl = View::factory('pages/' . strtolower($controller) . '/' . $action);
                } catch (Exception $e) {
                    throw new HTTP_Exception_404();
                }
            }

            $this->_checkCustomDesign();
            $this->_appendFilesBefore();
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
            View::set_global('notices', Model_Message::getList(['status' => Model_Message::MESSAGE_STATUS_NOTREAD]));

            if(!$this->request->is_ajax()) {
                $this->_checkGlobalMessages();
            }
        }

        $this->template->content = $this->tpl;

        parent::after();
    }

    protected function html($data){
        echo $data;
        exit;
    }

    /**
     * show xml
     * @param $xml
     */
    protected function _showXml($xml)
    {
        header('Content-type: text/xml');
        header('Content-Disposition: attachment; filename="export.xml"');

        echo $xml;
        exit;
    }

    protected function json($data){
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function jsonResult($result, $data = [])
    {
        self::json(['success' => $result, 'data' => $data, 'messages' => Messages::get()]);
    }

    protected function _isPost()
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

        if(!empty($_SERVER['SERVER_NAME'])){
            $url = str_replace(['.', '-'], '', $_SERVER['SERVER_NAME']);

            if(isset($design[$url])){
                $customView = $design[$url]['class'];
                $this->title[] = $design[$url]['title'];
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
                '/js/plugins/jGrowl/jGrowl.css',
                '/js/plugins/fancy/jquery.fancybox.css',
            ];
            $this->template->scripts = [
                '/js/plugins/jquery.2.1.3.min.js',
                '/js/plugins/jquery-ui.1.11.2.min.js',
                '/js/plugins/jGrowl/jGrowl.js',
                '/js/plugins/fancy/jquery.fancybox.js',
                '/js/ui.js',
                '/js/functions.js',
                '/js/common.js',
            ];
        }else{
            $this->template->styles = [];
            $this->template->scripts = [
                '/js/plugins/jquery.2.1.3.min.js',
                '/js/common.js',
            ];
        }

        $this->template->favicon = Common::getFaviconRawData();
    }

    private function _appendFiles()
    {
        foreach($this->styles as $style){
            $this->template->styles[] =  $style;
        }
        foreach($this->scripts as $script){
            $this->template->scripts[] =  $script;
        }
    }

    private function _appendFilesAfter()
    {
        if(Auth::instance()->logged_in()) {
            $this->template->styles[] = '/css/ui.css';
            $this->template->styles[] = '/css/style.css';
            $this->template->styles[] = '/css/design.css';

            $this->template->scripts[] = '/js/site.js';
        }else{
            $this->template->styles[] = '/css/style.css';
            $this->template->styles[] = '/css/design.css';
        }
    }

    /**
     * подключаем скрипты и стили редактора
     */
    protected function _initWYSIWYG()
    {
        $this->template->styles[] = '/js/plugins/trumbowyg/ui/trumbowyg.min.css';
        $this->template->styles[] = '/js/plugins/trumbowyg/plugins/colors/ui/trumbowyg.colors.min.css';
        $this->template->scripts[] = '/js/plugins/trumbowyg/trumbowyg.min.js';
        $this->template->scripts[] = '/js/plugins/trumbowyg/plugins/colors/trumbowyg.colors.min.js';
        $this->template->scripts[] = '/js/plugins/trumbowyg/plugins/noembed/trumbowyg.noembed.min.js';
        $this->template->scripts[] = '/js/plugins/trumbowyg/plugins/upload/trumbowyg.upload.min.js';
    }

    /**
     * подключаем скрипты и стили аяксовой загрузки картинок
     */
    protected function _initDropZone()
    {
        $this->template->styles[] = '/js/plugins/dropzone/dropzone.css';
        $this->template->scripts[] = '/js/plugins/dropzone/dropzone.js';
    }

    /**
     * подключаем скрипты и стили JsGrid
     */
    protected function _initJsGrid()
    {
        $this->template->styles[] = '/js/plugins/jsgrid/jsgrid.min.css';
        $this->template->styles[] = '/js/plugins/jsgrid/jsgrid-theme.min.css';
        $this->template->scripts[] = '/js/plugins/jsgrid/jsgrid.min.js';
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
        $PHPToExcel->dispay($filename . '_'.date('Ymd'), $rows, $headers);
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
            $popupGlobalMessages = Common::popupForm('ВАЖНО!', 'common/global_messages', [
                'globalMessages' => $globalMessages
            ]);

            View::set_global('popupGlobalMessages', $popupGlobalMessages);

            Model_Message::makeRead(['note_type' => Model_Message::MESSAGE_TYPE_GLOBAL]);
        }
    }
} // End Common