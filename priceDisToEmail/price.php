<?php
require_once ('../SE/model/database.php');
    $rezult = 'Producer;Artikle;Name;Presence;Cost;Time update'.PHP_EOL;
    $sql = "SELECT * FROM $tableProduct where `id_seller` = (SELECT id FROM `purveyors` WHERE `name` LIKE 'Diselectro' );";
    $stmt = $pdo->query($sql);
    while ($r = $stmt->fetch()) {
        unset($r['id_seller']);
        unset($r['id']);
        unset($r['additional']);
        unset($r['multiplicity']);
        unset($r['packaging']);
        $r['name']=mb_convert_encoding($r['name'],"Windows-1251","UTF-8");
        var_dump($r);
        $rezult .= "'".implode("';'",$r)."'".PHP_EOL;
    }
//    echo $rezult;
    file_put_contents('result.csv',$rezult);

    require_once ("../mailer/phpmailer/PHPMailerAutoload.php");

    $mail = new PHPMailer;
    //var_dump($mail);
    $mail->CharSet = 'utf-8';

    //$mail->SMTPDebug = 3;                               // Enable verbose debug output

    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.mail.ru';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'ivldv@mail.ru';                 // Наш логин
    $mail->Password = 'Rr1@fe5D2';                           // Наш пароль от ящика
    $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 465;                                    // TCP port to connect to

    $mail->setFrom('ivldv@mail.ru', 'Dmitriy');   // От кого письмо
    $mail->addAddress('eltec.online@mail.ru');     // Add a recipient
    //$mail->addAddress('ellen@example.com');               // Name is optional
    //$mail->addReplyTo('info@example.com', 'Information');
    //$mail->addCC('cc@example.com');
    //$mail->addBCC('bcc@example.com');
    $mail->addAttachment('result.csv');         // Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
    $mail->isHTML(true);                                  // Set email format to HTML

    $mail->Subject = 'Прайс по остаткам компании ООО Дисэлектро';
    $mail->Body    = 'Прайс по остаткам компании ООО Дисэлектро';
    $mail->AltBody = 'Это альтернативный текст';

    if(!$mail->send()) {
        echo 'Mail not send. ERROR';
    } else {
        echo 'Mail send';
}


