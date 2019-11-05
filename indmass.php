<?php
header('Access-Control-Allow-Origin: *');
if ($_SERVER["REQUEST_METHOD"]=="OPTIONS") {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST');
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Headers: content-type');
    die();
}

//if ($_SERVER["CONTENT_TYPE"] ==  "application/json; charset=utf-8") {
    $postData = file_get_contents('php://input');
    $data = json_decode($postData, true);
//}
//$data = ["SDN5801341","SDN0401160","040547","040546","040543","040542","040540","1520EINS","33543","33542","33544","264056","0225-0-0078","0225-0-0086","0225-0-0154","0230-0-0201","0230-0-0235","0230-0-0243","0230-0-0395","0230-0-0396","0230-0-0399","0230-0-0400","0230-0-0401","0230-0-0408"];
//$data = ['031210'];
require_once ($_SERVER['DOCUMENT_ROOT'].'/SE/model/database.php');

//require_once ($_SERVER['DOCUMENT_ROOT'].'/elevel_service/elevel.php');

//$url = 'http://web.se-ecatalog.ru/new-api/JSON/'.'getstock'.'?accessCode='.'4Hw-epOswVRBPDsr3cMIViposmD1A4dA';
foreach ($data as $key => $commercialRef) {

    $retDb = selectProductFromDB($commercialRef);
    $day = [0,0,0];
    if ($retDb != null) {
        foreach ($retDb as $value) {
            $value['presence'] = ($value['presence'] == null) ? 0 : $value['presence'] ;
            // $row[$value['producer']][]=(array)$value;
            if ($value['firma'] == "Diselectro") {
                $day[1] += $value['presence'];
            }elseif ($value['firma'] == 'Elevel') {
                $day[1] += $value['presence'];
                $day[2] += $value['additional'];
            }elseif ($value['firma'] == 'TDM') {
                $day[2] += $value['presence'];
            }else{
                if ($value['presence'] == "Много ") {
                    $day[1] += 500;
                }else{
                    $day[1] += $value['presence'];
                }
            }
        }
    }
    $rezult[] = $day[1].' / '.$day[2];
    //***************************************************************************************************************

}
$rez = json_encode($rezult);
echo $rez;



