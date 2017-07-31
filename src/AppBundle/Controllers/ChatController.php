<?php

namespace App\AppBundle\Controllers;


use App\AppBundle\Controller;
use App\AppBundle\Models\Likes;
use App\AppBundle\Models\Notifications;
use App\AppBundle\Models\Users;

class ChatController extends Controller
{
    public function indexAction($request, $response, $args)
    {
        $destId = $_GET['id'];
        $id = $this->getUserId();
        $match = new Likes($this->app);
        $user = new Users($this->app);
        if ($match->isMatch($id, $destId))
        {
            $notif = new Notifications($this->app);
            if ($user->getImageProfil($id))
                $profil = array_merge($user->getUserData($id) , $user->getImageProfil($id));
            else
                $profil = $user->getUserData($id);
            $messages = $notif->getMessages($id, $destId);
            return $this->app->view->render($response, 'views/chat/index.html.twig', [
                'app' => new Controller($this->app),
                'user' => $profil,
                'messages' => $messages,
            ]);
        }
        $this->app->flash->addMessage('error', 'You have to match with ' . $user->findOne('id', $destId)['lastname'] . ' to send a message');
        return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('homepage'));
    }

    public function listAction($request, $response, $args)
    {
        return $this->app->view->render($response, 'views/chat/list.html.twig', [
            'app' => new Controller($this->app),
        ]);
    }

    public function indexForm($request, $response, $args)
    {
        $message = $_POST['message'];
        if (isset($message) && !empty($message))
        {
            $id = $this->getUserId();
            $destId = $_GET['id'];
            $notif = new Notifications($this->app);
            $notif->sendNotification('message', $id, $destId, $message, $this->app->router->pathFor('chatPage', ['id' => 'match?id='.$id]));

            return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('chatPage', ['id' => 'match?id='.$destId]));
        }
        return $response;
    }
}