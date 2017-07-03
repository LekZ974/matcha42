<?php

namespace App\AppBundle;

use PHPMailer;
use SebastianBergmann\GlobalState\RuntimeException;

class Mail
{
    public $from;
    protected $app;

    public function __construct($c, $set)
    {
        $this->app = $c;
        $this->from = $set;
    }

    public function sendMail($destMail, $destName)
    {
        try {
        $mail = new PHPMailer();
        $mail->Host = 'smtp.orange.fr';
        $mail->SMTPAuth   = false;
        $mail->Port = 25; // Par défaut

// Expéditeur
        $mail->SetFrom($this->from['mail'], $this->from['from']);
// Destinataire
        $mail->AddAddress($destMail, $destName);
// Objet
        $mail->Subject = 'TEST ENVOI MESSAGE';

// Votre message
        $mail->MsgHTML('Test de l\'envoi dun mail avec phpmailer');

        $mail->Send();

        } catch(\Exception $e) {
            return $e->getMessage();
        }
        return true;
    }
}