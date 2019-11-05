<?php
$bbb =  explode ( '/' , __DIR__);
$a= array_pop ($bbb);
$filecron = implode ( '/', $bbb );
require_once ($filecron.'/model/database.php');

    $loadfile = 'esel.csv';
    $post_data = array (
        "email" => "1003522",
        "password" => "Tutanhaton77",
        "logining" => 1
    );
    $ua = 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; ru; rv:1.9.2.13) Gecko/20101203 MRA 5.7 (build 03796) Firefox/3.6.13';
        //Инициализирует сеанс
	$connection = curl_init();
	//Устанавливаем адрес для подключения
    curl_setopt($connection, CURLOPT_URL, "https://www.ec-electric.ru/login/");
    curl_setopt($connection, CURLOPT_USERAGENT, $ua);
	//Указываем, что мы будем вызывать методом POST
	curl_setopt($connection, CURLOPT_POST, 1);
	//Передаем параметры методом POST
    curl_setopt($connection, CURLOPT_POSTFIELDS, $post_data);
	//Говорим, что нам необходим результат
    curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($connection, CURLOPT_VERBOSE, true);
    curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($connection, CURLOPT_SSL_VERIFYHOST,falsE);
    curl_setopt($connection, CURLOPT_HEADER, 1);
    $verbose = fopen('php://temp', 'w+');
    curl_setopt($connection, CURLOPT_STDERR, $verbose);
	//Выполняем запрос с сохранением результата в переменную
    $rezult=curl_exec($connection);
    
    if ($rezult === FALSE) {
        printf("cUrl error (#%d): %s<br>\n", curl_errno($connection),
               htmlspecialchars(curl_error($connection)));
    }
    
    rewind($verbose);
    $verboseLog = stream_get_contents($verbose);
    
//    echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";

    $info = curl_getinfo($connection);

    preg_match_all('/Set-Cookie:\s*([^;]*)/iD', $rezult, $matches);
	$cookies = array();
	foreach($matches[1] as $item) {
	    parse_str($item, $cookie);
	    $cookies = array_merge($cookies, $cookie);
    }
    //Завершает сеанс
    curl_close($connection);

// Сохранение файла с удаленного хостинга:
function save_get_file($URL='', $cookie='')
{
    global $loadfile,$filecron;
    if (strlen($URL)<=0) return false;
    $filename = $filecron.'/uploads/'.$loadfile; 
	//echo $filename;
    $fp = fopen($filename, 'w+');
    if (!$fp)
        return false;
    else
    {
        $ua = 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; ru; rv:1.9.2.13) Gecko/20101203 MRA 5.7 (build 03796) Firefox/3.6.13';
        $ch = curl_init($URL);
        curl_setopt($ch, CURLOPT_USERAGENT, $ua);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,falsE);
        curl_setopt($ch, CURLOPT_FILE, $fp); // чтобы выгрузить в файл;
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        $verbose = fopen('php://temp1', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);
    
        $rezult=curl_exec ($ch);

        if ($rezult === FALSE) {
            printf("cUrl error (#%d): %s<br>\n", curl_errno($connection),
                   htmlspecialchars(curl_error($connection)));
        }
        
        rewind($verbose);
        $verboseLog = stream_get_contents($verbose);
        

        curl_close ($ch);
        return $filename;
    }
}
save_get_file('https://www.ec-electric.ru/stock_free/', 'PHPSESSID='.$cookies['PHPSESSID']);

echo ' Загрузил ,';

if(tableExists($tableProduct)===FALSE){
  createProduct();
  echo " создал таблицу ,";
}else{
  echo ' очистил таблицу ,'.$tableProduct."<br>";
  deleteProductByIdPurveyors('ООО «ЕС Электрик»');
};

$inputFileName = $filecron."/uploads/".$loadfile;
$handle = fopen($inputFileName, "r");
while (!feof($handle)) {
    $buffer = fgets($handle, 4096);
    $str = str_replace('"', '', $buffer);
	if (empty($str)) continue;
    $arr = [];
    $arr = explode (';', $str);
    $cell['producer']= (str_replace(' ', '', $arr[0])==='Schneider') ? 'Schneider Electric' : str_replace(' ', '', $arr[0]) ;
//    Ivliev\validation\validation::validationProducer(str_replace(' ', '', $arr[0]))
    $cell['artikle']= trim($arr[1]);
    $cell['name'] = trim($arr[2]);
    $ddddd = trim($arr[3]);
    if(!is_numeric ($ddddd)){

        $cell['presence'] = 500; 
    }else{
        $cell['presence'] = (int)$ddddd;
    }

    if($cell['artikle']!='') {
      insertProduct('ООО «ЕС Электрик»',$cell);
    }
}
fclose($handle);
         echo 'все';