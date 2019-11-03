<?php

header('Access-Control-Allow-Origin: *');

require_once ($_SERVER['DOCUMENT_ROOT'].'/SE/model/database.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/SE/elevel/elevel.php');
$url = $_POST['url'];
$summ ;
$summAdd = 0;
$schneider = true;
$tdm = false;
$timeQ = date("H");
$commercialRef = $_POST['commercialRef'];

//  $url = 'http://web.se-ecatalog.ru/new-api/JSON/'.'getstock'.'?accessCode='.'4Hw-epOswVRBPDsr3cMIViposmD1A4dA';
//  $commercialRef = '13981';
$rez = '{"result":"success","data":[{"commercialRef":"'.$commercialRef.'","stocks":[';
// $stmt = $pdo->query("SELECT * FROM '".$tableProduct."' WHERE artikle = '".$commercialRef."'");
//        var_dump(selectProductFromDB($commercialRef));

    // while ($row = $stmt->fetch())
    $retDb = selectProductFromDB($commercialRef);
    if ($retDb != NULL) {
        foreach ($retDb as $row)
        {  
            if (substr($rez,-1)=='}') {
                $rez = $rez.','; 
            }
            if ($row['firma']==='Diselectro'){
                if ($timeQ <15) {
                    $row['dt']='Cегодня,1 рабочий день,Cегодня';
                }elseif ($timeQ <18) {
                    $row['dt']='Cегодня,1 рабочий день, ';
                }else{
                    $row['dt']='1 рабочий день,1 рабочий день, ';
                }
                $rez = $rez.'{"id":"55555","warehouse":"Склад Москва","count":"'.$row['presence'].'","last_update":"'.$row['dt'].'"}';
            }elseif($row['firma']==='TDM'){
                $tdm = true;
                $summAdd+=$row['presence'];
            }else{
                if ($row['presence']==='Много ') {
                    $summ+=1000;
                }else{
                    $summ[$row['producer']]+=$row['presence'];
                }
            }
        }
    }
    $elevel = getStockElevel($commercialRef);
    if ($elevel != false) {
        foreach ($elevel as  $value) {
            if ($value->Stock !=0) {
                $summ[$value->producer]+=$value->Stock;
            } 
            $summAdd+=$value->Additional;
        }
    }
    if (is_array ($summ)) {
        foreach ($summ as $key => $value) {
            if (substr($rez,-1)=='}') {
                $rez = $rez.','; 
            }
            if ($timeQ <15) {
                $row['dt']='1 рабочий день,1-2 рабочих дня,1 рабочий день';
            }elseif($timeQ <17) {
                $row['dt']='1-2 рабочих дня,1-3 рабочих дня,1 рабочий день';
            }else{
                $row['dt']='2 рабочих дня,2-3 рабочих дня,2 рабочих дня';
            }
            $rez = $rez.'{"id":"'.$key.'","warehouse":"Центральный склад","count":"'.$value.'","last_update":"'.$row['dt'].'"}';
        }

    }
    $data=@file_get_contents($url.'&commercialRef='.$commercialRef); 
    $prom =json_decode ($data);
    if ($prom->data != NULL){
        forEach ($prom->data as $elem){
            forEach ($elem->stocks as $value) {
                // var_dump($value);
                if ($value->warehouse ==='Лобня') {
                    if (substr($rez,-1)=='}') {
                        $rez = $rez.','; 
                    }
                    $value->last_update='7 рабочих дней,8 рабочих дней, ';
                    $value->warehouse = 'Склад производителя';
                    $rez = $rez.json_encode ($value);
                    $schneider = false;
                }
            }
        }
    };
    if ($summAdd!=0 && $schneider) {
        if (substr($rez,-1)=='}') {
            $rez = $rez.','; 
        }
        if($tdm){
            $row['dt']='2-3 рабочих дня,3-4 рабочих дня,2-3 рабочих дня';
        }else {
                $row['dt']='7 рабочих дней,8 рабочих дней,7 рабочих дней';
        }
        $rez = $rez.'{"id":"55555","warehouse":"Склад производителя","count":"'.$summAdd.'","last_update":"'.$row['dt'].'"}';
    }
    $rez = $rez.']}]}';
    echo $rez;
	ini_set('display_errors','Off');


