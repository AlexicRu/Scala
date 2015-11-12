<?php defined('SYSPATH') or die('No direct script access.');
 
abstract class Controller_Common extends Controller_Template {
 
    public $template = 'layout';
    public $title = [];
    public $errors = [];
    public $tpl = '';

    public function before()
    {
        $controller = $this->request->controller();
        $action = $this->request->action();

        if(!Auth::instance()->logged_in()){
            if(!in_array($action, ['login', 'logout']) && $_SERVER['REQUEST_URI'] != '/'){
                $this->redirect('/');
            }
            $this->template = 'not_auth';
        }else{
            if($controller == 'Index' && $action == 'index') {
                $this->redirect('/clients');
            }
        }

        parent::before();

        $allow = Access::allow(strtolower($controller.'_'.$action));

        //если не аяксовый запрос
        if(!$this->request->is_ajax()) {
            if($allow == false){
                throw new HTTP_Exception_403();
            }

            //рендерим шаблон страницы
            if (!in_array($controller, ['Index'])) {
                $this->tpl = View::factory('/pages/' . strtolower($controller) . '/' . $action);
            }

            $this->_checkCustomDesign();
            $this->_appendFiles();
        }

        //если все таки аякс
        if($allow == false){
            echo '<script>alert("У вас недостаточно прав доступа");</script>';
            die;
        }
    }

    /**
     * прописываем глобальные конфиги
     *
     * @throws Kohana_Exception
     */
    public function after()
    {
        View::set_global('user', Auth_Oracle::instance()->get_user());

        $config = Kohana::$config->load('main')->as_array();
        foreach ($config as $k => $v) {
            View::set_global($k, $v);
        }

        View::set_global('title', implode(" :: ",$this->title));
        View::set_global('errors', $this->errors);

        $this->template->content = $this->tpl;

        parent::after();
    }

    protected function html($data){
        echo $data;
        exit;
    }

    protected function json($data){
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function jsonResult($result, $data = []){
        self::json(['success' => $result, 'data' => $data]);
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
            $url = str_replace('.', '', $_SERVER['SERVER_NAME']);

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
    private function _appendFiles()
    {
        if(Auth::instance()->logged_in()) {
            $menu = Kohana::$config->load('menu');
            $content = View::factory('/includes/menu')
                ->bind('menu', $menu);

            View::set_global('menu', $content);

            $this->template->styles = [
                '/js/plugins/jGrowl/jGrowl.css',
                '/js/plugins/fancy/jquery.fancybox.css',
                '/style.css',
            ];
            $this->template->scripts = [
                'https://yastatic.net/jquery/2.1.3/jquery.min.js',
                'https://yastatic.net/jquery-ui/1.11.2/jquery-ui.min.js',
                '/js/plugins/jGrowl/jGrowl.js',
                '/js/plugins/fancy/jquery.fancybox.js',
                '/js/plugins/site.js',
            ];
        }else{
            $this->template->styles = [
                '/style.css'
            ];
            $this->template->scripts = [];
        }
    }
} // End Common