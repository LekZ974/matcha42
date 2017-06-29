<?php

namespace App\AppBundle\Controllers;


use App\AppBundle\Controller;

class UsersController extends Controller
{
    public function indexAction($request, $response, $args)
    {

        return $this->app->view->render($response, 'views/users/home.html.twig');
    }
}