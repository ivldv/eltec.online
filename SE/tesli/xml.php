<?

function webi_xml($file)
{

    ####################################################
    ### функция работы с данными
    function data ($parser, $data)
    {
        echo 'data: ';
        print $data;
        echo '<br>';

    }
    ############################################


    ####################################################
    ### функция открывающих тегов
    function startElement($parser, $name, $attrs)
    {
        if ($name ==='CATEGORIES'){
            $flag = 1;
        }
        echo 'start: ';
        print $name;
        print_r($attrs);
        echo '<br>';
    }
    ###############################################


    #################################################
    ## функция закрывающих тегов
    function endElement($parser, $name)
    {
        echo 'end: ';
        print $name;
        echo '<br>';

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

}

webi_xml('catalog.xml');

?>