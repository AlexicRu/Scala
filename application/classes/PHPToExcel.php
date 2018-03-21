<?php defined('SYSPATH') or die('No direct script access.');

class PHPToExcel
{
    /**
     * @var null|\PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    private $_phpExcel = null;
    private $_isFirstSheetHasData = false;

    public function __construct()
    {
        $this->_phpExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    }

    public function display($filename = 'file', $data = array(), $headers = array())
    {
        $mime = implode(', ', Kohana::$config->load('mimes')['xlsx']);

        if(!empty($data) || !empty($headers)) {
            $this->addSheet($data, $headers);
        }

        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->_phpExcel, 'Xlsx');

        header("Content-Type: ". $mime ."; charset=utf-8");
        header("Content-Disposition: attachment; filename=".$filename.".xlsx");  //File name extension was wrong
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private",false);

        $objWriter->save('php://output');

        die;
    }

    /**
     * Добавить страницу в xls
     * @param array $data
     * @param array $headers
     * @param null $name
     * @throws Exception
     */
    public function addSheet($data = array(), $headers = array(), $name = null)
    {
        if($this->_isFirstSheetHasData) {
            $sheet = $this->_phpExcel->createSheet();
        } else {
            $sheet = $this->_phpExcel->setActiveSheetIndex(0);
        }

        if(!empty($name)) {
            $sheet->setTitle($name);
        }

        $row = 1;

        //заголовки
        if(!empty($headers)){
            $col = 0;
            foreach($headers as $val) {
                $sheet->setCellValueByColumnAndRow($col++, $row, $val);
            }
            $row++;
        }

        //контент
        foreach($data as $columns) {
            $col = 0;

            foreach($columns as $val) {
                $sheet->setCellValueByColumnAndRow($col++, $row, $val);
            }

            $row++;
        }

        $this->_isFirstSheetHasData = true;
    }
}