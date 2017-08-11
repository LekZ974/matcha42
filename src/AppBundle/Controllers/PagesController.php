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
                $users = new Users($this->app);
                $user = $users->getUserData($this->getUserId());
                $suggest = $suggest + ['distance' => round($this->distance($user['lat'], $user['lon'], $suggest['lat'], $suggest['lon'], 'K'), 2)];
            });
            uasort($suggests, function ($a, $b){
                return $a['distance'] - $b['distance'];
            });
        }

        return $this->app->view->render($response, 'views/pages/homepage.html.twig', [
            'app' => new Controller($this->app),
            'users' => $users->getHome($this->getUserId()),
            'suggest' => $suggests,
        ]);
    }
}