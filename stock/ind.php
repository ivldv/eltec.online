<?php
header('Access-Control-Allow-Origin: *');

require_once ($_SERVER['DOCUMENT_ROOT'].'/SE/model/database.php');

require_once ($_SERVER['DOCUMENT_ROOT'].'/SE/elevel/elevel.php');

$url = $_POST['url'];
$summ ;
$summAdd = 0;
$schneider = true;
$tdm = false;
$timeQ = date("H")+3;
$commercialRef = $_POST['commercialRef'];

//  $url = 'http://web.se-ecatalog.ru/new-api/JSON/'.'getstock'.'?accessCode='.'4Hw-epOswVRBPDsr3cMIViposmD1A4dA';
//  $commercialRef = '13981';
// $stmt = $pdo->query("SELECT * FROM '".$tableProduct."' WHERE artikle = '".$commercialRef."'");
//        var_dump(selectProductFromDB($commercialRef));

    $retDb = selectProductFromDB($commercialRef);
    // $elevel = getStockElevel($commercialRef);
    // if ($elevel != false) {
    //     foreach ($elevel as  $value) {
    //         $retDb[] = array(
    //             'article' =>$commercialRef ,
    //             'producer'=>$value->producer,
    //             'presence'=>$value->Stock,
    //             'firma'   =>'Elevel',
    //             'additional'=>$value->Additional);
    //     }
    // }
    //$data=@file_get_contents($url.'&commercialRef='.$commercialRef); 
    //$prom =json_decode ($data);
	// $prom->data = NULL;
    // if ($prom->data != NULL){
    //     forEach ($prom->data as $elem){
    //         forEach ($elem->stocks as $value) {
    //             // var_dump($value);
    //             if ($value->warehouse ==='Лобня') {
    //                 if (substr($rez,-1)=='}') {
    //                     $rez = $rez.','; 
    //                 }
    //                 $retDb[] = array(
    //                     'article' =>$commercialRef ,
    //                     'producer'=>'Schneider Electric',
    //                     'presence'=>0,
    //                     'firma'   =>'Schneider Electric',
    //                     'additional'=>$value->count);
    //             }
    //         }
    //     }
    // };
    foreach ($retDb as $value) {
        $row[$value['producer']][]=(array)$value;
    }
    //***************************************************************************************************************
    $rez = '{"result":"success","data":[';
    foreach ($row as $key => $ret) {
        if (substr($rez,-1)=='}') {
            $rez = $rez.','; 
        }
        $summ=0;
        $summAdd = 0;
        $rez .= '{"commercialRef":"'.$commercialRef.' ('.$key.')","stocks":[';
        foreach ($ret as $value) {
            if (substr($rez,-1)=='}') {
                $rez = $rez.','; 
            }
            if ($value['firma']==='Diselectro'){
                if ($timeQ <15) {
                    $dostavka='Cегодня,1 рабочий день,Cегодня';
                }elseif ($timeQ <18) {
                    $dostavka='Cегодня,1 рабочий день, ';
                }else{
                    $dostavka='1 рабочий день,1 рабочий день, ';
                }
                $rez = $rez.'{"id":"'.$value['producer'].'","warehouse":"Склад Москва","count":"'.$value['presence'].'","last_update":"'.$dostavka.'"}';
            }elseif($value['firma']==='TDM'){
                $tdm = true;
                $summAdd += $value['presence'];
            }else{
                if ($value['presence']==='Много ') {
                    $summ+=1000;
                }else{
                    $summ += $value['presence'];
                   if ($value['firma'] === 'Elevel'){
                        $summAdd += $value['additional'];
                    }
                }
            }
        }

        // if (substr($rez,-1)=='}') {
        //     $rez = $rez.','; 
        // }
        if ($summ !=0) {
			if (substr($rez,-1)=='}') {
                $rez = $rez.','; 
            }

            if ($timeQ <15) {
                $dostavka='1 рабочий день,1-2 рабочих дня,1 рабочий день';
            }elseif($timeQ <17) {
                $dostavka='1-2 рабочих дня,1-3 рабочих дня,1 рабочий день';
            }else{
                $dostavka='2 рабочих дня,2-3 рабочих дня,2 рабочих дня';
            }
            $rez = $rez.'{"id":"'.$value['producer'].'","warehouse":"Центральный склад","count":"'.$summ.'","last_update":"'.$dostavka.'"}';
        }
        if ($summAdd !=0) {
            if (substr($rez,-1)=='}') {
                $rez = $rez.','; 
            }
            if($tdm){
                $row['dt']='2-3 рабочих дня,3-4 рабочих дня,2-3 рабочих дня';
            }else {
                    $row['dt']='7 рабочих дней,8 рабочих дней,7 рабочих дней';
            }
            $rez .= '{"id":"55555","warehouse":"Склад производителя","count":"'.$summAdd.'","last_update":"'.$row['dt'].'"}]}';
        }else{
            $rez .= ']}';
        }
    }
    $rez = $rez.']}';
    echo $rez;



