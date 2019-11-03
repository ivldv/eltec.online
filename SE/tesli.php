<?php
require_once (__DIR__.'/tesli/teslioffer.php');
//echo (__DIR__.'/tesli/teslioffer.php');
ini_set('display_errors', 'On');
error_reporting(E_ERROR);
$loadfile = 'tesli.zip';
$local=__DIR__."/uploads/".$loadfile;
$post_data = array (
    "X-Tesli-login:ileonenko@diselec.ru",
    "X-Tesli-password:Cfvjtukfdyjt",
    "X-Tesli-inn:7719579076"
);
$ua = 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; ru; rv:1.9.2.13) Gecko/20101203 MRA 5.7 (build 03796) Firefox/3.6.13';
    //Инициализирует сеанс
$connection = curl_init();
//Устанавливаем адрес для подключения
curl_setopt($connection, CURLOPT_URL, "https://www.tesli.com/api/v1/stocks/balances");
curl_setopt($connection, CURLOPT_USERAGENT, $ua);
curl_setopt($connection, CURLOPT_HTTPHEADER, $post_data);
//Говорим, что нам необходим результат
curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($connection, CURLOPT_VERBOSE, true);
curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($connection, CURLOPT_SSL_VERIFYHOST,falsE);
// curl_setopt($connection, CURLOPT_HEADER, 1);
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

//echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";

$info = curl_getinfo($connection);

//Завершает сеанс
curl_close($connection);
//Выводим на экран
//echo $rezult ;

$obj=json_decode($rezult);

foreach ($obj->files as $key => $value) {
if ($value->stock->city == 'Москва'){
        $remoteUrl = $value->urlFile;
		break;
    };
};
//echo $remoteUrl;
$ch = curl_init($remoteUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
$output = curl_exec($ch);
$fh = fopen($local, 'w');
fwrite($fh, $output);
fclose($fh);

// assuming file.zip is in the same directory as the executing script.
$file = 'file.zip';

// get the absolute path to $file
$path = pathinfo(realpath($local), PATHINFO_DIRNAME);

$zip = new ZipArchive;
$res = $zip->open($local);
if ($res === TRUE) {
    $zip->renameIndex(0,'offers.xml');
    $zip->close();
}
$res = $zip->open($local);
if ($res === TRUE) {
  // extract it to the path we determined above
  $zip->extractTo($path);
  $zip->close();
  echo "WOOT! $local extracted to $path";
} else {
  echo "Doh! I couldn't open $local";
}
if(tableExists($tableProduct)===FALSE){
    createProduct();
    echo " создал таблицу ,";
  }else{
    echo ' очистил таблицу ,'.$tableProduct."<br>";
    deleteProductByIdPurveyors('Tesli');
  };
  
//	var_dump($path.'/offers.xml');	

webi_xml($path.'/offers.xml');
