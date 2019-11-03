<?php 
$phone = $_POST['phone'];
$name = $_POST['name'];
$mail = $_POST['email'];
$region = $_POST['region'];
require_once "recaptchalib.php";
$secret = "6Lc0XHQUAAAAAK9CLVCi_LIzg90swYnK25C-yS3x";
$responce = null;
// проверка секретного ключа
$reCaptcha = new ReCaptcha($secret);
// if submitted check response
if ($_POST["g-recaptcha-response"]) {
    $response = $reCaptcha->verifyResponse(
            $_SERVER["REMOTE_ADDR"],
            $_POST["g-recaptcha-response"]
        );
    }
    var_dump($responce);
/*if ($response != null && $response->success) {
        require_once ("/mailer/phpmailer/PHPMailerAutoload.php");
        $mail = new PHPMailer;
        $mail->CharSet = 'utf-8';
        echo "Hi " . $_POST["name"] . " (" . $_POST["email"] . "), thanks for submitting the form!";
       
        //$mail->SMTPDebug = 3;                               // Enable verbose debug output
        
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.mail.ru';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'ivldv@mail.ru';                 // Наш логин
        $mail->Password = 'Rr1@fe5D2';                           // Наш пароль от ящика
        $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 465;                                    // TCP port to connect to
         
        $mail->setFrom('ivldv@mail.ru', 'dmitriy');   // От кого письмо 
        $mail->addAddress('ivldv@mail.ru');     // Add a recipient
        //$mail->addAddress('ellen@example.com');               // Name is optional
        //$mail->addReplyTo('info@example.com', 'Information');
        //$mail->addCC('cc@example.com');
        //$mail->addBCC('bcc@example.com');
        //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        $mail->isHTML(true);                                  // Set email format to HTML
        
        $mail->Subject = 'Это тема сообщения';
        $mail->Body    = '
            Пользователь оставил свои данные <br> 
            Телефон: ' . $phone . '<br>
            Имя: ' . $name . '
            Регион: '.$region ;
        $mail->AltBody = 'Это альтернативный текст';
        
        if(!$mail->send()) {
            return false;
        } else {
            return true;
        }
        
      } else {   
        return true;
      }
?>