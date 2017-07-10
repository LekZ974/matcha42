<?php

namespace App\AppBundle;

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
}