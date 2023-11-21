<?php

namespace Crawler\Database;

class MailIndexer
{
    private $ins;
    private $check;
    private $con;

    public function __construct()
    {
        $this->ins = $this->con->prepare("INSERT INTO mails(email,type) VALUES(:mail,:type)");
        $this->check = $this->con->prepare("SELECT * FROM mails WHERE mail = :mail");
    }

    private function mail_exist($mail)
    {
        $this->check->bindParam(':mail', $mail);
        $this->check->execute();
        return $this->check->rowCount() > 0 ? true : false;
    }

    public function insert($res)
    {
        if (!$this->mail_exist($res['mail'])) {
            $this->ins->bindParam(':mail', $res['mail']);
            $this->ins->bindParam(':type', $res['type']);
            $this->ins->execute();
        }
    }

    public function __destruct()
    {
        $this->ins = null;
        $this->con = null;
    }
}
