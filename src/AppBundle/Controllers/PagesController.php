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
            elseif (!empty($_POST['genderM']) || !empty($_POST['genderF']) || !empty($_POST['oriBi']) || !empty($_POST['oriHetero']) || !empty($_POST['oriHomo']))
            {
                if ($_POST['genderF'] === 'on' && $_POST['genderM'] === 'on') {
                    if ($_POST['oriHetero'] == 'on') {
                        $suggests = $users->getUsersByOrientation($this->getUserId(), 'hetero');
                    }
                    elseif ($_POST['oriHomo'] == 'on') {
                        $suggests = $users->getUsersByOrientation($this->getUserId(), 'homo');
                    }
                    elseif ($_POST['oriBi'] == 'on') {
                        $suggests = $users->getUsersByOrientation($this->getUserId(), 'bisexual');
                    }
                    else {
                        $suggests = $users->findSearch('%', $this->getUserId());
                    }
                }
                else if ($_POST['genderM'] === 'on') {
                        if ($_POST['oriHetero'] == 'on') {
                            $suggests = $users->getSuggest($this->getUserId(), 'man', 'female');
                        }
                        elseif ($_POST['oriHomo'] == 'on') {
                            $suggests = $users->getSuggest($this->getUserId(), 'man', 'male');
                        }
                        elseif ($_POST['oriBi'] == 'on') {
                            $suggests = $users->getSuggest($this->getUserId(), 'man', 'other');
                        }
                        else {
                            $suggests = $users->findSearch('male', $this->getUserId());
                    }
                }
                else if ($_POST['genderF'] === 'on') {
                    if ($_POST['oriHetero'] == 'on') {
                        $suggests = $users->getSuggest($this->getUserId(), 'woman', 'male');
                    }
                    elseif ($_POST['oriHomo'] == 'on') {
                        $suggests = $users->getSuggest($this->getUserId(), 'woman', 'female');
                    }
                    elseif ($_POST['oriBi'] == 'on') {
                        $suggests = $users->getSuggest($this->getUserId(), 'woman', 'other');
                    }
                    else {
                        $suggests = $users->findSearch('female', $this->getUserId());
                    }
                }
            }
            else
                $suggests = $users->findSearch('%', $this->getUserId());
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