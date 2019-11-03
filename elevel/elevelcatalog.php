<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/SE/model/database.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/SE/elevel/config.php');

set_time_limit(10000);
$time = time();
$object = 'products';
$metod = 'select';
$msk = time();
$url = $apiUrl.$object.'/'.$metod.'/';
$hash = sha1($key.$contract_id.$msk);
$ua = 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; ru; rv:1.9.2.13) Gecko/20101203 MRA 5.7 (build 03796) Firefox/3.6.13';
    //Инициализирует сеанс
$connection = curl_init();
//Устанавливаем адрес для подключения
curl_setopt($connection, CURLOPT_URL, $url);
curl_setopt($connection, CURLOPT_USERAGENT, $ua);
curl_setopt($connection, CURLOPT_POST, 1);
//Говорим, что нам необходим результат
curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($connection, CURLOPT_POSTFIELDS, 'user_id='.$user_id.'&contract_id='.$contract_id.'&time='.$msk.'&hash='.$hash);
curl_setopt($connection, CURLOPT_VERBOSE, true);
curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($connection, CURLOPT_SSL_VERIFYHOST,false);
//curl_setopt($connection, CURLOPT_HEADER, 1);
$verbose = fopen('php://temp', 'w+');
curl_setopt($connection, CURLOPT_STDERR, $verbose);
//Выполняем запрос с сохранением результата в переменную
$rezult=curl_exec($connection);
$obj=json_decode($rezult);
//var_dump($obj);
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

if(tableExists($tableElevel)===FALSE){
    createElevel($tableElevel);
    echo " создал таблицу ,";
  }else{
    echo ' очистил таблицу ,'.$tableElevel."<br>";
    clearTable($tableElevel);
};
foreach ($obj as $offer) {
    insertElevelCatalog($offer,$tableElevel);
}
echo '<h2> Все </h2>';