<?php
require_once '../vendor/autoload.php';
//include 'src/Email/resiveMail.php';

    $serv = new \Ivliev\service\sentToServer();
    $email = new \Ivliev\Email\resiveMail();

    $filename = $email->reciveAttachment();
    unset($email);

    $zip = new ZipArchive();
    $zip->open($filename);
    $sheet_xml = simplexml_load_string($zip->getFromName('xl/worksheets/sheet1.xml'));
    //$sheet_array = json_decode(json_encode($sheet_xml->sheetData), true);
    $values = simplexml_load_string($zip->getFromName('xl/sharedStrings.xml'));
    $values_array = json_decode(json_encode($values), true);
    unset($values);
    echo ($serv->send('delete','Elevel'));

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
                    $sql .= "(7, '$end_result[4]', '$artikle', '$name', '$end_result[6]', $end_result[7], '$end_result[9]')";
                    if ($r%50 === 0){
                        $sql .= ";";
                        echo ($serv->send('insert',$sql));
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
            echo ($serv->send('insert',$sql));
        }
//    imap_delete ( $inbox, $mail );
//    imap_expunge ( $inbox );
    }










