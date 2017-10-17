<?php

namespace App\AppBundle\Controllers;


use App\AppBundle\Controller;
use App\AppBundle\Models\Users;
use App\AppBundle\FormValidator;
use App\AppBundle\Mail;
use App\AppBundle\Security;
use App\AppBundle\Model;

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
        $formValidator->check('password2', ['required', 'isSamePassword']);
        if (empty($formValidator->error))
        {
            $user = new Users($this->app);
            $_SESSION['login'] = $_POST;
            $data = [
                'password'      => hash('whirlpool', Security::secureDB($_POST['password'])),
                'mail'        => Security::secureDB($_POST['mail']),
                'name'        => Security::secureDB($_POST['name']),
                'age'         => Security::secureDB($_POST['age']),
                'lastname'    => Security::secureDB($_POST['lastname']),
                'gender'      => 'other',
                'orientation' => 'bisexual',
                'token'       => md5(microtime(TRUE)*100000),
                'verified'    => 0,
            ];
            if ($user->insert($data))
            {
// Envoi du mail avec gestion des erreurs
                $mail = new Mail($this->app, 'ahoareau@student.42.fr');
                $user = $user->findLast();

                if (!$mail->sendMail($data['mail'], $user, 'signup')) {
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
        foreach ($formValidator->error as $error)
            $this->app->flash->addMessage('error', $error[0]);
        return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('signUp'));
    }

    public function signInForm($request, $response, $args)
    {
        $log = new Users($this->app);
        $user = $log->checkLog($_POST);
        if ($user == false)
            $this->app->flash->addMessage('error', 'Invalid user or password');
        elseif ($user['verified'] != 1)
            $this->app->flash->addMessage('error', 'You have to confirm your subscribtion!');
        else
        {
            $_SESSION['user'] = $user;
            $log->updatedLogin($user['id'], 1);
        }
        return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('homepage'));
//        return $this->app->view->render($response, 'views/pages/homepage.html.twig', ["router" => $this->router, 'app' => new Controller($this->app)]);
    }

    public function logout($request, $response, $args)
    {
        if ($this->isLogged())
        {
            $users = new Users($this->app);
            $users->setDisconnected($this->getUserId());
            $_SESSION = [];
//            session_destroy();
            setcookie('login',"");
            setcookie('password',"");
            $this->app->flash->addMessage('success', 'You are disconnected! Already miss you!');
            $this->app->flash->getMessages();
            return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('homepage'));
        }
        $this->app->flash->addMessage('warning', '??');
        return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('homepage'));
    }

    public function activateAccountAction($request, $response, $args)
    {
        $this->activeUser(Security::secureXSS($_GET['id']), Security::secureXSS($_GET['key']));

        return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('homepage'));
    }

    public function forgotPasswordAction($request, $response, $args)
    {
        return $this->app->view->render($response, 'views/security/fPassword.html.twig');
    }

    public function resetPasswordAction($request, $response, $args)
    {
        $id = Security::secureXSS($_GET['id']);
        $token = Security::secureXSS($_GET['key']);

        $users = new Users($this->app);
        $user = $users->findOne('id', $id);
        if (isset($id, $token) && !empty($id) && !empty($token) && $token === $user['token']) {
            return $this->app->view->render($response, 'views/security/resetPassword.html.twig', ['data' => ['id' => $id, 'token' => $token]]);
        }
        $this->app->flash->addMessage('error', 'You don\'t have permission to be here!');
        return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('homepage'));
    }

    public function forgotPassword($request, $response, $args)
    {
        if ($this->isLogged())
        {
            return $this->app->view->render($response, 'views/security/signUp.html.twig', ["router" => $this->router]);
        }
        $formValidator = $this->app->formValidator;
        $formValidator->check('mail', ['required', 'isMail', 'isExist']);
        if (empty($formValidator->error))
        {
            $users = new Users($this->app);
            $user = $users->findOne('mail', Security::secureDB($_POST['mail']));
            if (!empty($user))
            {
                $mail = new Mail($this->app, 'ahoareau@student.42.fr');

                if (!$mail->sendMail(Security::secureDB($_POST['mail']), $user, 'resetPassword')) {
                    $this->app->flash->addMessage('error', 'an error occurred');
                } else {
                    echo 'Message envoyé !';
                    $this->app->flash->addMessage('success', 'Go check your emails for reinitialize your password!');
                }
            }
            else
                $this->app->flash->addMessage('error', 'an error occurred');

            return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('homepage'));
        }
        foreach ($formValidator->error as $error)
            $this->app->flash->addMessage('error', $error[0]);
        return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('forgotPassword'));
    }

    protected function activeUser($id, $token)
    {
        $users = new Users($this->app);
        $user = $users->findOne('id', $id);
        if ($token === $user['token'])
        {
            $users->update($id, ['verified' => 1]);
            $this->app->flash->addMessage('success', 'Now you can enjoy matcha! Have sex with fun!');
            return true;
        }
        $this->app->flash->addMessage('error', 'wrong link activation');

        return false;
    }

    public function resetPassword($request, $response, $args)
    {
        $password = Security::secureDB($_POST['password']);
        $password2 = Security::secureDB($_POST['password2']);
        $id = Security::secureDB($_POST['id']);
        $token = Security::secureDB($_POST['key']);
        $formValidator = $this->app->formValidator;
        if (isset($password, $password2, $id, $token))
        {
            $formValidator->check('password', ['required', 'isPassword']);
            $formValidator->check('password2', ['required', 'isSamePassword']);
            if (empty($formValidator->error))
            {
                $user = new Users($this->app);
                if (!$user->findOne('id', $id))
                {
                    $this->app->flash->addMessage('error', 'An error is occurred, contact Alexandre HOAREAU to help you!!');
                    return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('resetPassword'));
                }
                $user->update($id, ['password' => hash('whirlpool', Security::secureDB($_POST['password']))]);
                $this->app->flash->addMessage('success', 'Your Password is up to date!');
                return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('homepage'));
            }
        }
        foreach ($formValidator->error as $error)
            $this->app->flash->addMessage('error', $error[0]);

        return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('resetPassword', [], ['id' => $id, 'key' => $token]));
    }

}