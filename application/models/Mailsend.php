<?php
class Mailsend extends CI_Model
{
    public $from_name = "";
    public $from = "";
    public $to = "";
    public $subject = "";
    public $body = "";

    public function __construct()
    {
        parent::__construct();
    }

    public function setFromName($str)
    {
        $this->from_name = $str;
    }

    public function setFrom($str)
    {
        $this->from = $str;
    }

    public function setTo($str)
    {
        $this->to = $str;
    }

    public function setSubject($str)
    {
        $this->subject = $str;
    }

    public function setBody($template)
    {
        $this->body = file_get_contents(config_item("mail")["path"] . $template);
    }

    public function strReplace($search, $replace, $type = true)
    {
        if ($type) {
            $this->body = str_replace("%" . $search . "%", trim($replace), $this->body);
        } else {
            $this->subject = str_replace("%" . $search . "%", trim($replace), $this->subject);
        }
    }

    public function str_replace($search, $replace, $type = true)
    {
        if ($type) {
            $this->body = str_replace("%" . $search . "%", trim($replace), $this->body);
        } else {
            $this->subject = str_replace("%" . $search . "%", trim($replace), $this->subject);
        }
    }

    public function send()
    {
        mb_language("japanese");
        mb_internal_encoding("UTF-8");
        $this->email->set_wordwrap(false);
        /* Email from */
        $this->email->from($this->from, mb_encode_mimeheader($this->from_name, 'ISO-2022-JP', 'UTF-8'));
        /* Email to */
        $this->email->to($this->to);
        /* Email subject */
        $this->email->subject(mb_convert_encoding($this->subject, 'ISO-2022-JP', 'UTF-8'));
        /* Email body */
        if (config_item("branch") === APP_HEAD) {
            $this->email->message(mb_convert_encoding(stripslashes($this->body), 'ISO-2022-JP', 'UTF-8'));
        } else {
            $this->email->message(mb_convert_encoding($this->body, 'ISO-2022-JP', 'UTF-8'));
        }
        /* Send email */
        return $this->email->send();
    }
}
