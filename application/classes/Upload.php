<?php defined('SYSPATH') or die('No direct script access.');

class Upload extends Kohana_Upload
{
    /**
     * создаем структуру папок под файл
     *
     * @param $filename
     * @param $component
     * @return string
     */
    public static function generateFileDirectory($filename, $component)
    {
        $root       = $_SERVER["DOCUMENT_ROOT"];
        $ds         = DIRECTORY_SEPARATOR;

        $directory  = $ds . 'upload' . $ds . $component . $ds . mb_strcut($filename, 0, 2) . $ds . mb_strcut($filename, 2, 2) . $ds;

        if(!is_dir($root.$directory)){
            mkdir($root.$directory, 0777, true);
        }

        return $directory;
    }

    /**
     * Upload constructor.
     */
    public static function uploadFile($component = 'file')
    {
        if (!empty($_FILES['file'])) {
            $name = explode('.', $_FILES['file']['name']);
            $filename = md5(time().'salt'.$_FILES['file']['name']).'.'.end($name);

            $directory = self::generateFileDirectory($filename, $component);

            if(self::save($_FILES['file'], $filename, $_SERVER["DOCUMENT_ROOT"].$directory)){
                return $directory.$filename;
            }
        }

        return false;
    }

    /**
     * убираем метку BOM и читаем файл
     *
     * @param $text
     * @return mixed
     */
    public static function readFile($file)
    {
        $text = file_get_contents($file);

        if (empty($text)) {
            return '';
        }

        $bom = pack('H*','EFBBBF');
        $text = preg_replace("/^$bom/", '', $text);

        return $text;
    }
}