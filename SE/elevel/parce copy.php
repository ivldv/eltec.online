<?php

require_once ('../model/database.php');
$zip = new ZipArchive();
$zip->open('19703_catalog.xlsx');
$fff = $zip->getFromName('xl/worksheets/sheet1.xml');

$sheet_xml = simplexml_load_string($fff);
echo '333';
die();

//$sheet_array = json_decode(json_encode($sheet_xml->sheetData), true);
$values = simplexml_load_string($zip->getFromName('xl/sharedStrings.xml'));
echo 'ffffff';
die();
$values_array = json_decode(json_encode($values), true);

deleteProductByIdPurveyors('Elevel');

$end_result = array();
if ($sheet_xml->sheetData) {
    $r = 0;
    foreach ($sheet_xml->sheetData->row as $row) {
        $end_result[$r] = array();
        foreach ($row->c as $c => $cell) {
            if (isset($cell['t'])) {
                if ((string)$cell['t'] == 's') {
                    $end_result[$r][] = $values_array['si'][(int)$cell->v]['t'];
                } else if ((string)$cell['t'] == 'e') {
                    $end_result[$r][] = '';
                } else if ((string)$cell['t'] == 'n') {
                    $end_result[$r][] = $cell->v;
                }
            } else {
                $end_result[$r][] = $cell->v;
            }
        }
    if ($r>=2) {
        $cell['producer'] = $end_result[$r][4];
        $cell['artikle'] = substr ( $end_result[$r][1] , 0 ,250 ); //артикул
        $cell['name'] = $end_result[$r][0]; //наименование
        $cell['presence'] = $end_result[$r][6];//количество
        $cell['additional'] = $end_result[$r][7];
        $cell['cost'] = $end_result[$r][9];
        echo ($cell['artikle'].'</br>');
        if ($cell['presence']  == 0 && $cell['additional'] == 0) continue;

        $s = insertElevelOfferNEW($cell);
    }
    $r++;

    }
}