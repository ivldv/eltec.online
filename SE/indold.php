<?php
header('Access-Control-Allow-Origin: *');
require_once __DIR__.'/model/database.php';

$url = $_POST['url'];
$commercialRef = $_POST['commercialRef'];
//$url = 'http://web.se-ecatalog.ru/new-api/JSON/'.'getstock'.'?accessCode='.'4Hw-epOswVRBPDsr3cMIViposmD1A4dA';
//$commercialRef = 'mgu2.002.18';
$rez = '{"result":"success","data":[{"commercialRef":"'.$commercialRef.'","stocks":[';
    foreach (selectProductFromDB($commercialRef) as $row)
    {  
        if (substr($rez,-1)=='}') {
            $rez = $rez.','; 
        }
        $rez = $rez.'{"id":"55555","warehouse":"'.$row['firma'].'","count":"'.$row['presence'].'","last_update":"'.$row['dt'].'"}';
        $ies = 1;
    }
    $data=file_get_contents($url.'&commercialRef='.$commercialRef); 
    $prom =json_decode ($data);
    if ($prom->data != NULL){
        forEach ($prom->data as $elem){
            forEach ($elem->stocks as $value) {
                if (substr($rez,-1)=='}') {
                    $rez = $rez.','; 
                }
                $rez = $rez.json_encode ($value);
            }
        }
    };
    $rez = $rez.']}]}';
    echo $rez;


