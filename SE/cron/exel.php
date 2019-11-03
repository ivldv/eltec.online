<?php
echo __DIR__;
$bbb =  explode ( '/' , __DIR__);
$a= array_pop ($bbb);
$filecron = implode ( '/', $bbb );
require_once ($filecron.'/model/database.php');
require_once $filecron."/PhPExcel-1.8/Classes/PHPExcel/IOFactory.php"; // подключаем класс для доступа к файлу
echo ' Подключился ,';
$loadfile = 'zayavka77.xls';
$local=$filecron."/uploads/".$loadfile;

$remoteUrl = "http://www.necm.ru/download/zayavka77.xls";
$userAgent = 'Googlebot/2.1 (http://www.googlebot.com/bot.html)';
$ch = curl_init($remoteUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
$output = curl_exec($ch);
$fh = fopen($local, 'w');
fwrite($fh, $output);
fclose($fh);

echo ' Загрузил ,';

if(tableExists($tableProduct)===FALSE){
  createProduct();
  echo " создал таблицу ,";
}else{
  echo ' очистил таблицу ,'.$tableProduct."<br>";
  deleteProductByIdPurveyors('TDM');
};

$objPHPExcel = PHPExcel_IOFactory::load($local);

foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) // цикл обходит страницы файла
{
  $highestRow = $worksheet->getHighestRow(); // получаем количество строк
  $highestColumn = $worksheet->getHighestColumn(); // а так можно получить количество колонок
 
  for ($row = 9; $row <= $highestRow; ++ $row) // обходим все строки
  {
    $cell['presence'] = $worksheet->getCellByColumnAndRow(3, $row)->getValue(); //количество
    if ($cell['presence']== NULL | !is_double($cell['presence'])) {
      continue;
    }
	$cell['producer'] = 'TDM';
    $cell['artikle'] = $worksheet->getCellByColumnAndRow(0, $row)->getValue(); //артикул
    $cell['name'] = $worksheet->getCellByColumnAndRow(1, $row)->getValue(); //наименование
    $cell['multiplicity'] = $worksheet->getCellByColumnAndRow(5, $row)->getValue(); //цена
    $cell['packaging'] = $worksheet->getCellByColumnAndRow(6, $row)->getValue(); //валюта
    $cell['cost'] = $worksheet->getCellByColumnAndRow(8, $row)->getCalculatedValue(); //единица измерения

    if($cell['artikle']!='') {
      insertProduct('TDM',$cell);
    }
  }
}
         echo 'все';