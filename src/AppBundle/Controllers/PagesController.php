<?php

namespace App\AppBundle\Controllers;

use App\AppBundle\Controller;
use App\AppBundle\Models\Users;
use App\AppBundle\Security;

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
        if ($this->isLogged())
        {
            $users = new Users($this->app);

            $suggests = [];
            $genderF = Security::secureDB($_POST['genderF']);
            $genderM = Security::secureDB($_POST['genderM']);
            $oriBi = Security::secureDB($_POST['oriBi']);
            $oriHetero = Security::secureDB($_POST['oriHetero']);
            $oriHomo = Security::secureDB($_POST['oriHomo']);
            $terms = Security::secureXSS($_GET['terms']);
            $tags = Security::secureInput($_GET['tags']);
            if ($this->isLogged())
            {
                if (isset($terms) && !empty($terms)) {
                    $suggests = $users->findSearch($terms, $this->getUserId());
                }
                elseif (isset($tags) && !empty($tags)) {
                    $suggests = $users->getUsersByInterest($this->getUserId(), $tags);
                }
                elseif (!empty($genderF) || !empty($genderM) || !empty($oriBi) || !empty($oriHetero) || !empty($oriHomo))
                {
                    if ($genderF === 'on' && $genderM === 'on') {
                        if ($oriHetero == 'on') {
                            $suggests = $users->getUsersByOrientation($this->getUserId(), 'hetero');
                        }
                        elseif ($oriHomo == 'on') {
                            $suggests = $users->getUsersByOrientation($this->getUserId(), 'homo');
                        }
                        elseif ($oriBi == 'on') {
                            $suggests = $users->getUsersByOrientation($this->getUserId(), 'bisexual');
                        }
                        else {
                            $suggests = $users->findSearch('%', $this->getUserId());
                        }
                    }
                    else if ($genderM === 'on') {
                            if ($oriHetero == 'on') {
                                $suggests = $users->getSuggest($this->getUserId(), 'man', 'female');
                            }
                            elseif ($oriHomo == 'on') {
                                $suggests = $users->getSuggest($this->getUserId(), 'man', 'male');
                            }
                            elseif ($oriBi == 'on') {
                                $suggests = $users->getSuggest($this->getUserId(), 'man', 'other');
                            }
                            else {
                                $suggests = $users->findSearch('male', $this->getUserId());
                        }
                    }
                    else if ($genderF === 'on') {
                        if ($oriHetero == 'on') {
                            $suggests = $users->getSuggest($this->getUserId(), 'woman', 'male');
                        }
                        elseif ($oriHomo == 'on') {
                            $suggests = $users->getSuggest($this->getUserId(), 'woman', 'female');
                        }
                        elseif ($oriBi == 'on') {
                            $suggests = $users->getSuggest($this->getUserId(), 'woman', 'other');
                        }
                        else {
                            $suggests = $users->findSearch('female', $this->getUserId());
                        }
                    }
                    elseif ($oriHetero == 'on') {
                        $suggests = array_merge($users->getSuggest($this->getUserId(), 'woman', 'male'), $users->getSuggest($this->getUserId(), 'man', 'female'));
                    }
                    elseif ($oriHomo == 'on') {
                        $suggests = array_merge($users->getSuggest($this->getUserId(), 'woman', 'female'), $users->getSuggest($this->getUserId(), 'man', 'male'));
                    }
                    elseif ($oriBi == 'on') {
                        $suggests = array_merge($users->getSuggest($this->getUserId(), 'man', 'other'), $users->getSuggest($this->getUserId(), 'woman', 'other'));
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
        $this->app->flash->addMessage('warning', 'Sign in or register you');


        return $response->withStatus(302)->withHeader('Location', $this->app->router->pathFor('signUp'));
    }
}