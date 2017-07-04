<?php

namespace App\AppBundle\Controllers;


use App\AppBundle\Controller;
use App\AppBundle\FormValidator;
use App\AppBundle\Models\Users;
use App\AppBundle\Models\Mail;
use PHPMailer;
use DateTime;

/**
 * @author Alexandre Hoareau <ahoareau@student.42.fr>
 */
class SecurityController extends Controller
{
    public function signUpAction($request, $response, $args)
    {
        return $this->app->view->render($response, 'views/security/signUp.html.twig');
    }

    public function signUpForm($request, $response, $args)
    {
        if ($this->isLogged())
        {
            return $this->app->view->render($response, 'views/security/signUp.html.twig', ["router" => $this->router]);
        }
        $formValidator = $this->app->formValidator;
        $formValidator->check('name', ['required', 'maxLength']);
        $formValidator->check('lastname', ['required', 'maxLength']);
        $formValidator->check('age', ['required', 'age', 'isNumeric']);
        $formValidator->check('mail', ['required', 'isMail', /*'isSingle'*/]);
        $formValidator->check('password', ['required', 'isPassword']);
        if (empty($formValidator->error))
        {
            $user = new Users($this->app);
            $_SESSION['login'] = $_POST;
            $data = [
                'password'      => hash('whirlpool', $_POST['password']),
                'mail'        => $_POST['mail'],
                'name'        => $_POST['name'],
                'age'         => $_POST['age'],
                'lastname'    => $_POST['lastname'],
                'gender'      => 'm',
                'orientation' => 'bisexuel',
                'token'       => md5(microtime(TRUE)*100000),
                'verified'    => 0,
            ];
            if ($user->insert($data))
            {
// Envoi du mail avec gestion des erreurs
                $mail = $this->app->mail;

                if (!$mail->sendMail($data['mail'], $data['name'], 'signup')) {
                    $message = ['error' => ['une erreur est survenue']];
                } else {
                    echo 'Message envoyé !';
                    $message = ['success' => ['va voir ta boite mail pour finaliser ton inscription']];
                }

            }
            else
                $message = ['error' => ['une erreur est survenue']];
//            $_SESSION['login']['id'] = $id;

            return $this->app->view->render($response, 'views/pages/homepage.html.twig', ["router" => $this->router, '_messages' => $message]);
        }
        return $this->app->view->render($response, 'views/security/signUp.html.twig', ["router" => $this->router, 'error' => $formValidator->error, 'form' => $_POST]);
    }

    public function signInForm($request, $response, $args)
    {
        $log = new Users($this->app);
        $user = $log->checkLog($_POST);
        if ($user == false)
        {
            $this->app->flash->addMessage('error', 'Utilisateur non trouve');
        }
        else
        {
            $_SESSION['user'] = $user;
        }
        return $this->app->view->render($response, 'views/pages/homepage.html.twig', ["router" => $this->router, 'app' => new Controller($this->app)]);
    }

    public function logout($request, $response, $args)
    {
        if ($this->isLogged())
        {
            $_SESSION = [];
            session_destroy();
            setcookie('login',"");
            setcookie('password',"");
            $this->app->flash->addMessage('success', 'Tu es deconnecté à bientôt!');
            return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('homepage'));
        }
        return $this->app->view->render($response, 'views/pages/homepage.html.twig', ["router" => $this->router, 'app' => new Controller($this->app)]);
    }

}