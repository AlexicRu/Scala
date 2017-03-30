<?php defined('SYSPATH') or die('No direct script access.');

class Sentry
{
    private $_client = null;

    public function __construct()
    {
        $config = Kohana::$config->load('config');

        $this->_client = new Raven_Client($config['sentry_dsn']);
    }

    public function error404()
    {
        $this->_client->captureMessage('403 error');
    }

    public function error403()
    {
        $this->_client->captureMessage('404 error');
    }

    public function error500()
    {
        $this->_client->captureMessage('500 error');
    }
}