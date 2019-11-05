<?php
error_reporting(-1); // показывать ВСЁ
ini_set("allow_url_fopen", true);
ini_set('display_errors', 'on');
 
$sendto   = "ivldv@mail.ru";
$usermail = "ivldv@mail.ru";
$username = "Vano";

// Формирование заголовка письма
$subject  = "Новое сообщение";
$headers  = "From: ivldv@mail.ru\r\n";
$headers .= "Reply-To: ivldv@mail.ru\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html;charset=utf-8 \r\n";
// Формирование тела письма
$msg  = "<html><body style='font-family:Arial,sans-serif;'>";
$msg .= "<h2 style='font-weight:bold;border-bottom:1px dotted #ccc;'>Новое сообщение</h2>\r\n";
$msg .= "<p><strong>Имя отправителя:</strong> ".$username."</p>\r\n";
$msg .= "<p><strong>E-mail отправителя:</strong> ".$usermail."</p>\r\n";
$msg .= "<p><strong>Дополнительные опции:</strong> </p>\r\n";
$msg .= "</body></html>";
 
// отправка сообщения
if(@mail($sendto, $subject, $msg, $headers)) {
    echo "true";
} else {
    echo "false";
}
 phpinfo();
?>