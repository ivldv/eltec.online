<?php
require_once ('../model/database.php');
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$filename = '19703_catalog.xlsx';

echo ('Начали');
$spreadsheet = IOFactory::load($filename);
echo ('загрузили');
$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
deleteProductByIdPurveyors('Elevel');
foreach ($sheetData as $key => $value) {
    if ($key<3) continue;
    $cell['producer'] = $value['E'];
    $cell['artikle'] = substr ( $value['B'] , 0 ,60 ); //артикул
    $cell['name'] = $value['A']; //наименование
    $cell['presence'] = $value['G'];//количество
    $cell['additional'] = $value['H'];
    $cell['cost'] = $value['J'];
    echo ($cell['artikle'].'</br>');
    if ($cell['presence']  == 0 && $cell['additional'] == 0) continue;

    $s = insertElevelOfferNEW($cell);
}
