<?php defined('SYSPATH') or die('No direct script access.');

class Common
{
    const CURRENCY_RUR 		            = 643;

    /**
     * favicon
     */
    public static function getFaviconRawData($customView = '')
    {
        switch ($customView) {
            case 'gpn':
                $favicon = '<link type="image/x-icon" href="/assets/favicon/projects/dealergpncardcom/favicon.ico" rel="icon">';
                break;
            case 'binoil':
                $favicon = '<link type="image/x-icon" href="/assets/favicon/projects/lkbinoilcom/favicon.ico" rel="icon">';
                break;
            default:
                $favicon = '
                    <link rel="apple-touch-icon" sizes="57x57" href="/assets/favicon/apple-touch-icon-57x57.png">
                    <link rel="apple-touch-icon" sizes="60x60" href="/assets/favicon/apple-touch-icon-60x60.png">
                    <link rel="apple-touch-icon" sizes="72x72" href="/assets/favicon/apple-touch-icon-72x72.png">
                    <link rel="apple-touch-icon" sizes="76x76" href="/assets/favicon/apple-touch-icon-76x76.png">
                    <link rel="apple-touch-icon" sizes="114x114" href="/assets/favicon/apple-touch-icon-114x114.png">
                    <link rel="apple-touch-icon" sizes="120x120" href="/assets/favicon/apple-touch-icon-120x120.png">
                    <link rel="apple-touch-icon" sizes="144x144" href="/assets/favicon/apple-touch-icon-144x144.png">
                    <link rel="apple-touch-icon" sizes="152x152" href="/assets/favicon/apple-touch-icon-152x152.png">
                    <link rel="apple-touch-icon" sizes="180x180" href="/assets/favicon/apple-touch-icon-180x180.png">
                    <link rel="icon" type="image/png" href="/assets/favicon/favicon-32x32.png" sizes="32x32">
                    <link rel="icon" type="image/png" href="/assets/favicon/android-chrome-192x192.png" sizes="192x192">
                    <link rel="icon" type="image/png" href="/assets/favicon/favicon-16x16.png" sizes="16x16">
                    <link rel="manifest" href="/assets/favicon/manifest.json">
                    <link rel="mask-icon" href="/assets/favicon/safari-pinned-tab.svg" color="#5bbad5">
                    <link rel="shortcut icon" href="/assets/favicon/favicon.ico">
                    <meta name="msapplication-TileColor" content="#da532c">
                    <meta name="msapplication-TileImage" content="/assets/favicon/mstile-144x144.png">
                    <meta name="msapplication-config" content="/assets/favicon/browserconfig.xml">
                    <meta name="theme-color" content="#ffffff">        
                ';
            }

        return $favicon;
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
     * получаем версию продукта

     */
    public static function getVersion($asProd = false)
    {
        $config = Kohana::$config->load('version');

        return self::isProd() || $asProd ? $config['hash'] : time();
    }

    /**
     * добавляем js для подключения в шапку
     *
     * @param $file
     */
    public static function addJs($file)
    {
        $js = (array)(new View())->js;

        View::set_global('js', array_merge($js, [Common::getAssetsLink() . 'js/' . $file]));
    }

    /**
     * добавляем css для подключения в шапку
     *
     * @param $file
     */
    public static function addCss($file)
    {
        $css = (array)(new View())->css;

        View::set_global('css', array_merge($css, [Common::getAssetsLink() . 'css/' . $file]));
    }

    /**
     * получаем ссылку на файлы
     *
     * @return string
     */
    public static function getAssetsLink()
    {
        return self::isProd() ? '/assets/build/' : '/assets/';
    }

    /**
     * шифруем
     *
     * @param $plaintext
     * @return string
     * @throws Kohana_Exception
     */
    public static function encrypt($plaintext)
    {
        $key            = Kohana::$config->load('config')['salt'];
        $ivlen          = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv             = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
        $hmac           = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
        $ciphertext     = base64_encode( $iv.$hmac.$ciphertext_raw );

        return str_replace(array('+', '/'), array('-', '_'), $ciphertext);
    }

    /**
     * дешифруем
     *
     * @param $ciphertext
     * @return string
     * @throws Kohana_Exception
     */
    public static function decrypt($ciphertext)
    {
        $key                = Kohana::$config->load('config')['salt'];
        $c                  = base64_decode(str_replace(array('-', '_'), array('+', '/'), $ciphertext));
        $ivlen              = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv                 = substr($c, 0, $ivlen);
        $hmac               = substr($c, $ivlen, $sha2len=32);
        $ciphertext_raw     = substr($c, $ivlen+$sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
        $calcmac            = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);

        //с PHP 5.6+ сравнение, не подверженное атаке по времени
        return hash_equals($hmac, $calcmac) ? $original_plaintext : '';
    }

    /**
     * строим дерево
     *
     * @param $result
     * @param $field
     * @param bool $noArray
     * @param bool $subField
     * @return array
     */
    public static function buildTreeFromDBResult($result, $field, $noArray = false, $subField = false)
    {
        $return = [];

        if(!empty($result)){
            $check = reset($result);

            if(!isset($check[$field])){
                return $return;
            }

            foreach($result as $row){
                if($noArray) {
                    $return[$row[$field]] = !empty($subField) ? $row[$subField] : $row;
                }else{
                    $return[$row[$field]][] =  !empty($subField) ? $row[$subField] : $row;
                }
            }
        }

        return $return;
    }
}