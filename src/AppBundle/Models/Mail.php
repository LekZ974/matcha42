<?php

namespace App\AppBundle\Models;

use App\AppBundle\Model;
use PHPMailer;
use SebastianBergmann\GlobalState\RuntimeException;

class Mail extends Model
{
    public function sendMail()
    {
        try {
        $mail = new PHPMailer();
        $mail->Host = 'smtp.orange.fr';
        $mail->SMTPAuth   = false;
        $mail->Port = 25; // Par défaut

// Expéditeur
        $mail->SetFrom('ahoareau@student.42.fr', 'HOAREAU Alexandre');
// Destinataire
        $mail->AddAddress($data['mail'], $data['name']);
// Objet
        $mail->Subject = 'TEST ENVOI MESSAGE';

// Votre message
        $mail->MsgHTML('Test de l\'envoi dun mail avec phpmailer');

        $mail->Send();

        } catch(RuntimeException $e) {
            $e->getMessage();
        }
    }
}