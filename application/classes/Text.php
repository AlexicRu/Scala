<?php defined('SYSPATH') OR die('No direct script access.');

class Text extends Kohana_Text
{
    const RUR = '₽';

    /**
     * echo plural_form(42, array('арбуз', 'арбуза', 'арбузов'));
     *
     * @param $n
     * @param $forms
     * @return mixed
     */
    public static function plural($n, $forms) {
        return $n%10==1&&$n%100!=11?$forms[0]:($n%10>=2&&$n%10<=4&&($n%100<10||$n%100>=20)?$forms[1]:$forms[2]);
    }

    /**
     * переводим из camelCase
     *
     * @param $str
     * @param string $delimiter
     * @return string
     */
    public static function camelCaseToDashed($str, $delimiter = '-')
    {
        return strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1' . $delimiter, $str));
    }

    /**
     * переводим в camelCase
     *
     * @param $str
     * @param bool $lcfirst
     * @return mixed
     */
    public static function dashesToCamelCase($str, $lcfirst = TRUE)
    {
        $pos = strpos($str, '-');
        if ( $pos !== FALSE && $pos !== 0 )
        {
            $str = str_replace(' ', '', ucwords(str_replace('-', ' ', $str)));
            if ( $lcfirst )
            {
                $str[0] = strtolower($str[0]);
            }
        }
        return $str;
    }

    /**
     * меняем кавычки для полей форм
     *
     * @param $str
     * @return mixed
     */
    public static function quotesForForms($str)
    {
        return str_replace('"', '&quot;', $str);
    }

    /**
     * парсим урлы из текста и пытаемся отрендеритьпревью сайта
     *
     * @param $str
     */
    public static function parseUrl($str)
    {
        $pattern = '~[a-z]+://\S+~';

        if($num_found = preg_match_all($pattern, $str, $urls))
        {
            $config = Kohana::$config->load('config');
            $cache = Cache::instance();

            $urls = $urls[0];
            $replace = [];

            foreach ($urls as $url) {
                $key = 'opengraph_url_' . $url;

                $siteInfo = $cache->get($key);

                if (empty($siteInfo)) {
                    $requestUrl = 'https://opengraph.io/api/1.1/site/' . urlencode($url);

                    $requestUrl = $requestUrl . '?app_id=' . $config['opengraph'];

                    $siteInformationJSON = file_get_contents($requestUrl);
                    $siteInfo = json_decode($siteInformationJSON, true);

                    if (!empty($siteInfo['hybridGraph'])) {
                        $cache->set($key, $siteInfo['hybridGraph'], 60 * 60 * 24 * 365);
                    }

                    $siteInfo = $siteInfo['hybridGraph'];
                }

                if (empty($siteInfo)) {
                    $charsCnt = mb_strlen($url);
                    $name = $url;

                    if ($charsCnt > 50) {
                        $name = mb_strcut($str, 0, 40) . '...' . mb_strcut($str, -7, 7);
                    }

                    $replace[$url] = '<a href="'. $url .'" target="_blank">'. $name .'</a>';
                } else {

                    $uniqid = uniqid();
                    $replace[$url] = '<a 
                            href="'. $url .'" 
                            target="_blank"
                            class="tooltip" data-tooltip-content="#tooltip_content_'.$uniqid.'"
                        ><i class="icon icon-reply"></i> '. $siteInfo['title'] .'</a>
                        <div style="display: none">
                            <div id="tooltip_content_'.$uniqid.'" class="tooltip__content">
                                <div class="tooltip__img"><img src="'.$siteInfo['image'].'"></div>
                                <div class="tooltip__name">'.self::quotesForForms($siteInfo['title']).'</div>
                                <div class="tooltip__info">'.self::quotesForForms($siteInfo['description']).'</div>
                            </div>                        
                        </div>
                    ';
                }
            }

            $str = str_replace(array_keys($replace), array_values($replace), $str);
        }

        return $str;
    }

    /**
     * получаем текстовое написание валюты
     *
     * @param $currency
     */
    public static function getCurrency($currency)
    {
        if ($currency == Common::CURRENCY_RUR) {
            return self::RUR;
        }

        return '';
    }

    /**
     * проверка корректности email
     *
     * @param $email
     * @return string
     * @throws HTTP_Exception_500
     */
    public static function checkEmailMulti($emails)
    {
        /*if (Valid::email($email)) {
            return true;
        }*/

        $emailsArr = array_map('trim', explode(',', $emails));

        foreach ($emailsArr as $email) {

            $dogPosition = strpos($email, '@');

            if ($dogPosition !== false) {
                $dotPosition = strrpos($email, '.');

                if ($dotPosition === false || $dotPosition <= $dogPosition) {
                    throw new HTTP_Exception_500('Неверный формат электронной почты');
                }
            } else {
                throw new HTTP_Exception_500('Неверный формат электронной почты');
            }
        }

        return $emails;
    }
}
