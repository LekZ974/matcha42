<?php

namespace App\AppBundle;


use App\AppBundle\Models\Users;

class IsConnected
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function connect()
    {
        if (isset($_SESSION['user']) && !empty($_SESSION['user']))
        {
            $idUser = $_SESSION['user']['id'];
            $user = new Users($this->app);
            $user->updatedLogin($idUser);
        }
    }

}