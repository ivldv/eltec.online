<?php
require_once '../../vendor/autoload.php';
use Ivliev\model\Product;
require_once '../../src/model/config.php';
function date2date_v2 ( $date )
{
    $p = '~^(0?[1-9]|[12]\d|3[01])[-./ ](0?[1-9]|1[012])[-./ ]((19|20)?\d{2})$~';
    if ( preg_match ( $p, $date, $m ) )
//        return mktime ( 0, 0, 0, $m[2], $m[1], isset ( $m[4] ) ? $m[3] : 2000 + $m[3] );
        return  (isset ( $m[4] ) ? $m[3] : 2000 + $m[3] )."-".$m[2] ."-".$m[1];
else
        return FALSE;
}
function myscandir($dir, $sort=0)
{
    $list = scandir($dir, $sort);

    // если директории не существует
    if (!$list) return false;

    // удаляем . и .. (я думаю редко кто использует)
    if ($sort == 0) unset($list[0],$list[1]);
    else unset($list[count($list)-1], $list[count($list)-1]);
    return $list;
}
$dir = __DIR__.'/UZD/';
$files1 = myscandir($dir);

$tov = new Product($configPDO) ;
foreach ($files1 as $key => $loadfile)
{
    $handle = fopen($dir . $loadfile, "r");
    $buffer = fgets($handle, 4096);
    $arr = explode('|', $buffer);
    $producer = str_replace('~','',$arr[1]);
    $buffer = fgets($handle, 4096);
    $buffer = fgets($handle, 4096);
    $buffer = fgets($handle, 4096);
    $arr = explode('|', $buffer);
    $dateLoad =date2date_v2 (str_replace('~','',$arr[1]));
    $buffer = fgets($handle, 4096);

    while (!feof($handle))
    {
        $buffer = fgets($handle, 4096);
        if (!$buffer)continue;
        $arr = explode('|', $buffer);
        $tov->insertAssortiment(array(
            'article'=> str_replace('~','',$arr[0]),
            'name'=>str_replace('~','',$arr[1]),
            'producer'=>$producer,
            'groupprodukt'=>str_replace('~','',$arr[4]),
            'cost'=>floatval($arr[2]),
            'dateload'=>$dateLoad
        ));
    }
}