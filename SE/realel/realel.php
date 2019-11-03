<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/SE/model/database.php';
require_once $_SERVER['DOCUMENT_ROOT']."/SE/PhPExcel-1.8/Classes/PHPExcel/IOFactory.php"; // подключаем класс для доступа к файлу
echo ' Подключился ,';
function loadRealStock($url,$producer=''){

    $loadfile = 'real.zip';
    $local = $_SERVER['DOCUMENT_ROOT'].'/SE/uploads/'.$loadfile;
    $path = $_SERVER['DOCUMENT_ROOT'].'/SE/uploads/';
    $userAgent = 'Googlebot/2.1 (http://www.googlebot.com/bot.html)';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
    $output = curl_exec($ch);
    $fh = fopen($local, 'w');
    fwrite($fh, $output);
    fclose($fh);
    
    echo ' Загрузил ,';

    $zip = new ZipArchive;
    $res = $zip->open($local);
    if ($res === TRUE) {
		// extract it to the path we determined above
		$filename = $zip->getNameIndex (0);
		$zip->extractTo($path);
		$zip->close();
		echo "WOOT! $local extracted to $path";
    } else {
		echo "Doh! I couldn't open $local";
    }

    $objPHPExcel = PHPExcel_IOFactory::load($path.$filename);
    
    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) // цикл обходит страницы файла
    {
      $highestRow = $worksheet->getHighestRow(); // получаем количество строк
      $highestColumn = $worksheet->getHighestColumn(); // а так можно получить количество колонок
     
      for ($row = 9; $row <= $highestRow; ++ $row) // обходим все строки
      {
        $cell['producer'] = $producer;
        $cell['artikle'] = $worksheet->getCellByColumnAndRow(2, $row)->getValue(); //артикул
        $cell['name'] = $worksheet->getCellByColumnAndRow(1, $row)->getValue(); //наименование
        $cell['presence'] = $worksheet->getCellByColumnAndRow(3, $row)->getValue(); //количество
//    var_dump($cell);
        if($cell['artikle']!='') {
          insertProduct('Realel',$cell);
        }
      }
    }
             echo 'все';
}
	  echo ' очистил таблицу ,'.$tableProduct."<br>";
	  deleteProductByIdPurveyors('Realel');

loadRealStock('https://www.realelectro.com/stock/Stock_ABB.zip','ABB');
loadRealStock('https://www.realelectro.com/stock/Stock_Legrand.zip','Legrand');
loadRealStock('https://www.realelectro.com/stock/Stock_BT.zip','Btchino');
loadRealStock('https://www.realelectro.com/stock/Stock_SE.zip','Schneider Electric');
loadRealStock('https://www.realelectro.com/stock/Stock_Devi.zip','Devi');
loadRealStock('https://www.realelectro.com/stock/Stock_Gira.zip','Gira');
loadRealStock('https://www.realelectro.com/stock/Stock_Jung.zip','Jung');
//loadRealStock('http://www.realelectro.com/stock/Stock_Merten.zip','Merten');
