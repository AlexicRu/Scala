<?php defined('SYSPATH') OR die('No direct script access.');

class HTTP_Exception_403 extends Kohana_HTTP_Exception_403
{
    public function get_response()
    {
        $response = Response::factory();

        $view = View::factory('errors/403');

        $response->body($view->render());

        if ($this->request()->is_ajax()) {
            $response->headers('Content-Type', 'application/json');
            $response->body(json_encode(['success' => false, 'data' => 'Доступ запрещен']));
        } else {
            (new Sentry())->error403('URL: ' . $this->request()->uri());
        }

        return $response;
    }
}
