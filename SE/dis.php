<?php
require_once __DIR__.'/model/database.php';

$filename = 'ostatok.xml';
copy("php://input", $filename);
$xml = simplexml_load_file($filename);
if(tableExists($tableProduct)===FALSE){
    createProduct();
    echo " Create table ,";
  }else{
    echo ' Clear table '.$tableProduct."<br>";
    deleteProductByIdPurveyors('Diselectro');
};
foreach ($xml as $value) {
    $cell = (array)$value;
    insertProduct('Diselectro',$cell);
}
echo 'ok';