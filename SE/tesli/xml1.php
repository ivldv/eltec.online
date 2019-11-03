<?
echo ($_SERVER['DOCUMENT_ROOT'].'/SE/model/database.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/SE/model/database.php');
function webi_xml($file)
{
    global $webi_depth;       // счетчик, для отслеживания глубины вложенности
    $webi_depth = 0;
    global $webi_tag_open;    // будет содержать массив открытых в данный момент тегов
    $webi_tag_open= array();
    global $webi_data_temp;   // этот массив будет содержать данные одного тега
    $offer = array();
    global $offer;
    $param = array();
    global $param;
    $img = array();
    global $img;


    ####################################################
    ### функция работы с данными
    function data ($parser, $data)
    {
        global $webi_depth;
        global $webi_tag_open;
        global $webi_data_temp;
        // добавляем данные в массив с указанием вложенности и открытого в данный момент тега
        $webi_data_temp[$webi_depth][$webi_tag_open[$webi_depth]]['data'].=$data;
    }
    ############################################


    ####################################################
    ### функция открывающих тегов
    function startElement($parser, $name, $attrs)
    {
        global $webi_depth;
        global $webi_tag_open;
        global $webi_data_temp;
        global $offer;
        global $param;
        global $img;
        
        // если уровень вложенности уже не нулевой, значит один тег уже открыт
        // и данные из него уже в массиве, можно их обработать
        switch ($webi_tag_open[$webi_depth]) {
            case "OFFER":
                if (!empty($webi_data_temp[$webi_depth][$webi_tag_open[$webi_depth]]['attrs']['ID']))
                {
                    $offer['id'] = $webi_data_temp[$webi_depth][$webi_tag_open[$webi_depth]]['attrs']['ID'];
                }
                break;
            case "OFFERS":
                if(is_null($webi_data_temp))
                {
                    // сохраняем offer в бд
                    insertTesliCatalog($param,$img,$offer);
                    unset($GLOBALS['offer'],$GLOBALS['param'],$GLOBALS['img']);
                }
                break;
            case "CATEGORIES":
            case "PARAMS":
            case 'YML_CATALOG':
            case 'SHOP':
            case 'CURRENCIES':
            case 'CATEGORIESIDS':
            case 'IMAGES':
                break;
           
            default:
                echo 'Start '.$webi_tag_open[$webi_depth].'--->'.$name.' <br>';
                print '<hr>';
                break;
        }
            // здесь начинается обработка данных, например добаление в базу, сохранение в файл и т.д.
            // $webi_tag_open содержит цепочку открытых тегов по уровню вложенности
            // например $webi_tag_open[$webi_depth] содержит название открытого тега чья информация сейчас обрабатывается
            // $webi_depth уровень вложенности тега
            // $webi_data_temp[$webi_depth][$webi_tag_open[$webi_depth]]['attrs'] массив атрибутов тега
            // $webi_data_temp[$webi_depth][$webi_tag_open[$webi_depth]]['data'] данные тега

            // print 'данные '.$webi_tag_open[$webi_depth].'--'.($webi_data_temp[$webi_depth][$webi_tag_open[$webi_depth]]['data']).'<br>';
            // print_r($webi_data_temp[$webi_depth][$webi_tag_open[$webi_depth]]['attrs']);
            // print '<br>';
            // print_r($webi_tag_open); // массив открытых тегов
            // var_dump($webi_tag_open);

            // после обработки данных удаляем их для освобождения памяти
            unset($GLOBALS['webi_data_temp'][$webi_depth]);
        // теперь пошло открытие следующего тега и дальше обработка его произойдет на следующем шаге
        $webi_depth++; // увеличиваем вложенность

        $webi_tag_open[$webi_depth]=$name; // добавляем открытый тег в массив информаци
        $webi_data_temp[$webi_depth][$name]['attrs']=$attrs; // теперь добавляем атрибуты тега

    }
    ###############################################



    #################################################
    ## функция закрывающих тегов
    function endElement($parser, $name)
    {
        global $webi_depth;
        global $webi_tag_open;
        global $webi_data_temp;
        global $offer;
        global $param;
        global $img;

        // echo 'endElement '.$webi_tag_open[$webi_depth].' <br>';
        switch ($webi_tag_open[$webi_depth]) {
            case 'CATEGORY':
                $category['name'] = $webi_data_temp[$webi_depth][$webi_tag_open[$webi_depth]]['data'];
                $category['id'] = $webi_data_temp[$webi_depth][$webi_tag_open[$webi_depth]]['attrs']['ID'];
                $category['parentid'] = $webi_data_temp[$webi_depth][$webi_tag_open[$webi_depth]]['attrs']['PARENTID'];

                insertTesliCategory($category);

                break;
            case 'URL':
            case 'CATEGORYID':
            case 'MODEL':
            case 'MAINIMAGE':
            case 'STEP_COUNT':
            case 'PRODUCER':
            case 'SERIES':
            case 'DISCOUNTCODE':
                $offer[strtolower($webi_tag_open[$webi_depth])] = $webi_data_temp[$webi_depth][$webi_tag_open[$webi_depth]]['data'];
                break;
            case 'VENDORСODE':
                $offer['vendorcode'] = $webi_data_temp[$webi_depth][$webi_tag_open[$webi_depth]]['data'];
                break;
            case 'PARAM':
                $prom['id'] = $webi_data_temp[$webi_depth][$webi_tag_open[$webi_depth]]['attrs']['ID'];
                $prom['name'] = $webi_data_temp[$webi_depth][$webi_tag_open[$webi_depth]]['attrs']['NAME'];
                $prom['data'] = $webi_data_temp[$webi_depth][$webi_tag_open[$webi_depth]]['data'];
                if ($prom['id']!='STEP_COUNT') {
                    $param[] = $prom;
                }
                break;
            case 'IMAGE':
                $img[]=$webi_data_temp[$webi_depth][$webi_tag_open[$webi_depth]]['data'];
                break;

            case 'CATEGORIESIDS':
            case 'PARAMS':
            case 'OFFER':
            case 'PARAMS':
            case 'CURRENCIES':
            case 'IMAGES':
            case 'CATEGORIES':
            case 'CURRENCY':

                break;

            default:
                echo 'endElement '.$webi_tag_open[$webi_depth].' <br>';
                print '<hr>';

                // здесь начинается обработка данных, например добаление в базу, сохранение в файл и т.д.
                // $webi_tag_open содержит цепочку открытых тегов по уровню вложенности
                // например $webi_tag_open[$webi_depth] содержит название открытого тега чья информация сейчас обрабатывается
                // $webi_depth уровень вложенности тега
                // $webi_data_temp[$webi_depth][$webi_tag_open[$webi_depth]]['attrs'] массив атрибутов тега
                // $webi_data_temp[$webi_depth][$webi_tag_open[$webi_depth]]['data'] данные тега

                // print 'данные '.$webi_tag_open[$webi_depth].'--'.($webi_data_temp[$webi_depth][$webi_tag_open[$webi_depth]]['data']).'<br>';
                // print_r($webi_data_temp[$webi_depth][$webi_tag_open[$webi_depth]]['attrs']);
                // print '<br>';
                // print_r($webi_tag_open);
                break;
        }
        unset($GLOBALS['webi_data_temp']); // после обработки данных удаляем массив с данными целиком, так как произошло закрытие тега
        unset($GLOBALS['webi_tag_open'][$webi_depth]); // удаляем информацию об этом открытом теге... так как он закрылся

        $webi_depth--; // уменьшаем вложенность
    }
    ############################################


    $xml_parser = xml_parser_create();
    xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, true);

    // указываем какие функции будут работать при открытии и закрытии тегов
    xml_set_element_handler($xml_parser, "startElement", "endElement");

    // указываем функцию для работы с данными
    xml_set_character_data_handler($xml_parser,"data");


    // открываем файл
    $fp = fopen($file, "r");


    $perviy_vxod=1; // флаг для проверки первого входа в файл
    $data="";  // сюда собираем частями данные из файла и отправляем в разборщик xml

    // цикл пока не найден конец файла
    while (!feof ($fp) and $fp)
    {
        $simvol = fgetc($fp); // читаем один символ из файла
        $data.=$simvol; // добавляем этот символ к данным для отправки

        // если символ не завершающий тег, то вернемся к началу цикла и добавим еще один символ к данным, и так до тех пор, пока не будет найден закрывающий тег
        if($simvol!='>') { continue;}
        // если закрывающий тег был найден, теперь отправим эти собранные данные в обработку

        // проверяем, если это первый вход в файл, то удалим все, что находится до тега <?
        // так как иногда может встретиться мусор до начала XML (корявые редакторы, либо файл получен скриптом с другого сервера)
        if($perviy_vxod) {$data=strstr($data, '<?'); $perviy_vxod=0;}


        // теперь кидаем данные в разборщик xml
        if (!xml_parse($xml_parser, $data, feof($fp))) {

            // здесь можно обработать и получить ошибки на валидность...
            // как только встретится ошибка, разбор прекращается
            echo "<br>XML Error: ".xml_error_string(xml_get_error_code($xml_parser));
            echo " at line ".xml_get_current_line_number($xml_parser);
            break;
        }

        // после разбора скидываем собранные данные для следующего шага цикла.
        $data="";
    }
    fclose($fp);
    xml_parser_free($xml_parser);
    // удаление глобальных переменных
    unset($GLOBALS['webi_depth']);  
    unset($GLOBALS['webi_tag_open']); 
    unset($GLOBALS['webi_data_temp']); 

}
echo 'начали ';
set_time_limit(50000);
echo 'начали ';
if(tableExists('teslioffers')===false){
    createTesli();
echo 'Создали таблицы ';
}else{
    clearTesli();
};

webi_xml('catalog.xml');

?>