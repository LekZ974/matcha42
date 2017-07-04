<?php

namespace App\AppBundle\Controllers;


use App\AppBundle\Controller;

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
}