<?php

namespace App\AppBundle\Controllers;


use App\AppBundle\Controller;

class ChatController extends Controller
{
    public function indexAction($request, $response, $args)
    {
        return $this->app->view->render($response, 'views/chat/index.html.twig');
    }
}