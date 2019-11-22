<?php

namespace Ivliev\validation;

class validation
{
    public static function validationProducer($producer)
    {
       if(($producer === 'LEGRAND')||($producer === 'legrand'))
       {
           $producer = 'Legrand' ;
       }
        if(($producer === 'Schneider')||($producer === 'DEKraft')||($producer==='Шнейдер Электрик'))
        {
            $producer = 'Schneider Electric' ;
        }
        if($producer === 'DKC')
        {
            $producer = 'ДКС' ;
        }

        return $producer;
    }

}