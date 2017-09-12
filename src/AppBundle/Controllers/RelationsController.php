<?php
/**
 * Created by PhpStorm.
 * User: lekz
 * Date: 16/07/17
 * Time: 16:51
 */

namespace App\AppBundle\Controllers;


use App\AppBundle\Controller;
use App\AppBundle\Models\Likes;
use App\AppBundle\Models\Messages;
use App\AppBundle\Models\Notifications;
use App\AppBundle\Models\Users;
use App\AppBundle\Models\UsersBlocked;

class RelationsController extends Controller
{
    public function like($request, $response, $args)
    {
        $user = new Users($this->app);
        if (!$user->getImageProfil($this->getUserId()))
        {
            $response = $response->withJson(['error' => "-1"]);
            return $response;
        }
        $likeId = $_POST['likeId'];
        $id = $this->getUserId();
        $response = $response->withHeader('Content-type', 'application/json');

        $like = new Likes($this->app);
        $likable = $like->likeUser($id, $likeId);
        if ($likable == -1)
        {
            $like->deleteLike($id, $likeId);
            $this->upPopularity($likeId, -10);
        }
        else
        {
            $like->insert(['id_user' => $id,
                'id_user_like' => $likeId,
            ]);
            $this->upPopularity($likeId, 5);
            $notif = new Notifications($this->app);
            if (!$this->isBlocked($likeId))
            {
                $notif->sendNotification('like', $id, $likeId, 'like your profil', $this->app->router->pathFor('viewProfil', ['id' => $id]));
                if ($like->isMatch($id, $likeId))
                {
                    $this->upPopularity($likeId, 10);
                    $notif->sendNotification('match', $id, $likeId, 'You have a match!', $this->app->router->pathFor('viewProfil', ['id' => $id]));
                }
            }
        }

        return $response;
    }

    public function countNotif($request, $response, $args)
    {
        $response = $response->withHeader('Content-type', 'application/json');
        $notif = new Notifications($this->app);
        $nb = $notif->getCountUnreadNotif($this->getUserId());
        $response = $response->withJson(['nb' => $nb]);

        return $response;
    }

    public function readNotif($request, $response, $args)
    {
        $notifId = $_POST['id'];
        $messageId = $_POST['id-message'];

        if (isset($messageId) && !empty($messageId))
        {
            $messages = new Messages($this->app);
            $message = $messages->findOne('id', $messageId);
            $notifId = $message['idNotif'];
        }

        if (isset($notifId) && !empty($notifId))
        {
            $notif = new Notifications($this->app);
            $notif->setAsRead($notifId);
            $nb = $notif->getCountUnreadNotif($this->getUserId());
            $response = $response->withHeader('Content-type', 'application/json');
            $response = $response->withJson(['nb' => $nb]);
        }

        return $response;
    }

    public function lastNotif($request, $response, $args)
    {
        $lastNotifications = $this->getLastNotifications();
        if (!empty($lastNotifications))
        {
            array_walk($lastNotifications, function (&$lastNotification)
            {
                $users = new Users($this->app);
                $user = $users->getUserData($lastNotification['id_user']);
                $lastNotification = array_merge($lastNotification, $user);

            });
        }
        $response = $response->withHeader('Content-type', 'application/json');
        $response = $response->withJson(['lastNotifications' => $lastNotifications]);

        return $response;
//        return $this->app->view->render($response, 'views/fragments/_unread-notifications.html.twig', ['app' => new Controller($this->app)]);
    }

    public function unreadNotif($request, $response, $args)
    {
        $unreadNotifications = $this->getUnreadNotifications();
        array_walk($unreadNotifications, function (&$unreadNotification)
        {
            $users = new Users($this->app);
            $user = $users->getUserData($unreadNotification['id_user']);
            $unreadNotification = array_merge($unreadNotification, $user);

        });
        $response = $response->withHeader('Content-type', 'application/json');
        $response = $response->withJson(['unreadNotifications' => $unreadNotifications]);

        return $response;
//        return $this->app->view->render($response, 'views/fragments/_unread-notifications.html.twig', ['app' => new Controller($this->app)]);
    }

    public function notif($request, $response, $args)
    {
        return $this->app->view->render($response, 'views/users/notifications.html.twig', ['app' => new Controller($this->app)]);
    }

    public function reportAsFake($request, $response, $args)
    {
        $id = $_POST['id_user'];
        $this->upPopularity($id, -5);
        $this->blockUser($request, $response, $args);

        return $response;
    }

    public function blockUser($request, $response, $args)
    {
        $id = $_POST['id_user'];
        $this->upPopularity($id, -5);
        $blocked = new UsersBlocked($this->app);
        $likes = new Likes($this->app);
        $blocked->insert([
            'id_user' => $this->getUserId(),
            'id_user_blocked' => $id,
        ]);
        $likes->deleteLike($this->getUserId(), $id);
        $likes->deleteLike($id, $this->getUserId());

        return $response;
    }

    public function unblockUser($request, $response, $args)
    {
        $id = $_POST['id_user'];
        $this->upPopularity($id, 5);
        $blocked = new UsersBlocked($this->app);
        $blocked->delete($blocked->findOne('id_user_blocked', $id)['id']);

        return $response;
    }
}