<?php

namespace Ivliev\Exell;

//require_once '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class priceExel
{
    private  $objPHPExcel;
    private $currentLine;
    private $filename;

    public function __construct($name = 'price.xlsx')
    {
        $this->objPHPExcel = new Spreadsheet();;

        // Set document properties
        $this->objPHPExcel->getProperties()->setCreator("OOO Diselectro")
            ->setLastModifiedBy("OOO Diselectro")
            ->setTitle("Прайс лист с остатками")
            ->setSubject("Прайс лист с остатками")
            ->setDescription("Прайс лист с остатками.")
            ->setKeywords("Прайс лист с остатками")
            ->setCategory("Прайс лист с остатками");
        $this->currentLine = 1;
        if($name === 'price.xlsx'){
            $this->filename = dirname(__FILE__).'/'.$name;
        }else{
            $this->filename = $name;
        }
    }
    public function  writeString($data)
    {
        $sheet = $this->objPHPExcel->getActiveSheet();
        $columnIndex = 1;
        foreach ($data as $value)
        {
            $sheet->setCellValueByColumnAndRow($columnIndex, $this->currentLine, $value);
            $columnIndex++;
        }
        $this->currentLine++;
    }
    public function __destruct()
    {
        $writer = new Xlsx($this->objPHPExcel);
        $writer->save($this->filename);
    }
}

