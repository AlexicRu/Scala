<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Index extends Controller_Common {

	/**
	 * функция авторизации
	 */
	public function action_login()
	{
		$post = Request::current()->post();

        $config = Kohana::$config->load('config');

        //проверка reCaptcha
        $options = array(
            CURLOPT_RETURNTRANSFER => true,     // return web page
            //CURLOPT_SSL_VERIFYPEER => false,     // Disabled SSL Cert checks,
            CURLOPT_POSTFIELDS => [
                'secret'    => $config['recaptcha_secret'],
                'response'  => !empty($post['g-recaptcha-response']) ? $post['g-recaptcha-response'] : ''
            ]
        );

        $ch      = curl_init( 'https://www.google.com/recaptcha/api/siteverify' );
        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        curl_close( $ch );

        $content = json_decode($content, true);

        if (empty($content['success'])) {
            /*
             * пока выключили
            Messages::put('Не пройдена проверка reCaptcha', 'error');
            $this->redirect('/');
            */
        }

		if(empty($post['login']) || empty($post['password'])){
			Messages::put('Не заполнен логин или пароль', 'error');
			$this->redirect('/');
		}

		Auth::instance()->login($post['login'], $post['password']);

        $this->redirect('/');
	}

    /**
     * разлогинивание
     *
     * @throws HTTP_Exception
     */
	public function action_logout()
	{
		Auth::instance()->logout();
		$this->redirect('/');
	}

    /**
     * принудительная авторизация
     *
     * @throws HTTP_Exception_403
     */
	public function action_forceLogin()
    {
        $hash = $this->request->param('hash');

        if (empty($hash)) {
            throw new HTTP_Exception_403();
        }

        $params = explode(" ", Common::decrypt($hash));

        $userFrom   = $params[0];
        $userTo     = !empty($params[1]) ? $params[1] : false;

        if (User::checkForceLogin($userFrom, $userTo)) {
            $manager = Model_Manager::getManager(['MANAGER_ID' => (int)$userTo]);

            if(Auth::instance()->login($manager['LOGIN'], ['hash' => $manager['PASSWORD']])){
                $this->redirect('/');
            }
        }

        throw new HTTP_Exception_403();
    }

    /**
     * грузим файлы аяксово
     */
	public function action_uploadFile()
    {
        $component = $this->request->query('component') ?: 'file';

        if ($file = Upload::uploadFile($component)) {
            $this->jsonResult(true, ['file' => $file]);
        }

        $this->jsonResult(false);
    }

    /**
     * подгружаем конфиг api
     */
    public function action_getJson()
    {
        $api = Api::getStructure();

        $html = View::factory('api/json')
            ->bind('api', $api)
        ;

        $this->html($html);
    }

    /**
     * отображаем пришедшие данные как xls
     */
    public function action_asXls()
    {
        $csv = $this->request->post('csv');

        $rows = [];
        $headers = [];

        if (!empty($csv)) {
            $rows = explode("\n", $csv);
            foreach ($rows as &$row) {
                $row = explode("|", $row);

                foreach ($row as &$col) {
                    $col = trim($col, '"');
                }
            }
            $headers = array_shift($rows);
        }

        $this->showXls('export', $rows, $headers);
    }

    /**
     * скачиваем файл с проверками
     */
    public function action_file()
    {
        $file = $this->request->param('file');

        //у инфопортала не проверяем доступы
        if (strpos($file, 'info') !== 0){
            if (!Access::file($file)) {
                throw new HTTP_Exception_403();
            }
        }

        $this->showFile($file);
    }

    /**
     * dashboard
     */
    public function action_index()
    {
        if (User::loggedIn()) {
            if (Access::allow('dashboard_index', true)) {
                $this->redirect('/dashboard');
            }
            $this->redirect('/clients');
        }
    }
} // End Welcome
