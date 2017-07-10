<?php

namespace App\AppBundle\Controllers;


use App\AppBundle\Controller;
use App\AppBundle\Models\Pictures;
use App\AppBundle\Models\Users;
use App\AppBundle\Upload;
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
        return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('edit', ['profil' => $args['profil']]));
    }

    protected function addPhoto($request, $response, $args)
    {
        if (isset($_FILES['photoUser']) && !empty($_FILES['photoUser']))
        {
            $userImage = new Pictures($this->app);
            $user = new Users($this->app);
            $uploadFile = new Upload($request->getUploadedFiles());
            $file = $uploadFile->uploadIsValid(__DIR__.'/../../../public/image/', 5000000);
            foreach ($uploadFile->error as $error)
            {
                $this->app->flash->addMessage('error', $error);
            }

            $userImage->insert([
               'id_user' =>  $this->getUserId(),
               'url' => '/image/'.$file,
               'is_profil' => 0,
               'created_at' => date("d/m/Y H:i:s"),
            ]);
        }

    }
}