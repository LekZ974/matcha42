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

    public function sendMail($destMail, $user, $case)
    {
        try {
            $mail = new PHPMailer();
            $mail->Host = 'smtp.orange.fr';
            $mail->SMTPAuth   = false;
            $mail->Port = 25; // Par défaut

// Expéd    iteur
            $mail->SetFrom($this->from['mail'], $this->from['from']);
// Desti    nataire
                $destName = $user['lastname'] . ' ' . $user['name'];
            $mail->AddAddress($destMail, $user['name']);

            switch ($case){
                case 'signup':
                    $mail->Subject = 'Bienvenue sur Matcha '.$destName.'!!';
                    $token = md5(microtime(TRUE)*100000);
                    $queryString = 'id='.urlencode($user['id']).'&key='.urlencode($token);
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
                    break;
                case 'resetPassword':
                    $mail->Subject = 'Réinitialisation de ton mot de passe Camagru';
                    $token = md5(microtime(TRUE)*100000);
                    $queryString = 'id='.urlencode($user['id']).'&key='.urlencode($token);
                    $message = <<<MESSAGE
<html>
		<head>
		<title>Reinitialisation mot de passe</title>
		</head>
		<body>
			<p>Bonjour $destName,</p>
			<br />
			<p>Quelqu’un a récemment demandé à réinitialiser ton mot de passe Matcha.</p>
			<a href="http://localhost:8081/resetPassword?$queryString">Cliques ici pour changer ton mot de passe.</a>
			<br />
			<p>---------------</p>
			<p>C'est un mail automatique, Merci de ne pas y répondre.</p>
		</body>
		</html>
MESSAGE;
                    break;
            }
// Votre message
            if (!empty($message))
            {
                $mail->MsgHTML($message);
                $mail->Send();
            }
        } catch(\Exception $e) {
            return $e->getMessage();
        }
        return true;
    }
}