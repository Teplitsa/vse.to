<?php defined('SYSPATH') or die('No direct script access.');

class XLS {

    /**
     * @var Spreadsheet_Excel_Reader
     */
    protected static $_reader;

    /**
     * @return Spreadsheet_Excel_Reader
     */
    public static function reader()
    {
        if (self::$_reader === NULL)
        {

            require_once(Modules::path('xls_reader') . 'lib/excel_reader2.php');
            self::$_reader = new Spreadsheet_Excel_Reader();
        }
        return self::$_reader;
    }
}