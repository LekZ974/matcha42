<?php

namespace App\AppBundle;
use App\AppBundle\Models\Pictures;

/**
 * @author Alexandre Hoareau <ahoareau@student.42.fr>
 */
class Controller
{
    protected $app;
    protected $view;
    protected $router;
    protected $flash;

    public function __construct($container)
    {
        $this->app = $container;
        $this->view = $container->view;
        $this->flash = $container->flash;
        $this->router = $container->router;
    }

    public function isLogged()
    {
        if (isset($_SESSION['user']) && !empty($_SESSION['user']))
        {
            $co = new IsConnected($this->app);
            $co->connect();
            return true;
        }
        return false;
    }

    public function getUserId()
    {
        if ($this->isLogged())
        {
            return ($_SESSION['user']['id']);
        }
        return false;
    }

    public function getProfilPic()
    {
        $img = new Pictures($this->app);
        $img = $img->getProfilPic($this->getUserId());
        return $img;
    }
}