<?php
// function myErrorHandler($errno, $errstr, $errfile, $errline)
// {
// 	echo 'error load ';
// 	global $buffer;
// 	var_dump ($buffer);
// }
// $old_error_handler = set_error_handler("myErrorHandler");
//  $bbb =  explode ( '/' , __DIR__);
//  $a= array_pop ($bbb);
//  $filecron = implode ( '/', $bbb );
 require_once ('../model/database.php');
    $ftp_server = "93.189.41.9";
    $ftp_user = "u842261";
    $ftp_pass = "k6oKnqRVVOyG";
    $local_file = 'price.xml';
    $server_file = '/tver/price.xml';
    // установить соединение или выйти
    $conn_id = ftp_connect($ftp_server) or die("Не удалось установить соединение с $ftp_server"); 
    
    // попытка входа
    if (@ftp_login($conn_id, $ftp_user, $ftp_pass)) {
        echo "Произведен вход на $ftp_server под именем $ftp_user\n";
    } else {
        echo "Не удалось войти под именем $ftp_user\n";
	}

	ftp_pasv($conn_id, true);
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
	$server_file = $name;
    // попытка скачать $server_file и сохранить в $local_file
    if (ftp_get($conn_id, $local_file, $server_file, FTP_BINARY)) {
	    echo "Произведена запись в $local_file\n";
		deleteProductByIdPurveyors('Russkyi svet');
		$doc = simplexml_load_file($local_file);
		foreach ($doc->DocDetail as $key => $element) {
			$cell['name'] = $element->ProductName->__toString();
			$cell['presence'] = strstr($element->QTY->__toString(), '.', true);
			$cell['artikle']= $element->VendorProdNum->__toString();
			$cell['producer']=$element->Brand->__toString();

			if($cell['presence']!=""){
				insertProduct('Russkyi svet',$cell);
			}

		}
		echo ' выполнено успешно ';
	}
    // закрыть соединение
    ftp_close($conn_id);  
