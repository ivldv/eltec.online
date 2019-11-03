<?php
 $bbb =  explode ( '/' , __DIR__);
 $a= array_pop ($bbb);
 $filecron = implode ( '/', $bbb );

 require_once ('../model/database.php');
$filename = "stock.csv";
$pop3_server = 'pop.mail.ru';
$pop3_login = 'eltec.online@mail.ru';
$pop3_password = 'Cfvjtukfdyjt';

echo ' Подключился ,';

$inbox = imap_open('{'.$pop3_server.':995/novalidate-cert/pop3/ssl}INBOX', $pop3_login, $pop3_password);
$emails = imap_search($inbox,'ALL');

foreach($emails as $mail){
    $headers = imap_headerinfo($inbox, $mail);
    $from = imap_utf8 ( $headers->fromaddress );
    $subject = imap_utf8 ( $headers->Subject );
    if ($from == "Irina Vysotskaya <irina.vysotskaya@danfoss.com>") {
        $message = imap_fetchbody($inbox, $mail, 1.2);

        if (imap_base64($message))
            $message = imap_base64($message);
        else {
            $message = imap_fetchbody($inbox, $mail, 1);
            if (imap_base64($message))
                $message = imap_base64($message);
        }
//        echo $message;
        $structure = imap_fetchstructure($inbox, $mail);

        if (is_array($structure->parts))
        {
            foreach ($structure->parts as $key => $part)
            {
                if ($part->ifdisposition && $part->disposition == "ATTACHMENT")
                {
                    if ($part->ifdparameters && is_array($part->dparameters)) {
                        foreach($part->dparameters as $object)
                        if(strtolower($object->attribute) == 'filename' && $object->value == "stock.csv")
                        {
                                $content = imap_fetchbody($inbox, $mail, $key+1);
                                if($part->encoding == 3)
                                    $content = base64_decode($content);
                                elseif($part->encoding == 4)
                                    $content = quoted_printable_decode($content);
                                    file_put_contents($filename, $content);

                                    $handle = fopen($filename, "r");
                                    $buffer = fgets($handle, 4096);
                                    deleteProductByIdPurveyors('Danfoss');
                                    $onlyone = 2;
                                    while (!feof($handle)) {
                                        $buffer = iconv("windows-1251", "utf-8", fgets($handle, 4096));
                                        if(empty($buffer) && $onlyone != 0){
                                            $onlyone = $onlyone-1;
                                            continue;
                                        }
                                        $str = str_replace('"', '', $buffer);
                                        $arr = [];
                                        $arr = explode (';', $str);
                                        if ($arr[3]==0) continue;
                                        $cell['artikle']= $arr[2];
                                        $cell['name'] = $arr[0];
                                        $cell['presence'] = (int)$arr[3];
                                        $cell['producer'] = 'Danfoss';
                            
                                        //"artikle","name","presence",'multiplicity','packaging','cost','producer'
                                        insertProduct('Danfoss',$cell);
                                        //echo $arr[0].'</br>';
                                    }
                                                                // }
                        }
                    }
                }
            }
        }
    imap_delete ( $inbox, $mail ); 
    imap_expunge ( $inbox );
    echo '--> Готово';
    }
}








