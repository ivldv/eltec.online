<?php
namespace Ivliev\model;

use PDO;

class Product {
// данные (свойства):
var $pdo;
var $tableProduct;
var $tableoffers;

// Конструктор
public function __construct(array $configPDO)
{
    $dsn = "mysql:host=".$configPDO['host'].";dbname=".$configPDO['db'].";charset=".$configPDO['charset'];
    $opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $this->pdo = new PDO($dsn, $configPDO['user'], $configPDO['pass'], $opt);
    $this->pdo->query("SET wait_timeout=9999;");
    $this->tableProduct = $configPDO['tableProduct'];
    $this->tableoffers = $configPDO['tableoffers'];

}
    function pdoSet($allowed, &$values, $source = array())
    {
        $set = '';
        $values = array();
        // if (!$source) $source = &$_POST;
        foreach ($allowed as $field) {
            if (isset($source[$field])) {
                $set.="`".str_replace("`","``",$field)."`". "=:$field, ";
                $values[$field] = $source[$field];
            }
        }
        return substr($set, 0, -2);
    }
    public function deleteProductByIdPurveyors($purveyors){
        $sql = "DELETE FROM ".$this->tableProduct." WHERE `id_seller`= ( SELECT id FROM purveyors where name = '".$purveyors."')";
//    echo ($sql);
        $stm = $this->pdo->prepare($sql);
        $stm->execute();
    }

    /**
     * Insert product in the current database.
     *
     * @param $Purveyors
     * @param $products
     * @return bool
     */
    public function insertProduct($Purveyors,$products){
        $allowed = array("artikle","name","presence",'multiplicity','packaging','cost','producer','groupprodukt'); // allowed fields

        $sql = "INSERT INTO ".$this->tableProduct." SET id_seller = ( SELECT id FROM purveyors WHERE name = '".$Purveyors."' ),".$this->pdoSet($allowed,$values,$products);

        $stm = $this->pdo->prepare($sql);
        return $stm->execute($values);
    }
    /**
     * Check if a table exists in the current database.
     *
     * @param string $table Table to search for.
     * @return bool TRUE if table exists, FALSE if no table found.
     */
    public function tableExists($table) {
        // Try a select statement against the table
        // Run it in try/catch in case PDO is in ERRMODE_EXCEPTION.
        try {
            $result = $this->pdo->query("SELECT 1 FROM $table LIMIT 1");
        } catch (Exception $e) {
            // We got an exception == table not found
            return FALSE;
        }

        // Result is either boolean FALSE (no table found) or PDOStatement Object (table found)
        return $result !== FALSE;
    }

    /**
     * Check if a table exists in the current database.
     *
     * @param string article
     * @return array TRUE if table exists, FALSE if no table found.
     */
    public function selectProductFromDB($art){
        $row = null;
        $sql = "SELECT `id` FROM ".$this->tableoffers." WHERE `vendorcode`= '".$art."'";
        $stmt = $this->pdo->query($sql);
        $idPos = $stmt->fetchAll(PDO::FETCH_COLUMN);
        if ($idPos) {
            $sql = "SELECT  ".$this->tableoffers.".vendorcode AS `artikle`,
                        ".$this->tableoffers.".model AS `name`,
                        ".$this->tableoffers.".producer,
                presence,dt,purveyors.name as firma  
                FROM ".$this->tableProduct." 
                LEFT JOIN purveyors ON ".$this->tableProduct.".id_seller = purveyors.id 
                LEFT JOIN ".$this->tableoffers." ON ".$this->tableProduct.".name = ".$this->tableoffers.".id
                WHERE ".$this->tableProduct.".name = ";
            foreach ($idPos as $key => $value) {
                $sql .= ($key != 0) ? " OR ".$this->tableProduct.".name = " : "" ;
                $sql .="'".$value."'";
            }
            $sql .=" AND ".$this->tableProduct.".id_seller = (SELECT id FROM purveyors where name = 'Tesli')";
            //     echo ($sql);
            $stmt = $this->pdo->query($sql);
            while ($r = $stmt->fetch()){
                if (gettype($r)==='array'){
                    $row[]= $r;
                    // var_dump($r);
                    // echo '<br>';
                }
            }
        }

        $sql = "SELECT artikle,".$this->tableProduct.".name,presence,additional,dt,producer,purveyors.name as firma  FROM ".$this->tableProduct." 
            LEFT JOIN purveyors ON ".$this->tableProduct.".id_seller = purveyors.id WHERE artikle='".$art."'";
        //echo ($sql);
        $stmt = $this->pdo->query($sql);
        while ($r = $stmt->fetch()){
            if (gettype($r)==='array') $row[]= $r;
            // var_dump($r);
            // echo '<br>';
        }
        return $row;
    }
}
require_once 'config.php';

$tov = new Product($configPDO) ;
$aaa = $tov->selectProductFromDB('774320');
var_dump($aaa);