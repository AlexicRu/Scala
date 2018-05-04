<?php defined('SYSPATH') OR die('No direct script access.');

class HTTP_Exception_500 extends Kohana_HTTP_Exception_500
{
    public function get_response()
    {
        $response = Response::factory();
        $message = $this->getMessage();

        (new Sentry())->error500($message);

        if ($this->request()->is_ajax()) {
            $response->headers('Content-Type', 'application/json');
            $response->body(json_encode(['success' => false, 'data' => $message]));
        } else {
            $view = View::factory('errors/500');

            // We're inside an instance of Exception here, all the normal stuff is available.
            $view->message = $message;

            $response->body($view->render());
        }

        return $response;
    }
}
