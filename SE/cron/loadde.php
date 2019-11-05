<?php
$bbb =  explode ( '/' , __DIR__);
$a= array_pop ($bbb);
$filecron = implode ( '/', $bbb );
require_once ($filecron.'/model/database.php');

$pop3_server = 'pop.mail.ru';
$pop3_login = 'eltec.online@mail.ru';
$pop3_password = 'Cfvjtukfdyjt';

$inbox = imap_open('{'.$pop3_server.':995/novalidate-cert/pop3/ssl}INBOX', $pop3_login, $pop3_password);
$emails = imap_search($inbox,'ALL');
foreach($emails as $mail){
    $headers = imap_headerinfo($inbox, $mail);
    $from = imap_utf8 ( $headers->fromaddress );
    $subject = imap_utf8 ( $headers->Subject );
    if ($from == "info@ge-el.ru") {
        $message = imap_fetchbody($inbox, $mail, 1.2);

        if (imap_base64($message))
            $message = imap_base64($message);
        else {
            $message = imap_fetchbody($inbox, $mail, 1);
            if (imap_base64($message))
                $message = imap_base64($message);
        }
        echo $message;
		deleteProductByIdPurveyors('DE');
        $structure = imap_fetchstructure($inbox, $mail);

        if (is_array($structure->parts))
        {
            foreach ($structure->parts as $key => $part)
            {
                if ($part->ifdisposition && $part->disposition == "ATTACHMENT")
                {
                    if ($part->ifdparameters && is_array($part->dparameters)) {
                        foreach($part->dparameters as $object)
                            if(strtolower($object->attribute) == 'filename' && $object->value == "stock-all-json.json")
                            {
                                $content = imap_fetchbody($inbox, $mail, $key+1);
                                if($part->encoding == 3)
                                    $content = base64_decode($content);
                                elseif($part->encoding == 4)
                                    $content = quoted_printable_decode($content);
                                $stock = json_decode ( $content);
                                foreach ($stock as $key1 => $element) {
                                   if ($key1 == 0) continue;
  //                                 if ( !empty($element[4]) && !empty($element[5]) ) {
                                        $cell['producer']= $element[0];
										if($cell['producer'] == 'LEGRAND') $cell['producer'] = 'Legrand';
                                        $cell['artikle']= $element[1];
                                        $cell['name'] = $element[3];
                                        $cell['presence'] = $element[4]+$element[5];
                            
                                        //"artikle","name","presence",'multiplicity','packaging','cost','producer'
                                        insertProduct('DE',$cell);
                                                              
//                                   }
                                   $a=$a;
                                }
                            }
                    }
                }
            }
        }
		imap_delete ( $inbox, $mail ); 
        imap_expunge ( $inbox );

    }
}