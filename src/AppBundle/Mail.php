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

    public function sendMail($destMail, $destName, $case)
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

// Votre message
        switch ($case) {
            case 'signup':
                $mail->Subject = 'Bienvenue sur Matcha $destName!!';
                $token = md5(microtime(TRUE)*100000);
                $queryString = 'log='.urlencode($destName).'&key='.urlencode($token);
                $message = <<<MESSAGE
<html>
	<head>
	<title>Bienvenue sur Matcha $destName!!</title>
	</head>
	<body>
	   <h1>Bienvenue sur Matcha $destName!!</h1>
		<br />
		<p>Pour activer ton compte, cliques sur le lien ci dessous ou copier/coller dans ton navigateur internet.</p>
		<a href="http://localhost:8081/activate?$queryString">Cliques ici pour activer ton compte.</a>
		<br />
		<p>---------------</p>
		<p>C'est un mail automatique, Merci de ne pas y répondre.</p>
	</body>
</html>
MESSAGE;

        }
        $mail->MsgHTML($message);
        $mail->Send();

        } catch(\Exception $e) {
            return $e->getMessage();
        }
        return true;
    }
}