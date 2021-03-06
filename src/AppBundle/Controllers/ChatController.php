<?php

namespace App\AppBundle\Controllers;


use App\AppBundle\Controller;
use App\AppBundle\Models\Likes;
use App\AppBundle\Models\Messages;
use App\AppBundle\Models\Notifications;
use App\AppBundle\Models\Users;
use App\AppBundle\Models\UsersBlocked;
use App\AppBundle\Security;

class ChatController extends Controller
{
    public function indexAction($request, $response, $args)
    {
        $destId = Security::secureXSS($_GET['id']);
        $id = $this->getUserId();
        $match = new Likes($this->app);
        $user = new Users($this->app);
        $blocked = new UsersBlocked($this->app);
        if ($match->isMatch($id, $destId))
        {
            if (!$this->hasProfilPic())
            {
                $this->app->flash->addMessage('error', 'You have to add an avatar for chat with ' . $user->findOne('id', $destId)['lastname']);
                return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('homepage'));
            }

            return $this->app->view->render($response, 'views/chat/index.html.twig', [
                'app' => new Controller($this->app),
                'user' => array_merge($user->getUserData($id) , $user->getImageProfil($id)),
                'userDest' => array_merge($user->getUserData($destId) , $user->getImageProfil($destId)),
                'messages' => array_reverse($this->getMessages($id, $destId)),
            ]);
        }
        $this->app->flash->addMessage('error', 'You have to match with ' . $user->findOne('id', $destId)['lastname'] . ' to send a message');
        return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('homepage'));
    }

    public function sendMessage($request, $response, $args)
    {
        $message = Security::secureDB($_POST['message']);
        if (isset($message) && !empty($message))
        {
            $id = $this->getUserId();
            $destId = Security::secureXSS($_GET['id']);
            $notif = new Notifications($this->app);
            if (!$this->isBlocked($destId))
                $notif->sendNotification('message', $id, $destId, Security::secureInput($message), $this->app->router->pathFor('chatPage', ['id' => 'match?id='.$id]));

//            return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('chatPage', ['id' => 'match?id='.$destId]));
        }
        return $response;
    }
    
    public function getMessagesAction($request, $response, $args)
    {
        $destId = Security::secureXSS($_GET['id']);
        $id = $this->getUserId();
        $user = new Users($this->app);

        return $this->app->view->render($response, 'views/fragments/_chat-messages.html.twig', [
            'app' => new Controller($this->app),
            'user' => array_merge($user->getUserData($id) , $user->getImageProfil($id)),
            'userDest' => array_merge($user->getUserData($destId) , $user->getImageProfil($destId)),
            'messages' => array_reverse($this->getMessages($id, $destId)),
        ]);
    }

    public function getListAction($request, $response, $args)
    {
        if ($this->isLogged())
        {
            $id = $this->getUserId();
            $user = new Users($this->app);
            $likes = new Likes($this->app);
            $listLikes = $likes->find('id_user', $id);

            $i = 0;
            foreach ($listLikes as $likeUser) {
                if ($likes->isMatch($id, $likeUser['id_user_like'])) {
                    $listMatch[$i] = $likeUser;
                    $i++;
                }
            }
            array_walk($listMatch, function (&$match) {

                $user = new Users($this->app);
                $notif = new Messages($this->app);

                $match = $match + $user->getUserData($match['id_user_like']) + $notif->getLastMessage($match['id_user'], $match['id_user_like']);
                if (isset($match['message']))
                    $match['message'] = $this->subTextIfTooLong($match['message'], 40, '(...)');
            });

            usort($listMatch, function ($x, $y) {
                return strtotime($x['dateNotif']) < strtotime($y['dateNotif']);
            });

            return $this->app->view->render($response, 'views/chat/list.html.twig', [
                'app' => new Controller($this->app),
                'user' => array_merge($user->getUserData($id), $user->getImageProfil($id)),
                'listUsers' => $listMatch,
            ]);
        }
        $this->app->flash->addMessage('warning', 'Sign in or register you');


        return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('signUp'));
    }

    protected function getMessages($id, $destId)
    {
        $notif = new Messages($this->app);

        return $notif->getMessages($id, $destId);
    }
}