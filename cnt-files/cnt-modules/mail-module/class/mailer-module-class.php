<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Mailer_module
{
    var $entry;
    var $build;
    var $mail;

    public function __construct()
    {
        $this->build = array();
        $this->mail = new PHPMailer();
    }

    public function mailer_compound()
    {
        $mailer = new Mailer_module();
        $mailer->entry = $this->entry;
        $mailer->mail = $this->mail;
        $mailer->build = $this->build;
        $mailer->mail = $mailer->get_smtp();
        $mailer->mail = $mailer->host_and_user_data();
        $mailer->mail = $mailer->send_to_replay();
        $mailer->mail = $mailer->body_message();
        $mailer->mail = $mailer->to_send();
        return;
    }
    /*SMTP DATA*/
    public function get_smtp()
    {
        $this->mail->isSMTP();
        //$this->mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $this->mail->SMTPAuth = true;
        return $this->mail;
    }
    /*DATA HOST, USERNAME AND PASSWORD*/
    public function host_and_user_data()
    {
        $this->mail->Host = MAILER["host"];
        $this->mail->Port = MAILER["port"];
        $this->mail->Username = MAILER["username"];
        $this->mail->Password = base64_decode(MAILER["password"]);
        return $this->mail;
    }
    /*SEND DATA TO*/
    public function send_to_replay()
    {
        $this->mail->setFrom(MAILER["username"], explode("@", MAILER["username"])[0]);
        $this->mail->addReplyTo(MAILER["username"], explode("@", MAILER["username"])[0]);
        $this->mail->addAddress('romulo.franco@cleaner.com.br', 'Romulo Franco');
        $this->mail->addAddress('fhelipe.santos@cleaner.com.br', 'Fhelipe Santos');
        $this->mail->addAddress('gustavo.vasconcelos@cleaner.com.br', 'Gustavo Vasconcelos');
        $this->mail->addAddress('luiz.gustavo.devasconcelos@gmail.com', 'Gustavo Vasconcelos');
        $this->mail->addAddress('matheus.vello@cleaner.com.br', 'Matheus Vello');
        $this->mail->addAddress('wesley.candido@cleaner.com.br', 'Wesley Candido');
        return $this->mail;
    }
    /*SEND MESSAGE*/
    public function body_message()
    {
        $data = $this->entry;
        //Set the subject line
        $this->mail->Subject = 'Certificado Expirado em Canhotos';
        $this->mail->isHTML(true);
        $this->mail->CharSet = "UTF-8";
        $message = file_get_contents("../../mail-module/template/view/view-mail-expirados-template.php");
        $message = explode("[put-content]", $message);
        $this->mail->Body    = $message[0];
        //for ($i = 0; $i < count($data); $i++) {}
        if (intval($data->dias_p_expirar) < 12) {
            $this->mail->Body    .= '<tr>';
            $this->mail->Body    .= '<td style="font-size: .9rem;">' . $data->cnpj . '</td>';
            $this->mail->Body    .= '<td style="font-size: .9rem;">' . $data->empresa . '</td>';
            $this->mail->Body    .= '<td style="font-size: .9rem;">' . $data->expire . '</td>';
            $this->mail->Body    .= '<td style="font-size: .9rem;">' . $data->timer . '</td>';
            $this->mail->Body    .= '</tr>';
        }
        $this->mail->Body    .= $message[1];
        $this->mail->AltBody = 'Verifique os certificados expirados na tela de Canhotos!';
        return $this->mail;
    }
    /*SEND MESSAGE*/
    public function to_send()
    {
        $nArray = array();
        (!$this->mail->send()) ? $nArray["message"] = 'Mailer Error: ' . $this->mail->ErrorInfo : $nArray["message"] = 'Message sent!';
        return;
    }
}
