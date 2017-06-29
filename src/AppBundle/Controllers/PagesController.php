<?php

namespace App\AppBundle\Controllers;

use App\AppBundle\Controller;

/**
 * @author Alexandre Hoareau <ahoareau@student.42.fr>
 */
class PagesController extends Controller
{
    public function indexAction($request, $response, $args)
    {
        return $this->app->view->render($response, 'views/pages/homepage.html.twig', ['app' => new Controller($this->app)]);
    }
}