<?php defined('SYSPATH') or die('No direct script access.');
 
abstract class Controller_Common extends Controller_Template {
 
    public $template = 'layout';
    public $title = array();
 
    public function before()
    {
        parent::before();

        if(!Auth::instance()->logged_in()){
            $this->template = 'not_auth';
        }
       
        $config = Kohana::$config->load('main');
        foreach($config as $k=>$v){
            if($k == 'title')
                $this->title[] = $v;
            else
                View::set_global($k, $v);
        }        
       
        $this->template->content = '';
        $this->template->styles = [];
        $this->template->scripts = [];

        $menu = Kohana::$config->load('menu');
        $content = View::factory('/includes/menu')
            ->bind('menu', $menu);
        ;

        View::set_global('menu', $content);
    }
    
    public function after(){
        View::set_global('title', implode(" :: ",$this->title));
        parent::after();
    }
    
    protected function display_json($data){
        header('Content-Type: application/json');
        echo json_encode($data);    
        exit;
    }
 
} // End Common