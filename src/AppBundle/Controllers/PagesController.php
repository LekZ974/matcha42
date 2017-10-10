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

            if (isset($_GET['terms']) && !empty($_GET['terms'])) {
                $terms = Security::secureXSS($_GET['terms']);
                $suggests = $users->findSearch($terms, $this->getUserId());
            }
            elseif (isset($_GET['tags']) && !empty($_GET['tags'])) {
                $tags = Security::secureInput($_GET['tags']);
                $suggests = $users->getUsersByInterest($this->getUserId(), $tags);
            }
            elseif (!empty($_POST['genderF']) || !empty($_POST['genderM']) || !empty($_POST['oriBi']) || !empty($_POST['oriHetero']) || !empty($_POST['oriHomo']))
            {
                $genderF = $_POST['genderF'];
                $genderM = $_POST['genderM'];
                $oriBi = $_POST['oriBi'];
                $oriHetero = $_POST['oriHetero'];
                $oriHomo = $_POST['oriHomo'];
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
                elseif ($oriBi == 'on' && $oriHetero == 'on' && $oriHomo == 'on') {
                    $suggests = array_merge($users->getSuggest($this->getUserId(), 'man', 'other'), $users->getSuggest($this->getUserId(), 'woman', 'other'), $users->getSuggest($this->getUserId(), 'woman', 'male'), $users->getSuggest($this->getUserId(), 'man', 'female'), $users->getSuggest($this->getUserId(), 'man', 'male'), $users->getSuggest($this->getUserId(), 'woman', 'female'));
                }
                elseif ($oriHetero == 'on' && $oriHomo == 'on') {
                    $suggests = array_merge($users->getSuggest($this->getUserId(), 'woman', 'male'), $users->getSuggest($this->getUserId(), 'man', 'female'), $users->getSuggest($this->getUserId(), 'man', 'male'), $users->getSuggest($this->getUserId(), 'woman', 'female'));
                }
                elseif ($oriHomo == 'on' && $oriBi == 'on') {
                    $suggests = array_merge($users->getSuggest($this->getUserId(), 'woman', 'other'), $users->getSuggest($this->getUserId(), 'man', 'other'), $users->getSuggest($this->getUserId(), 'woman', 'female'), $users->getSuggest($this->getUserId(), 'man', 'male'));
                }
                elseif ($oriBi == 'on' && $oriHetero == 'on') {
                    $suggests = array_merge($users->getSuggest($this->getUserId(), 'man', 'other'), $users->getSuggest($this->getUserId(), 'woman', 'other'), $users->getSuggest($this->getUserId(), 'woman', 'male'), $users->getSuggest($this->getUserId(), 'man', 'female'));
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