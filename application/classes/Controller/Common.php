<?php defined('SYSPATH') or die('No direct script access.');
 
abstract class Controller_Common extends Controller_Template {
 
    public $template = 'layout';
    public $title = array();
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

        View::set_global('user', Auth_Oracle::instance()->get_user());

        if(!$this->request->is_ajax()) {
            //рендерим шаблон страницы
            if (!in_array($controller, ['Index'])) {
                $this->tpl = View::factory('/pages/' . strtolower($controller) . '/' . $action);
            }

            $config = Kohana::$config->load('main');
            foreach ($config as $k => $v) {
                if ($k == 'title')
                    $this->title[] = $v;
                else
                    View::set_global($k, $v);
            }

            $this->template->content = '';
            $this->template->styles = [];
            $this->template->scripts = [];

            $menu = Kohana::$config->load('menu');
            $content = View::factory('/includes/menu')
                ->bind('menu', $menu);;

            View::set_global('menu', $content);
        }
    }
    
    public function after(){
        View::set_global('title', implode(" :: ",$this->title));
        $this->template->content = $this->tpl;
        parent::after();
    }
    
    protected function json($data){
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function jsonResult($result, $data = []){
        self::json(['success' => $result, 'data' => $data]);
    }
} // End Common