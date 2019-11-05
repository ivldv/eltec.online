<?php
function myErrorHandler($errno, $errstr, $errfile, $errline)
{
	echo 'error load ';
	global $buffer;
	var_dump ($buffer);
}
$old_error_handler = set_error_handler("myErrorHandler");
$bbb =  explode ( '/' , __DIR__);
$a= array_pop ($bbb);
$filecron = implode ( '/', $bbb );
require_once ($filecron.'/model/database.php');
    $ftp_server = "93.189.41.9";
    $ftp_user = "u842261";
    $ftp_pass = "k6oKnqRVVOyG";
    $local_file = $filecron.'/uploads/priceetm.csv';
    $server_file = 'price.csv';
    // установить соединение или выйти
    $conn_id = ftp_connect($ftp_server) or die("Не удалось установить соединение с $ftp_server"); 
    
    // попытка входа
    if (ftp_login($conn_id, $ftp_user, $ftp_pass)) {
        echo "Произведен вход на $ftp_server под именем $ftp_user\n";
    } else {
        echo "Не удалось войти под именем $ftp_user\n";
    }
    // попытка скачать $server_file и сохранить в $local_file
//    if (!(ftp_chdir($conn_id, "msk"))) {
//        echo "Не удалось сменить директорию\n";
//    }
    $contents = ftp_mlsd($conn_id, ".");
    $number = 0;$modify = 0 ;
    foreach ($contents as $key => $filename) {
        if ($filename['type'] =='file') {
            if ($filename['modify']>$modify) {
                $modify = $filename['modify'];
                $number = $key;
                $name = $filename['name'];
            }
        }
    }
    // вывод $contents
    var_dump($name);


    if (ftp_get($conn_id, $local_file, $server_file, FTP_BINARY)) {
        echo "Произведена запись в $local_file\n";

		$inputFileName = $local_file;

		$handle = fopen($inputFileName, "r");
		$buffer = fgets($handle, 4096);
		deleteProductByIdPurveyors('ETM');
		$onlyone = 2;
		while (!feof($handle)) {
			$buffer = iconv("windows-1251", "utf-8", fgets($handle, 4096));
			if(empty($buffer) && $onlyone != 0){
				$onlyone = $onlyone-1;
				continue;
			}
			$str = str_replace('"', '', $buffer);
			$arr = [];
			$arr = explode (';', $str);
			if ($arr[3]==0) continue;
            $cell['producer'] = Ivliev\validation\validation::validationProducer($arr[5]);
//			$cell['producer']= ($arr[5]=='Шнейдер Электрик') ? 'Schneider Electric' : $arr[5] ;
//			if($cell['producer'] == 'LEGRAND') $cell['producer'] = 'Legrand';
			$cell['artikle']= $arr[4];
			$cell['name'] = $arr[1];
			$cell['presence'] = (int)$arr[3];
			$cell['packaging'] = (int)$arr[10];
			$cell['multiplicity'] = (int)$arr[11];

			//"artikle","name","presence",'multiplicity','packaging','cost','producer'
			insertProduct('ETM',$cell);
			//echo $arr[0].'</br>';
		}
		fclose($handle);
		echo ' выполнено успешно ';
    } else {
        echo "Не удалось завершить операцию\n";
    }
	
    // закрыть соединение
    ftp_close($conn_id);  
