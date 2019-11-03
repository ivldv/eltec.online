<?php
header('Access-Control-Allow-Origin: *');

require_once ($_SERVER['DOCUMENT_ROOT'].'/SE/model/database.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/SE/elevel/elevel.php');
$commercialRef = $_GET['commercialRef'];
$url = 'http://web.se-ecatalog.ru/new-api/JSON/'.'getstock'.'?accessCode='.'4Hw-epOswVRBPDsr3cMIViposmD1A4dA';
//var_dump ($_GET['commercialRef']) ;

    $retDb = selectProductFromDB($commercialRef);

	if (is_null($retDb)){
		echo '0~0';
		return;
		};
		
    foreach ($retDb as $value) {
		$strprod = strtolower ( $value['producer'] );
		if ( $strprod == 'dekraft') {
			$value['producer']='Schneider Electric';
			} 
        $row[$value['producer']][]=(array)$value;
    }
    //***************************************************************************************************************
	//var_dump ($row);
    foreach ($row as $key => $ret) {
			
		$dis = 0;
        $summ = 0;
        $summAdd = 0;
        foreach ($ret as $value) {
            if ($value['firma']==='Diselectro'){
				$dis = $value['presence'];
            }elseif($value['firma']==='TDM'){
                $tdm = true;
                $summAdd += $value['presence'];
            }else{
                if ($value['presence']==='Много ') {
                    $summ+=1000;
                }else{
                    $summ += $value['presence'];
                    // if (!($value['firma'] === 'Elevel' && $value['producer'] ==='Schneider Electric')){
                    if ($value['firma'] === 'Elevel'){
                        $summAdd += $value['additional'];
                    }
                }
            }
        }
	 echo ($summ.'~'.$summAdd);	
    }
   

