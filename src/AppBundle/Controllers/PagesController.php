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

        foreach ($suggests as $suggest)
            print_r($suggest);

        return $this->app->view->render($response, 'views/pages/homepage.html.twig', [
            'app' => new Controller($this->app),
            'users' => $users->getHome($this->getUserId()),
            'suggests' => $suggests,
        ]);
    }

    public function mapLocation($request, $response, $args)
    {
        if ($this->isLogged())
        {
            $users = new Users($this->app);
            $listUsers = $users->getSuggest($this->getUserId());
            $user = $users->getUserData($this->getUserId());

            return $this->app->view->render($response, 'views/pages/map.html.twig', ['app' => new Controller($this->app), 'users' => $listUsers, 'user' => $user]);
        }
        $this->app->flash->addMessage('warning', 'Sign in or register you');


        return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('signUp'));
    }

    public function searchAction($request, $response, $args)
    {
        $users = new Users($this->app);

        $suggests = [];
        if ($this->isLogged())
        {
            if (!empty($_POST['terms'])) {
                $suggests = $users->findSearch($_POST['terms'], $this->getUserId());
            }
            else
                $suggests = $users->getSuggest($this->getUserId());
            array_walk($suggests, function (&$suggest){
                $suggest = $this->addDistanceColumn($suggest);
            });
            uasort($suggests, function ($a, $b){
                return $a['distance'] - $b['distance'];
            });
        }

        return $this->app->view->render($response, 'views/pages/search.html.twig', [
            'app' => new Controller($this->app),
            'users' => $users->getHome($this->getUserId()),
            'suggests' => $suggests,
        ]);
    }
}