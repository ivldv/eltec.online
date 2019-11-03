<?php

//require_once $_SERVER['DOCUMENT_ROOT'].'/model/database.php';
require_once $_SERVER['DOCUMENT_ROOT']."/PhPExcel-1.8/Classes/PHPExcel/IOFactory.php"; // подключаем класс для доступа к файлу
echo ' Подключился ,';
class chunkReadFilter implements PHPExcel_Reader_IReadFilter 
{
    private $_startRow = 0; 
    private $_endRow = 0; 
    /**  Set the list of rows that we want to read  */ 
    public function setRows($startRow, $chunkSize) { 
        $this->_startRow    = $startRow; 
        $this->_endRow      = $startRow + $chunkSize; 
    } 
    public function readCell($column, $row, $worksheetName = '') { 
        //  Only read the heading row, and the rows that are configured in $this->_startRow and $this->_endRow 
        if (($row == 1) || ($row >= $this->_startRow && $row < $this->_endRow)) { 
            return true; 
        } 
        return false; 
    } 
}

$filename = '19703_catalog.xlsx';
$pop3_server = 'pop.mail.ru';
$pop3_login = 'eltec.online@mail.ru';
$pop3_password = 'Cfvjtukfdyjt';

// $inbox = imap_open('{'.$pop3_server.':995/novalidate-cert/pop3/ssl}INBOX', $pop3_login, $pop3_password);
// $emails = imap_search($inbox,'ALL');

// foreach($emails as $mail){
//     $headers = imap_headerinfo($inbox, $mail);
//     $from = imap_utf8 ( $headers->fromaddress );
//     $subject = imap_utf8 ( $headers->Subject );
//     if ($from == "Тех. Поддержка eWay <e.way@elevel.ru>") {
//         $message = imap_fetchbody($inbox, $mail, 1.2);

//         if (imap_base64($message))
//             $message = imap_base64($message);
//         else {
//             $message = imap_fetchbody($inbox, $mail, 1);
//             if (imap_base64($message))
//                 $message = imap_base64($message);
//         }
//         echo $message;
//         $structure = imap_fetchstructure($inbox, $mail);

//         if (is_array($structure->parts))
//         {
//             foreach ($structure->parts as $key => $part)
//             {
//                 if ($part->ifdisposition && $part->disposition == "ATTACHMENT")
//                 {
//                     if ($part->ifdparameters && is_array($part->dparameters)) {
//                         foreach($part->dparameters as $object)
//                         if(strtolower($object->attribute) == 'filename' && $object->value == "19703_catalog.xlsx")
//                         {
//                                 $content = imap_fetchbody($inbox, $mail, $key+1);
//                                 if($part->encoding == 3)
//                                     $content = base64_decode($content);
//                                 elseif($part->encoding == 4)
//                                     $content = quoted_printable_decode($content);
//                                     file_put_contents($filename, $content);

                                // $objPHPExcel = PHPExcel_IOFactory::load($filename);

                                // foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) // цикл обходит страницы файла
                                // {
                                //     $highestRow = $worksheet->getHighestRow(); // получаем количество строк
                                //     $highestColumn = $worksheet->getHighestColumn(); // а так можно получить количество колонок
                                    
                                //     for ($row = 2; $row <= 200; ++ $row) // обходим все строки
                                //     {
                                //         $cell['producer'] = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                                //         $cell['artikle'] = $worksheet->getCellByColumnAndRow(2, $row)->getValue(); //артикул
                                //         $cell['name'] = $worksheet->getCellByColumnAndRow(1, $row)->getValue(); //наименование
                                //         $cell['presence'] = $worksheet->getCellByColumnAndRow(7, $row)->getValue(); //количество
                                //         $cell['additional'] = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
                                //         if($cell['artikle']!='') {
                                //         }
                                //     }
                                // }

                                
                                $file =  $filename;
                                set_time_limit(1800);
                                ini_set('memory_liit', '128M');
                                /*	some vars	*/
                                $chunkSize = 2000;		//размер считываемых строк за раз
                                $startRow = 3;			//начинаем читать со строки 2, в PHPExcel первая строка имеет индекс 1, и как правило это строка заголовков
                                $exit = false;			//флаг выхода
                                $empty_value = 0;		//счетчик пустых знаений
                                /*	some vars	*/
                                if (!file_exists($file)) {
                                    exit();
                                }
                                
                                $objReader = PHPExcel_IOFactory::createReaderForFile($file);
                                $objReader->setReadDataOnly(true);
                                
                                $chunkFilter = new chunkReadFilter(); 
                                $objReader->setReadFilter($chunkFilter); 
                                //внешний цикл, пока файл не кончится
                                while ( !$exit ) 
                                {
                                    $chunkFilter->setRows($startRow,$chunkSize); 	//устанавливаем знаечние фильтра
                                    $objPHPExcel = $objReader->load($file);		//открываем файл
                                    $objPHPExcel->setActiveSheetIndex(0);		//устанавливаем индекс активной страницы
                                    $objWorksheet = $objPHPExcel->getActiveSheet();	//делаем активной нужную страницу
                                    for ($i = $startRow; $i < $startRow + $chunkSize; $i++) 	//внутренний цикл по строкам
                                    {

                                        $cell['producer'] = $objWorksheet->getCellByColumnAndRow(4, $i)->getValue();
                                        $cell['artikle'] = $objWorksheet->getCellByColumnAndRow(2, $i)->getValue(); //артикул
                                        $cell['name'] = $objWorksheet->getCellByColumnAndRow(0, $i)->getValue(); //наименование
                                        $cell['presence'] = $objWorksheet->getCellByColumnAndRow(6, $i)->getValue(); //количество
                                        $cell['additional'] = $objWorksheet->getCellByColumnAndRow(7, $i)->getValue();


                                        $value = trim(htmlspecialchars($objWorksheet->getCellByColumnAndRow(0, $i)->getValue()));		//получаем первое знаение в строке
                                        if ( empty($value) )		//проверяем значение на пустоту
                                            $empty_value++;			
                                        if ($empty_value == 6)		//после трех пустых значений, завершаем обработку файла, думая, что это конец
                                        {	
                                            $exit = true;	
                                            continue;		
                                        }	
                                        /*Манипуляции с данными каким Вам угодно способом, в PHPExcel их превеликое множество*/
                                    }
                                    $objPHPExcel->disconnectWorksheets(); 				//чистим 
                                    unset($objPHPExcel); 						//память
                                    $startRow += $chunkSize;					//переходим на следующий шаг цикла, увеличивая строку, с которой будем читать файл
                                }                               
                                        echo 'все';
    //                     }
    //                 }
    //             }
    //         }
    //     }
    // imap_delete ( $inbox, $mail ); 
    // imap_expunge ( $inbox );

    // }
//}
