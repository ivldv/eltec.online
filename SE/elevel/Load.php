<?php
ini_set('display_errors','On');
error_reporting('E_ALL');


require_once ('../model/database.php');
require 'vendor/autoload.php';

$filename = '19703_catalog.xlsx';
$pop3_server = 'pop.mail.ru';
$pop3_login = 'eltec.online@mail.ru';
$pop3_password = 'Cfvjtukfdyjt';

echo ' Подключился ,';

// Класс, непосредственно читающий файл
use PhpOffice\PhpSpreadsheet\IOFactory;

$inbox = imap_open('{'.$pop3_server.':995/novalidate-cert/pop3/ssl}INBOX', $pop3_login, $pop3_password);
$emails = imap_search($inbox,'ALL');

foreach($emails as $mail){
    $headers = imap_headerinfo($inbox, $mail);
    $from = imap_utf8 ( $headers->fromaddress );
    $subject = imap_utf8 ( $headers->Subject );
    if ($from == "Тех. Поддержка eWay <e.way@elevel.ru>") {
        $message = imap_fetchbody($inbox, $mail, 1.2);

        if (imap_base64($message))
            $message = imap_base64($message);
        else {
            $message = imap_fetchbody($inbox, $mail, 1);
            if (imap_base64($message))
                $message = imap_base64($message);
        }
        echo $message;
        $structure = imap_fetchstructure($inbox, $mail);

        if (is_array($structure->parts))
        {
            foreach ($structure->parts as $key => $part)
            {
                if ($part->ifdisposition && $part->disposition == "ATTACHMENT")
                {
                    if ($part->ifdparameters && is_array($part->dparameters)) {
                        foreach($part->dparameters as $object)
                        if(strtolower($object->attribute) == 'filename' && $object->value == "19703_catalog.xlsx")
                        {
							$content = imap_fetchbody($inbox, $mail, $key+1);
							if($part->encoding == 3)
								$content = base64_decode($content);
							elseif($part->encoding == 4)
								$content = quoted_printable_decode($content);
							file_put_contents($filename, $content);
							echo ('скачал');

							$spreadsheet = IOFactory::load($filename);
							echo ('загрузил');
							$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
							deleteProductByIdPurveyors('Elevel');
							foreach ($sheetData as $key => $value) {
								if ($key<3) continue;
								$cell['producer'] = $value['E'];
								$cell['artikle'] = substr ( $value['B'] , 0 ,60 ); //артикул
								$cell['name'] = $value['A']; //наименование
								$cell['presence'] = $value['G'];//количество
								$cell['additional'] = $value['H'];
								$cell['cost'] = $value['J'];
								echo ($cell['artikle'].'</br>');
								if ($cell['presence']  == 0 && $cell['additional'] == 0) continue;

								$s = insertElevelOfferNEW($cell);
							}
                        }
                    }
                }
            }
        }
    // imap_delete ( $inbox, $mail ); 
    imap_expunge ( $inbox );

    }
}





