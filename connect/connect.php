<?php
header('Access-Control-Allow-Origin: *');
if(isset($_POST['login'])) {
    require_once ('../SE/model/database.php');
    global $pdo,$tableProduct;
    if ($_POST['comand'] === 'delete'){
        $sql = "DELETE FROM ".$tableProduct." WHERE `id_seller`= ( SELECT id FROM purveyors where name = '".$_POST['data']."')";
        //    echo ($sql);
    }elseif($_POST['comand'] === 'insert'){
        $sql = $_POST['data'];
    }
    $stm = $pdo->prepare($sql);
    if($stm->execute()){
        echo 'ok';
    }else{
        echo 'error';
    }

}