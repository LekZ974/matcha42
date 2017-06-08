<?php

namespace App\AppBundle;

class Controller
{
    protected $app;
    protected $view;
    protected $router;

    public function __construct($container)
    {
        $this->app = $container;
        $this->view = $container->view;
        $this->router = $container->router;
    }
}