<?php

$url = 'http://eltec.online.local/connect/connect.php';

Function send($url,$comand,$data){
    $params = array(
        'login' => 'WQP',
        'password' => 'qwerty',
        'comand' => $comand,
        'data' => $data
    );
    $result = file_get_contents($url, false, stream_context_create(array(
        'http' => array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => http_build_query($params)
        )
    )));
    return ($result);

}

$filename = '19703_catalog.xlsx';
$pop3_server = 'pop.mail.ru';
$pop3_login = 'eltec.online@mail.ru';
$pop3_password = 'Cfvjtukfdyjt';

echo ' Подключился ,';

$inbox = imap_open('{'.$pop3_server.':995/novalidate-cert/pop3/ssl}INBOX', $pop3_login, $pop3_password);
//$emails = imap_search($inbox,'ALL');
$dddd = date('d F Y');
$emails = imap_search($inbox,"FROM \"Тех. Поддержка eWay <e.way@elevel.ru>\" ON \"$dddd\" ");

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

                            $zip = new ZipArchive();
                            $zip->open($filename);
                            $sheet_xml = simplexml_load_string($zip->getFromName('xl/worksheets/sheet1.xml'));
                            //$sheet_array = json_decode(json_encode($sheet_xml->sheetData), true);
                            $values = simplexml_load_string($zip->getFromName('xl/sharedStrings.xml'));
                            $values_array = json_decode(json_encode($values), true);
                            unset($values);
                            echo (send($url,'delete','Elevel'));
                            $sql = "INSERT INTO `product` (`id_seller`, `producer`, `artikle`, `name`, `presence`, `additional`, `cost`) VALUES ";
                            
                            if ($sheet_xml->sheetData) {
                                $r = 0;
                                foreach ($sheet_xml->sheetData->row as $row) {
                                    $end_result = array();
                                    foreach ($row->c as $c => $cell) {
                                        if (isset($cell['t'])) {
                                            if ((string)$cell['t'] == 's') {
                                                $end_result[] = $values_array['si'][(int)$cell->v]['t'];
                                            } else if ((string)$cell['t'] == 'e') {
                                                $end_result[] = '';
                                            } else if ((string)$cell['t'] == 'n') {
                                                $end_result[] = (int)$cell->v;
                                            }
                                        } else {
                                            $end_result[] = $cell->v;
                                        }
                                    }
                                    if ($r>=2) {
                                        if ($end_result[6]  == 0 && $end_result[7] == 0) continue;
                                            $artikle = substr ( $end_result[1] , 0 ,250 ); //артикул
                                            $name = addslashes ($end_result[0]);
                                            $producer = $end_result[4];
                                            if($producer == 'IEK (ИЭК)') $producer = 'IEK';
                                            $sql .= "(7, '$producer', '$artikle', '$name', '$end_result[6]', $end_result[7], '$end_result[9]')";
                                            if ($r%50 === 0){
                                                $sql .= ";";
                                                echo (send($url,'insert',$sql));
                                                $sql = "INSERT INTO `product` (`id_seller`, `producer`, `artikle`, `name`, `presence`, `additional`, `cost`) VALUES ";
                                            }else{
                                                $sql .= ",";
                                            }
                                    }
                                $r++;
                                }
                                if($r%50 != 0){
                                    $sql = substr($sql,0,-1);
                                    $sql .= ";";
                                    echo (send($url,'insert',$sql));
                                }
                            } 
                        }                       
                    }
                }
            }
        }
//    imap_delete ( $inbox, $mail );
    imap_expunge ( $inbox );

    }
}










