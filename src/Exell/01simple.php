<?php

    require_once '../../vendor/autoload.php';
    $arr = ['aaa','bbb','vvv','ggg'];
    $ex = new Ivliev\Exell\priceExel();
    $ex->writeString($arr);