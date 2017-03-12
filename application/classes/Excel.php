<?php defined('SYSPATH') or die('No direct script access.');

class Excel
{
    /**
     * @todo
     * Excel constructor.
     */
    public function __construct()
    {
        /** Include path **/
        set_include_path(get_include_path() . PATH_SEPARATOR . 'includes/PHPExcel/');

        /** PHPExcel_IOFactory */
        include 'PHPExcel/IOFactory.php';
    }
}