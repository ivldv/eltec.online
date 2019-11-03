<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Hello World !');

$writer = new Xlsx($spreadsheet);
$writer->save('hello world.xlsx');


$spreadSheet = new Spreadsheet();
$workSheet = $spreadSheet->getActiveSheet();

// Set details for the formula that we want to evaluate, together with any data on which it depends
$workSheet->fromArray(
    [1, 2, 3],
    null,
    'A1'
);

$cellC1 = $workSheet->getCell('C1');
echo 'Value: ', $cellC1->getValue(), '; Address: ', $cellC1->getCoordinate(), PHP_EOL;

$cellA1 = $workSheet->getCell('A1');
echo 'Value: ', $cellA1->getValue(), '; Address: ', $cellA1->getCoordinate(), PHP_EOL;

echo 'Value: ', $cellC1->getValue(), '; Address: ', $cellC1->getCoordinate(), PHP_EOL;