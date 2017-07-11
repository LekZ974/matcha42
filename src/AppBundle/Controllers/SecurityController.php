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
        $formValidator->check('mail', ['required', 'isMail', 'isSingle']);
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
                    $this->app->flash->addMessage('error', 'an error occurred');
                } else {
                    echo 'Message envoyé !';
                    $this->app->flash->addMessage('success', 'To finalize your subscription, go check your emails!');
                }

            }
            else
                $this->app->flash->addMessage('error', 'an error occurred');

            return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('homepage'));
        }
        $this->app->flash->addMessage('error', $formValidator->error['mail'][0]);
        return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('signUp'));
    }

    public function signInForm($request, $response, $args)
    {
        $log = new Users($this->app);
        $user = $log->checkLog($_POST);
        if ($user == false)
        {
            $this->app->flash->addMessage('error', 'Invalid user or password');
        }
        else
        {
            $_SESSION['user'] = $user;
        }
        return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('homepage'));
//        return $this->app->view->render($response, 'views/pages/homepage.html.twig', ["router" => $this->router, 'app' => new Controller($this->app)]);
    }

    public function logout($request, $response, $args)
    {
        if ($this->isLogged())
        {
            $_SESSION = [];
//            session_destroy();
            setcookie('login',"");
            setcookie('password',"");
            $this->app->flash->addMessage('success', 'You are disconnected! Already miss you!');
            $messages = $this->app->flash->getMessage();
            return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('homepage'));
        }
        $this->app->flash->addMessage('warning', '??');
        return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('homepage'));
    }

}