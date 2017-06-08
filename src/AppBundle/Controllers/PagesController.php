<?php

namespace App\AppBundle\Controllers;

use App\AppBundle\Controller;

class PagesController extends Controller
{
    public function indexAction($request, $response, $args)
    {
        return $this->app->view->render($response, 'views/homepage.html.twig');
    }
}