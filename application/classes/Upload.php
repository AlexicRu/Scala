<?php defined('SYSPATH') or die('No direct script access.');

class Upload extends Kohana_Upload
{
    const MIME_TYPE_XLS     = 'application/vnd.ms-excel';
    const MIME_TYPE_OFFICE  = 'application/vnd.ms-office';
    const MIME_TYPE_XLSX    = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    const MIME_TYPE_TXT     = 'text/plain';
    const MIME_TYPE_JSON    = 'application/json';

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
                return [
                    'name'  => str_replace(" ", "_", $_FILES['file']['name']),
                    'file'  => $directory.$filename
                ];
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
        $mimeType = mime_content_type($file);

        switch ($mimeType) {
            case self::MIME_TYPE_OFFICE:
            case self::MIME_TYPE_XLS:
            case self::MIME_TYPE_XLSX:
                $objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);

                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                    $data = $worksheet->toArray();
                    break;
                }
                break;
            case self::MIME_TYPE_TXT:
                $text = file_get_contents($file);

                if (!empty($text)) {
                    $bom = pack('H*','EFBBBF');
                    $data = preg_replace("/^$bom/", '', $text);

                    if (Text::isJson($data)) {
                        //json
                        $data = json_decode($data, true);

                        $data = !empty($data['ROWS']) ? $data['ROWS'] : [];
                        
                        $mimeType = self::MIME_TYPE_JSON; //немного костыль
                    } else {
                        //csv
                        $data = explode("\n", $data);

                        if (!empty($data)) {
                            $data = array_filter($data);

                            foreach ($data as &$row) {
                                $row = explode(';', trim($row));
                            }
                        }
                    }
                }

                break;
        }

        if (empty($data)) {
            return [[], false];
        }

        return [$data, $mimeType];
    }
}