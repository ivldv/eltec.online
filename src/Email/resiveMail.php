<?php


namespace Ivliev\Email;


class resiveMail
{
    private $pop3_server;
    private $pop3_login;
    private $pop3_password;
    private $inbox;

    public function __construct($serverName = '',$login = '',$password = '')
    {
        if($serverName ===''){
            $this->pop3_server = 'pop.mail.ru';
            $this->pop3_login = 'eltec.online@mail.ru';
            $this->pop3_password = 'Cfvjtukfdyjt';
        }else{
            $this->pop3_server = $serverName;
            $this->pop3_login = $login;
            $this->pop3_password = $password;
        }
        $this->inbox = imap_open('{'.$this->pop3_server.':995/novalidate-cert/pop3/ssl}INBOX', $this->pop3_login, $this->pop3_password);
    }
    public function __destruct()
    {
        //    imap_delete ( $this->inbox, $mail );
        imap_expunge ( $this->inbox );

    }

    public function reciveAttachment($from = '',$date = '')
    {
        if($date ==='') $date = date('d F Y');

        if($from === '') $from = "FROM \"Тех. Поддержка eWay <e.way@elevel.ru>\" ON \"$date\" NEW";
        $emails = imap_search($this->inbox,$from);
        if (is_null($emails)) return (null);

        $message = imap_fetchbody($this->inbox, $emails[0], 1.2);

        if (imap_base64($message))
            $message = imap_base64($message);
        else {
            $message = imap_fetchbody($this->inbox, $emails[0], 1);
            if (imap_base64($message))
                $message = imap_base64($message);
        }
        echo $message;
        $structure = imap_fetchstructure($this->inbox, $emails[0]);

        if (is_array($structure->parts)) {
            foreach ($structure->parts as $key => $part)
            {
                if ($part->ifdisposition && $part->disposition == "ATTACHMENT")
                {
                    if ($part->ifdparameters && is_array($part->dparameters))
                    {
                        foreach ($part->dparameters as $object)
                        {
                            if (strtolower($object->attribute) == 'filename' )
                            {
                                $filename = $object->value;
                                $content = imap_fetchbody($this->inbox, $emails[0], $key + 1);
                                if ($part->encoding == 3)
                                    $content = base64_decode($content);
                                elseif ($part->encoding == 4)
                                    $content = quoted_printable_decode($content);
                                file_put_contents($filename, $content);
                                return $filename;
                            }
                        }
                    }
                }
            }
        }
        return null;
    }
}