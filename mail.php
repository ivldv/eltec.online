
<?php 
$phone = $_POST['phone'];
$name = $_POST['name'];
$email = $_POST['email'];
$region = $_POST['region'];
require_once "recapcha.php";
require_once ('mailer/phpmailer/PHPMailerAutoload.php');
$secret = "6Lc0XHQUAAAAAK9CLVCi_LIzg90swYnK25C-yS3x";
$responce = null;
// проверка секретного ключа
$reCaptcha = new ReCaptcha($secret);
// if submitted check response
echo ' проверка ключа пройдена \r\n';
if ($_POST["g-recaptcha-response"]) {
    $response = $reCaptcha->verifyResponse(
            $_SERVER["REMOTE_ADDR"],
            $_POST["g-recaptcha-response"]
        );
    }
	
if ($response != null && $response->success) {
	
        $mail = new PHPMailer;
        $mail->CharSet = 'utf-8';
    
        //$mail->SMTPDebug = 3;                               // Enable verbose debug output
        
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.mail.ru';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'ivldv@mail.ru';                 // Наш логин
        $mail->Password = 'Rr1@fe5D2';                           // Наш пароль от ящика
        $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 465;                                    // TCP port to connect to
         
        $mail->setFrom('ivldv@mail.ru', 'dmitriy');   // От кого письмо 
        $mail->addAddress('eltec.online@yandex.ru');     // Add a recipient
        $mail->addAddress('ivldv@mail.ru');               // Name is optional
        //$mail->addReplyTo('info@example.com', 'Information');
        //$mail->addCC('cc@example.com');
        //$mail->addBCC('bcc@example.com');
        //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        $mail->isHTML(true);                                  // Set email format to HTML
    
        $mail->Subject = 'Это тема сообщения';
        $mail->Body    = 'Пользователь оставил свои данные <br>Телефон: ' . $phone .'<br>Email: ' . $email . '<br>Имя: ' . $name . '<br>Регион: '.$region ;

        $mail->AltBody = 'Это альтернативный текст';
//**************************************************************
$subject  = "Новое сообщение";
$headers  = "From: ivldv@mail.ru\r\n";
$headers .= "Reply-To: ivldv@mail.ru\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html;charset=utf-8 \r\n";
$msg  = "<html><body style='font-family:Arial,sans-serif;'>";
$msg .= "<h2 style='font-weight:bold;border-bottom:1px dotted #ccc;'>Новое сообщение</h2>\r\n";
$msg .= "<p><strong>Имя отправителя:</strong> ".$name."</p>\r\n";
$msg .= "<p><strong>Телефон:</strong> ".$phone."</p>\r\n";
$msg .= "<p><strong>E-mail отправителя:</strong> ".$email."</p>\r\n";
$msg .= "<p><strong>Регион отправителя:</strong> ".$region."</p>\r\n";
$msg .= "</body></html>";

		@mail('ivldv@mail.ru',$subject,$msg,$headers);
//**************************************************************		
        if(!$mail->send()) {
			echo"false";
            return false;
        } else {
			echo"true";
           return true;
        }
        
      } else {  
		echo"false1";
        return false;
      }
?>