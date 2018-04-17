<?php defined('SYSPATH') or die('No direct script access.');

class Sentry
{
    private $_client = null;

    public function __construct()
    {
        $config = Common::getEnvironmentConfig();

        $this->_client = new Raven_Client($config['sentry_dsn']);
    }

    public function error404($message = '')
    {
        $this->_client->captureMessage('404 error. '.$message);
    }

    public function error403($message = '')
    {
        $this->_client->captureMessage('403 error. '.$message);
    }

    public function error500($message = '')
    {
        $this->_client->captureMessage('500 error. '.$message);
    }
}