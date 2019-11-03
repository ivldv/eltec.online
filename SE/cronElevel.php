<?php
set_time_limit ( 10000 );
ini_set('display_errors', 'On');
ini_set('memory_limit', '100M'); 
error_reporting(E_ALL); // E_ALL - отображаем ВСЕ ошибки

	$goods_id = '';
    $user_id = '14346';
    $contract_id = '19703';
    $key = '745185adf8b7c1b8f0b3289d1b2775cfa2c05a66';
    $apiUrl = "https://eway.elevel.ru/api/v2/";
    $tableElevel = 'elevelcatalog';
    $time = time();
    $object = 'stocks';
    $metod = 'select';
    $msk = time();
    $url = $apiUrl.$object.'/'.$metod.'/';
    $hash = sha1($key.$contract_id.$goods_id.$msk);
    $ua = 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; ru; rv:1.9.2.13) Gecko/20101203 MRA 5.7 (build 03796) Firefox/3.6.13';
echo 'start';	
        //Инициализирует сеанс
    $connection = curl_init();
    //Устанавливаем адрес для подключения
    curl_setopt($connection, CURLOPT_URL, $url);
    curl_setopt($connection, CURLOPT_USERAGENT, $ua);
    curl_setopt($connection, CURLOPT_POST, 1);
    //Говорим, что нам необходим результат
    curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($connection, CURLOPT_POSTFIELDS, 'user_id='.$user_id.'&contract_id='.$contract_id.'&goods_id='.$goods_id.'&time='.$msk.'&hash='.$hash);
    curl_setopt($connection, CURLOPT_VERBOSE, true);
    curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($connection, CURLOPT_SSL_VERIFYHOST,falsE);
    curl_setopt($connection, CURLOPT_TIMEOUT,3000);
    $verbose = fopen('php://temp', 'w+');
    curl_setopt($connection, CURLOPT_STDERR, $verbose);
    //Выполняем запрос с сохранением результата в переменную
    $rezult=curl_exec($connection);
    
    //$obj=json_decode($rezult);
    if ($rezult === FALSE) {
        printf("cUrl error (#%d): %s<br>\n", curl_errno($connection),
               htmlspecialchars(curl_error($connection)));
    }
    
    rewind($verbose);
    $verboseLog = stream_get_contents($verbose);
    
    //echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";
    
    $info = curl_getinfo($connection);
    //Завершает сеанс
    curl_close($connection);
//    var_dump($obj);
//$stringjson = json_encode($obj);
//echo (__DIR__);
 var_dump(file_put_contents(__DIR__.'/eleveloffer.json', $rezult));
echo 'insert';
 /*foreach ($b as $key => $value) {
    if ($value->Stock == 0 && $value->Additional == 0) continue;
    $c = selectElevelID($value->Id,$tableElevel);
    //  if ($b->Stock == 0 && $b->Additional == 0) continue;
    $value->cost =  $value->Price;
    $value->producer =  $c["Producer"];
    $value->presence = $value->Stock;
    $value->artikle = $c["Marking"];
    $value->additional =  $value->Additional;
    $offer = json_decode(json_encode($value), True);
echo 'insert';	
    $s = insertElevelOffer($offer);
 }*/
