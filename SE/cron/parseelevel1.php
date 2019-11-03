<?php
$bbb =  explode ( '/' , __DIR__);
$a= array_pop ($bbb);
$filecron = implode ( '/', $bbb );
require_once ($filecron.'/model/database.php');

//require_once ($filecron.'/cron/elevel/elevel.php');

require_once ($filecron.'/elevel/config.php');

$current = file_get_contents($filecron.'/eleveloffer.json');

$b = json_decode($current);
echo (count ( $b ).'<br/>');
$count1 = 0;
//for ( $i = 0; $i<60000; $i++)
foreach ($b as $key => $value)
 {
//	 $key = $i;
//	 $value = $b[$i];
     if ($value->Stock == 0 && $value->Additional == 0) {$count1++; continue;};
     $c = selectElevelID($value->Id,$tableElevel);
     $value->cost =  $value->Price;
     $value->producer =  $c["Producer"];
     $value->presence = $value->Stock;
     $value->artikle = $c["Marking"];
     $value->additional =  $value->Additional;
     $offer = json_decode(json_encode($value), True);
//	 echo ($key.' -> '.$value->artikle.' -> '. $value->presence . '<br/>');	

    $s = insertElevelOffer($offer);
//	if($key>10000) die();
 }
echo ($count1.'<br/>');
echo 'все';
