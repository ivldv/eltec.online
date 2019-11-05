<?php
header('Access-Control-Allow-Origin: *');
//$url = file_get_contents('php://input'); 
//var_dump ($_POST);
$url = $_POST['url'];
$commercialRef = $_POST['commercialRef'];
$data=file_get_contents($url.'&commercialRef='.$commercialRef); 
// echo $url;
// $data=file_get_contents($url);
echo $data;
//var_dump ($_POST);