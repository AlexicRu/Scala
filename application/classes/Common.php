<?php defined('SYSPATH') or die('No direct script access.');

class Common
{
    const CURRENCY_RUR 		            = 643;

	/**
	 * генерация html для попапа
	 *
	 * @param $header
	 * @param $form
	 * @param $data
	 */
	public static function popupForm($header, $form, $data = [], $formName = '')
	{
		$formBody = View::factory('forms/'.$form);

		if(!empty($data) && is_array($data)){
			foreach($data as $key => $value){
				$formBody->set($key, $value);
			}
		}

		$id = $formName ?: str_replace('/', "_", $form);

		$content = View::factory('includes/popup')
			->bind('popupHeader', $header)
			->bind('popupBody', $formBody)
			->bind('popupId', $id)
		;

		return $content;
	}

    /**
     * генерация шаблона конкретного типа поля
     *
     * @param $type
     * @param $name
     * @param $value
     * @param $params
     */
	public static function buildFormField($type, $name, $value = false, $params = [])
    {
        try {
            $value = is_array($value) ? implode(',', $value) : $value;

            $content = View::factory('forms/_fields/' . $type)
                ->bind('type', $type)
                ->bind('name', $name)
                ->bind('value', $value)
                ->bind('params', $params)
            ;
        } catch (Exception $e){
            return $type;
        }

        return $content;
    }

    /**
     * favicon
     */
    public static function getFaviconRawData()
    {
        return '
            <link rel="apple-touch-icon" sizes="57x57" href="/favicon/apple-touch-icon-57x57.png">
            <link rel="apple-touch-icon" sizes="60x60" href="/favicon/apple-touch-icon-60x60.png">
            <link rel="apple-touch-icon" sizes="72x72" href="/favicon/apple-touch-icon-72x72.png">
            <link rel="apple-touch-icon" sizes="76x76" href="/favicon/apple-touch-icon-76x76.png">
            <link rel="apple-touch-icon" sizes="114x114" href="/favicon/apple-touch-icon-114x114.png">
            <link rel="apple-touch-icon" sizes="120x120" href="/favicon/apple-touch-icon-120x120.png">
            <link rel="apple-touch-icon" sizes="144x144" href="/favicon/apple-touch-icon-144x144.png">
            <link rel="apple-touch-icon" sizes="152x152" href="/favicon/apple-touch-icon-152x152.png">
            <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon-180x180.png">
            <link rel="icon" type="image/png" href="/favicon/favicon-32x32.png" sizes="32x32">
            <link rel="icon" type="image/png" href="/favicon/android-chrome-192x192.png" sizes="192x192">
            <link rel="icon" type="image/png" href="/favicon/favicon-16x16.png" sizes="16x16">
            <link rel="manifest" href="/favicon/manifest.json">
            <link rel="mask-icon" href="/favicon/safari-pinned-tab.svg" color="#5bbad5">
            <link rel="shortcut icon" href="/favicon/favicon.ico">
            <meta name="msapplication-TileColor" content="#da532c">
            <meta name="msapplication-TileImage" content="/favicon/mstile-144x144.png">
            <meta name="msapplication-config" content="/favicon/browserconfig.xml">
            <meta name="theme-color" content="#ffffff">        
        ';
    }

    /**
     * @return bool
     */
    public static function isProd()
    {
        if (Kohana::$environment == Kohana::PRODUCTION) {
            return true;
        }
        return false;
    }

    /**
     * получаем конфиг для текущего состояния
     */
    public static function getEnvironmentConfig()
    {
        $state = 'dev';

        if (self::isProd()) {
            $state = 'prod';
        }

        return Kohana::$config->load($state);
    }

    public static function stringFromKeyValueFromArray($array, $delimiter = '<br>')
    {
        $result = [];

        foreach ($array as $key => $value) {
            $result[] = $key . ' - ' . $value;
        }

        return implode($delimiter, $result);
    }

    /**
     * рассчитываем соль
     *
     * @param string $type
     * @throws Kohana_Exception
     */
    public static function getSalt($type = 'css')
    {
        $config = Kohana::$config->load('main');

        $salt = time();

        switch ($type) {
            case 'css':
                if (!empty($config['cssSalt']) && self::isProd()) {
                    $salt = $config['cssSalt'];
                }
                break;
            case 'js':
                if (!empty($config['jsSalt']) && self::isProd()) {
                    $salt = $config['jsSalt'];
                }
                break;
        }

        return $salt;
    }

    /**
     * добавляем js для подключения в шапку
     *
     * @param $file
     */
    public static function addJs($file)
    {
        $js = (array)(new View())->js;

        View::set_global('js', array_merge($js, ['/assets/build/js/' + $file]));
    }

    /**
     * добавляем css для подключения в шапку
     *
     * @param $file
     */
    public static function addCss($file)
    {
        $css = (array)(new View())->css;

        View::set_global('css', array_merge($css, ['/assets/build/css/' + $file]));
    }
}