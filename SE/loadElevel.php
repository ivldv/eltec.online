<?php
set_time_limit ( 10000 );
echo __DIR__;
require_once __DIR__.'/model/database.php';
echo __DIR__;
require_once (__DIR__.'/elevel/elevel.php');
echo __DIR__;
require_once (__DIR__.'/elevel/config.php');
echo __DIR__;
// $mass = selectAllElevelID($tableElevel);
//  $b = getStockElevelbyID($value["Idd"]);
 $current = getStockElevelbyID("");
 file_put_contents('eleveloffer.json', json_encode($current));
echo 'insert';
 /*foreach ($b as $key => $value) {
    if ($value->Stock == 0 && $value->Additional == 0) continue;
    $c = selectElevelID($value->Id,$tableElevel);
    //  if ($b->Stock == 0 && $b->Additional == 0) continue;
    $value->cost =  $value->Price;
    $value->producer =  $c["Producer"];
    $value->presence = $value->Stock;
    $value->artikle = $c["Marking"];
    $value->additional =  $value->Additional;
    $offer = json_decode(json_encode($value), True);
echo 'insert';	
    $s = insertElevelOffer($offer);
 }*/
