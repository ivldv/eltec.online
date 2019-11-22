<?php
require_once '../vendor/autoload.php';
require_once ('../SE/model/database.php');

    $sql = "SELECT `producer`,`artikle`,`presence` FROM $tableProduct where `id_seller` = (SELECT id FROM `purveyors` WHERE `name` LIKE 'Diselectro' );";
    $stmt = $pdo->query($sql);
    $i = 0;

    $priceFull = new Ivliev\Exell\priceExel(dirname(__FILE__).'/pricefull.xlsx');
    $priceFull->writeString(['Producer','Article','Name','1-day','2-day','4-day']);

    while ($rd = $stmt->fetch()){
        $rdis[$i]['artikle'] = trim($rd['artikle']);
        $rdis[$i]['producer'] = trim($rd['producer']);
        $rdis[$i]['presence'] = $rd['presence'];
        $i++;
    }

    $sql = "SELECT `producer`,`artikle`,ANY_VALUE(`name`) AS `name`,SUM(`presence`) AS `pres`,SUM(`additional`) AS `add` FROM `product` WHERE `id_seller` != (SELECT id FROM `purveyors` WHERE `name` LIKE 'Diselectro' ) GROUP BY `artikle`,`producer` ORDER BY `producer`,`artikle` ASC;";
    $stmt = $pdo->query($sql);
    while ($r = $stmt->fetch()) {
        $r['artikle'] = trim($r['artikle']);
        $r['producer'] = trim($r['producer']);
        $r['disel'] = 0;
        foreach ($rdis as $dis){
            if($r['artikle'] === $dis['artikle'] and $r['producer'] === $dis['producer']){
                $r['disel'] = $dis['presence'];
                break;
            }
        }
        $priceFull->writeString([$r['producer'],$r['artikle'],$r['name'],$r['disel'],$r['pres'],$r['add']]);

    }

//    require_once ("../mailer/phpmailer/PHPMailerAutoload.php");
//
//    $mail = new PHPMailer;
//    $mail->CharSet = 'utf-8';
//
//    $mail->isSMTP();                                      // Set mailer to use SMTP
//    $mail->Host = 'smtp.mail.ru';  // Specify main and backup SMTP servers
//    $mail->SMTPAuth = true;                               // Enable SMTP authentication
//    $mail->Username = 'ivldv@mail.ru';                 // Наш логин
//    $mail->Password = 'Rr1@fe5D2';                           // Наш пароль от ящика
//    $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
//    $mail->Port = 465;                                    // TCP port to connect to
//
//    $mail->setFrom('ivldv@mail.ru', 'Dmitriy');   // От кого письмо
//    $mail->addAddress('eltec.online@mail.ru');     // Add a recipient
//    $mail->addAttachment(dirname(__FILE__).'/pricefull.xlsx');         // Add attachments
//    $mail->isHTML(true);                                  // Set email format to HTML
//
//    $mail->Subject = 'Прайс по остаткам компании ООО Дисэлектро';
//    $mail->Body    = 'Прайс по остаткам компании ООО Дисэлектро';
//    $mail->AltBody = 'Это альтернативный текст';
//
//    if(!$mail->send()) {
//        echo 'Mail not send. ERROR';
//    } else {
//        echo 'Mail send';
//}


