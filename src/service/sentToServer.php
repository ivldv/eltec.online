<?php


namespace Ivliev\service;


class sentToServer
{
    private $url;

    public function  __construct($url = null)
    {
        if (is_null($url)) $this->url = 'http://eltec.online/connect/connect.php';
        else $this->url = $url;
    }


    public function send($comand,$data)
    {
        $params = array(
            'login' => 'WQP',
            'password' => 'qwerty',
            'comand' => $comand,
            'data' => $data
        );
        $result = file_get_contents($this->url, false, stream_context_create(array(
            'http' => array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query($params)
            )
        )));
        return ($result);
    }
}