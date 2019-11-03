<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/model/database.php';
class Product {
// данные (свойства):
var $artikle;
var $name;
var $presence;
var $multiplicity;
var $packaging;
var $cost;

// Конструктор
 function Product($prod) {
     $this->article = $prod['artikle'];
     $this->name = $prod['name'];
     $this->presence = $prod['presence'];
     $this->multiplicity = $prod['multiplicity'];
     $this->packaging = $prod['packaging'];
     $this->cost = $prod['cost'];
 }

}
$tov = new Product([
    'artikle'=>'gggggg',
    'name' => 'hgjhgjhgjhgjhgjhgjhgjhgjhgj'
]);
var_dump ($tov->name);