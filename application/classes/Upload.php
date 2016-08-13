<?php defined('SYSPATH') or die('No direct script access.');

class Upload extends Kohana_Upload
{
    /**
     * создаем структуру папок под картинки
     *
     * @param $filename
     * @return string
     */
    public static function generateImageDirectory($filename)
    {
        $root       = $_SERVER["DOCUMENT_ROOT"];
        $ds         = DIRECTORY_SEPARATOR;

        $directory  = $ds . 'upload' . $ds . mb_strcut($filename, 0, 2) . $ds . mb_strcut($filename, 2, 2) . $ds;

        if(!is_dir($root.$directory)){
            mkdir($root.$directory, 0777, true);
        }

        return $directory;
    }
}