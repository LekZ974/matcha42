<?php

namespace App\AppBundle\Controllers;

use App\AppBundle\Controller;
use App\AppBundle\Models\Users;

/**
 * @author Alexandre Hoareau <ahoareau@student.42.fr>
 */
class PagesController extends Controller
{
    public function indexAction($request, $response, $args)
    {
        $users = new Users($this->app);

        $suggests = [];
        if ($this->isLogged())
        {
            $suggests = $users->getSuggest($this->getUserId());
            array_walk($suggests, function (&$suggest){
                $suggest = $this->addDistanceColumn($suggest);
            });
            uasort($suggests, function ($a, $b){
                return $a['distance'] - $b['distance'];
            });
        }

        return $this->app->view->render($response, 'views/pages/homepage.html.twig', [
            'app' => new Controller($this->app),
            'users' => $users->getHome($this->getUserId()),
            'suggests' => $suggests,
        ]);
    }
}