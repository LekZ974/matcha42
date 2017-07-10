<?php

namespace App\AppBundle\Controllers;


use App\AppBundle\Controller;
use App\AppBundle\Models\Pictures;
use App\AppBundle\Models\Users;
use Prophecy\Exception\Exception;

class UsersController extends Controller
{
    public function indexAction($request, $response, $args)
    {
        if ($this->isLogged())
        {
            return $this->app->view->render($response, 'views/users/'.$args['profil'].'.html.twig', ['app' => new Controller($this->app)]);
        }
        return $this->app->view->render($response, 'views/pages/homepage.html.twig', ['app' => new Controller($this->app)]);
    }

    public function editUser($request, $response, $args)
    {
        if ($this->isLogged())
        {
            $this->addPhoto($request, $response, $args);
        }
        return $this->app->view->render($response, 'views/users/'.$args['profil'].'.html.twig', ['app' => new Controller($this->app)]);
    }

    protected function addPhoto($request, $response, $args)
    {
        if (isset($_FILES['photoUser']) && !empty($_FILES['photoUser']))
        {
            $userImage = new Pictures($this->app);
            $user = new Users($this->app);
            $file = $userImage->downloadFromServer($request, 'photoUser', __DIR__.'/../../../public/image/');
            $userImage->insert([
               'id_user' =>  $this->getUserId(),
               'url' => '/image/'.$file,
               'is_profil' => 0,
               'created_at' => date("d/m/Y H:i:s"),
            ]);
        }

    }
}