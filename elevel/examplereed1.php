<?
require 'vendor/autoload.php';
// Класс, непосредственно читающий файл
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
$inputFileType = 'Xlsx';
$file = '19703_catalog.xlsx'; 
// ...
// Создаём ридер 
$reader = new Xlsx();
// Если вы не знаете, какой будет формат файла, можно сделать ридер универсальным:
// $reader = IOFactory::createReaderForFile($file);
// $reader->setReadDataOnly(true);
    $spreadsheet = $reader->load($file);
    $cells = $spreadsheet->getActiveSheet()->getCellCollection();
    //Далее перебираем все заполненные строки (столбцы B - O)
    for ($row = 10; $row <= 100; $row++){
        // for ($col = 'B'; $col <= 'O'; $col++) {
            // Так можно получить значение конкретной ячейки
            echo ($cells->get('A'.$row)->getValue().'</br');
            // а также здесь можно поместить ваш функциональный код
        // }
    }            
 
// Если вы хотите установить строки и столбцы, которые необходимо читать, создайте класс ReadFilter
//$reader->setReadFilter( new MyReadFilter(11, 1000, range('B', 'O')) );
// $reader->setReadDataOnly(true);
// Читаем файл и записываем информацию в переменную
// $spreadsheet = $reader->load($file);
 
// // Так можно достать объект Cells, имеющий доступ к содержимому ячеек
// $cells = $spreadsheet->getActiveSheet()->getCellCollection();
// // $maxRow = $cells->getHighestRow();  
// // var_dump($maxRow); 
// // die();      
// //Далее перебираем все заполненные строки (столбцы B - O)
// for ($row = 10; $row <= 100; $row++){
//     // for ($col = 'B'; $col <= 'O'; $col++) {
//         // Так можно получить значение конкретной ячейки
//         echo ($cells->get('A'.$row)->getValue().'</br');
 
//         // а также здесь можно поместить ваш функциональный код
//     // }
// }            
 
return true;


// $inputFileType = 'Xls';
// $inputFileName = './sampleData/example2.xls';

// /**  Define a Read Filter class implementing \PhpOffice\PhpSpreadsheet\Reader\IReadFilter  */
// class ChunkReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
// {
//     private $startRow = 0;
//     private $endRow   = 0;

//     /**  Set the list of rows that we want to read  */
//     public function setRows($startRow, $chunkSize) {
//         $this->startRow = $startRow;
//         $this->endRow   = $startRow + $chunkSize;
//     }

//     public function readCell($column, $row, $worksheetName = '') {
//         //  Only read the heading row, and the configured rows
//         if (($row == 1) || ($row >= $this->startRow && $row < $this->endRow)) {
//             return true;
//         }
//         return false;
//     }
// }

// /**  Create a new Reader of the type defined in $inputFileType  **/
// $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);

// /**  Define how many rows we want to read for each "chunk"  **/
// $chunkSize = 2048;
// /**  Create a new Instance of our Read Filter  **/
// $chunkFilter = new ChunkReadFilter();

// /**  Tell the Reader that we want to use the Read Filter  **/
// $reader->setReadFilter($chunkFilter);

// /**  Loop to read our worksheet in "chunk size" blocks  **/
// for ($startRow = 2; $startRow <= 65536; $startRow += $chunkSize) {
//     /**  Tell the Read Filter which rows we want this iteration  **/
//     $chunkFilter->setRows($startRow,$chunkSize);
//     /**  Load only the rows that match our filter  **/
//     $spreadsheet = $reader->load($inputFileName);
//     //    Do some processing here
// }