<?php
header('Access-Control-Allow-Origin: *');
require_once __DIR__.'/model/database.php';
require_once ($_SERVER['DOCUMENT_ROOT'].'/SE/elevel/elevel.php');

$url = $_POST['url'];
$commercialRef = $_POST['commercialRef'];
// $url = 'http://web.se-ecatalog.ru/new-api/JSON/'.'getstock'.'?accessCode='.'4Hw-epOswVRBPDsr3cMIViposmD1A4dA';
//$commercialRef = 'mgu2.002.18';
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
            $rez = $rez.'{"id":"55555","warehouse":"'.$row['firma'].'","count":"'.$row['presence'].'","last_update":"'.$row['dt'].'"}';
            $ies = 1;
            if ($row['firma'] == "Elevel") {
            if (substr($rez,-1)=='}') {
                $rez = $rez.','; 
            }
                $rez = $rez.'{"id":"55555","warehouse":"Elevel-alt","count":"'.$row['additional'].'","last_update":"'.$row['dt'].'"}';
            }
        }
    }
    // $elevel = getStockElevel($commercialRef);
    // if ($elevel != false) {
    //     foreach ($elevel as  $value) {
    //         if (substr($rez,-1)=='}') {
    //             $rez = $rez.','; 
    //         }
    //         if ($value->Stock !=0) {
    //             $rez = $rez.'{"id":"55555","warehouse":"Elevel","count":"'.$value->Stock.'","last_update":"'.date("d-m-Y H:i:s").'"},';                # code...
    //         } 
    //         $rez = $rez.'{"id":"55555","warehouse":"Elevel-alt","count":"'.$value->Additional.'","last_update":"'.date("d-m-Y H:i:s").'"}';
    //     }
    // }
    // $data=@file_get_contents($url.'&commercialRef='.$commercialRef); 
    // $prom =json_decode ($data);
    // if ($prom->data != NULL){
    //     forEach ($prom->data as $elem){
    //         forEach ($elem->stocks as $value) {
    //             if (substr($rez,-1)=='}') {
    //                 $rez = $rez.','; 
    //             }
    //             $rez = $rez.json_encode ($value);
    //         }
    //     }
    // };
    $rez = $rez.']}]}';
    echo $rez;



