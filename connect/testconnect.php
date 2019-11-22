<?php
 
//выбираем страницу на которую необходимо отправить запрос
$url = 'http://eltec.online.local/connect/connect.php';
//параметры которые необходимо передать
$params = array(
    'login' => 'WQP',
    'password' => 'qwerty',
    'rem' => 'y'
);
$result = file_get_contents($url, false, stream_context_create(array(
    'http' => array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => http_build_query($params)
    )
)));
 
echo $result;?>