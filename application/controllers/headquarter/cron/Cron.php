<?php
class Cron extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function deleteUpFileTbl()
    {
        $this->Tmpupfiletbl->deleteTmpUpFile();
    }

    public function deleteUpFileDir()
    {
        $files = get_dir_file_info($this->config->item('upload_file_tmp_path'));
        foreach ($files as $key => $val) {
            if ($val['date'] < strtotime("-1 hour")) {
                $path = str_replace('(', '\(', $val['server_path']);
                $path = str_replace(')', '\)', $path);
                exec('rm -f ' . $path);
            }
        }
    }

    /**
     * Send alert email
     *
     * @param integer $type (1: 依頼メール (１、３、６ヶ月、１年) 2: 依頼メール (２年目以降) 3: 督促メール(１、３、６ヶ月、１年) 4: 督促メール(２年目以降))
     * @return void
     */
    public function sendAlertMail(int $type)
    {
        $recipientList = array();
        $typeMst = config_item("mail")["follow_up"]["type"];
        switch ($type) {
            /* 1: 依頼メール (１、３、６ヶ月、１年) */
            case $typeMst[0]:
                $recipientList = $this->Tishokugokeika->getForSendAlertEmail(date(DATE_TIME_DEFAULT_2, strtotime("+3 day")), date(DATE_TIME_DEFAULT_2, strtotime("+9 day")), null, 1);
                break;
            /* 2: 依頼メール (２年目以降) */
            case $typeMst[1]:
                $currentYear = date("Y");
                $nextYear = $currentYear + 1;
                $recipientList = $this->Tishokugokeika->getForSendAlertEmail(date(DATE_TIME_DEFAULT_2, strtotime("$currentYear-4-1")), date(DATE_TIME_DEFAULT_2, strtotime("$nextYear-3-31")), 2);
                break;
            /* 3: 督促メール(１、３、６ヶ月、１年) */
            case $typeMst[2]:
                $recipientList = $this->Tishokugokeika->getForSendAlertEmail(null, date(DATE_TIME_DEFAULT_2, strtotime("-1 day")), null, 1);
                break;
            /* 4: 督促メール(２年目以降) */
            case $typeMst[3]:
                $currentYear = date("Y");
                $nextYear = $currentYear + 1;
                $recipientList = $this->Tishokugokeika->getForSendAlertEmail(date(DATE_TIME_DEFAULT_2, strtotime("$currentYear-4-1")), date(DATE_TIME_DEFAULT_2, strtotime("$nextYear-3-31")), 2);
                /* 期限が切れていない人には送らない */
                $recipientList = array_filter($recipientList, function ($recipient) {
                    return $recipient->REPORT_DEADLINE < date(DATE_TIME_DEFAULT_2);
                });
                break;
        }

        /* Exit if un-handled recipients list is empty */
        if (empty($recipientList)) {
            echo "Recepients list is empty.";
            exit();
        }

        /* Get institution, organ */
        /* Get number of un-handled recipients (by institution and organ id) */
        $institutionWithOrgan = array();
        foreach ($recipientList as $recipient) {
            $organId = $recipient->ZOKI_CODE;
            $institutionCode = $recipient->ISHOKUGO_KEIKAJYOUHOU_SISETU_CD ?? $recipient->ISYOKU_ISYOKUSISETU_CD;
            if (empty($institutionCode)) {
                continue;
            }
            if (empty($institutionWithOrgan[$institutionCode])) {
                $institutionWithOrgan[$institutionCode] = array($organId => 1);
            } else {
                if (array_key_exists($organId, $institutionWithOrgan[$institutionCode])) {
                    $institutionWithOrgan[$institutionCode][$organId]++;
                } else {
                    $institutionWithOrgan[$institutionCode][$organId] = 1;
                }
            }
        }

        /* Get doctor email and set number of un-handled recipients for each doctor */
        $doctorMailsWithCount = array();
        foreach ($institutionWithOrgan as $institutionCode => $organIdWithCount) {
            foreach ($organIdWithCount as $organId => $count) {
                foreach ($this->Doctortbl->getForSendAlertEmail($institutionCode, $organId) as $doctor) {
                    $doctorMail = $doctor->mail;
                    if (array_key_exists($doctorMail, $doctorMailsWithCount)) {
                        $doctorMailsWithCount[$doctorMail] += $count;
                    } else {
                        $doctorMailsWithCount[$doctorMail] = $count;
                    }
                }
            }
        }
        /* Send email */
        $this->sendMail($doctorMailsWithCount, $type);
    }

    /**
     * Send email
     *
     * @param array $mails
     * @param string $mailTemplate
     * @return void
     */
    private function sendMail(array $mailsWithCount, string $mailType)
    {
        $currentYear = date("Y");
        $mailSettings = config_item("mail");
        $followUpMailSettings = $mailSettings["follow_up"];
        $reportDeathline = date(DATE_TIME_DEFAULT_JP) . "～" . date(DATE_TIME_DEFAULT_JP, strtotime("+9 day"));
        $this->email->set_mailtype("html");
        foreach ($mailsWithCount as $mailAddress => $count) {
            $this->Mailsend->setFromName($mailSettings["from_name"]);
            $this->Mailsend->setFrom($mailSettings["from_address"]);
            $this->Mailsend->setBody("follow_up_alert_mail_type_$mailType.html");
            $this->Mailsend->setTo($mailAddress);
            $this->Mailsend->setSubject($followUpMailSettings["subject"][$mailType]);
            $this->Mailsend->str_replace("RECIPIENT_COUNT", $count);
            $this->Mailsend->str_replace("URL", config_item("tp_base_url"));
            $this->Mailsend->str_replace("CURRENT_YEAR", $currentYear);
            $this->Mailsend->str_replace("LAST_YEAR", $currentYear - 1);
            $this->Mailsend->str_replace("REPORT_DEATHLINE", $reportDeathline);
            $this->Mailsend->str_replace("FOLLOW_UP_MAIL", $followUpMailSettings["contact_mail"]);
            $this->Mailsend->send();
        }
        $this->email->set_mailtype("text");
        $emailCount = count($mailsWithCount);
        echo "$emailCount mail(s) sended.";
    }
}
