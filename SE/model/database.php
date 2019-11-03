
<?php
if(isset($filecron)){
	require_once ($filecron.'/model/config.php');
//	echo $filecron.'/model/config.php';
}else{
	require_once $_SERVER['DOCUMENT_ROOT'].'/SE/model/config.php';
}
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO($dsn, $user, $pass, $opt);
$pdo->query("SET wait_timeout=9999;");

function pdoSet($allowed, &$values, $source = array()) {
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

Function deleteProductByIdPurveyors($purveyors){
    global $pdo,$tableProduct;
    $sql = "DELETE FROM ".$tableProduct." WHERE `id_seller`= ( SELECT id FROM purveyors where name = '".$purveyors."')";
//    echo ($sql);
    $stm = $pdo->prepare($sql);
    $stm->execute();
}
/**
 * Check if a table exists in the current database.
 *
 * @param string $table Table to search for.
 * @return bool TRUE if table exists, FALSE if no table found.
 */
function insertProduct($Purveyors,$products){
    global $pdo,$tableProduct;
    $allowed = array("artikle","name","presence",'multiplicity','packaging','cost','producer'); // allowed fields
    
    $sql = "INSERT INTO ".$tableProduct." SET id_seller = ( SELECT id FROM purveyors WHERE name = '".$Purveyors."' ),".pdoSet($allowed,$values,$products);

    $stm = $pdo->prepare($sql);
    $stm->execute($values);
}
function createProduct(){
    global $pdo,$tableProduct;
    $sql = " CREATE TABLE `".$tableProduct."` (
        `id_seller` int(11) NOT NULL,
        `producer` varchar(50) NOT NULL,
        `artikle` varchar(30) NOT NULL,
        `name` varchar(250) NOT NULL,
        `presence` varchar(11) NOT NULL,
        `multiplicity` int(11) NOT NULL,
        `packaging` int(11) NOT NULL,
        `cost` varchar(11) NOT NULL,
        `dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ";
     echo $sql;
    $stm = $pdo->prepare($sql);
    $stm->execute($values);

    $sql = "ALTER TABLE `".$tableProduct."` ADD `id` INT NOT NULL AUTO_INCREMENT , ADD PRIMARY KEY (`id`);";
    $stm = $pdo->prepare($sql);
    $stm->execute($values);

};
function createTesli(){
    global $pdo;
    $sql = " CREATE TABLE `teslicategory` ( 
        `idd` INT NOT NULL AUTO_INCREMENT ,
        `id` VARCHAR(50) NOT NULL ,
        `parentId` VARCHAR(50) NOT NULL , 
        `name` VARCHAR(250) NOT NULL , 
        PRIMARY KEY (`idd`)) ENGINE = InnoDB;
    ";
     echo $sql;
    $stm = $pdo->prepare($sql);
    $stm->execute($values);

    $sql = "CREATE TABLE `teslioffers` ( 
        `id` VARCHAR(50) NOT NULL , 
        `url` VARCHAR(200) NOT NULL , 
        `categoryid` VARCHAR(50) NOT NULL , 
        `model` VARCHAR(250) NOT NULL , 
        `mainimage` VARCHAR(250) NOT NULL , 
        `step_count` INT NOT NULL , 
        `producer` VARCHAR(200) NOT NULL , 
        `vendorcode` VARCHAR(40) NOT NULL , 
        `series` VARCHAR(200) NOT NULL , 
        `discountcode` VARCHAR(50) NOT NULL , 
        `imgid` INT NOT NULL , 
        `paramid` INT NOT NULL , 
        `internalid` INT NOT NULL AUTO_INCREMENT , 
        PRIMARY KEY (`internalid`,`id`(50))) ENGINE = InnoDB;
    ";
    //  echo $sql;
    $stm = $pdo->prepare($sql);
    $stm->execute($values);

    $sql = " CREATE TABLE `tesliparam` ( 
        `id` VARCHAR(50) NOT NULL , 
        `name` VARCHAR(200) NOT NULL , 
        `paramid` INT NOT NULL AUTO_INCREMENT , 
        PRIMARY KEY (`paramid`,`id`(50))) ENGINE = InnoDB;
    ";
    //  echo $sql;
    $stm = $pdo->prepare($sql);
    $stm->execute($values);

    $sql = " CREATE TABLE `tesliimg` ( 
        `id` INT NOT NULL AUTO_INCREMENT , 
        `idoffer` VARCHAR(50) NOT NULL , 
        `url` VARCHAR(250) NOT NULL , 
        PRIMARY KEY (`id`)) ENGINE = InnoDB;
    ";
    //  echo $sql;
    $stm = $pdo->prepare($sql);
    $stm->execute($values);

    $sql = "CREATE TABLE `tesliValueParam` ( 
        `id` INT NOT NULL AUTO_INCREMENT , 
        `idoffer` VARCHAR(50) NOT NULL ,
        `idParam` VARCHAR(50) NOT NULL , 
        `data` VARCHAR(100) NOT NULL , 
        PRIMARY KEY (`id`)) ENGINE = InnoDB;
    ";
    //  echo $sql;
    $stm = $pdo->prepare($sql);
    $stm->execute($values);

};
function clearTesli(){
    global $pdo;
    echo '<h1>clear tesli</h1>';
    try {
        $sql = "TRUNCATE TABLE `tesliimg`;";
        $pdo->exec($sql);
    }
    catch(PDOException $e){
        echo $sql . "<br>" . $e->getMessage();
    } 
    try {
        $sql = "TRUNCATE TABLE `tesliparam`;";
        $pdo->exec($sql);
    }
    catch(PDOException $e){
        echo $sql . "<br>" . $e->getMessage();
    }        
    try {
        $sql = "TRUNCATE TABLE `teslioffers`;";
        $pdo->exec($sql);
    }
    catch(PDOException $e){
        echo $sql . "<br>" . $e->getMessage();
    }        
    try {
        $sql = "TRUNCATE TABLE `teslicategory`;";
        $pdo->exec($sql);
    }
    catch(PDOException $e){
        echo $sql . "<br>" . $e->getMessage();
    } 
    try {
        $sql = "TRUNCATE TABLE `teslivalueparam`;";
        $pdo->exec($sql);
    }
    catch(PDOException $e){
        echo $sql . "<br>" . $e->getMessage();
    }        
       
    // $stm = $pdo->prepare($sql);
    // $stm->execute();
}
function insertTesliCatalog($params,$imgs,$offers){
    global $pdo;
    // static $debug = 0;
    $tablePparams = 'tesliparam';
    $tableValues = 'teslivalueparam';
    $tableoffers = 'teslioffers';
    $tableimg = 'tesliimg';
    foreach ($params as $key => $param) {
        $sql = "SELECT ".$tablePparams.".id
                FROM ".$tablePparams." 
                WHERE ".$tablePparams.".id='".$param['id']."';"; 

        // echo $sql.'<br>'; 
        $stmt = $pdo->query($sql);
        $flag1 = true;
        while ($r = $stmt->fetch()){
        //    var_dump ($r);
           $flag1 = false;
        }
        if ($flag1){
            $allowed = array("id","name");
            $sql = "INSERT INTO ".$tablePparams." SET ".pdoSet($allowed,$values,$param);
            // echo '<br>';
            $stm = $pdo->prepare($sql);
            $stm->execute($values);
        };
        $param['idParam']=$param['id'];
        $param['idoffer']=$offers['id'];
        $allowed = array("idParam","data",'idoffer');
        $sql = "INSERT INTO ".$tableValues." SET ".pdoSet($allowed,$values,$param);
        // echo $sql;
        // echo '<br>';
        $stm = $pdo->prepare($sql);
        $stm->execute($values);
    }
    if (! is_null($imgs)) {
        foreach ($imgs as $key => $value) {
            $img['idoffer']=$offers['id'];
            $img['url']=$value;
            $allowed = array("url","idoffer"); // allowed fields
            $sql = "INSERT INTO ".$tableimg." SET ".pdoSet($allowed,$values,$img);
                    // echo $sql;
                    // echo '<br>';
            $stm = $pdo->prepare($sql);
            $stm->execute($values);
        };
    };
    // var_dump($imgs);
    $allowed = array("id","url","categoryid",'model','mainimage','step_count','producer','vendorcode','series','discountcode'); // allowed fields
    $sql = "INSERT INTO ".$tableoffers." SET ".pdoSet($allowed,$values,$offers);
            // echo $sql;
            // echo '<br>';
    $stm = $pdo->prepare($sql);
    $stm->execute($values);

    // $debug++;
    // if ($debug ===10000) die();
}
function insertTesliCategory($category){
    global $pdo;
    $tableCategory = 'teslicategory';
    $allowed = array("name","id","parentid"); // allowed fields
    $sql = "INSERT INTO ".$tableCategory." SET ".pdoSet($allowed,$values,$category);

    $stm = $pdo->prepare($sql);
    $stm->execute($values);
}
function insertTesliOffer($offer){
    global $pdo,$tableProduct,$tableoffers;
        $sql = "SELECT id FROM purveyors where name = 'Tesli';";
        $stmt = $pdo->query($sql);
        while ($r = $stmt->fetch()){
            $offer['id_seller'] = $r['id'];
                // var_dump($r);
                // echo '<br>';
            }  
        $offer['name'] = $offer['id'];

        $allowed = array('id_seller','artikle',"name",'presence','multiplicity','cost'); // allowed fields
        $sql = "INSERT INTO ".$tableProduct." SET ".pdoSet($allowed,$values,$offer);
    
        $stm = $pdo->prepare($sql);
        $stm->execute($values);
}
function createElevel($name){
    global $pdo;
    $sql = " CREATE TABLE `".$name."` ( 
        `id` INT NOT NULL AUTO_INCREMENT ,
        `Idd` VARCHAR(50) NOT NULL ,
        `Unit` VARCHAR(5) NOT NULL ,
        `Series` VARCHAR(30) NOT NULL ,
        `Category` INT NOT NULL ,
        `Multiplicity` INT NOT NULL ,
        `Marking` VARCHAR(50) NOT NULL , 
        `Anons` VARCHAR(2000) NOT NULL ,
        `Producer` VARCHAR(50) NOT NULL , 
        `Name` VARCHAR(250) NOT NULL , 
        `Break` INT NOT NULL , 
        PRIMARY KEY (`id`)) ENGINE = InnoDB;
    ";
    $stm = $pdo->prepare($sql);
    $stm->execute($values);
}
function insertElevelCatalog($offer,$table){
    global $pdo;
    foreach ($offer as $key => $value) {
        $param[$key]=$value;
    }
    $param['Idd'] = $param['Id'];
    $allowed = array('Idd','Unit','Series','Category','Multiplicity','Marking','Anons','Producer','Name','Break');
    $sql = "INSERT INTO ".$table." SET ".pdoSet($allowed,$values,$param);
    // echo $sql;
    // echo '<br>';
    $stm = $pdo->prepare($sql);
    $stm->execute($values);
}
function clearTable($name){
    global $pdo;
    echo ('<h1>clear '.$name.'</h1>');
    try {
        $sql = "TRUNCATE TABLE `".$name."`;";
        $pdo->exec($sql);
    }
    catch(PDOException $e){
        echo $sql . "<br>" . $e->getMessage();
    } 
}
function selectIdElevel($article,$tableElevel){
    global $pdo;
    $sql = "SELECT Idd AS id, Producer AS producer FROM ".$tableElevel." where Marking = '".$article."';";
    $stmt = $pdo->query($sql);
    while ($r = $stmt->fetch()){
        $rez = $r;
            // var_dump($r);
            // echo '<br>';
        } 
        $retVal = (is_null($rez)) ? false : $rez ; 
        return $retVal;

}

function selectElevelID($id,$tableElevel){
    global $pdo;
    $sql = "SELECT Producer,Marking  FROM ".$tableElevel." WHERE Idd = ".$id.";";
    $stmt = $pdo->query($sql);
    while ($r[] = $stmt->fetch()){
        // $rez = $r;
            // var_dump($r);
            // echo '<br>';
        } 
        // $retVal = (is_null($rez)) ? false : $rez ; 
        return $r[0];

}

function insertElevelOffer($offer){
	
    global $pdo,$tableProduct,$tableoffers;
        $sql = "SELECT id FROM purveyors where name = 'Elevel';";
        $stmt = $pdo->query($sql);
        while ($r = $stmt->fetch()){
            $offer['id_seller'] = $r['id'];
            } 
			
//        $offer['name'] = $offer['id'];
        $a=0;
        $sql = "SELECT `id`,`presence`,`additional` FROM ".$tableProduct." WHERE id_seller = ".$offer["id_seller"]." AND artikle = '".$offer["artikle"]."'";
//		echo($sql.'</br>');
        $stmt = $pdo->query($sql);
		$a=0;
        while ($r = $stmt->fetch()){
            $a = $r['id'];
            $presence = $r['presence'];
            $additional = $r['additional'];
//			var_dump($r);
//			echo ('</br>');
            } 
        if ((isset ( $presence ))&&($presence == $offer["presence"]) && ($additional == $offer['additional'])) return;
        if ($offer['producer']==NULL) $offer['producer'] = "";
        if ($a != 0) {
            $allowed = array('presence','additional','producer','cost'); // allowed fields
            $sql = "UPDATE `".$tableProduct."` SET ".pdoSet($allowed,$values,$offer).",`dt` = NOW() WHERE `id` = ".$a;
//		echo($sql.'</br>');
        
            $stm = $pdo->prepare($sql);
            $stm->bindParam('presence', $offer["presence"]);
            $stm->bindParam('additional', $offer['additional']);
            $stm->bindParam('producer', $offer['producer']);
            $stm->bindParam('cost', $offer['cost']);

//echo ('update'.$offer["artikle"].'</br>');
            $result = $stm->execute();
        } else {
            $allowed = array('id_seller','artikle','presence','additional','producer','cost'); // allowed fields
            $sql = "INSERT INTO ".$tableProduct." SET ".pdoSet($allowed,$values,$offer);
//print_r($offer);
//echo ('insert'.$offer["artikle"].'</br>');

            $stm = $pdo->prepare($sql);
            $result = $stm->execute($values);
        }
}

//TRUNCATE `test1`.`rrrrrr`
/**
 * Check if a table exists in the current database.
 *
 * @param string $table Table to search for.
 * @return bool TRUE if table exists, FALSE if no table found.
 */
function tableExists($table) {
    global $pdo,$tableProduct;
    // Try a select statement against the table
    // Run it in try/catch in case PDO is in ERRMODE_EXCEPTION.
    try {
        $result = $pdo->query("SELECT 1 FROM $table LIMIT 1");
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
 * @param string $table Table to search for.
 * @return bool TRUE if table exists, FALSE if no table found.
 */
function selectProductFromDB($art){
    global $pdo,$tableProduct,$tableoffers;
    $sql = "SELECT `id` FROM ".$tableoffers." WHERE `vendorcode`= '".$art."'";
    $stmt = $pdo->query($sql);
    $idPos = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if ($idPos) {
        $sql = "SELECT  ".$tableoffers.".vendorcode AS `artikle`,
                        ".$tableoffers.".model AS `name`,
                        ".$tableoffers.".producer,
                presence,dt,purveyors.name as firma  
                FROM ".$tableProduct." 
                LEFT JOIN purveyors ON ".$tableProduct.".id_seller = purveyors.id 
                LEFT JOIN ".$tableoffers." ON ".$tableProduct.".name = ".$tableoffers.".id
                WHERE ".$tableProduct.".name = ";
        foreach ($idPos as $key => $value) {
            $sql .= ($key != 0) ? " OR ".$tableProduct.".name = " : "" ;
            $sql .="'".$value."'";
        }
        // SELECT `id` FROM ".$tableoffers." WHERE `vendorcode`= '".$art."')
        $sql .=" AND ".$tableProduct.".id_seller = (SELECT id FROM purveyors where name = 'Tesli')";
        //     echo ($sql);
        $stmt = $pdo->query($sql);
        while ($r = $stmt->fetch()){
            if (gettype($r)==='array'){
                $row[]= $r;
                // var_dump($r);
                // echo '<br>';
            }  
        }
    }

    $sql = "SELECT artikle,".$tableProduct.".name,presence,additional,dt,producer,purveyors.name as firma  FROM ".$tableProduct." 
            LEFT JOIN purveyors ON ".$tableProduct.".id_seller = purveyors.id WHERE artikle='".$art."'";
    //echo ($sql);
    $stmt = $pdo->query($sql);
    while ($r = $stmt->fetch()){
        if (gettype($r)==='array') $row[]= $r; 
        // var_dump($r);
        // echo '<br>';
    }
    return $row;
}
//var_dump(selectProductFromDB('GSL000600C'));